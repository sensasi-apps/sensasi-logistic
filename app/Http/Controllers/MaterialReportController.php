<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\Material;
use App\Models\MaterialIn;
use App\Models\MaterialOut;
use App\Models\MaterialInDetail;
use App\Models\MaterialOutDetail;
use Carbon\Carbon;

class MaterialReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->daterange) {
            $dateRange = explode('_', $request->daterange);

            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
        } else {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }


        $materialIns = MaterialIn::with('details.material')
            ->where('at', '>=', $startDate)
            ->where('at', '<=', $endDate)
            ->orderBy('at')
            ->get();

        $materialsInGroup = $materialIns->reduce(function ($carry, $item) {
            return $carry->merge($item->details);
        }, collect([]))->groupBy('material.id')->sortBy('material.name');




        


        $materialOutDetailNota = MaterialOut::with('details.materialInDetail.material')
            ->where('at', '>=', $startDate)
            ->where('at', '<=', $endDate)
            ->where('type', '!=', 'Manufacture')->get();

        $materialOutDetailItem = MaterialOut::groupBy('materials.name')->select(
            'materials.name',
            DB::raw('sum(material_out_details.qty) as qty'),
            DB::raw("group_concat(DISTINCT materials.unit) as unit")
        )
            ->join('material_out_details', 'material_outs.id', 'material_out_details.material_out_id')
            ->join('material_in_details', 'material_in_details.id', 'material_out_details.material_in_detail_id')
            ->join('materials', 'materials.id', 'material_in_details.material_id')
            ->where('material_outs.at', '>=', $startDate)
            ->where('material_outs.at', '<=', $endDate)
            ->where('material_outs.type', '!=', 'Manufacture')->get();

        return view('pages.report.material.index', compact('materialIns', 'materialsInGroup', 'materialOutDetailNota', 'materialOutDetailItem'));
    }
}
