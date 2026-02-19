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
        $sort = $request->input('sort', 'expert_score'); // Default to Expert Score

        // Cache entire HTML response for 5 minutes
        $cacheKey = 'phones_index_html_' . $sort . '_v2';
        return Cache::remember($cacheKey, 300, function () use ($sort) {
            $query = \App\Models\Phone::query();

            if ($sort == 'expert_score') {
                 $query->orderBy('expert_score', 'desc');
            } elseif ($sort == 'value_score') {
                 $query->orderBy('value_score', 'desc');
            } elseif ($sort == 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($sort == 'overall_score') {
                $query->orderBy('overall_score', 'desc');
            } elseif ($sort == 'ueps_score') {
                $query->orderBy('ueps_score', 'desc');
            } else {
                $query->orderBy('expert_score', 'desc');
            }

            $phones = $query->with(['platform', 'benchmarks', 'battery', 'body'])->take(50)->get();
            
            return view('phones.index', compact('phones', 'sort'))->render();
        });
    }

    public function grid(Request $request)
    {
        $sort = $request->input('sort', 'expert_score');

        $cacheKey = 'phones_grid_html_' . $sort . '_v3';
        $html = Cache::remember($cacheKey, 300, function () use ($sort) {
            // Only load minimal relations for grid view
            $query = \App\Models\Phone::query()->with(['platform', 'benchmarks']);

            if ($sort == 'expert_score') {
                 $query->orderBy('expert_score', 'desc');
            } elseif ($sort == 'value_score') {
                 $query->orderBy('value_score', 'desc');
            } elseif ($sort == 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($sort == 'overall_score') {
                $query->orderBy('overall_score', 'desc');
            } elseif ($sort == 'ueps_score') {
                $query->orderBy('ueps_score', 'desc');
            } else {
                 $query->orderBy('expert_score', 'desc');
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
        $tab = $request->input('tab', 'overall'); // Default tab to Overall
        $page = $request->input('page', 1);
        
        // Dynamic Price Range
        $maxDatabasePrice = \App\Models\Phone::max('price');
        // Round up to nearest 1000 for cleaner UI
        $maxDatabasePrice = ceil($maxDatabasePrice / 1000) * 1000;

        // Filter Params
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', $maxDatabasePrice);
        
        // Advanced Filters
        $minRam = $request->input('min_ram', 4);
        $maxRam = $request->input('max_ram', 24);
        $minStorage = $request->input('min_storage', 64);
        $maxStorage = $request->input('max_storage', 1024); // 1TB
        $bootloader = $request->boolean('bootloader');
        $turnip = $request->boolean('turnip');
        $showUnverified = $request->boolean('show_unverified', false);

        // Define ranking metric based on tab
        $rankExpression = match($tab) {
            'performance' => 'overall_score',
            'value' => 'value_score',
            'gaming' => 'gpx_score',
            'cms' => 'cms_score',
            'endurance' => 'endurance_score',
            default => 'expert_score', // Default to Expert/Overall Score
        };

        $defaultSort = match($tab) {
            'performance' => 'overall_score',
            'value' => 'value_score',
            'gaming' => 'gpx_score',
            'cms' => 'cms_score',
            'endurance' => 'endurance_score',
            default => 'expert_score',
        };

        $sort = $request->input('sort', $defaultSort);
        $direction = $request->input('direction', 'desc');

        // Cache key includes all filter parameters
        $brandsKey = implode('_', $request->input('brands', []));
        $ipRatingsKey = implode('_', $request->input('ip_ratings', []));
        $minAntutu = $request->input('min_antutu', 0);
        $maxAntutu = $request->input('max_antutu', 3000000);

        $cacheKey = "rankings_{$tab}_{$sort}_{$direction}_{$page}_p{$minPrice}-{$maxPrice}_r{$minRam}-{$maxRam}_s{$minStorage}-{$maxStorage}_b{$bootloader}_t{$turnip}_un{$showUnverified}_br{$brandsKey}_ip{$ipRatingsKey}_a{$minAntutu}-{$maxAntutu}_html_v8";

        $queryParams = $request->query();

        // Fetch Filter Options (Cached)
        $filterOptions = Cache::remember('ranking_filter_options_v1', 3600, function() {
            return [
                'brands' => \App\Models\Phone::distinct()->orderBy('brand')->pluck('brand')->toArray(),
                'ip_ratings' => \App\Models\SpecBody::distinct()->whereNotNull('ip_rating')->where('ip_rating', '!=', '')->orderBy('ip_rating')->pluck('ip_rating')->toArray(),
                'max_antutu' => \App\Models\Benchmark::max('antutu_score') ?? 3000000,
            ];
        });

        $html = Cache::remember($cacheKey, 300, function() use ($tab, $sort, $direction, $rankExpression, $page, $queryParams, $minPrice, $maxPrice, $maxDatabasePrice, $minRam, $maxRam, $minStorage, $maxStorage, $bootloader, $turnip, $showUnverified, $filterOptions, $request) {
            
            // Subquery to calculate Rank for ALL phones based on the Tab's metric
            $rankingSubquery = \App\Models\Phone::query()
                ->whereBetween('price', [$minPrice, $maxPrice])
                ->when(!$showUnverified, function($q) {
                    $q->whereHas('benchmarks', function($sq) {
                        $sq->whereNotNull('antutu_score')
                           ->whereNotNull('geekbench_single')
                           ->whereNotNull('geekbench_multi')
                           ->whereNotNull('dmark_wild_life_extreme');
                    });
                })
                ->whereHas('platform', function($q) use ($minRam, $maxRam, $minStorage, $maxStorage, $bootloader, $turnip) {
                    $q->where(function($query) use ($minRam, $maxRam) {
                         $query->where('ram_max', '>=', $minRam)
                               ->where('ram_min', '<=', $maxRam);
                    })
                    ->where(function($query) use ($minStorage, $maxStorage) {
                         $query->where('storage_max', '>=', $minStorage)
                               ->where('storage_min', '<=', $maxStorage);
                    });
                    
                    if ($bootloader) {
                        $q->where('bootloader_unlockable', true);
                    }
                    if ($turnip) {
                        $q->where('turnip_support', true);
                    }
                })
                // Brand Filter
                ->when($request->has('brands'), function($q) use ($request) {
                     $q->whereIn('brand', $request->input('brands'));
                })
                // IP Rating Filter
                ->when($request->has('ip_ratings'), function($q) use ($request) {
                     $q->whereHas('body', function($sq) use ($request) {
                         $sq->whereIn('ip_rating', $request->input('ip_ratings'));
                     });
                })
                // AnTuTu Filter
                ->when($request->filled('min_antutu') || $request->filled('max_antutu'), function($q) use ($request) {
                    $q->whereHas('benchmarks', function($sq) use ($request) {
                        if ($request->filled('min_antutu')) {
                            $sq->where('antutu_score', '>=', $request->input('min_antutu'));
                        }
                        if ($request->filled('max_antutu')) {
                            $sq->where('antutu_score', '<=', $request->input('max_antutu'));
                        }
                    });
                })
                ->select('id')
                ->selectRaw("RANK() OVER (ORDER BY {$rankExpression} DESC) as computed_rank");

            $query = \App\Models\Phone::query()
                ->whereBetween('price', [$minPrice, $maxPrice])
                ->when(!$showUnverified, function($q) {
                    $q->whereHas('benchmarks', function($sq) {
                        $sq->whereNotNull('antutu_score')
                           ->whereNotNull('geekbench_single')
                           ->whereNotNull('geekbench_multi')
                           ->whereNotNull('dmark_wild_life_extreme');
                    });
                })
                ->whereHas('platform', function($q) use ($minRam, $maxRam, $minStorage, $maxStorage, $bootloader, $turnip) {
                    $q->where(function($query) use ($minRam, $maxRam) {
                         $query->where('ram_max', '>=', $minRam)
                               ->where('ram_min', '<=', $maxRam);
                    })
                    ->where(function($query) use ($minStorage, $maxStorage) {
                         $query->where('storage_max', '>=', $minStorage)
                               ->where('storage_min', '<=', $maxStorage);
                    });
                    
                    if ($bootloader) {
                        $q->where('bootloader_unlockable', true);
                    }
                    if ($turnip) {
                        $q->where('turnip_support', true);
                    }
                })
                // Brand Filter
                ->when($request->has('brands'), function($q) use ($request) {
                     $q->whereIn('brand', $request->input('brands'));
                })
                // IP Rating Filter
                ->when($request->has('ip_ratings'), function($q) use ($request) {
                     $q->whereHas('body', function($sq) use ($request) {
                         $sq->whereIn('ip_rating', $request->input('ip_ratings'));
                     });
                })
                // AnTuTu Filter
                ->when($request->filled('min_antutu') || $request->filled('max_antutu'), function($q) use ($request) {
                    $q->whereHas('benchmarks', function($sq) use ($request) {
                        if ($request->filled('min_antutu')) {
                            $sq->where('antutu_score', '>=', $request->input('min_antutu'));
                        }
                        if ($request->filled('max_antutu')) {
                            $sq->where('antutu_score', '<=', $request->input('max_antutu'));
                        }
                    });
                })
                ->joinSub($rankingSubquery, 'rankings_table', function ($join) {
                    $join->on('phones.id', '=', 'rankings_table.id');
                });

            // Join benchmarks table if sorting by benchmark fields
            if (in_array($sort, ['antutu_score', 'geekbench_multi', 'geekbench_single', 'dmark_wild_life_extreme', 'battery_endurance_hours', 'dxomark_score', 'phonearena_camera_score'])) {
                 $query->with(['benchmarks', 'battery', 'body'])
                       ->leftJoin('benchmarks', 'phones.id', '=', 'benchmarks.phone_id')
                       ->select('phones.*', 'rankings_table.computed_rank') // Select phones.* explicitly
                       ->orderBy('benchmarks.' . $sort, $direction);
            } elseif ($sort == 'price') {
                $query->select('phones.*', 'rankings_table.computed_rank')
                      ->orderBy('price', $direction);
            } elseif ($sort == 'ueps_score') {
                $query->select('phones.*', 'rankings_table.computed_rank')
                      ->orderBy('ueps_score', $direction);
            } elseif ($sort == 'value_score') {
                 $query->select('phones.*', 'rankings_table.computed_rank')
                       ->orderBy('value_score', $direction);
            } elseif ($sort == 'expert_score') {
                 $query->select('phones.*', 'rankings_table.computed_rank')
                       ->orderBy('expert_score', $direction);
            } elseif ($sort == 'gpx_score') {
                 $query->select('phones.*', 'rankings_table.computed_rank')
                       ->orderBy('gpx_score', $direction);
            } elseif ($sort == 'cms_score') {
                 $query->select('phones.*', 'rankings_table.computed_rank')
                       ->orderBy('cms_score', $direction);
            } elseif ($sort == 'endurance_score') {
                 $query->select('phones.*', 'rankings_table.computed_rank')
                       ->orderBy('endurance_score', $direction);
            } elseif ($sort == 'price_per_ueps') {
                 $query->select('phones.*', 'rankings_table.computed_rank')
                       ->orderByRaw('price / ueps_score ' . $direction);
            } elseif ($sort == 'price_per_fpi') {
                 $query->select('phones.*', 'rankings_table.computed_rank')
                       ->orderByRaw('price / overall_score ' . $direction);
            } elseif ($sort == 'price_per_cms') {
                 $query->select('phones.*', 'rankings_table.computed_rank')
                       ->orderByRaw('price / cms_score ' . $direction);
            } elseif ($sort == 'price_per_endurance') {
                 $query->select('phones.*', 'rankings_table.computed_rank')
                       ->orderByRaw('price / endurance_score ' . $direction);
            } else {
                // Default sort (usually matches the tab metric)
                $query->select('phones.*', 'rankings_table.computed_rank')
                      ->orderBy('expert_score', $direction);
            }

            // Optimization: Manual pagination
            $perPage = 50;
            $total = \App\Models\Phone::whereBetween('price', [$minPrice, $maxPrice])
                ->when(!$showUnverified, function($q) {
                    $q->whereHas('benchmarks', function($sq) {
                        $sq->whereNotNull('antutu_score')
                           ->whereNotNull('geekbench_single')
                           ->whereNotNull('geekbench_multi')
                           ->whereNotNull('dmark_wild_life_extreme');
                    });
                })
                ->whereHas('platform', function($q) use ($minRam, $maxRam, $minStorage, $maxStorage, $bootloader, $turnip) {
                    $q->where(function($query) use ($minRam, $maxRam) {
                         $query->where('ram_max', '>=', $minRam)
                               ->where('ram_min', '<=', $maxRam);
                    })
                    ->where(function($query) use ($minStorage, $maxStorage) {
                         $query->where('storage_max', '>=', $minStorage)
                               ->where('storage_min', '<=', $maxStorage);
                    });
                    
                    if ($bootloader) {
                        $q->where('bootloader_unlockable', true);
                    }
                    if ($turnip) {
                        $q->where('turnip_support', true);
                    }
                })->count();
            
            $items = $query->skip(($page - 1) * $perPage)
                           ->take($perPage)
                           ->get();

            $phones = new \Illuminate\Pagination\LengthAwarePaginator(
                $items, 
                $total, 
                $perPage, 
                $page, 
                [
                    'path' => \Illuminate\Support\Facades\Request::url(),
                    'query' => $queryParams,
                ]
            );

            // Extract ranks for the view compatibility
            // The view likely expects $ranks[$id] = rank
            $ranks = $phones->pluck('computed_rank', 'id')->toArray();

            return view('phones.rankings', compact(
                'phones', 'sort', 'direction', 'tab', 'ranks', 
                'minPrice', 'maxPrice', 'maxDatabasePrice',
                'minRam', 'maxRam', 'minStorage', 'maxStorage', 'bootloader', 'turnip', 'showUnverified', 'filterOptions'
            ))->render();
        });

        return response($html);
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
    public function uepsMethodology()
    {
        return view('ueps.methodology');
    }

    public function cmsMethodology()
    {
        return view('cms.methodology');
    }

    public function fpiMethodology()
    {
        return view('fpi.methodology');
    }

    public function enduranceMethodology()
    {
        return view('endurance.methodology');
    }

    public function gpxMethodology()
    {
        return view('docs.gpx');
    }
}
