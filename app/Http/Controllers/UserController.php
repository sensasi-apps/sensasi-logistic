<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function validateInput(Request $request, User $user = null)
    {
        $validationRules = [
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users,email' . ($user ? ",$user->id,id" : null)
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.system.users');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validatedInput = $this->validateInput($request, $user);

        $user->syncRoles($request->roles)->update($validatedInput);

        return redirect()->back()->with('notifications', [
            [__('User') . " <b>$user->name</b> " . __('has been updated successfully'), 'success']

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function selfUpdate(Request $request)
    {
        $user = Auth::user();
        $validatedInput = $this->validateInput($request, $user);

        $user->update($validatedInput);

        return redirect()->back()->with('notifications', [
            [__('Your profile has been updated successfully'), 'success']
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('notifications', [
            [__('User') . " <b>$user->name</b> " . __('has been deleted successfully'), 'warning']

        ]);
    }
}
