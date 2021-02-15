<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryLiteratureCatalog extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['deleted_at'];

    public static $media = [
    	'Печатное издание',
    	'Аудио',
    	'Видео',
    	'Электронный формат'
    ];

    public static $literature_type = [
    	'Научная',
    	'Учебная',
    	'Учебно- методическая',
    	'Художественная',
    	'Другое'
    ];

    public static $publication_type = [
    	'Учебник', 
    	'словарь', 
    	'глоссарий', 
    	'реферат', 
    	'монография', 
    	'журнал', 
    	'справочник', 
    	'энциклопедия', 
    	'диссертация', 
    	'сборник задач', 
    	'сборник упражнений', 
    	'переодическое издание', 
    	'практическое пособие', 
    	'учебное пособие', 
    	'курс лекций', 
    	'видео лекция', 
    	'сборник научных трудов', 
    	'cборник конференций', 
    	'серия книг', 
    	'другое'
    ];

    public static $language = [
    	'Русский',
    	'Казахский',
    	'Арабский',
    	'Английский',
    	'Французский',
    	'Немецкий',
    	'Турецкий'
    ];

    public function knowledgeSections(){
        return $this->hasManyThrough(
            'App\LibraryKnowledgeSection', 
            'App\LibratyCatalogKnowledgeSection', 
            'literature_catalog_id', 
            'id', 
            'id', 
            'knowledge_section_id'
        );
    }

    public function disciplines(){
        return $this->hasManyThrough('App\Discipline', 'App\LibraryCatalogDiscipline', 'literature_catalog_id', 'id', 'id', 'discipline_id');
    }
}
