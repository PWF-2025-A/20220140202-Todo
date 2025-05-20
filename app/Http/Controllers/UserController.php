<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
       
     $search = request('search');
     if($search) {
         $users = User::with('todo')->where(function($query) use ($search) {
             $query->where('name', 'like', '%'.$search.'%')
             ->orWhere('email', 'like', '%'.$search.'%');
         })
         ->orderBy('name')
         ->where('id', '!=', 1)
         ->paginate(20)
         ->withQueryString();
     }else {
         $users = User::where('id', '!=', 1)->orderBy('name')->paginate(20);
     }
     return view('user.index', compact('users'));
    }


    public function makeadmin(User $user)
    {
        $user->timestamps = false;
        $user->is_admin = true;
        $user->save();

        return back()->with('success', 'Make admin successfully!');
    }

    public function removeadmin(User $user)
    {
        if ($user->id != 1) {
            $user->timestamps = false;
            $user->is_admin = false;
            $user->save();

            return back()->with('success', 'Remove admin successfully!');
        } else {
            return redirect()->route('user.index');
        }
    }

        public function destroy(User $user)
    {
        if ($user->id != 1) {
            // Hapus user jika ID-nya bukan 1
            $user->delete();
            
            // Kembali ke halaman sebelumnya dengan pesan sukses
            return back()->with('success', 'Delete user successfully!');
        }

        // Redirect jika mencoba menghapus user dengan ID 1
        return redirect()->route('user.index')->with('danger', 'Delete user failed!');
    }
}
