<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\MaterialOut;
use App\Models\ProductIn;

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
        $materialOutDetailNota = MaterialOut::with('details.materialInDetail.material')
        ->where('type', '=', 'Manufacture')->get();

        $materialOutDetailItem = MaterialOut::groupBy('materials.name')->select('materials.name', 
            DB::raw('sum(material_out_details.qty) as qty'),
            DB::raw("group_concat(DISTINCT materials.unit) as unit"))
        ->join('material_out_details', 'material_outs.id', 'material_out_details.material_out_id')
        ->join('material_in_details', 'material_in_details.id', 'material_out_details.material_in_detail_id')
        ->join('materials', 'materials.id', 'material_in_details.material_id')
        ->where('type', '=', 'Manufacture')
        ->get();

        $productInDetailNota = productIn::with('details.product')
        ->where('type', '=', 'Manufacture')->get();

        $productInDetailItem = productIn::select('products.name',
            DB::raw('sum(product_in_details.qty) as qty'),
            DB::raw("group_concat(DISTINCT products.unit) as unit"))
        ->join('product_in_details', 'product_in_details.product_in_id', 'product_ins.id')
        ->join('products', 'products.id', 'product_in_details.product_id')
        ->groupBy('products.name')
        ->where('type', '=', 'Manufacture')->get();

        if ($request->daterange) {
            $productInDetailNota = productIn::with('details.product')
            ->where('product_ins.at', '>=', $dateRange[0])
            ->where('product_ins.at', '<=', $dateRange[1])
            ->where('type', '=', 'Manufacture')->get();

            $productInDetailItem = productIn::groupBy('products.name')->select('products.name',
                DB::raw('sum(product_in_details.qty) as qty'),
                DB::raw("group_concat(DISTINCT products.unit) as unit"))
            ->join('product_in_details', 'product_in_details.product_in_id', 'product_ins.id')
            ->join('products', 'products.id', 'product_in_details.product_id')
            ->where('product_ins.at', '>=', $dateRange[0])
            ->where('product_ins.at', '<=', $dateRange[1])
            ->where('type', '=', 'Manufacture')->get();

            $materialOutDetailNota = MaterialOut::with('details.materialInDetail.material')
            ->where('at', '>=', $dateRange[0])
            ->where('at', '<=', $dateRange[1])
            ->where('type', '=', 'Manufacture')->get();

            $materialOutDetailItem = MaterialOut::groupBy('materials.name')->select('materials.name', 
                DB::raw('sum(material_out_details.qty) as qty'),
                DB::raw("group_concat(DISTINCT materials.unit) as unit"))
            ->join('material_out_details', 'material_outs.id', 'material_out_details.material_out_id')
            ->join('material_in_details', 'material_in_details.id', 'material_out_details.material_in_detail_id')
            ->join('materials', 'materials.id', 'material_in_details.material_id')
            ->where('material_outs.at', '>=', $dateRange[0])
            ->where('material_outs.at', '<=', $dateRange[1])
            ->where('material_outs.type', '=', 'Manufacture')->get();
        }
        return view('pages.report.manufacture.index', compact('productInDetailItem', 'productInDetailNota','materialOutDetailItem', 'materialOutDetailNota'));
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
