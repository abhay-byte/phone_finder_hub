<?php

namespace App\Http\Controllers;

use App\Repositories\PhoneRepository;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    protected PhoneRepository $phones;

    public function __construct(PhoneRepository $phones)
    {
        $this->phones = $phones;
    }

    public function index(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $ids = array_filter($ids);

        if (empty($ids)) {
            $phones = collect();

            return view('phones.compare', compact('phones'));
        }

        $ids = array_slice($ids, 0, 4);
        $phones = $this->phones->findMany($ids);

        usort($phones, function ($a, $b) use ($ids) {
            $aIndex = array_search($a->id, $ids);
            $bIndex = array_search($b->id, $ids);

            return $aIndex <=> $bIndex;
        });

        return view('phones.compare', compact('phones'));
    }
}
