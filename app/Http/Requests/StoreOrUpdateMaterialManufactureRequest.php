<?php

namespace App\Http\Requests;

use App\Models\MaterialInDetail;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateMaterialManufactureRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     */
    public function rules(): array
    {
        $materialInDetailIds = collect($this->material_out['details'])->pluck('material_in_detail_id')->toArray();
        $existsMaterialInDetails = MaterialInDetail::with('materialIn')->whereIn('id', $materialInDetailIds)->get();
        $newestAt = $existsMaterialInDetails->max('materialIn.at');

        return [
            'code' => "nullable|string|unique:material_manufactures,code,{$this->id}",
            'note' => 'nullable|string',
            'at' => 'required|date|before:tomorrow|after_or_equal:' . $newestAt,

            'material_out.details' => 'required|array',
            'material_out.details.*.qty' => 'required|numeric|min:0',
            'material_out.details.*.material_in_detail_id' => 'required|exists:material_in_details,id',

            'material_in.details' => 'required|array',
            'material_in.details.*.qty' => 'required|numeric|min:0',
            'material_in.details.*.material_id' => 'required|exists:materials,id',
            'material_in.details.*.price' => 'required|numeric|min:0',
            'matereal_in.details.*.expired_at' => "nullable|date|after_or_equal:{$this->at}",
            'material_in.details.*.manufactured_at' => "nullable|date|before_or_equal:{$this->at}"
        ];
    }
}
