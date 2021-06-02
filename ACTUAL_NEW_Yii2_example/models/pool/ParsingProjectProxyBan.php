<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\ValidationRules;
use app\models\reference\Competitor;
use app\models\reference\ParsingProject;
use app\models\reference\Vpn;
use app\models\register\Parsing;
use yii;
use yii\helpers\Json;

/**
 * Модель бана прокси к проекту парсинга
 *
 * @property string vpn_id
 * @property string parsing_project_id
 *
 * @property Vpn vpn
 * @property ParsingProject parsingProject
 */
class ParsingProjectProxyBan extends Pool
{
    /**
     * @inheritDoc
     */
    public static function noCount()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Бан прокси к проекту парсинга';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Баны прокси к проектам парсинга';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired(['id', 'proxy_id', 'parsing_project_id']),
            ValidationRules::ruleUuid(['id', 'parsing_project_id']),
            [
                [['proxy_id'], 'string'],
            ]
        );
    }
}