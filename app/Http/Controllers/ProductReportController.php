<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ProductInDetail;
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
        $productInDetail = productInDetail::with('productIn')
        ->with('product')
        ->join('product_ins', 'product_ins.id', 'product_in_details.product_in_id')
        ->where('product_ins.deleted_at', null)
        ->where('type', 'is Not', 'Manufacture')->get();

        $productOutDetail = productOutDetail::with('productOut')
        ->with('productInDetail.product')
        ->join('product_outs', 'product_outs.id', 'product_out_details.product_out_id')
        ->where('product_outs.deleted_at', null)->get();
        
        if ($request->daterange) {
            $productInDetail = productInDetail::with('productIn')
            ->with('product')
            ->join('product_ins', 'product_ins.id', 'product_in_details.product_in_id')
            ->where('product_ins.at', '>=', $dateRange[0])
            ->where('product_ins.at', '<=', $dateRange[1])
            ->where('product_ins.deleted_at', null)->get();

            $productInDetail = productInDetail::with('productIn')
            ->with('productInDetail.product')
            ->join('product_outs', 'product_outs.id', 'product_out_details.product_out_id')
            ->where('product_outs.at', '>=', $dateRange[0])
            ->where('product_outs.at', '<=', $dateRange[1])
            ->where('product_outs.deleted_at', null)->get();
        }

        return view('pages.report.product.index', compact('productInDetail'));
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
