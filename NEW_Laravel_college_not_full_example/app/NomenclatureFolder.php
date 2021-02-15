<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class NomenclatureFolder extends Model
{
    protected $guarded = [];

    public static $statuses = [
        'new' => 'в процессе',
        'has_files' => 'файл(ы) загружен',
        'expired_date' => 'просрочен срок загрузки',
        'has_agreement' => 'согласовано',
        'has_auditor_agreement' => 'проверено аудитором'
    ];

    public static $statuses_colors = [
        'new' => 'btn-secondary',
        'has_files' => 'btn-info',
        'expired_date' => 'btn-danger',
        'has_agreement' => 'btn-primary',
        'has_auditor_agreement' => 'btn-success'
    ];

    public static $years = [
        '2019-2020',
        '2020-2021',
        '2021-2022'
    ];

    public function ctreateOuptutFolders($years){
        $str = '';
        function createTreeView ($array, $currentParent, $str, $currLevel = 0, $prevLevel = -1){
            foreach ($array as $categoryId => $category) {
                if ($currentParent == $category['parent_id']) {
                    if ($currLevel > $prevLevel) $str .= ' <ul> ';
                    if ($currLevel == $prevLevel) $str .= ' </li> ';

                    $folder = NomenclatureFolder::where('id', $category['id'])->first();
                    $statuses_list = $folder->folderStatuses();

                    $str .= '<li>
                                <label @click="showFolder('.$category['id'].')">'
                                    .$category['name'].' '.$statuses_list.'
                                </label>';

                    if ($currLevel > $prevLevel) { $prevLevel = $currLevel; }

                    $currLevel++;

                    $str = createTreeView ($array, $category['id'], $str, $currLevel, $prevLevel);

                    $currLevel--;
                }
            }
            if ($currLevel == $prevLevel) $str .= ' </li>  </ul> ';

            return $str;
        }

        if($years == null){
            return createTreeView(self::all()->toArray(), 0, $str);
        } else {
            return createTreeView(self::where('years', $years)->get()->toArray(), 0, $str);
        }

    }

    public function templates(){
        return $this->hasMany('App\NomenclatureFolderTemplate', 'folder_id', 'id');
    }

    public function files(){
        $templates = $this->templates;
        $files = collect();
        foreach($templates as $template){
            if(count($template->files) > 0){
                $files->push($template->files);
            }
        }

        return $files;
    }

    public function folderStatuses(){
        $templates =  $this->templates;
        $statuses = [];

        if(count($templates) == 0){
            array_push($statuses, 'new');
        } elseif($this->status == 'has_auditor_agreement'){
            array_push($statuses, $this->status);
        } elseif($this->status == 'has_agreement'){
            array_push($statuses, 'has_agreement');
        }else {
            foreach($templates as $template){
                if(count($template->files) == 0 && !$template['load_date']->gt(Carbon::now())){
                    array_push($statuses, 'expired_date');
                }
                if(count($template->files) == 0 && $template['load_date']->gt(Carbon::now())){
                    array_push($statuses, 'new');
                }
                foreach($template->files as $file){
                    if($file->name != null){
                        array_push($statuses, 'has_files');
                    }
                }
            }
        }

        $statuses_list = '';

        foreach($statuses as $status){
            $statuses_list .= '<small class="dot media-middle mr-5 '.NomenclatureFolder::$statuses_colors[$status].'"></small>';
        }

        return $statuses_list;
    }
}
