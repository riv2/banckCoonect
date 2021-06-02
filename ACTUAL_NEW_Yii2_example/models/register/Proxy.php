<?php
namespace app\models\register;

use app\components\base\type\Register;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\pool\ParsingProjectProxy;
use app\models\pool\ParsingProjectProxyBan;
use app\models\reference\ParsingProject;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Модель Прокси
 *
 * @property string id
 * @property int $until
 * @property boolean $is_public
 */
class Proxy extends Register
{
    /**
     * @inheritDoc
     */
    public static function getSingularNominativeName()
    {
        return 'Прокси';
    }

    /**
     * @inheritDoc
     */
    public static function getPluralNominativeName()
    {
        return 'Прокси';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleRequired('id'),
            ValidationRules::ruleDateTime('created_at', 'updated_at'),
            ValidationRules::ruleDateTime('until'),
            [
                [['is_public'], 'boolean'],
                [['is_public'], 'default', 'value' => true],
            ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'until' => 'Действителен до',
            'is_public' => 'Публичный'
        ]);
    }

    /**
     * @inheritDoc
     */
    public function crudIndexColumns()
    {
        return [
            'id',
            'until' => [
                'attribute' => 'until',
                'format' => 'datetime',
                'value' => function ($model) {
                    /** @var Proxy $model */
                    return $model->until ? $model->until->format('Y-m-d') : null;
                },
                'contentOptions' => function ($model) {
                    /** @var Proxy $model */
                    if (!$model->until) {
                        return [];
                    }
                    $timestamp = $model->until->getTimestamp();
                    if ($timestamp < time()) {
                        return ['style' => 'background-color:#ff00005c;'];
                    } else if ($timestamp < strtotime('7 days')) {
                        return ['style' => 'background-color:#ffff005c;'];
                    }
                    return [];
                }
            ],
            'parsingProjects' => [
                'label' => 'Проекты парсинга',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var Proxy $model */
                    $parsingProjectsNames = ParsingProjectProxy::find()
                        ->alias('ppp')
                        ->select([
                            '*',
                            'name' => '(' .
                                ParsingProject::find()
                                    ->select('name')
                                    ->andWhere('id = ppp.parsing_project_id')
                                    ->createCommand()
                                    ->getRawSql()
                                . ')',
                        ])
                        ->andWhere(['proxy_id' => $model->id])
                        ->asArray()
                        ->all()
                    ;
                    if (!count($parsingProjectsNames)) {
                        return false;
                    }
                    $result = '';
                    foreach ($parsingProjectsNames as $i => $data) {
                        if ($i !== 0) {
                            $result .= ', ';
                        }
                        $result .= Html::a($data['name'], Url::to(['/parsing-project/update', 'id' => $data['parsing_project_id']]));
                    }
                    return $result;
                },
            ],
            'is_public' => [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::checkbox('', $model->is_public);
                }
            ],
        ];
    }

    public function recycle()
    {
        ParsingProjectProxy::deleteAll('proxy_id LIKE \'%' . $this->id . '%\'');
        ParsingProjectProxyBan::deleteAll('proxy_id LIKE \'%' . $this->id . '%\'');
        return parent::recycle();
    }

    public static function findOne($condition)
    {
        $query = self::find();
        if (is_array($condition) && isset($condition['id'])) {
            $id = $condition['id'];
            unset($condition['id']);
            $condition = ['LIKE', 'id', $id];
        } else if (is_string($condition)) {
               $condition = ['LIKE', 'id', $condition];
       }
        $query->andWhere($condition);
        return $query->one();
    }
}