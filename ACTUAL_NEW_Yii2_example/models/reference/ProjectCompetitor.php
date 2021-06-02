<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;
use app\models\enum\SelectPriceLogic;
use app\models\enum\Status;
use Yii;
use yii\base\InvalidValueException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 *
 * Конкуренты проекта
 *
 * Class ProjectCompetitor
 *
 * @package app\components\base\type
 * @property string     competitor_id               Конкурент
 * @property string     project_id                  Проект
 * @property int        select_price_logic_id       Алгоритм выбора цены
 *
 * @property boolean    is_key_competitor           Ключевой конкурент
 * @property float      min_margin                  Мин. наценка
 * @property float      price_variation_modifier    Процент отклонения
 * @property float      price_final_modifier   Изменить РЦ на  https://glpi.vseinstrumenti.ru/front/ticket.form.php?id=137700
 *
 * @property Competitor         competitor           Конкурент
 * @property Project            project              Проект
 * @property SelectPriceLogic   selectPriceLogic     Алгоритм выбора цены
 *
 * @property \app\models\reference\ProjectCompetitorBrand[]       projectCompetitorBrands          Связь с брендами
 * @property \app\models\reference\ProjectCompetitorCategory[]    projectCompetitorCategories       Связь с категориями
 * @property Brand[]        brands          Бренды
 * @property Category[]     categories      Категории
 * @property array        newBrands          Бренды
 * @property array     newCategories      Категории
 */

class ProjectCompetitor extends Reference
{
    protected $_brands           = null;
    protected $_categories       = null;
    protected $_brandsBanned     = false;
    protected $_categoriesBanned = false;
    protected $_itemsExcluded    = false;


