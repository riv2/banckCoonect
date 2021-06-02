<?php
namespace app\components\base;
use app\components\base\type\Enum;
use app\components\ValidationRules;
use yii;
use yii\db\IntegrityException;
use yii\helpers\ArrayHelper;

/**
 * @property string class_name
 * @property string entity_type
 * @property string action
 * @property string alias
 * @property string is_logging
 * @property string parent_id
 */
class Entity extends Enum
{
    const ENTITY_TYPE_ENUM          = 'enum';
    const ENTITY_TYPE_REGISTER      = 'register';
    const ENTITY_TYPE_REFERENCE     = 'reference';
    const ENTITY_TYPE_DOCUMENT      = 'document';
    const ENTITY_TYPE_CROSS         = 'cross';
    const ENTITY_TYPE_POOL          = 'pool';
    
    const Status                    = 0;
    const User                      = 1;
    const Source                    = 2;
    const Brand                     = 3;
    const Category                  = 4;
    const PriceFormerType           = 5;
    const Region                    = 6;
    const Competitor                = 7;

    const JournalSettings           = 8;
    
    const Item                      = 10;
    const CompetitorItem            = 11;
    const SelectPriceLogic          = 12;
    const PriceExportMode           = 13;
    const CompetitionMode           = 14;
    const CategoryItem              = 15;

    const PriceParsed               = 17;
    const PriceRefined              = 18;

    const ProjectExecutionStatus    = 19;

    const Project                   = 20;
    const ProjectItem               = 21;
    const ProjectCompetitor         = 22;
    const ProjectCompetitorBrand    = 23;
    const ProjectCompetitorCategory = 24;
    const ProjectExecution          = 25;
    const ProjectSource             = 26;

    const PriceCalculated           = 27;

    const LogProjectExecution       = 28;
    const LogPriceCalculation       = 29;

    const CategoryCategory          = 30;

    const ErrorType                 = 31;
    const Error                     = 32;

    const ExchangeSystem            = 33;
    const ExchangeImport            = 34;
    const ExchangeExport            = 35;

    const FileType                  = 36;
    const FileExchangeSettings      = 37;
    const FileExchange              = 38;

    const CompetitorShopName        = 39;
    const CompetitorShopDomain      = 40;
    const CompetitorShopIndex       = 41;

    const TaskType                  = 42;
    const TaskStatus                = 43;
    const Task                      = 44;
    
    const ReportKeywordsControl     = 45;
    const ReportCalculationOverview = 46;
    
    const ProjectTheme              = 47;
    
    const ParsingProject            = 48;
    const ParsingProjectRegion      = 49;
    const Parsing                   = 50;
    const ParsingStatus             = 16;

    const Schedule                  = 51;
    
    const ParsingProjectProject     = 52;
    const PriceParsedStatus         = 53;
    const ParsingBuffer             = 54;
    
    const ProjectPriceFormerType    = 55;
    const Screenshot                = 56;
    const ProjectRegion             = 57;
    const Masks                     = 59;
    const Robot                     = 60;
    
    const ParsingError              = 61;
    const ParsingProjectMasks       = 62;

    const AntiCaptchaTask           = 63;

    const BrandFilter               = 64;
    const HoradricCubeStatus        = 65;
    const HoradricCube              = 66;

    const FileProcessingSettings    = 67;
    const FileProcessing            = 68;


    const ReportKpi                 = 69;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['class_name','table_name','alias','action'], 'string'],
                [['is_logging'], 'boolean'],
            ],
            ValidationRules::ruleDefault('is_logging',false),
            ValidationRules::ruleRequired('class_name','table_name','alias'),
            ValidationRules::ruleEnum('parent_id', Entity::className())
        );
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'class_name'    => 'Класс',
                'action'        => 'Контроллер',
                'alias'         => 'Имя',
                'status'        => 'Состояние',
            ]
        );
    }

    /**
     * Загрузка сущностей из кэша
     * @return array
     */
    protected static function loadFromCache()
    {
        $tableName = Yii::$app->db->schema->getRawTableName(static::tableName());
        if (!($cache = Yii::$app->cache->get('#' . $tableName . '#'))) {
            $entities = static::find()->orderBy(['name' => SORT_ASC])->all();
            /** @var Entity $entity */
            foreach ($entities as $entity) {
                $cache['idByClassName'][$entity->class_name] = $entity->id;
                $cache['idByName'][$entity->name] = $entity->id;
                $cache['nameById'][$entity->id] = $entity->name;
                $cache['byId'][$entity->id] = $entity->toArray();
            }
            Yii::$app->cache->set('#' . $tableName . '#', $cache, 0);
        }
        return $cache;
    }

    /**
     * Возвращает идентификатор сущности по названию класса
     * @param string $className
     * @return mixed
     * @throws IntegrityException
     */
    public static function getIdByClassName($className) {
        $cache = static::loadFromCache();
        if (isset($cache['idByClassName'][$className])) {
            return $cache['idByClassName'][$className];
        }
        throw new IntegrityException('Сущности с классом "' . $className . '" не существует');
    }

    /**
     * Возвращает название класса по идентификатору
     * @param integer $id
     * @return mixed
     * @throws IntegrityException
     */
    public static function getClassNameById($id) {
        $cache = static::loadFromCache();
        if (isset($cache['byId'][$id]['class_name'])) {
            return $cache['byId'][$id]['class_name'];
        }
        throw new IntegrityException('Сущности с идентификатором "' . $id . '" не существует');
    }

    /**
     * Возвращает статус логирования по идентификатору
     * @param integer $id
     * @return mixed
     * @throws IntegrityException
     */
    public static function getLoggingStatusById($id)
    {
        $cache = static::loadFromCache();
        if (isset($cache['byId'][$id]['class_name'])) {
            return $cache['byId'][$id]['class_name'];
        }
        throw new IntegrityException('Сущности с идентификатором "' . $id . '" не существует');
    }
}