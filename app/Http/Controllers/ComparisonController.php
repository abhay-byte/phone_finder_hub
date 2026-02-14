<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phone;

class ComparisonController extends Controller
{
    /**
     * Display the comparison page.
     */
    public function index(Request $request)
    {
        // Get IDs from query string (comma separated)
        $ids = explode(',', $request->query('ids', ''));
        
        // Filter out empty or non-numeric IDs
        $ids = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        if (empty($ids)) {
            $phones = collect();
            return view('phones.compare', compact('phones'));
        }

        // Limit to 3 or 4 phones for UI sanity (let's say 4)
        $ids = array_slice($ids, 0, 4);

        $phones = Phone::whereIn('id', $ids)
            ->with(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks'])
            ->get();
            
        // Maintain the order of IDs as requested
        $phones = $phones->sortBy(function($model) use ($ids) {
            return array_search($model->id, $ids);
        })->values();

        return view('phones.compare', compact('phones'));
    }
}
