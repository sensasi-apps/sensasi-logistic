<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Helper;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function validateInput(Request $request, User $user = null)
    {
        $validationRules = [
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users,email,' . ($user ? $user->id : 'NULL'),
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

        return Helper::getSuccessCrudResponse('created', __('users'), $user->name);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validatedInput = $this->validateInput($request, $user);

        $user->syncRoles($request->roles)->update($validatedInput);

        return Helper::getSuccessCrudResponse('updated', __('users'), $user->name);
    }

    public function selfUpdate(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $validatedInput = $this->validateInput($request, $user);

        $user->update($validatedInput);

        return Helper::getSuccessCrudResponse('updated', __('users'), $user->name);
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
