<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view("manager.profile", ["user"=>auth()->user()]);
    }

    public function update(Request $request)
    {
        $fields = [
            "name"=>["string","min:1"],
            "phone"=>["string","nullable"],
            "skype"=>["string","nullable"],
            "pay_account"=>["string","nullable"],
        ];
        $result = $request->validate($fields);

        auth()->user()->fill($result)->save();
        return redirect(route("manager.profile"))->with("success",["Сохранено успешно"]);
    }
}
