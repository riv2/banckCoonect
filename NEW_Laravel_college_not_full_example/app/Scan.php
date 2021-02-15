<?php

namespace App;

use App\Practice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Scan extends Model
{
    protected $table = 'scans';

    public function add($id, $file)
    {
        $scanName = '';

        $originalName = explode('.', $file->getClientOriginalName());
        $scanName = str_random('40');

        if (!empty($originalName[1])) {
            $this->file_name = $scanName;
            $this->extension = $originalName[1];
            $this->original_name = $originalName[0];
            $scanName .= '.' . $originalName[1];

        }
        $file->storeAs(Practice::$filePath, $scanName );

        $this->practice_id = $id;
        $this->save();
    }
}
