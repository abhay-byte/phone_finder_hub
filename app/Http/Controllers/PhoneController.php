<?php

namespace App\Http\Controllers;

use App\Repositories\BlogRepository;
use App\Repositories\CommentRepository;
use App\Repositories\PhoneRepository;
use App\Services\SEO\SEOData;
use App\Services\SEO\SeoManager;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class PhoneController extends Controller
{
    protected PhoneRepository $phones;

    protected BlogRepository $blogs;

    protected CommentRepository $comments;

    public function __construct(
        PhoneRepository $phones,
        BlogRepository $blogs,
        CommentRepository $comments
    ) {
        $this->phones = $phones;
        $this->blogs = $blogs;
        $this->comments = $comments;
    }

    public function index(Request $request, SeoManager $seo)
    {
        $sort = $request->input('sort', 'expert_score');

        $phones = $this->phones->all();

        usort($phones, function ($a, $b) use ($sort) {
            $aVal = $a->__get($sort) ?? 0;
            $bVal = $b->__get($sort) ?? 0;

            return $bVal <=> $aVal;
        });

        $phones = array_slice($phones, 0, 50);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($phones);
        }

        $cacheKey = 'phones_index_grid_html_'.$sort.'_v7';
        $gridHtml = Cache::remember($cacheKey, 300, function () use ($phones) {
            return view('phones.partials.grid', compact('phones'))->render();
        });

        $latestBlogs = Cache::remember('home_latest_blogs_v3', 300, function () {
            return $this->blogs->published(3);
        });

        $seo->set(new SEOData(
            title: 'PhoneFinderHub - Smartphone Data & Benchmarks',
            description: 'Compare the latest smartphones, view detailed specifications, benchmarks, and performance metrics.',
            url: route('home'),
        ));

        return view('phones.index', compact('gridHtml', 'sort', 'latestBlogs'));
    }

    public function grid(Request $request)
    {
        $sort = $request->input('sort', 'expert_score');

        $cacheKey = 'phones_grid_html_'.$sort.'_v4';
        $html = Cache::remember($cacheKey, 300, function () use ($sort) {
            $phones = $this->phones->all();

            usort($phones, function ($a, $b) use ($sort) {
                $aVal = $a->__get($sort) ?? 0;
                $bVal = $b->__get($sort) ?? 0;

                return $bVal <=> $aVal;
            });

            $phones = array_slice($phones, 0, 50);

            return view('phones.partials.grid', compact('phones'))->render();
        });

        return response($html)->header('Vary', 'X-Requested-With');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $phones = $this->phones->search($query);
        $phones = array_slice($phones, 0, 10);

        $results = array_map(function ($phone) {
            return [
                'id' => $phone->id,
                'name' => $phone->name,
                'brand' => $phone->brand,
                'image' => $phone->image_url,
                'full_name' => $phone->brand.' '.$phone->name,
                'price' => $phone->price,
                'value_score' => $phone->value_score ?? 'N/A',
                'chipset' => $phone->platform->chipset ?? null,
            ];
        }, $phones);

        return response()->json($results);
    }

    public function rankings(Request $request, SeoManager $seo)
    {
        $tab = $request->input('tab', 'overall');
        $page = (int) $request->input('page', 1);

        $maxDatabasePrice = Cache::remember('max_database_price', 3600, function () {
            $max = $this->phones->maxPrice();

            return ceil($max / 1000) * 1000;
        });

        $maxDatabaseAntutu = Cache::remember('max_database_antutu', 3600, function () {
            return $this->phones->maxAntutu() ?: 3000000;
        });

        $minPrice = (float) $request->input('min_price', 0);
        $maxPrice = (float) $request->input('max_price', $maxDatabasePrice);
        $minRam = (int) $request->input('min_ram', 4);
        $maxRam = (int) $request->input('max_ram', 24);
        $minStorage = (int) $request->input('min_storage', 64);
        $maxStorage = (int) $request->input('max_storage', 1024);
        $bootloader = $request->boolean('bootloader');
        $turnip = $request->boolean('turnip');
        $showUnverified = $request->boolean('show_unverified', false);

        $defaultSort = match ($tab) {
            'performance' => 'overall_score',
            'value' => 'value_score',
            'gaming' => 'gpx_score',
            'cms' => 'cms_score',
            'endurance' => 'endurance_score',
            default => 'expert_score',
        };

        $sort = $request->input('sort', $defaultSort);
        $direction = $request->input('direction', 'desc');

        $brandsKey = implode('_', $request->input('brands', []));
        $ipRatingsKey = implode('_', $request->input('ip_ratings', []));
        $minAntutu = (int) $request->input('min_antutu', 0);
        $maxAntutu = (int) $request->input('max_antutu', $maxDatabaseAntutu);

        $cacheKey = "rankings_{$tab}_{$sort}_{$direction}_{$page}_p{$minPrice}-{$maxPrice}_r{$minRam}-{$maxRam}_s{$minStorage}-{$maxStorage}_b{$bootloader}_t{$turnip}_un{$showUnverified}_br{$brandsKey}_ip{$ipRatingsKey}_a{$minAntutu}-{$maxAntutu}_html_v9";

        $queryParams = $request->query();

        $filterOptions = Cache::remember('ranking_filter_options_v2', 3600, function () {
            return [
                'brands' => $this->phones->brands(),
                'ip_ratings' => $this->phones->distinct('body.ip_rating'),
                'max_antutu' => $this->phones->maxAntutu() ?: 3000000,
            ];
        });

        $tableHtml = Cache::remember($cacheKey, 300, function () use (
            $tab, $sort, $direction, $page, $queryParams,
            $minPrice, $maxPrice, $minRam, $maxRam, $minStorage, $maxStorage,
            $bootloader, $turnip, $showUnverified, $request, $minAntutu, $maxAntutu
        ) {
            $result = $this->phones->rankings(
                [
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'min_ram' => $minRam,
                    'max_ram' => $maxRam,
                    'min_storage' => $minStorage,
                    'max_storage' => $maxStorage,
                    'bootloader' => $bootloader,
                    'turnip' => $turnip,
                    'show_unverified' => $showUnverified,
                    'brands' => $request->input('brands', []),
                    'ip_ratings' => $request->input('ip_ratings', []),
                    'min_antutu' => $minAntutu,
                    'max_antutu' => $maxAntutu,
                ],
                $sort,
                $direction,
                $page
            );

            $phones = new LengthAwarePaginator(
                $result['items'],
                $result['total'],
                $result['per_page'],
                $result['current_page'],
                [
                    'path' => \Illuminate\Support\Facades\Request::url(),
                    'query' => $queryParams,
                ]
            );

            $ranks = [];
            foreach ($result['items'] as $phone) {
                $ranks[$phone->id] = $phone->computed_rank ?? 0;
            }

            return view('phones.partials.rankings_table', compact(
                'phones', 'sort', 'direction', 'tab', 'ranks'
            ))->render();
        });

        $seoTitle = match ($tab) {
            'performance' => 'Top Performance Phones | PhoneFinderHub Rankings',
            'value' => 'Best Value Phones | PhoneFinderHub Rankings',
            'gaming' => 'Best Gaming Phones | PhoneFinderHub Rankings',
            'cms' => 'Best Camera Phones | PhoneFinderHub Rankings',
            'endurance' => 'Best Battery Life Phones | PhoneFinderHub Rankings',
            default => 'Smartphone Rankings | PhoneFinderHub',
        };

        $seo->set(new SEOData(
            title: $seoTitle,
            description: 'Browse our comprehensive smartphone rankings based on precise benchmarks and expert scores.',
            url: url()->current().($request->getQueryString() ? '?'.$request->getQueryString() : ''),
        ));

        return view('phones.rankings', compact(
            'tableHtml', 'sort', 'direction', 'tab',
            'minPrice', 'maxPrice', 'maxDatabasePrice',
            'minRam', 'maxRam', 'minStorage', 'maxStorage', 'bootloader', 'turnip', 'showUnverified', 'filterOptions'
        ));
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id, SeoManager $seo, Request $request)
    {
        $phone = Cache::remember('phone_data_v3_'.$id, 3600, function () use ($id) {
            return $this->phones->findOrFail($id);
        });

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($phone);
        }

        $phoneDetailsHtml = Cache::remember('phone_details_html_v3_'.$id, 3600, function () use ($phone) {
            return view('phones.partials.phone_details', compact('phone'))->render();
        });

        $comments = Cache::remember('phone_comments_v4_'.$phone->id, 60, function () use ($phone) {
            return $this->comments->forPhone($phone->id);
        });

        $totalComments = Cache::remember('phone_comments_count_v3_'.$phone->id, 60, function () use ($phone) {
            return $this->comments->countForPhone($phone->id);
        });

        $seo->set($phone->getSEOData());

        return view('phones.show', compact('phone', 'comments', 'phoneDetailsHtml', 'totalComments'));
    }

    public function update(Request $request, string $id)
    {
        //
    }

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
