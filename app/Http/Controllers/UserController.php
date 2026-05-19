<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "user not found"
            ], 404);
        }
        $user->delete();
        return response()->json([
            "success" => true,
            "message" => "user deleted successfully"
        ], 200);
    }
}
