<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;

class NomenclatureFolderTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'load_date' => 'date',
        'isset_files' => 'boolean'
    ];

    public function saveFile($file){
        $fileName = time().'_'.$file->getClientOriginalName();
        $file->move(storage_path('app/nomenclature/files/'), $fileName);

        return $fileName;
    }

    public function deleteFile($file){
        Storage::delete('/nomenclature/files/'.$file);
        NomenclatureTemplateFiles::where('name', $file)->delete();

        return true;
    }

    public function getFile($fileName){
        $path = storage_path('app/nomenclature/files/').$fileName;

        return $path;
    }

    public function folder(){
        return $this->hasOne('App\NomenclatureFolder', 'id', 'folder_id');
    }

    public function files(){
        return $this->hasMany('App\NomenclatureTemplateFiles', 'template_id', 'id');
    }
}
