<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Http\Resources\TechResource;

class TechController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role()->first()->id < 2) {
            return response()->json(["message" => "Ο συγκεκριμένος χρήστης δεν έχει πρόσβαση στο πεδία αυτό"], 401);
        }

        return TechResource::collection(User::whereHas('role', function ($q) {
            $q->where('title', 'Τεχνικός');
        })->where('active', true)->get());
    }
}
