<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Damage;
use App\Http\CustomClasses\v1\TechMail;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $damage = Damage::find(18);
        return TechMail::sendToTechs($damage, "βλάβη", "update");
    }
}
