<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\MaterialInRepository;
use Illuminate\Http\RedirectResponse;

class MaterialInController extends Controller
{
    public function __construct(private MaterialInRepository $repo)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->only(['id', 'code', 'type', 'note', 'at']);

        try {
            $materialIn = $this->repo->create($data, $request->details);
        } catch (\Throwable $th) {
            return back()->withErrors(json_decode($th->getMessage()));
        }

        return back()->with('notifications', [
            [
                __('notification.data_added', ['type' => __('Material In'), 'name' => "<b>{$materialIn->at->format('d-m-Y')}</b>"]),
                'success'
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->only(['code', 'type', 'note', 'at']);
        $detailsData = $request->details;

        try {
            $materialIn = $this->repo->update($data, $detailsData);
        } catch (\Throwable $th) {
            return back()->withErrors(json_decode($th->getMessage()));
        }

        return back()->with('notifications', [
            [
                __('notification.data_updated', ['type' => __('Material In'), 'name' => "<b>{$materialIn->at->format('d-m-Y')}</b>"]),
                'success'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(): RedirectResponse
    {
        try {
            $materialIn = $this->repo->deleteData();
        } catch (\Throwable $th) {
            return back()->withErrors(json_decode($th->getMessage()));
        }


        return back()->with('notifications', [
            [
                __('notification.data_updated', ['type' => __('Material In'), 'name' => "<b>{$materialIn->at->format('d-m-Y')}</b>"]),
                'success'
            ]
        ]);
    }
}
