<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\ProductInDetail;
use App\Models\ProductIn;
use App\Models\ProductOut;
use App\Models\ProductOutDetail;
class ProductReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dateRange = explode('_', $request->daterange);
        $productInDetailNota = productIn::with('details.product')
        ->where('type', '!=', 'Manufacture')->get();

        $productInDetailItem = productIn::select('products.name',
            DB::raw('sum(product_in_details.qty) as qty'),
            DB::raw("group_concat(DISTINCT products.unit) as unit"))
        ->join('product_in_details', 'product_in_details.product_in_id', 'product_ins.id')
        ->join('products', 'products.id', 'product_in_details.product_id')
        ->groupBy('products.name')
        ->where('type', '!=', 'Manufacture')->get();

        $productOutDetailNota = productOut::with('details.productInDetail.product')
        ->where('type', '!=', 'Manufacture')->get();

        $productOutDetailItem = productOut::groupBy('products.name')->select('products.name',
            DB::raw('sum(product_out_details.qty) as qty'),
            DB::raw('sum(product_out_details.qty*product_out_details.price) as total'),
            DB::raw("group_concat(DISTINCT products.unit) as unit"))
        ->join('product_out_details', 'product_out_details.product_out_id', 'product_outs.id')
        ->join('product_in_details', 'product_in_details.id', 'product_out_details.product_in_detail_id')
        ->join('products', 'products.id', 'product_in_details.product_id')
        ->where('type', '!=', 'Manufacture')->get();
        
        if ($request->daterange) {

            $productInDetailNota = productIn::with('details.product')
            ->where('product_ins.at', '>=', $dateRange[0])
            ->where('product_ins.at', '<=', $dateRange[1])
            ->where('type', '!=', 'Manufacture')->get();

            $productInDetailItem = productIn::groupBy('products.name')->select('products.name',
                DB::raw('sum(product_in_details.qty) as qty'),
                DB::raw("group_concat(DISTINCT products.unit) as unit"))
            ->join('product_in_details', 'product_in_details.product_in_id', 'product_ins.id')
            ->join('products', 'products.id', 'product_in_details.product_id')
            ->where('product_ins.at', '>=', $dateRange[0])
            ->where('product_ins.at', '<=', $dateRange[1])
            ->where('type', '!=', 'Manufacture')->get();

            $productOutDetailItem = productOut::groupBy('products.name')->select('products.name',
                DB::raw('sum(product_out_details.qty) as qty'),
                DB::raw('sum(product_out_details.qty*product_out_details.price) as total'),
                DB::raw("group_concat(DISTINCT products.unit) as unit"))
            ->join('product_out_details', 'product_out_details.product_out_id', 'product_outs.id')
            ->join('product_in_details', 'product_in_details.id', 'product_out_details.product_in_detail_id')
            ->join('products', 'products.id', 'product_in_details.product_id')
            ->where('product_outs.at', '>=', $dateRange[0])
            ->where('product_outs.at', '<=', $dateRange[1])
            ->where('type', '!=', 'Manufacture')->get();

            $productOutDetailNota = productOut::with('details.productInDetail.product')
            ->where('product_outs.at', '>=', $dateRange[0])
            ->where('product_outs.at', '<=', $dateRange[1])->get();
        }

        return view('pages.report.product.index', compact('productInDetailNota', 'productInDetailItem','productOutDetailNota', 'productOutDetailItem'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}