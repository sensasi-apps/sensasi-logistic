<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.system.user');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $user = $request->validate([
            'email' => 'required|unique:mysql_system.users',
            'name' => 'required',
            'password' => 'required|min:8'
        ]);

        if ($request->password === $request->password2) {
            $user['password'] = bcrypt($request->password);
            User::create($user)->assignRole([$request->role]);
            return redirect()->route('system.user.index')->with('notifications', [
                [__('User data has been added successfully'), 'success']
            ]);
        } else {
            return redirect()->route('system.user.index')->with('notifications', [
                [__('Password and password confirmation is not same'), 'danger']
            ]);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $allRoles = User::find($id)->getRoleNames();
        
         $user = $request->validate([
            'email' => 'required',
            'name' => 'required',
        ]);

        if($request->password == null && $request->password2 == null){
            foreach ($request->role as $row) {
                User::find($id)->syncRoles([$request->role])->update($user);
            }
            return redirect()->route('system.user.index')->with('notifications', [
                [__('User data has been updated successfully'), 'success']
            ]);
        }elseif ($request->password === $request->password2) {
            $user['password'] = bcrypt($request->password);
            foreach ($request->role as $row) {
                User::find($id)->syncRoles([$request->role])->update($user);
            }
            return redirect()->route('system.user.index')->with('notifications', [
                [__('User data has been updated successfully'), 'success']
            ]);
        }else {
            return redirect()->route('system.user.index')->with('notifications', [
                [__('Password and password confirmation is not same'), 'danger']
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();

        return redirect()->route('system.user.index')->with('notifications', [
            [__('User data has been deleted'), 'danger']
        ]);
    }
}
