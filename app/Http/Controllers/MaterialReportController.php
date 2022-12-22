<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\Material;
use App\Models\MaterialInDetail;
use App\Models\MaterialOutDetail;

class MaterialReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dateRange = explode('_', $request->daterange);
        $materialInDetail = MaterialInDetail::with('material')
        ->with('materialIn')
        ->join('material_ins', 'material_ins.id', 'material_in_details.material_in_id')
        ->where('material_ins.deleted_at', null)->get();
        $materialOutDetail = MaterialOutDetail::with('materialInDetail.material')
        ->with('materialOut')
        ->join('material_outs', 'material_outs.id', 'material_out_details.material_out_id')
        ->where('material_outs.deleted_at', null)->get();
        if ($request->daterange) {
            $materialInDetail = MaterialInDetail::with('material')
            ->with('materialIn')
            ->join('material_ins', 'material_ins.id', 'material_in_details.material_in_id')
            ->where('material_ins.at', '>=', $dateRange[0])
            ->where('material_ins.at', '<=', $dateRange[1])
            ->where('material_ins.deleted_at', null)->get();

            $materialOutDetail = MaterialOutDetail::with('materialInDetail.material')
            ->with('materialOut')
            ->join('material_outs', 'material_outs.id', 'material_out_details.material_out_id')
            ->where('material_outs.at', '>=', $dateRange[0])
            ->where('material_outs.at', '<=', $dateRange[1])
            ->where('material_outs.deleted_at', null)->get();
        }
        return view('pages.report.material.index', compact('materialInDetail','materialOutDetail'));
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
