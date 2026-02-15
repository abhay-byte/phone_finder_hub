<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PhoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'value_score'); // Default to Value Score

        // Cache entire HTML response for 5 minutes
        $cacheKey = 'phones_index_html_' . $sort;
        return Cache::remember($cacheKey, 300, function () use ($sort) {
            $query = \App\Models\Phone::query();

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

            $phones = $query->with(['platform', 'benchmarks', 'battery', 'body'])->take(50)->get();
            
            return view('phones.index', compact('phones', 'sort'))->render();
        });
    }

    public function grid(Request $request)
    {
        $sort = $request->input('sort', 'value_score');

        $cacheKey = 'phones_grid_html_' . $sort;
        $html = Cache::remember($cacheKey, 300, function () use ($sort) {
            // Only load minimal relations for grid view
            $query = \App\Models\Phone::query()->with(['platform', 'benchmarks']);

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
            return view('phones.partials.grid', compact('phones'))->render();
        });

        return response($html)
            ->header('Vary', 'X-Requested-With');
            // Removed no-cache headers to allow browser caching if desired, or keep them if strict freshness is needed.
            // For now, removing them to let the server-side cache do the work.
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $phones = \App\Models\Phone::with('platform')
            ->where('name', 'ilike', "%{$query}%")
            ->orWhere('brand', 'ilike', "%{$query}%")
            ->orWhereHas('platform', function ($q) use ($query) {
                $q->where('chipset', 'ilike', "%{$query}%")
                  ->orWhere('cpu', 'ilike', "%{$query}%");
            })
            ->select('id', 'name', 'brand', 'image_url', 'price', 'overall_score')
            ->limit(10)
            ->get();

         $results = $phones->map(function ($phone) {
            return [
                'id' => $phone->id,
                'name' => $phone->name,
                'brand' => $phone->brand,
                'image' => $phone->image_url,
                'full_name' => $phone->brand . ' ' . $phone->name,
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
        $page = $request->input('page', 1);
        
        // Define default sort based on tab if not provided
        $defaultSort = match($tab) {
            'performance' => 'overall_score',
            'value' => 'value_score',
            default => 'ueps_score',
        };

        $sort = $request->input('sort', $defaultSort);
        $direction = $request->input('direction', 'desc');

        // Cache key includes all query parameters
        $cacheKey = "rankings_{$tab}_{$sort}_{$direction}_{$page}";

        $result = Cache::remember($cacheKey, 300, function() use ($tab, $sort, $direction) {
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

            $query = \App\Models\Phone::query();

            // Join benchmarks table if sorting by benchmark fields to allow ordering
            if (in_array($sort, ['antutu_score', 'geekbench_multi', 'geekbench_single', 'dmark_wild_life_extreme', 'battery_endurance_hours'])) {
                 $query->with(['benchmarks', 'battery', 'body'])
                       ->leftJoin('benchmarks', 'phones.id', '=', 'benchmarks.phone_id')
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

            return ['phones' => $phones, 'ranks' => $ranks];
        });

        $phones = $result['phones'];
        $ranks = $result['ranks'];

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
    public function show($id)
    {
        // Check cache first to avoid DB query (Model Binding bypass)
        return Cache::remember('phone_show_' . $id, 3600, function () use ($id) {
            $phone = \App\Models\Phone::with(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks'])
                ->findOrFail($id);
                
            return view('phones.show', compact('phone'))->render();
        });
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
