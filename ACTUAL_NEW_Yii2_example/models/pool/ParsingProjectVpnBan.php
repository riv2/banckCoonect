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
 * Модель бана VPN-сервера к проекту парсинга
 *
 * @property string vpn_id
 * @property string parsing_project_id
 *
 * @property Vpn vpn
 * @property ParsingProject parsingProject
 */
class ParsingProjectVpnBan extends Pool
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
        return 'Бан VPN-сервера к проекту парсинга';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Баны VPN-серверов к проектам парсинга';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired(['id', 'vpn_id', 'parsing_project_id']),
            ValidationRules::ruleUuid(['id', 'vpn_id', 'parsing_project_id']),
        );
    }
}