    /**
     * @inheritdoc
     */
    public static function isBigData() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function noCount() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Конкурент проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Конкуренты проекта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('project_id','competitor_id'),
            [
                [['min_margin','price_variation_modifier'], 'number'],
                [['is_key_competitor'], 'boolean'],
                [['price_final_modifier'],'number'],
                [['newCategories','newBrands'],'safe'],
            ],
            ValidationRules::ruleDefault('is_key_competitor', false),
            ValidationRules::ruleDefault('select_price_logic_id', SelectPriceLogic::LOGIC_A),
            ValidationRules::ruleUuid('competitor_id'),
            ValidationRules::ruleUuid('project_id'),
            ValidationRules::ruleEnum('select_price_logic_id', SelectPriceLogic::className()),
        );
    }
    
    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if ($this->scenario != static::SCENARIO_SEARCH) {
            if (!$this->name && $this->competitor_id) {
                $this->name = $this->competitor->name;
            }
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'competitor_id'             => 'Конкурент',
                'name'                      => 'Конкурент',
                'project_id'                => 'Проект',
                'min_margin'                => 'Мин. наценка',
                'price_variation_modifier'  => 'Процент отклонения',
                'price_final_modifier' => 'Изменить РЦ на',
                'select_price_logic_id'     => 'Алгоритм средней цены',
                'is_key_competitor'         => 'Ключевой конкурент',
                'competitor'                => 'Конкурент',
                'project'                   => 'Проект',
                'selectPriceLogic'          => 'Алгоритм средней цены',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function fileImportPresetColumns()
    {
        return array_merge(parent::fileImportPresetColumns(), [
            'project_id',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function excludeFieldsFileImportColumns()
    {
        return array_merge(parent::excludeFieldsFileImportColumns(), [
            'id',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'project',
            'competitor',
            'is_key_competitor',
            'price_variation_modifier',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(), [
            'competitor',
            'project',
            'projectCompetitorCategories',
            'projectCompetitorBrands',
            'projectCompetitorItems',
        ]);
    }

    /**
     * @return bool
     */
    public function brandsBanned() {
        $this->categories();
        return $this->_brandsBanned;
    }

    /**
     * @return bool
     */
    public function categoriesBanned() {
        $this->brands();
        return $this->_categoriesBanned;
    }

    /**
     * @return array
     */
    public function brands() {
        if ($this->isNewRecord) {
            return [];
        }
        if ($this->_brands == null) {
            $this->_brands = [];
            $brands = $this->getProjectCompetitorBrands()->alias('t')->innerJoin(['b' => Brand::tableName()],'b.id = t.brand_id')->select(['name' => 'b.name', 'brand_id' => 't.brand_id', 'status_id' => 't.status_id'])->asArray()->all();
            foreach ($brands as $brand) {
                $this->_brands[$brand['brand_id']] = $brand['name'];
                $this->_brandsBanned = ($brand['status_id'] == Status::STATUS_DISABLED);
            }
        }
        return $this->_brands;
    }

    /**
     * @return array
     */
    public function categories() {
        if ($this->isNewRecord) {
            return [];
        }
        if ($this->_categories == null) {
            $this->_categories = [];
            $categories = $this->getProjectCompetitorCategories()->alias('t')->innerJoin(['c' => Category::tableName()],'c.id = t.category_id')->select(['name' => 'c.name', 'category_id' => 't.category_id', 'status_id' => 't.status_id'])->asArray()->all();
            foreach ($categories as $category) {
                $this->_categories[$category['category_id']] = $category['name'];
                $this->_categoriesBanned = ($category['status_id'] == Status::STATUS_DISABLED);
            }
        }
        return $this->_categories;
    }

    /**
     * @param Project $project
     * @param $competitorId
     * @param array $data
     * @return ProjectCompetitor
     */
    public static function create(Project $project, $competitorId,  $data = []) {

        $data = array_merge([
            'min_margin' => $project->min_margin,
            'is_key_competitor' => false,
            'price_variation_modifier' => null,
            'price_final_modifier' => null,
            'rrp_regulations' => false,
            'status_id' => Status::STATUS_ACTIVE,
        ], $data);

        $projectCompetitor = new ProjectCompetitor();
        $projectCompetitor->loadDefaultValues();
        $projectCompetitor->project_id = $project->id;
        $projectCompetitor->competitor_id = $competitorId;
        $projectCompetitor->min_margin = $data['min_margin'] ?: null;
        $projectCompetitor->is_key_competitor = $data['is_key_competitor'] ?: false;
        $projectCompetitor->status_id = $data['status_id'] ? Status::STATUS_DISABLED : Status::STATUS_ACTIVE;
        $projectCompetitor->price_variation_modifier = (isset($data['price_variation_modifier']) && $data['price_variation_modifier'] != '') ? floatval($data['price_variation_modifier']) : null;
        $projectCompetitor->price_final_modifier = (isset($data['price_final_modifier']) && $data['price_final_modifier'] != '') ? floatval($data['price_final_modifier']) : null;

        if (!$projectCompetitor->validate()) {
            throw new InvalidValueException(Json::encode($projectCompetitor->errors));
        }

        $projectCompetitor->save();

        return $projectCompetitor;
    }

    public function setNewBrands($rows) {
        if ($this->isNewRecord) {
            return;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            ProjectCompetitorBrand::deleteAll([
                'project_competitor_id' => $this->id,
            ]);
            $common = [
                'project_id' => $this->project_id,
                'competitor_id' => $this->competitor_id,
                'project_competitor_id' => $this->id,
            ];
            foreach ($rows as $brand) {
                $projectCompetitorBrand = new ProjectCompetitorBrand;
                $projectCompetitorBrand->setAttributes(array_merge($common, [
                    'brand_id' => $brand['brand_id'],
                    'status_id' => $brand['status_id'],
                ]));
                $projectCompetitorBrand->save();
            }
            $transaction->commit();
            $this->_brands = null;
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
    }

    public function setNewCategories($rows) {
        if ($this->isNewRecord) {
            return;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {

            ProjectCompetitorCategory::deleteAll([
                'project_competitor_id' => $this->id,
            ]);
            $common = [
                'project_id' => $this->project_id,
                'competitor_id' => $this->competitor_id,
                'project_competitor_id' => $this->id,
            ];
            foreach ($rows as $category) {
                $projectCompetitorBrand = new ProjectCompetitorCategory;
                $projectCompetitorBrand->setAttributes(array_merge($common, [
                    'category_id' => $category['category_id'],
                    'status_id' => $category['status_id'],
                ]));
                $projectCompetitorBrand->save();
            }
            $transaction->commit();
            $this->_categories = null;
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
    }

    public function setNewExcludedItems($rows) {
        if ($this->isNewRecord) {
            return;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            ProjectCompetitorItem::deleteAll([
                'project_competitor_id' => $this->id,
            ]);
            $common = [
                'project_id' => $this->project_id,
                'competitor_id' => $this->competitor_id,
                'project_competitor_id' => $this->id,
            ];
            foreach ($rows as $itemId) {
                $projectCompetitorItem = new ProjectCompetitorItem;
                $projectCompetitorItem->setAttributes(array_merge($common, [
                    'item_id' => $itemId,
                    'status_id' => Status::STATUS_ACTIVE,
                ]));
                $projectCompetitorItem->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor()
    {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSelectPriceLogic()
    {
        return $this->hasOne(SelectPriceLogic::className(), ['id' => 'select_price_logic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCompetitorBrands()
    {
        return $this->hasMany(ProjectCompetitorBrand::className(), ['project_competitor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCompetitorCategories()
    {
        return $this->hasMany(ProjectCompetitorCategory::className(), ['project_competitor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCompetitorItems()
    {
        return $this->hasMany(ProjectCompetitorItem::className(), ['project_competitor_id' => 'id']);
    }
}