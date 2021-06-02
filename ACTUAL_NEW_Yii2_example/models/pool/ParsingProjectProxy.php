<?php

namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\ValidationRules;
use app\models\reference\ParsingProject;
use app\models\register\Proxy;

/**
 * Class ParsingProjectProject
 *
 * Связь проекта парсинга и прокси
 *
 * @package app\models\reference
 * @property string parsing_project_id
 * @property string proxy_id
 *
 * @property ParsingProject parsingProject
 * @property Proxy proxy
 */
class ParsingProjectProxy extends Pool
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Связь проекта парсинга и прокси';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Связь проектов парсинга и прокси';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleRequired('parsing_project_id','proxy_id'),
            ValidationRules::ruleUuid('parsing_project_id'),
            ValidationRules::ruleUuid('id')
        );
    }

    /**
     * @inheritDoc
     */
    public static function relations()
    {
        return [
            'parsingProject',
            'proxy',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingProject()
    {
        return $this->hasOne(ParsingProject::className(), ['id' => 'parsing_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProxy()
    {
        return $this->hasOne(Proxy::className(), ['id' => 'proxy_id']);
    }

    public function getPrimaryKey($isArray = false)
    {
        return $this->id || null;
    }
}