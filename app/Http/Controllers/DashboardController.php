<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialIn;
use App\Models\MaterialOut;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\ProductOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = $request->month ?? date('m');
        $currentYear = date('Y');

        $months = [
            __('January'), __('February'), __('March'), __('April'), __('May'), __('June'),
            __('July'), __('August'), __('September'), __('October'), __('November'), __('December')
        ];

        $stats['material']['ins']['nCategories'] =
            MaterialIn::select(DB::raw('count(id) as count, type'))
            ->whereMonth('at', '=', $currentMonth)
            ->whereYear('at', '=', $currentYear)
            ->groupBy('type')
            ->get();

        $stats['material']['ins']['total'] =
            $stats['material']['ins']['nCategories']->sum('count');

        $stats['material']['ins']['color'] = 'primary';

        $stats['material']['outs']['nCategories'] =
            MaterialOut::select(DB::raw('count(id) as count, type'))
            ->whereMonth('at', '=', $currentMonth)
            ->whereYear('at', '=', $currentYear)
            ->groupBy('type')
            ->get();

        $stats['material']['outs']['total'] =
            $stats['material']['outs']['nCategories']->sum('count');

        $stats['product']['ins']['nCategories'] =
            ProductIn::select(DB::raw('count(id) as count, type'))->whereMonth('at', '=', $currentMonth)->whereYear('at', '=', $currentYear)->groupBy('type')->get();

        $stats['product']['ins']['total'] =
            $stats['product']['ins']['nCategories']->sum('count');

        $stats['product']['outs']['nCategories'] =
            ProductOut::select(DB::raw('count(id) as count, type'))->whereMonth('at', '=', $currentMonth)->whereYear('at', '=', $currentYear)->groupBy('type')->get();

        $stats['product']['outs']['total'] =
            $stats['product']['outs']['nCategories']->sum('count');

        $stats['product']['outs']['color'] = 'primary';


        $worths['materials'] = Material::select('id')->with('monthlyMovements')
            ->whereHas(
                'monthlyMovements',
                fn ($q) => $q
                    ->select('in', 'out', 'avg_in_price')
                    ->where('month', '<=', $currentMonth)
                    ->where('year', '<=', $currentYear)
            )
            ->get()
            ->map(fn ($material) => ($material->monthlyMovements->sum('in') - $material->monthlyMovements->sum('out')) * $material->monthlyMovements->avg('avg_in_price'))->sum();

        $worths['products'] = Product::select('id', 'default_price')->with('monthlyMovements')
            ->whereHas(
                'monthlyMovements',
                fn ($q) => $q
                    ->select('in', 'out', 'default_price')
                    ->where('month', '<=', $currentMonth)
                    ->where('year', '<=', $currentYear)
            )
            ->get()
            ->map(fn ($product) => ($product->monthlyMovements->sum('in') - $product->monthlyMovements->sum('out')) * $product->default_price)->sum();

        return view('dashboard', compact('months', 'stats', 'currentMonth', 'worths'));
    }
}
