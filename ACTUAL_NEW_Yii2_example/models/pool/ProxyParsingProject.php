<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\ValidationRules;
use app\models\reference\Competitor;
use app\models\reference\ParsingProject;
use app\models\reference\Vpn;
use app\models\register\Parsing;
use app\models\register\Proxy;
use yii;
use yii\helpers\Json;

/**
 * Модель связки прокси к парсингу
 *
 * @property string proxy_id
 * @property string parsing_project_id
 * @property string parsing_id
 *
 * @property Proxy proxy
 * @property ParsingProject parsingProject
 * @property Parsing parsing
 */
class ProxyParsingProject extends Pool
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
        return 'Прокси к парсингу';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Прокси к парсингам';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired(['id', 'proxy_id', 'parsing_project_id', 'parsing_id']),
            ValidationRules::ruleUuid(['id', 'proxy_id', 'parsing_project_id', 'parsing_id'])
        );
    }
}