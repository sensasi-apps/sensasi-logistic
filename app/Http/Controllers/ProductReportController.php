<?php

namespace App\Http\Controllers;

use App\Models\ProductIn;
use App\Models\ProductOut;
use Carbon\Carbon;
use Illuminate\View\View;

class ProductReportController extends Controller
{
    public function __invoke(): View
    {
        [$startDate, $endDate] = $this->getDateRange();

        $productIns = ProductIn::with('details.product')
            ->where('at', '>=', $startDate)
            ->where('at', '<=', $endDate)
            ->orderBy('at')
            ->get();

        $productInDetailsGroupByProduct = $productIns->reduce(function ($carry, $item) {
            return $carry->merge($item->details);
        }, collect([]))
            ->sortBy('product.name')
            ->groupBy('product_id');

        $productOuts = ProductOut::with('details.productInDetail.product')
            ->where('at', '>=', $startDate)
            ->where('at', '<=', $endDate)
            ->orderBy('at')
            ->get();

        $productOutDetailsGroupByProduct = $productOuts->reduce(function ($carry, $item) {
            return $carry->merge($item->details);
        }, collect([]))
            ->sortBy('productInDetail.product.name')
            ->groupBy('productInDetail.product_id');


        $reportPageId = 'product';

        $title = __('report.name-report', ['name' => __($reportPageId)]);
        $tabs = [
            'in' => __('report.name-ins', ['name' => __($reportPageId)]),
            'out' => __('report.name-outs', ['name' => __($reportPageId)]),
        ];

        $subtabs = [
            'invoice' => __('by invoice'),
            'item' => __('by item')
        ];

        return view('pages.report.page-template', compact(
            'productIns',
            'productInDetailsGroupByProduct',
            'productOuts',
            'productOutDetailsGroupByProduct',

            'title',
            'reportPageId',
            'tabs',
            'subtabs'
        ));
    }

    private function getDateRange(): array
    {
        if (request()->daterange) {
            $dateRange = explode('_', request()->daterange);

            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
        } else {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        return [$startDate, $endDate];
    }
}
