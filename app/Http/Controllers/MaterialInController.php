<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\MaterialInRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MaterialInController extends Controller
{
    public function __construct(private MaterialInRepository $repo)
    {
    }

    private function getSuccessMessage(string $action, string $name): string
    {
        switch ($action) {
            case 'store':
                $key = 'added';
                break;

            case 'update':
                $key = 'updated';
                break;

            case 'destroy':
                $key = 'deleted';
                break;
        }

        return __("notification.data_{$key}", ['type' => __('Material In'), 'name' => "<b>{$name}</b>"]);
    }


    private function repoHandler(string $action, Request $request)
    {
        $data = $request->only(['code', 'type', 'note', 'at']);
        $materialIn = $this->repo->$action($data, $request->details);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getSuccessMessage($action, $materialIn->id_for_human),
            ], 200);
        }

        return back()->with('notifications', [
            [
                $this->getSuccessMessage($action, $materialIn->id_for_human),
                $action === 'destroy' ? 'warning' : 'success'
            ]
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        return $this->repoHandler('store', $request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        return $this->repoHandler('update', $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request): RedirectResponse|jsonResponse
    {
        return $this->repoHandler('destroy', $request);
    }
}
