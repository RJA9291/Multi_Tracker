<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AdminController extends Controller
{
    private function guard(Request $request): void
    {
        abort_unless($request->user() && $request->user()->is_admin, 403, 'Akses admin diperlukan.');
    }

    public function users(Request $request)
    {
        $this->guard($request);

        return response()->json([
            'users' => User::orderBy('created_at')->get(['id', 'name', 'email', 'is_admin', 'approved', 'created_at']),
        ]);
    }

    public function approve(Request $request, int $id)
    {
        $this->guard($request);

        $user = User::find($id);
        abort_unless($user, 404);

        $user->approved = true;
        $user->save();

        return response()->json(['ok' => true, 'email' => $user->email]);
    }

    public function remove(Request $request, int $id)
    {
        $this->guard($request);

        $user = User::find($id);
        abort_unless($user, 404);

        if ($user->id === $request->user()->id) {
            abort(422, 'Tidak boleh padam akaun sendiri.');
        }

        $user->delete();

        return response()->json(['ok' => true]);
    }

    public function reset(Request $request, int $id)
    {
        $this->guard($request);

        $user = User::find($id);
        abort_unless($user, 404);

        Password::sendResetLink(['email' => $user->email]);

        return response()->json(['ok' => true, 'email' => $user->email]);
    }

    public function resetAll(Request $request)
    {
        $this->guard($request);

        $sent = 0;
        User::all()->each(function (User $user) use (&$sent) {
            Password::sendResetLink(['email' => $user->email]);
            $sent++;
        });

        return response()->json(['ok' => true, 'sent' => $sent]);
    }
}
