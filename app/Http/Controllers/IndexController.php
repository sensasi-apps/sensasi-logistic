<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class IndexController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->hasRole('Super Admin')) {
            return redirect()->route('dashboard');
        }

        if ($user->hasRole('Admin')) {
            return redirect()->route('system.users.index');
        }

        if ($user->hasRole('Stackholder')) {
            return redirect()->route('dashboard');
        }

        if ($user->hasRole('Warehouse')) {
            return redirect()->route('materials.index');
        }

        if ($user->hasRole('Sales')) {
            return redirect()->route('products.index');
        }

        if ($user->hasRole('Purchase')) {
            return redirect()->route('materials.index');
        }

        if ($user->hasRole('Manufacture')) {
            return redirect()->route('manufactures.index');
        }

        (new AuthController())->logout(request());

        return abort(403);
    }
}
