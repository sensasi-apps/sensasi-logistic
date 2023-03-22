<?php

namespace App\Services;

use App\Models\MaterialManufacture;
use App\Repositories\MaterialInRepository;
use App\Repositories\MaterialOutRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialManufactureService extends BaseModelService
{
	protected string $modelClass = MaterialManufacture::class;

	protected array $withs = [
		'materialOut',
		'materialIn'
	];

	public function __construct(private MaterialOutRepository $materialOutRepository, private MaterialInRepository $materialInRepository)
	{
		parent::__construct();

		if ($this->workingInstance->materialOut) {
			$this->materialOutRepository = new MaterialOutRepository($this->workingInstance->materialOut);
		}

		if ($this->workingInstance->materialIn) {
			$this->materialInRepository = new MaterialInRepository($this->workingInstance->materialIn);
		}
	}

	private function preprocessData(array &$data)
	{
		$data['type'] = ucfirst(__('manufacture'));

		foreach (['at', 'code', 'note', 'type'] as $key) {
			$data['material_out'][$key] = $data[$key];
			$data['material_in'][$key] = $data[$key];
		}
	}

	public function store(Request $request): MaterialManufacture
	{
		$data = $request->all();
		$this->preprocessData($data);

		try {
			DB::beginTransaction();

			$materialOut = $this->materialOutRepository->store($data['material_out']);
			$materialIn = $this->materialInRepository->store($data['material_in']);

			$data = [
				'code' => $data['code'],
				'note' => $data['note'],
				'at' => $data['at'],
				'material_out_id' => $materialOut->id,
				'material_in_id' => $materialIn->id
			];

			$createdInstance = $this->workingInstance::create($data);

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		$createdInstance->load($this->withs);

		return $createdInstance;
	}

	public function update(Request $request): MaterialManufacture
	{
		$data = $request->all();
		$this->preprocessData($data);

		try {
			DB::beginTransaction();

			$this->materialOutRepository->update($data['material_out'], $data['material_out']['details']);
			$this->materialInRepository->update($data['material_in'], $data['material_in']['details']);
			$this->workingInstance->update($data);

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance->fresh();
	}

	public function destroy(): MaterialManufacture
	{
		try {
			DB::beginTransaction();

			$this->workingInstance->delete();
			$this->materialOutRepository->destroy();
			$this->materialInRepository->destroy();

			DB::commit();
		} catch (\Throwable $th) {
			DB::rollBack();
			throw $th;
		}

		return $this->workingInstance;
	}
}
