<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function validateInput(Request $request, User $user = null)
    {
        $validationRules = [
            'name' => 'required|max:255',
            'email' => "required|email:dns|unique:users,email,{$user->id}"
        ];

        if (!$user || $user->has_default_password || $request->password) {
            $validationRules['password'] = 'required|confirmed|min:8|max:255';
        }

        $validatedInput = $request->validate($validationRules);

        if (isset($validatedInput['password'])) {
            $validatedInput['password'] = bcrypt($validatedInput['password']);
        }

        return $validatedInput;
    }

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
        $validatedInput = $this->validateInput($request);
        $user = User::create($validatedInput)->assignRole($request->roles);

        return redirect()->back()->with('notifications', [
            [__('Password and password confirmation is not same'), 'danger']
        ]);
        return redirect()->back()->with('notifications', [
            [__('User') . " <b>$user->name</b> " . __('has been added successfully'), 'success']
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validatedInput = $this->validateInput($request, $user);

        $user->syncRoles($request->roles)->update($validatedInput);

        return redirect()->back()->with('notifications', [
            [__('User') . " <b>$user->name</b> " . __('has been updated successfully'), 'success']
        ]);
    }

    public function selfUpdate(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $validatedInput = $this->validateInput($request, $user);

        $user->update($validatedInput);

        return redirect()->back()->with('notifications', [
            [__('Your profile has been updated successfully'), 'success']
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        // $user->delete();

        return redirect()->back()->with('notifications', [
            [__('User') . " <b>$user->name</b> " . __('has been deleted successfully'), 'warning']
        ]);
    }
}
