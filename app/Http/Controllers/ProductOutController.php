<?php

namespace App\Http\Controllers;

use Helper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Repositories\ProductOutRepository;

class ProductOutController extends Controller
{
    public function __construct(private ProductOutRepository $repo)
    {
    }

    private function repoHandler(string $action, Request $request)
    {
        $data = $request->only(['code', 'type', 'note', 'at', 'details']);
        $materialIn = $this->repo->$action($data);

        $events = [
            'store' => 'added',
            'update' => 'updated',
            'destroy' => 'deleted'
        ];

        return Helper::getSuccessCrudResponse($events[$action], __('Product Out'), $materialIn->id_for_human);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        return $this->repoHandler('store', $request);
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        return $this->repoHandler('update', $request);
    }

    public function destroy(Request $request): RedirectResponse|JsonResponse
    {
        return $this->repoHandler('destroy', $request);
    }
}
