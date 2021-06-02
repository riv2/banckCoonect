<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;
use yii;
use yii\helpers\Json;

/**
 * Настройки журнала
 *
 * Class JournalSettings
 *
 * @package app\models\reference
 * @property string     journal_id
 * @property int        per_page
 * @property string     sort_order
 * @property string     enabled_columns
 * @property string     applied_filters
 *
 * @property array      sortOrder
 * @property array      enabledColumns
 * @property array      appliedFilters
 *
 */
class JournalSettings extends Reference
{
    /**
    * @inheritdoc
    */
    public static function getSingularNominativeName()
    {
        return 'Настройки журнала';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Настройки журналов';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleDefault('per_page', 50),
            [
                [['journal_id', 'sort_order', 'enabled_columns','applied_filters'], 'string'],
                [['per_page'], 'integer'],
                [['journal_id'], 'unique', 'targetAttribute' => ['journal_id', 'created_user_id'], 'except' => self::SCENARIO_SEARCH],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'journal_id'        => 'Журнал',
            'sort_order'        => 'Сортировка',
            'enabled_columns'   => 'Колонки',
            'applied_filters'   => 'Фильтры',
            'per_page'          => 'Кол-во на страницу',
        ]);
    }

    /**
     * @param $journalId
     * @return JournalSettings
     */
    public static function getUserJournalSettings($journalId) {
        if (!Yii::$app->user) {
            return null;
        }
        if (!Yii::$app->user->identity) {
            return null;
        }

        $journalSettings = static::find()
            ->andWhere([
                'journal_id'        => $journalId,
                'created_user_id'   => Yii::$app->user->identity->getId(),
            ])
            ->one();

        if (!$journalSettings) {
            $journalSettings                    = new static;
            $journalSettings->loadDefaultValues();
            $journalSettings->journal_id        = $journalId;
            $journalSettings->created_user_id   = Yii::$app->user->identity->getId();
        }
        return $journalSettings;
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        return [
            'name',
            'createdUser',
            'sort_order',
            'per_page'
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!$this->name) {
            $this->name = $this->journal_id;
        }
        return parent::beforeValidate();
    }

    public function getEnabledColumns() {
        return explode(',', $this->enabled_columns);
    }

    public function setEnabledColumns($value) {
        $this->enabled_columns = join(',', $value);
    }

    public function getAppliedFilters() {
        return Json::decode($this->applied_filters, true);
    }

    public function setAppliedFilters($value) {
        $this->applied_filters = Json::encode($value);
    }

    public function getSortOrder() {
        $sortAttributes = explode(',', $this->sort_order);
        $sortParams = [];
        foreach ($sortAttributes as $attribute) {
            if ($attribute) {
                if ($attribute{0} == '-') {
                    $attribute = substr($attribute, 1);
                    $sortParams[$attribute] = SORT_DESC;
                } else {
                    $sortParams[$attribute] = SORT_ASC;
                }
            }
        }
        return $sortParams;
    }

    public function setSortOrder($sortParams) {
        $sortAttributes = [];
        foreach ($sortParams as $param => $sortOrder) {
            if ($sortOrder == SORT_DESC) {
                $sortAttributes[] = '-'.$param;
            } else {
                $sortAttributes[] = $param;
            }
        }
        $this->sort_order = join(',', $sortAttributes);
    }

}