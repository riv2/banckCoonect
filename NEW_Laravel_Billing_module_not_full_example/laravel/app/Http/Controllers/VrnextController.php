<?php
namespace App\Http\Controllers;
use vrnext\vrnextObj;
use Illuminate\Http\Request;

class VrnextController extends Controller
{

    public function __construct()
    {
    }

    public function index()
    {
        $vrnextObj = new vrnextObj();
        $vrnextObj->goRender();
    }

    public function log(Request $request)
    {
        $request = $request->all();

        if (isset($request['name'])) {
            dumpVar(dumpRead($request['name']));
        }

        if (isset($request['rename'])) {
            dumpRename($request['name']);
        }

        if (isset($request['list'])) {
            dumpVar(dumpList());
        }
    }

}
