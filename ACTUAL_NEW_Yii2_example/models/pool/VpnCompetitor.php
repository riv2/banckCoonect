<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\ValidationRules;
use app\models\reference\Competitor;
use app\models\reference\Vpn;
use app\models\register\Parsing;
use yii;
use yii\helpers\Json;

/**
 * Модель связки vpn-конкурент
 *
 * @property string vpn_id
 * @property string competitor_id
 * @property string parsing_id
 *
 * @property Vpn vpn
 * @property Competitor competitor
 * @property Parsing parsing
 */
class VpnCompetitor extends Pool
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
        return 'Vpn-сервер к конкуренту';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Vpn-сервера к конкурентам';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired(['id', 'vpn_id', 'competitor_id', 'parsing_id']),
            ValidationRules::ruleUuid(['id', 'vpn_id', 'competitor_id', 'parsing_id']),
            [
                [['vpn_id', 'competitor_id', 'parsing_id'], 'integer']
            ]
        );
    }
}