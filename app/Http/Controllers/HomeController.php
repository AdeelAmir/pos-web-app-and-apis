<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function checkUserEmail(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where(function ($query) use ($request) {
                if ($request->id != '') {
                    $query->where('id', '!=', $request->id);
                }
            })
            ->withTrashed()
            ->first();

        if (empty($user)) {
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }
}
