<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'value_score'); // Default to Value Score

        $query = \App\Models\Phone::with(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks']);

        if ($sort == 'value_score') {
             $query->orderByRaw('overall_score / price desc');
        } elseif ($sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort == 'overall_score') {
            $query->orderBy('overall_score', 'desc');
        } elseif ($sort == 'ueps_score') {
            $query->orderBy('ueps_score', 'desc');
        } else {
             $query->orderBy('ueps_score', 'desc');
        }

        $phones = $query->take(50)->get();

        return view('phones.index', compact('phones', 'sort'));
    }

    public function grid(Request $request)
    {
        $sort = $request->input('sort', 'value_score');

        $query = \App\Models\Phone::with(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks']);

        if ($sort == 'value_score') {
             $query->orderByRaw('overall_score / price desc');
        } elseif ($sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort == 'overall_score') {
            $query->orderBy('overall_score', 'desc');
        } elseif ($sort == 'ueps_score') {
            $query->orderBy('ueps_score', 'desc');
        } else {
             $query->orderBy('ueps_score', 'desc');
        }

        $phones = $query->take(50)->get();

        return response()
            ->view('phones.partials.grid', compact('phones'))
            ->header('Vary', 'X-Requested-With')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $phones = \App\Models\Phone::with('platform')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('brand_name', 'like', "%{$query}%")
            ->orWhereHas('platform', function ($q) use ($query) {
                $q->where('chipset', 'like', "%{$query}%")
                  ->orWhere('cpu', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'brand_name', 'image_url', 'price', 'value_score') // Added price, value_score
            ->limit(10)
            ->get();

         $results = $phones->map(function ($phone) {
            return [
                'id' => $phone->id,
                'name' => $phone->name,
                'brand' => $phone->brand_name,
                'image' => $phone->image_url,
                'full_name' => $phone->brand_name . ' ' . $phone->name,
                'price' => $phone->price,
                'value_score' => $phone->value_score ?? 'N/A',
                'chipset' => $phone->platform->chipset ?? null, // Optional: return chipset for UI
            ];
        });

        return response()->json($results);
    }

    public function rankings(Request $request)
    {
        $tab = $request->input('tab', 'ueps'); // Default tab
        
        // Define default sort based on tab if not provided
        $defaultSort = match($tab) {
            'performance' => 'overall_score',
            'value' => 'value_score',
            default => 'ueps_score',
        };

        $sort = $request->input('sort', $defaultSort);
        $direction = $request->input('direction', 'desc');

        // Pre-calculate ranks based on the tab's primary metric
        $rankMetric = match($tab) {
            'performance' => 'overall_score',
            'value' => 'value_score',
            default => 'ueps_score',
        };

        // Fetch all phones ordered by the metric to determine rank
        // For Value Score, we need to order by the calculated value
        if ($rankMetric === 'value_score') {
             $rankedPhones = \App\Models\Phone::select('id', 'price', 'overall_score')
                ->get()
                ->sortByDesc(function ($phone) {
                    return $phone->value_score; // Use accessor
                });
        } else {
             $rankedPhones = \App\Models\Phone::select('id', $rankMetric)
                ->orderBy($rankMetric, 'desc')
                ->get();
        }

        // Map Phone ID => Rank
        $ranks = [];
        $currentRank = 1;
        foreach ($rankedPhones as $phone) {
            $ranks[$phone->id] = $currentRank++;
        }

        $query = \App\Models\Phone::with(['benchmarks', 'battery', 'body']);

        // Join benchmarks table if sorting by benchmark fields to allow ordering
        if (in_array($sort, ['antutu_score', 'geekbench_multi', 'geekbench_single', 'dmark_wild_life_extreme', 'battery_endurance_hours'])) {
             $query->leftJoin('benchmarks', 'phones.id', '=', 'benchmarks.phone_id')
                   ->select('phones.*') // Select phones.* to avoid id conflicts
                   ->orderBy('benchmarks.' . $sort, $direction);
        } elseif ($sort == 'price') {
            $query->orderBy('price', $direction);
        } elseif ($sort == 'ueps_score') {
            $query->orderBy('ueps_score', $direction);
        } elseif ($sort == 'value_score') { // Sort purely by the accessor logic if needed, but DB sort is harder for calculated attributes without raw SQL
             // For value_score sort, we need raw SQL since it's calculated
             if ($sort == 'value_score') {
                $query->orderByRaw('overall_score / price ' . $direction);
             }
        } elseif ($sort == 'price_per_ueps') {
             $query->orderByRaw('price / ueps_score ' . $direction);
        } elseif ($sort == 'price_per_fpi') {
             $query->orderByRaw('price / overall_score ' . $direction);
        } else {
            $query->orderBy('overall_score', $direction); // Default fallthrough to FPI/Overall
        }

        $phones = $query->paginate(50)->withQueryString();

        return view('phones.rankings', compact('phones', 'sort', 'direction', 'tab', 'ranks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Phone $phone)
    {
        $phone->load(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks']);
        
        // Lazy update UEPS score to ensure consistency
        $ueps = \App\Services\UepsScoringService::calculate($phone);
        if ($phone->ueps_score != $ueps['total_score']) {
            $phone->ueps_score = $ueps['total_score'];
            $phone->saveQuietly();
        }

        // Lazy update FPI (Overall Score) to ensure consistency
        $fpi = $phone->calculateFPI();
        if (is_array($fpi) && $phone->overall_score != $fpi['total']) {
            $phone->overall_score = $fpi['total'];
            $phone->saveQuietly();
        }

        return view('phones.show', compact('phone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function methodology()
    {
        return view('ueps.methodology');
    }

    public function fpiMethodology()
    {
        return view('fpi.methodology');
    }
}
