<?php
/**
 * Project qpartners
 * Created by danila 13.07.2020 @ 15:39
 */

namespace App\Http\Controllers;


use App\Models\MultiStorage;
use PhpParser\Node\Expr\AssignOp\Mul;

class StorageController extends Controller
{

    public function getMaterial($type, $file)
    {
        $f = "materials/{$type}/" . $file;
        if (!MultiStorage::exists($f)) {
            throw new \Exception("File not found");
        }
        header("Content-type: " . MultiStorage::getMime($f), true);
        echo MultiStorage::get($f);
        die();
    }

    public function getOfferImage($id)
    {
        if (!MultiStorage::exists("offers/" . $id)) {
            throw new \Exception("File not found");
        }
        header("Content-type: " . MultiStorage::getMime("offers/" . $id), true);
        echo MultiStorage::get("offers/" . $id);
        die();
    }

}
