<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MaterialOutDetail;
use App\Models\ProductInDetail;

class ManufactureReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dateRange = explode('_', $request->daterange);
        $materialOutDetail = MaterialOutDetail::with('materialInDetail.material')
        ->with('materialOut')
        ->join('material_outs', 'material_outs.id', 'material_out_details.material_out_id')
        ->where('material_outs.deleted_at', null)
        ->where('material_outs.type', '=', 'Manufacture')->get();

        $productInDetail = productInDetail::with('productIn')
        ->with('product')
        ->join('product_ins', 'product_ins.id', 'product_in_details.product_in_id')
        ->where('product_ins.deleted_at', null)
        ->where('product_ins.type', '=', 'Manufacture')->get();

        if ($request->daterange) {
            $productInDetail = productInDetail::with('productIn')
            ->with('product')
            ->join('product_ins', 'product_ins.id', 'product_in_details.product_in_id')
            ->where('product_ins.at', '>=', $dateRange[0])
            ->where('product_ins.at', '<=', $dateRange[1])
            ->where('product_ins.deleted_at', null)
            ->where('product_ins.type', '=', 'Manufacture')->get();

            $materialOutDetail = MaterialOutDetail::with('materialInDetail.material')
            ->with('materialOut')
            ->join('material_outs', 'material_outs.id', 'material_out_details.material_out_id')
            ->where('material_outs.at', '>=', $dateRange[0])
            ->where('material_outs.at', '<=', $dateRange[1])
            ->where('material_outs.deleted_at', null)
            ->where('material_outs.type', '=', 'Manufacture')->get();
        }
        return view('pages.report.manufacture.index', compact('productInDetail','materialOutDetail'));
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
