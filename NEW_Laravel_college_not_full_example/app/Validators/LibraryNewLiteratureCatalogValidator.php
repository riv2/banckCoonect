<?php
/**
 * User: vlad
 * Date: 27.02.20
 * Time: 9:46
 */

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class LibraryNewLiteratureCatalogValidator extends Validator
{

    /**
     * validation data
     * @param array $aData
     * @param array|null $aRuleList
     * @param array $aMessageList
     * @param array $aCustomAttributeList
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function make(array $aData, array $aRuleList = null, array $aMessageList = [], array $aCustomAttributeList = [])
    {

        $aRuleList = $aRuleList ? $aRuleList :
            [
                "catalog.name"              => "required|string|max:255",
                "catalog.media"             => "required|string|max:255",
                "catalog.literature_type"   => "required|string|max:255",
                "catalog.publication_type"  => "required|string|max:255",
                "catalog.publication_year"  => "required|date",
                "catalog.isbn"              => "nullable|string|max:255",
                "catalog.ydk"               => "nullable|string|max:255",
                "catalog.bbk"               => "nullable|string|max:255",
                "catalog.author"            => "required|string|max:255",
                "catalog.more_authors"      => "nullable|string|max:255",
                "catalog.language"          => "required|string|max:255",
                "catalog.number_pages"      => "required|string",
                "catalog.key_words"         => "nullable|string|max:255",
                "catalog.cost"              => "required|numeric",
                "catalog.receipt_date"      => "required|date",
                "catalog.source_income"     => "required|string|max:255",
                "catalog.e_books_name"      => "nullable|file",
                "catalog.publisher"         => "nullable|string",
                "catalog.publication_place" => "nullable|string",
                "knowledge_section"         => "required"
            ];

        return parent::make($aData, $aRuleList, $aMessageList, $aCustomAttributeList);
    }

}

