<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NomenclatureTemplateFiles extends Model
{
    protected $guarded = [];

    protected $casts = [
        'checked' => 'boolean'
    ];

    public function votesList(){
        return $this->hasMany('App\NomenclatureFileVoteUsers', 'file_id', 'template_id');
    }

    public function template(){
        return $this->belongsTo('App\NomenclatureFolderTemplate', 'template_id', 'id');
    }
}
