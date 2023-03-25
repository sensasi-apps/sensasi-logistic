<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Helper;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(): View
    {
        return view('pages.system.users', [
            'userDatatableApiUrl' => route('api.datatable', [
                'model_name' => 'User',
                'params_json' => urlencode(json_encode([
                    'withs' => ['roles']
                ]))
            ])
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedInput = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users,email',
            'password' => 'confirmed|min:8|max:255'
        ]);

        $validatedInput['password'] = bcrypt($validatedInput['password'] ?? Str::password());

        $user = User::create($validatedInput)->assignRole($request->roles);

        return Helper::getSuccessCrudResponse('added', __('users'), $user->name);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validatedInput = $request->validate([
            'name' => 'required|max:255',
            'email' => "required|email:dns|unique:users,email,{$user->id}",
            'password' => 'confirmed|min:8|max:255',
        ]);

        if (isset($validatedInput['password'])) {
            $validatedInput['password'] = bcrypt($validatedInput['password']);
        }

        $user->syncRoles($request->roles)->update($validatedInput);

        return Helper::getSuccessCrudResponse('updated', __('users'), $user->name);
    }

    public function selfUpdate(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validatedInput = $request->validate([
            'name' => 'required|max:255',
            'email' => "required|email:dns|unique:users,email,{$user->id}"
        ]);

        $user->update($validatedInput);

        return Helper::getSuccessCrudResponse('updated', __('users'), $user->name);
    }

    public function selfUpdatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'new_password' => 'required|confirmed|min:8|max:255',
            'current_password' => 'required|min:8|max:255',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => __('validation.password')
            ]);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return Helper::getSuccessCrudResponse('updated', __('change password'), $user->name);
    }

    /**
     * unused
     *
     **/
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return Helper::getSuccessCrudResponse('deleted', __('users'), $user->name);
    }
}
