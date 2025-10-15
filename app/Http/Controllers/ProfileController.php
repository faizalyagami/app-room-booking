<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        return view('pages.profile.index', compact(
            'user'
        ));
    }

    public function edit()
    {
        $user = auth()->user();

        return view('pages.profile.edit', compact(
            'user'
        ));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $message = User::whereId($user->id)->first();
        if($message !== null) {
            $message->email = $request->email;
            $message->description = $request->description;
            $message->save();

            $request->session()->flash('alert-success', 'Profile berhasil diupdate');
            return redirect()->route('profile');
        }

        $request->session()->flash('alert-failed', 'Profile gagal diupdate');
        return redirect()->route('profile');
    }
}
