<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Models\Manufacture;
use App\Models\ProductInDetail;
use App\Models\ProductIn;
use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;

class Url_generatorController extends Controller
{
    private function validateInput(Request $request)
    {
        $manufactureFromInput = $request->validate([
            'manufacture' => 'required|array',
            'manufacture.code' => 'nullable|string|unique:mysql.manufactures,code',
            'manufacture.note' => 'nullable|string',
            'manufacture.at' => 'required|date'
        ])['manufacture'];


        $materialOutFromInput['code'] = $manufactureFromInput['code'];
        $materialOutFromInput['at'] = $manufactureFromInput['at'];
        $materialOutFromInput['type'] = 'Manufacture';
        $materialOutFromInput['last_updated_by_user_id'] = $request->user;

        $materialOutDetailsFromInput = $request->validate([
            'detailsMaterialOut' => 'required|array',
            'detailsMaterialOut.*.material_in_detail_id' => 'required|exists:mysql.material_in_details,id',
            'detailsMaterialOut.*.qty' => 'required|integer',
        ])['detailsMaterialOut'];

        $productInFromInput['code'] = $manufactureFromInput['code'];
        $productInFromInput['at'] = $manufactureFromInput['at'];
        $productInFromInput['type'] = 'Manufacture';
        $productInFromInput['last_updated_by_user_id'] = $request->user;

        $productInDetailsFromInput = $request->validate([
            'detailsProductIn' => 'required|array',
            'detailsProductIn.*.product_id' => 'required|exists:mysql.products,id',
            'detailsProductIn.*.qty' => 'required|integer',
        ])['detailsProductIn'];

        $manufactureFromInput['created_by_user_id'] = $request->user;
        $materialOutFromInput['created_by_user_id'] = $request->user;
        $productInFromInput['created_by_user_id'] = $request->user;


        return [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput];
    }

    public function index(Request $request)
    {
        $data = $request->data;
        return view('pages.url-generator.url_expired', compact('data'));
    }

    public function generate(){
        return view('pages.url-generator.index');
    }

    public function store(Request $request)
    {
        // dd();
        
        [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput] = $this->validateInput($request);

        try {
            if ($materialOut = MaterialOut::create($materialOutFromInput)) {
                foreach ($materialOutDetailsFromInput as &$materialOutDetailFromInput) {
                    $materialOutDetailFromInput['material_out_id'] = $materialOut->id;
                }

                MaterialOutDetail::insert($materialOutDetailsFromInput);
            }

            if ($productIn = ProductIn::create($productInFromInput)) {
                foreach ($productInDetailsFromInput as &$productInDetailFromInput) {
                    $productInDetailFromInput['product_in_id'] = $productIn->id;
                }

                ProductInDetail::insert($productInDetailsFromInput);
            }

            $manufactureFromInput['material_out_id'] = $materialOut->id;
            $manufactureFromInput['product_in_id'] = $productIn->id;

            Manufacture::create($manufactureFromInput);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect('/')->with('notifications', [
                [__('Someting went wrong')]
            ]);
        }

        return redirect()->back()->with('notifications', [
            [" <b>" . __('Manufacture data') . "</b> " . __('has been added successfully'), 'success']

        ]);
    }
}
