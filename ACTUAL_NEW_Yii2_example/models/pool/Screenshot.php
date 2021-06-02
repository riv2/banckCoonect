<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 30.11.16
 * Time: 13:37
 */

namespace app\models\pool;


use app\components\base\type\Pool;
use app\components\ValidationRules;
use app\models\reference\Competitor;
use app\models\reference\Item;
use yii;

/**
 * Class Screenshot
 * @package app\models\pool
 *
 * @property string item_id
 * @property string competitor_id
 * @property string parsing_id
 * @property float price
 * @property float rrp
 * @property string competitor_shop_name
 * @property string url
 * @property string filename
 * @property string public_url
 * @property bool is_published
 * @property int index
 */
class Screenshot extends Pool
{
    public static function noCount()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Скриншот';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Скриншоты';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired(['item_id','filename']),
            ValidationRules::ruleUuid(['item_id', 'competitor_id', 'parsing_id']),
            [
                [['filename','public_url','competitor_shop_name','url'], 'string'],
                [['is_published'], 'boolean'],
                [['price','rrp'], 'number'],
            ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'item_id'                   => 'Товар',
            'competitor_id'             => 'Конкурент',
            'price'                     => 'Цена',
            'rrp'                       => 'РРЦ',
            'competitor_shop_name'      => 'Магазин',
            'url'       => 'УРЛ товара',
            'filename'                  => 'Файл',
            'public_url'                => 'Скриншот',
            'is_published'              => 'Опубликован',
            'index'                     => 'Индекс',
        ]);
    }

    public function crudIndexColumns()
    {
        return array_merge(parent::crudIndexColumns(),[
            'created_at',
            'item' ,
            'competitor',
            'price',
            'rrp',
            'screenshot' => [
                'label'     => 'Скрин',
                'format'    => 'raw',
                'value'     => function($model) {
                    /** @var Screenshot  $model */
                    $url = $model->public_url;
                    if (!$url) {
                        return null;
                    }
                    $js = "var el = this; setTimeout(function(){var e = jQuery('<i></i>');e.addClass('fa fa-exclamation-triangle');e.css('color','#f39c12');e.css('font-size','140%');jQuery(el).replaceWith(e);},1000);";
                    return '<a href="'.$url.'" target="_blank" style="text-align: center; display: block;" class="magnifier-image"><img src="'.$url.'" width="64" height="37" style="border: none; width 64px; height: 37px; margin: -8px; vertical-align: middle;" onerror="'.$js.'" /> <img src="'.$url.'" width="600" class="magnified-image" style="display:none;" onerror="this.remove();" /></a>';
                }
            ],
            'public_url' => [
                'attribute' => 'public_url',
                'label' => 'УРЛ скрина',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Screenshot  $model */
                    $url = $model->public_url;
                    if (!$url) {
                        return null;
                    }
                    return '<nobr><a href="'.$url.'" target="_blank">'.$url.'</a></nobr>';
                }
            ],
            'url' ,
            'is_published'
        ]);
    }

    public static function relations()
    {
        return array_merge(parent::relations(),[
            'item' ,
            'competitor',
        ]);
    }

    public function crudIndexSearchRelations()
    {
        return array_merge(parent::crudIndexSearchRelations(),[
            'item' ,
            'competitor',
        ]);
    }



    /**
     * Расшарить скриншот из одной папки ВебДав в другую
     * @return null|string
     */
    public function publish() {
        if (!$this->is_published &&
            isset(Yii::$app->params['webDavScreenshots']['folderFrom']) &&
            isset(Yii::$app->params['webDavScreenshots']['folderTo'])) {
            $from = Yii::$app->params['webDavScreenshots']['folderFrom'];
            $to = Yii::$app->params['webDavScreenshots']['folderTo'];
            $extension = pathinfo($this->filename, PATHINFO_EXTENSION);
            $uniqueFileName = $this->id.'.'.$extension;
            $fileName = $this->filename;
            // > /dev/null &
            $exec = "curl -X COPY --header 'Destination:".$to."/".$uniqueFileName."'  '$from/$fileName'";
            exec($exec);
            if (Yii::$app->params['webDavScreenshots']['publicRoot']) {
                $uniqueFileName = Yii::$app->params['webDavScreenshots']['publicRoot'].'/'.$uniqueFileName;
            }
            $this->public_url = $uniqueFileName;
            $this->is_published = true;
            $this->save();
            return $uniqueFileName;
        }
        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor()
    {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id']);
    }

}