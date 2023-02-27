<?php

namespace App\Repositories;

use App\Models\Manufacture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ManufactureRepository extends BaseRepository
{
	protected string $urlParamName = 'manufacture';

	protected string $modelClass = Manufacture::class;

	protected array $withs = [
		'materialOut.details',
		'productIn.details'
	];

	public function __construct(
		private MaterialOutRepository $materialOutRepository,
		private ProductInRepository $productInRepository
	) {
		parent::__construct();

		if ($this->workingInstance->materialOut) {
			$this->materialOutRepository = new MaterialOutRepository($this->workingInstance->materialOut);
		}

		if ($this->workingInstance->productIn) {
			$this->productInRepository = new ProductInRepository($this->workingInstance->productIn);
		}
	}

	private function validateData(array $data): array
	{
		return Validator::make($data, [
			'code' => "nullable|string|unique:manufactures,code,{$this->workingInstance->id}",
			'note' => 'nullable|string',
			'at' => 'required|date',
			'material_out' => 'required|array',
			'product_in' => 'required|array'
		])->validate();
	}

	private function preprocessData(array &$data)
	{
		$data['type'] = 'Manufacture';
		$data['material_out']['type'] = 'Manufacture';
		$data['product_in']['type'] = 'Manufacture';

		foreach (['at', 'code', 'note'] as $key) {
			$data['material_out'][$key] = $data[$key];
			$data['product_in'][$key] = $data[$key];
		}
	}

	public function store(array $data): Manufacture
	{
		$validatedData = $this->validateData($data);
		$this->preprocessData($validatedData);

		try {
			DB::beginTransaction();

			$materialOut = $this->materialOutRepository->store($validatedData['material_out'], $validatedData['material_out']['details']);
			$productIn = $this->productInRepository->store($validatedData['product_in'], $validatedData['product_in']['details']);

			$validatedData['material_out_id'] = $materialOut->id;
			$validatedData['product_in_id'] = $productIn->id;

			$createdManufacture = $this->workingInstance::create($validatedData);

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		$createdManufacture->load($this->withs);

		$this->workingInstance = $createdManufacture;

		return $createdManufacture;
	}

	public function update(array $data): Manufacture
	{
		$validatedData = $this->validateData($data);
		$this->preprocessData($validatedData);

		try {
			DB::beginTransaction();

			$this->materialOutRepository->update($validatedData['material_out'], $validatedData['material_out']['details']);
			$this->productInRepository->update($validatedData['product_in'], $validatedData['product_in']['details']);
			$this->workingInstance->update($validatedData);

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance->fresh();
	}

	public function destroy(): Manufacture
	{
		try {
			DB::beginTransaction();

			$this->workingInstance->delete();
			$this->materialOutRepository->destroy();
			$this->productInRepository->destroy();

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance;
	}
}
