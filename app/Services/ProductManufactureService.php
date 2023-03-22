<?php

namespace App\Services;

use App\Models\ProductManufacture;
use App\Repositories\MaterialOutRepository;
use App\Repositories\ProductInRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductManufactureService extends BaseModelService
{
	protected string $modelClass = ProductManufacture::class;

	protected array $withs = [
		'materialOut',
		'productIn'
	];

	public function __construct(private MaterialOutRepository $materialOutRepository, private ProductInRepository $productInRepository)
	{
		parent::__construct();

		if ($this->workingInstance->materialOut) {
			$this->materialOutRepository = new MaterialOutRepository($this->workingInstance->materialOut);
		}

		if ($this->workingInstance->productIn) {
			$this->productInRepository = new ProductInRepository($this->workingInstance->productIn);
		}
	}

	private function preprocessData(array &$data)
	{
		$data['type'] = ucfirst(__('manufacture'));

		foreach (['at', 'code', 'note', 'type'] as $key) {
			$data['material_out'][$key] = $data[$key];
			$data['product_in'][$key] = $data[$key];
		}
	}

	public function store(Request $request): ProductManufacture
	{
		$data = $request->all();
		$this->preprocessData($data);

		try {
			DB::beginTransaction();

			$materialOut = $this->materialOutRepository->store($data['material_out']);
			$productIn = $this->productInRepository->store($data['product_in']);

			$data = [
				'code' => $data['code'],
				'note' => $data['note'],
				'at' => $data['at'],
				'material_out_id' => $materialOut->id,
				'product_in_id' => $productIn->id
			];

			$createdInstance = $this->workingInstance::create($data);

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $createdInstance;
	}

	public function update(Request $request): ProductManufacture
	{
		$data = $request->all();
		$this->preprocessData($data);

		try {
			DB::beginTransaction();

			$this->materialOutRepository->update($data['material_out'], $data['material_out']['details']);
			$this->productInRepository->update($data['product_in'], $data['product_in']['details']);
			$this->workingInstance->update($data);

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance->fresh();
	}

	public function destroy(): ProductManufacture
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
