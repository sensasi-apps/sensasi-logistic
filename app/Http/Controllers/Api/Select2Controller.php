<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Select2Controller extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $modelName)
    {
        if ($request->ajax()) {
            $modelClass = "App\Models\\$modelName";

            $term = trim($request->q);

            $results = $modelClass::search($term);

            return response()->json($results);
        }
    }
}
