<?php

namespace App\Repositories;

use App\Models\Phone;

class PhoneRepository extends FirestoreRepository
{
    protected string $collection = 'phones';

    protected function makeModel(array $data): object
    {
        return new Phone($data);
    }

    protected function modelClass(): string
    {
        return Phone::class;
    }

    /**
     * Search phones by name, brand, or chipset.
     */
    public function search(string $query): array
    {
        $all = $this->all();
        $lower = strtolower($query);

        return array_values(array_filter($all, function (Phone $phone) use ($lower) {
            if (str_contains(strtolower($phone->name), $lower)) {
                return true;
            }
            if (str_contains(strtolower($phone->brand), $lower)) {
                return true;
            }
            $chipset = strtolower($phone->platform->chipset ?? '');
            if (str_contains($chipset, $lower)) {
                return true;
            }

            return false;
        }));
    }

    /**
     * Get distinct brands.
     */
    public function brands(): array
    {
        return $this->distinct('brand');
    }

    /**
     * Get max price.
     */
    public function maxPrice(): float
    {
        return $this->max('price');
    }

    /**
     * Get max AnTuTu score from embedded benchmarks.
     */
    public function maxAntutu(): float
    {
        $docs = $this->client->listDocuments($this->collection);
        $max = 0;
        foreach ($docs as $doc) {
            $score = $doc['benchmarks']['antutu_score'] ?? 0;
            if ($score > $max) {
                $max = $score;
            }
        }

        return (float) $max;
    }

    /**
     * Get paginated rankings with PHP-level filtering and sorting.
     */
    public function rankings(
        array $filters,
        string $sort,
        string $direction,
        int $page,
        int $perPage = 50
    ): array {
        $phones = $this->all();

        // Apply filters
        $phones = array_filter($phones, function (Phone $phone) use ($filters) {
            // Price filter
            if (isset($filters['min_price']) && $phone->price < $filters['min_price']) {
                return false;
            }
            if (isset($filters['max_price']) && $phone->price > $filters['max_price']) {
                return false;
            }

            // Verified filter
            if (! ($filters['show_unverified'] ?? false)) {
                $bench = $phone->benchmarks;
                if (! $bench || ! isset($bench->antutu_score) || ! isset($bench->geekbench_single) || ! isset($bench->geekbench_multi) || ! isset($bench->dmark_wild_life_extreme)) {
                    return false;
                }
            }

            // Platform filters (RAM, storage, bootloader, turnip)
            $platform = $phone->platform;
            if ($platform) {
                $ramMax = $platform->ram_max ?? 0;
                $ramMin = $platform->ram_min ?? 0;
                $minRam = $filters['min_ram'] ?? 0;
                $maxRam = $filters['max_ram'] ?? 999;
                if ($ramMax < $minRam || $ramMin > $maxRam) {
                    return false;
                }

                $storageMax = $platform->storage_max ?? 0;
                $storageMin = $platform->storage_min ?? 0;
                $minStorage = $filters['min_storage'] ?? 0;
                $maxStorage = $filters['max_storage'] ?? 99999;
                if ($storageMax < $minStorage || $storageMin > $maxStorage) {
                    return false;
                }

                if (($filters['bootloader'] ?? false) && ! ($platform->bootloader_unlockable ?? false)) {
                    return false;
                }
                if (($filters['turnip'] ?? false) && ! ($platform->turnip_support ?? false)) {
                    return false;
                }
            }

            // Brand filter
            if (! empty($filters['brands']) && ! in_array($phone->brand, $filters['brands'])) {
                return false;
            }

            // IP rating filter
            if (! empty($filters['ip_ratings'])) {
                $body = $phone->body;
                $ipRating = $body->ip_rating ?? null;
                if (! $ipRating || ! in_array($ipRating, $filters['ip_ratings'])) {
                    return false;
                }
            }

            // AnTuTu filter
            $bench = $phone->benchmarks;
            if ($bench) {
                $antutu = $bench->antutu_score ?? 0;
                if (isset($filters['min_antutu']) && $antutu < $filters['min_antutu']) {
                    return false;
                }
                if (isset($filters['max_antutu']) && $antutu > $filters['max_antutu']) {
                    return false;
                }
            }

            return true;
        });

        // Sort
        usort($phones, function (Phone $a, Phone $b) use ($sort, $direction) {
            $aVal = $this->getSortValue($a, $sort);
            $bVal = $this->getSortValue($b, $sort);
            $cmp = $aVal <=> $bVal;

            return strtolower($direction) === 'desc' ? -$cmp : $cmp;
        });

        // Add rank
        $rank = 1;
        foreach ($phones as $phone) {
            $phone->computed_rank = $rank++;
        }

        // Paginate
        $total = count($phones);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($phones, $offset, $perPage);

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
        ];
    }

    /**
     * Extract sort value from phone based on sort field.
     */
    protected function getSortValue(Phone $phone, string $sort): float
    {
        return match ($sort) {
            'price' => (float) ($phone->price ?? 0),
            'expert_score' => (float) ($phone->expert_score ?? 0),
            'value_score' => (float) ($phone->value_score ?? 0),
            'ueps_score' => (float) ($phone->ueps_score ?? 0),
            'gpx_score' => (float) ($phone->gpx_score ?? 0),
            'cms_score' => (float) ($phone->cms_score ?? 0),
            'endurance_score' => (float) ($phone->endurance_score ?? 0),
            'antutu_score' => (float) ($phone->benchmarks->antutu_score ?? 0),
            'geekbench_multi' => (float) ($phone->benchmarks->geekbench_multi ?? 0),
            'geekbench_single' => (float) ($phone->benchmarks->geekbench_single ?? 0),
            'dmark_wild_life_extreme' => (float) ($phone->benchmarks->dmark_wild_life_extreme ?? 0),
            'battery_endurance_hours' => (float) ($phone->benchmarks->battery_endurance_hours ?? 0),
            'dxomark_score' => (float) ($phone->benchmarks->dxomark_score ?? 0),
            'phonearena_camera_score' => (float) ($phone->benchmarks->phonearena_camera_score ?? 0),
            'price_per_ueps' => $phone->ueps_score > 0 ? ($phone->price / $phone->ueps_score) : 0,
            'price_per_fpi' => $phone->overall_score > 0 ? ($phone->price / $phone->overall_score) : 0,
            'price_per_cms' => $phone->cms_score > 0 ? ($phone->price / $phone->cms_score) : 0,
            'price_per_endurance' => $phone->endurance_score > 0 ? ($phone->price / $phone->endurance_score) : 0,
            default => (float) ($phone->expert_score ?? 0),
        };
    }
}
