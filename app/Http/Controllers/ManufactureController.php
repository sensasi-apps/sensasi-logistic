<?php

namespace App\Http\Controllers;

use App\Repositories\ManufactureRepository;
use Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ManufactureController extends Controller
{
    public function index()
    {
        $manufactureDatatableAjaxUrl = route('api.datatable', [
            'model_name' => 'Manufacture',
            'params_json' => urlencode(json_encode([
                'withs' => [
                    'materialOut.details.materialInDetail' => [
                        'materialIn',
                        'material'
                    ],
                    'productIn.details' => [
                        'outDetails',
                        'product',
                        'stock'
                    ]
                ]
            ]))
        ]);

        return view('pages.manufacture.index', compact('manufactureDatatableAjaxUrl'));
    }

    public function __construct(private ManufactureRepository $repo)
    {
    }

    private function repoHandler(string $action, Request $request)
    {
        $data = $request->only(['code', 'note', 'at', 'material_out', 'product_in']);
        $productIn = $this->repo->$action($data, $request->details);

        $events = [
            'store' => 'added',
            'update' => 'updated',
            'destroy' => 'deleted'
        ];

        return Helper::getSuccessCrudResponse($events[$action], __('Manufacture'), $productIn->id_for_human);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        return $this->repoHandler('store', $request);
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        return $this->repoHandler('update', $request);
    }

    public function destroy(Request $request): RedirectResponse|jsonResponse
    {
        return $this->repoHandler('destroy', $request);
    }
}
