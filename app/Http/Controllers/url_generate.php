<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use URL;

class url_generate extends Controller
{
    private function validateInput(Request $request, int $manufactureId = null, int $materialOutId = null, int $productInId = null)
    {
        $manufactureFromInput = $request->validate([
            'manufacture' => 'required|array',
            'manufacture.code' => 'nullable|string',
            'manufacture.note' => 'nullable|string',
            'manufacture.at' => 'required|date'
        ])['manufacture'];


        $materialOutFromInput['code'] = $manufactureFromInput['code'];
        $materialOutFromInput['at'] = $manufactureFromInput['at'];
        $materialOutFromInput['type'] = 'Manufactures';
        $materialOutFromInput['last_updated_by_user_id'] = Auth::user()->id;

        $materialOutDetailsFromInput = $request->validate([
            'detailsMaterialOut' => 'required|array',
            'detailsMaterialOut.*.material_in_detail_id' => 'required|exists:mysql.material_in_details,id',
            'detailsMaterialOut.*.qty' => 'required|integer',
        ])['detailsMaterialOut'];

        $productInFromInput['code'] = $manufactureFromInput['code'];
        $productInFromInput['at'] = $manufactureFromInput['at'];
        $productInFromInput['type'] = 'Manufacture';
        $productInFromInput['last_updated_by_user_id'] = Auth::user()->id;

        $productInDetailsFromInput = $request->validate([
            'detailsProductIn' => 'required|array',
            'detailsProductIn.*.product_id' => 'required|exists:mysql.products',
            'detailsProductIn.*.qty' => 'required|integer',
        ])['detailsProductIn'];



        if (!$manufactureId) {
            $manufactureFromInput['created_by_user_id'] = Auth::user()->id;
            $materialOutFromInput['created_by_user_id'] = Auth::user()->id;
            $productInFromInput['created_by_user_id'] = Auth::user()->id;
        }


        return [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput];
    }

    public function generate(){
        // return response()->json([
        //     'url'=>URL::temporarySignedRoute('valid-url', now()->addHours(1), [
        //         'data' => 'mamang'
        //     ])
        // ]);

        return view('pages.generate');
    }

    public function index($data){
        // return response()->json([
        //     'data' => $data
        // ]);

        return view('pages.test_exp');
    }

    public function store(Request $request){
        dd($request->all());
        
        [$manufactureFromInput, $materialOutFromInput, $materialOutDetailsFromInput, $productInFromInput, $productInDetailsFromInput] = $this->validateInput($request);


        DB::beginTransaction();

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

            return redirect()->back()->with('notifications', [
                [__('Someting went wrong')]
            ]);
        }

        return redirect()->back()->with('notifications', [
            [" <b>" . __('Manufacture data') . "</b> " . __('has been added successfully'), 'success']

        ]);
    }
}
