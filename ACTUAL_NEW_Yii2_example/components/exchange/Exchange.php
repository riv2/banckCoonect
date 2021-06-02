<?php
namespace app\components\exchange;


use app\components\base\BaseModel;
use app\components\base\Entity;
use app\models\enum\ErrorType;
use app\models\reference\ExchangeSystem;
use app\models\register\Error;
use app\models\register\ExchangeImport;
use yii;
use yii\base\BaseObject;


/**
 * Class Exchange
 * @package app\components\exchange
 *
 *
 */
class Exchange extends BaseObject
{

    public $importEnabled          = false;
    public $exportEnabled          = false;

    protected static $importedCache = [];
    protected static $importingCache = [];
    protected $transaction = null;
    protected $exchangeSystem = null;

    public function __construct()
    {
        $this->exchangeSystem = ExchangeSystem::find()->andWhere(['class_name' => $this->className()])->one();

        if (!$this->exchangeSystem) {
            $this->exchangeSystem               = new ExchangeSystem();
            $this->exchangeSystem->class_name   = $this->className();
            $this->exchangeSystem->name         = (new \ReflectionClass($this))->getShortName();
            $this->exchangeSystem->save();
        }
        parent::__construct($this->exchangeSystem->getParams());
    }

    public function save() {
        $params = call_user_func('get_object_vars', $this);
        $this->exchangeSystem->setParams($params);
        $this->exchangeSystem->save();
    }

    public static function systemName() {
        return 'Какая-то система';
    }

    /**
     * Статистика по обмену данными
     * @return array|mixed
     * @throws yii\db\IntegrityException
     */
    public static function stats() {

        $stats =  Yii::$app->cache->get("Exchange::stats");

        if (!$stats) {

            $stats = [
                'import' => [],
                'export' => [],
                'importErrors' => [],
                'exportErrors' => [],
                'totals' => [
                    'import'        => 0,
                    'export'        => 0,
                    'importErrors'  => 0,
                    'exportErrors'  => 0,
                ],
            ];

            $import = ExchangeImport::find()
                ->groupBy(['requester_entity_id', 'requester_id', 'remote_entity'])
                ->select([
                    'requester_entity_id'   => 'requester_entity_id',
                    'requester_id'          => 'requester_id',
                    'remote_entity'         => 'remote_entity',
                    'count'                 => 'count(nullif(is_error = true, true))',
                    'count_errors'          => 'count(nullif(is_error = false, true))',
                ])
                ->asArray()
                ->all();

            foreach ($import as $row) {
                $requesterType = 'Обновления';
                $requesterName = "";
                $remoteName = "";
                $systemName = "";

                if ($row['requester_entity_id']) {
                    $requesterClass = Entity::getClassNameById($row['requester_entity_id']);
                    /** @var BaseModel $requesterClass */
                    $requesterType = 'через «'.$requesterClass::getSingularNominativeName().'»';
                    if ($row['requester_id']) {
                        $requesterObject = $requesterClass::findOne($row['requester_id']);
                        if ($requesterObject) {
                            $requesterName = "$requesterObject";
                        }
                    }
                }
                if (isset(Yii::$app->params['exchange']['importLabels']) &&
                    isset(Yii::$app->params['exchange']['importLabels'][$row['remote_entity']])) {
                    $remoteName = Yii::$app->params['exchange']['importLabels'][$row['remote_entity']];
                } else {
                    $remoteName = $row['remote_entity'];
                }
                if (isset(Yii::$app->params['exchange']['import']) &&
                    isset(Yii::$app->params['exchange']['import'][$row['remote_entity']])) {
                    $systemClass = Yii::$app->params['exchange']['import'][$row['remote_entity']];
                    /** @var Exchange $systemClass */
                    $systemName = $systemClass::systemName();
                }

                // Buffer count
                if (intval($row['count'], 10) > 0) {
                    if (!isset($stats['import'][$requesterType])) {
                        $stats['import'][$requesterType] = [];
                    }
                    $count = intval($row['count'], 10);
                    $stats['import'][$requesterType][] = [
                        'name'                  => $remoteName,
                        'remote_entity'         => $row['remote_entity'],
                        'requester_type'        => $requesterType,
                        'requester_name'        => $requesterName,
                        'requester_entity_id'   => $row['requester_entity_id'],
                        'requester_id'          => $row['requester_id'],
                        'count'                 => $count,
                        'system_name'           => $systemName,
                    ];
                    $stats['totals']['import'] += $count;
                }

                // Errors count
                if (intval($row['count_errors'], 10) > 0) {
                    if (!isset($stats['importErrors'][$requesterType])) {
                        $stats['importErrors'][$requesterType] = [];
                    }
                    $count = intval($row['count_errors'], 10);
                    $stats['importErrors'][$requesterType][] = [
                        'name'                  => $remoteName,
                        'remote_entity'         => $row['remote_entity'],
                        'requester_type'        => $requesterType,
                        'requester_name'        => $requesterName,
                        'requester_entity_id'   => $row['requester_entity_id'],
                        'requester_id'          => $row['requester_id'],
                        'count'                 => $count,
                        'system_name'           => $systemName,
                    ];
                    $stats['totals']['importErrors'] += $count;
                }
            }

            Yii::$app->cache->set("Exchange::stats", $stats, 60);
        }

        return $stats;
    }

    /**
     * Запуск обмена
     * @param string    $direction  Направление: import/export
     * @param array     $entities   Сущности и их параметры. Пример: ['Items' => ['date' => '2016-01-01 23:00:00'], 'Categories' => []]
     * @return bool
     */
    private static function runExchange($direction = 'import', $entities) {

        $output = false;

        if (!isset(Yii::$app->params['exchange'][$direction])) {
            throw new yii\base\InvalidCallException("Направление обмена не существует. [$direction]");
        }

        if (!is_array($entities)) {
            throw new yii\base\InvalidCallException("Не верно описаны сущности для обмена. [$entities]");
        }

        $entitiesArray = [];

        foreach (Yii::$app->params['exchange'][$direction] as $entity => $class) {
            if (isset($entities[$entity]) &&
                isset(Yii::$app->params['exchange']['systems'][$class]) &&
                Yii::$app->params['exchange']['systems'][$class]) {
                $entitiesArray[$entity] = $entities[$entity];
            }
        }

        if (count($entitiesArray) == 0) {
            return false;
        }

        foreach($entitiesArray as $remoteEntity => $params) {
            $output = false;

            if ($output) echo "[" . date('H:i:s') . "] $remoteEntity {$direction} ..." . PHP_EOL;

            $class = Yii::$app->params['exchange'][$direction][$remoteEntity];

            /** @var Exchange $exchange */
            $exchange = new $class();

            if ($direction === 'export') {
                if ($exchange->exportEnabled) {
                    $method         = $direction . $remoteEntity;
                    if (method_exists($exchange, $method)) {
                        call_user_func_array([$exchange, $method], [$params]);
                        if ($output) echo "[" . date('H:i:s') . "] $remoteEntity exported!". PHP_EOL . PHP_EOL;
                    }
                } else {
                    if ($output) echo "[" . date('H:i:s') . "] $remoteEntity {$direction} disabled!" . PHP_EOL . PHP_EOL;
                }
                $exchange->save();
            }

            if ($direction === 'import') {
                if ($exchange->importEnabled) {
                    $enqueueMethod  = $direction . $remoteEntity . 'Enqueue';
                    $method         = $direction . $remoteEntity;
                    if (method_exists($exchange, $enqueueMethod)) {

                        if (isset($params['enqueueIds']) && count($params['enqueueIds']) > 0) {
                            $count = $exchange->importEnqueue($remoteEntity, $params['enqueueIds']);
                            if ($output) echo "[" . date('H:i:s') . "] $remoteEntity import prepared: $count" . PHP_EOL;
                            $exchange->save();
                        }

                        if (isset($params['importIds'])) {
                            $params = array_merge([
                                'requesterObject' => null,
                                'forced' => true,
                            ], $params);
                            $items = $exchange->import($remoteEntity, $params['importIds'], $params['requesterObject'], $params['forced']);
                            if ($output) echo "[" . date('H:i:s') . "] $remoteEntity imported! $remoteEntity imported:" . count($items) . PHP_EOL;
                        } else if (!isset($params['autoEnqueue']) || $params['autoEnqueue'] != false) {
                            $toEnqueue = $exchange->$enqueueMethod($params);
                            if ($output) echo "[" . date('H:i:s') . "] $remoteEntity to enqueue: " . count($toEnqueue) . PHP_EOL;
                            $count = $exchange->importEnqueue($remoteEntity, $toEnqueue);
                            if ($output) echo "[" . date('H:i:s') . "] $remoteEntity import prepared: $count" . PHP_EOL;
                            $exchange->save();
                        }

                        if (isset($params['importQueue'])) {
                            $params['importQueue'] = intval($params['importQueue'], 10);
                            if ($params['importQueue'] <= 0) {
                                $params['importQueue'] = 500;
                            }
                            /** @var ExchangeImport[] $imports */
                            foreach (ExchangeImport::find()->andWhere([
                                'remote_entity' => $remoteEntity,
                                'is_error'      => false,
                            ])
                                         ->limit($params['importQueue'])
                                         ->batch(500) as $imports) {

                                $remoteIds = [];
                                foreach ($imports as $exchangeImport) {
                                    $remoteIds[] = $exchangeImport->remote_id;
                                }
                                if ($output) echo "[" . date('H:i:s') . "] Importing  " . count($remoteIds) . ' '. $remoteEntity . '...' . PHP_EOL;
                                $items = $exchange->import($remoteEntity, $remoteIds);
                                if ($output) echo "[" . date('H:i:s') . "] $remoteEntity imported: " . count($items) .' ! ' . PHP_EOL;
                            }
                        }
                    } else {
                        if (isset($params['importIds'])) {
                            $params = array_merge([
                                'requesterObject' => null,
                                'forced' => true,
                            ], $params);
                            $items = $exchange->import($remoteEntity, $params['importIds'], $params['requesterObject'], $params['forced']);
                            if ($output) echo "[" . date('H:i:s') . "] $remoteEntity imported! $remoteEntity imported:" . count($items) . PHP_EOL;
                        } else if (method_exists($exchange, $method)) {
                            $items = $exchange->import($remoteEntity);
                            if ($output) echo "[" . date('H:i:s') . "] $remoteEntity imported! $remoteEntity imported: " . count($items) . PHP_EOL . PHP_EOL;
                        }
                    }
                } else {
                    if ($output) echo "[" . date('H:i:s') . "] $remoteEntity {$direction} disabled!" . PHP_EOL . PHP_EOL;
                }
                $exchange->save();
            }
        }

        return true;
    }

    /**
     * Запуск экспорта
     * @param $entities
     * @return bool
     */
    public static function runExport($entities) {
        return static::runExchange('export', $entities);
    }

    /**
     * Запуск импорта
     * @param null $entities
     * @return bool
     */
    public static function runImport($entities = null) {
        // Если это иморт, и не передано ничего - импортировать ВСЁ
        if ($entities === null) {
            $keys = array_keys(Yii::$app->params['exchange']['import']);
            $values = [];
            foreach ($keys as $key) {
                $values[] = [];
            }
            $entities = array_combine($keys, $values);
        }
        return static::runExchange('import', $entities);
    }

    public function import($remoteEntity, $remoteIds = null, $requesterObject = null, $forced = true) {

        $enqueueMethod  = 'import' . $remoteEntity . 'Enqueue';
        $method         = 'import' . $remoteEntity;
        $checkMethod    = 'check' . $remoteEntity;
        $queue          = method_exists($this, $enqueueMethod);


        $imported       = [];
        if ($remoteIds !== null) {

            if (!is_array($remoteIds)) {
                $remoteIds = [$remoteIds];
            }

            $toImport = array_combine($remoteIds, $remoteIds);

            foreach ($remoteIds as $remoteId) {
                if (isset(static::$importedCache[$remoteEntity . '_' . $remoteId])) {
                    unset($toImport[$remoteId]);
                    if (static::$importedCache[$remoteEntity . '_' . $remoteId]) {
                        $imported[$remoteId] = static::$importedCache[$remoteEntity . '_' . $remoteId];
                    }
                }
            }
            $remoteIds = array_merge([], $toImport);

            if (!$forced && count($toImport) > 0 && method_exists($this, $checkMethod)) {
                /** @var BaseModel[] $alreadyImported */
                $alreadyImported = $this->$checkMethod($remoteIds);
                foreach ($alreadyImported as $row) {
                    $remoteId =  $row->id;
                    static::$importedCache[$remoteEntity . '_' . $remoteId] = $row;
                    $imported[$remoteId] = static::$importedCache[$remoteEntity . '_' . $remoteId];
                    unset($toImport[$remoteId]);
                }
            }

            if (count($toImport) == 0) {
                return $imported;
            }

            if ($queue) {
                $toQueue = array_merge([], $toImport);
                /** @var ExchangeImport[] $importing */
                $importing = ExchangeImport::find()->andWhere([
                    'remote_entity' => $remoteEntity,
                    'remote_id'     => $toQueue,
                    'is_error'      => false,
                ])->all();
                if ($importing) {
                    foreach ($importing as $exchangeImport) {
                        static::$importingCache[$remoteEntity . '_' . $exchangeImport->remote_id] = $exchangeImport;
                        unset($toQueue[$exchangeImport->remote_id]);
                    }
                }
                $this->importEnqueue($remoteEntity, array_values($toQueue), $requesterObject);
            }

            return  array_merge($imported, $this->$method( array_values($toImport)));
        }
        return  array_merge($imported, $this->$method());
    }

    /**
     * @param $remoteEntity
     * @param $remoteIds
     * @param BaseModel|null $requesterObject
     * @return int
     * @throws yii\db\Exception
     * @throws yii\db\IntegrityException
     */
    public function importEnqueue($remoteEntity, $remoteIds, $requesterObject = null) {

        $enqueueMethod  = 'import' . $remoteEntity . 'Enqueue';
        $queue          = method_exists($this, $enqueueMethod);
        if (!$queue) {
            return 0;
        }

        $insideAnotherTransaction = false;
        if ($this->transaction) {
            $insideAnotherTransaction = true;
        } else {
            $this->transaction = Yii::$app->db->beginTransaction();
        }
        $count = 0;
        $chunkCount = 0;

        $requesterEntityId  = null;
        $requesterId        = null;

        /** @var BaseModel $requesterObject */
        if ($requesterObject) {
            $requesterEntityId  = Entity::getIdByClassName($requesterObject->className());
            $requesterId        = $requesterObject->id;
        }
        try {
            $chunk = [];
            foreach ($remoteIds as $remoteId) {
                $chunk[] = [
                    'remote_entity'         => $remoteEntity,
                    'remote_id'             => $remoteId,
                    'requester_entity_id'   => $requesterEntityId,
                    'requester_id'          => $requesterId,
                    'created_at'            => date("Y-m-d H:i:s"),
                    'updated_at'            => date("Y-m-d H:i:s"),
                ];
//                $import = ExchangeImport::findOne([
//                    'remote_entity' => $remoteEntity,
//                    'remote_id'     => $remoteId
//                ]);
//                if (!$import) {
                    $import = new ExchangeImport;
//                }
                $import->remote_id              = $remoteId;
                $import->remote_entity          = $remoteEntity;
                $import->requester_entity_id    = $requesterEntityId;
                $import->requester_id           = $requesterId;
//                $import->save(false);

                static::$importingCache[$remoteEntity . '_' . $import->remote_id] = $import;
                $count++;
                if(count($chunk) >= 1000) {
                    ExchangeImport::getDb()->createCommand()->batchInsert(ExchangeImport::tableName(),array_keys($chunk[0]), $chunk)->execute();
                    echo "[".date("H:i:s")."] $remoteEntity enqueued $count".PHP_EOL;
                    $chunk = [];
                    if (!$insideAnotherTransaction) {
                        $this->transaction->commit();
                        $this->transaction = null;
                        $this->transaction = Yii::$app->db->beginTransaction();
                    }
                }
            }
            if(count($chunk) > 0) {
                ExchangeImport::getDb()->createCommand()->batchInsert(ExchangeImport::tableName(), array_keys($chunk[0]), $chunk)->execute();
                echo "[".date("H:i:s")."] $remoteEntity enqueued $count".PHP_EOL;
                if (!$insideAnotherTransaction) {
                    $this->transaction->commit();
                    $this->transaction = null;
                    $this->transaction = Yii::$app->db->beginTransaction();
                }
            }
            if (!$insideAnotherTransaction && $this->transaction) {
                $this->transaction->commit();
                $this->transaction = null;
            }
        } catch (\Exception $e) {
            if (!$insideAnotherTransaction) {
                $this->transaction->rollBack();
                $this->transaction = null;
            }
            echo $e->getMessage().PHP_EOL;
            Error::logError($e, ErrorType::TYPE_IMPORT);
        }
        return $count;
    }

    public static function getRemoteEntityLabel($remoteEntity) {
        if (isset(Yii::$app->params['exchange']['importLabels']) &&
            isset(Yii::$app->params['exchange']['importLabels'][$remoteEntity])) {
            return Yii::$app->params['exchange']['importLabels'][$remoteEntity];
        } else {
            return $remoteEntity;
        }
    }

    /**
     * Импорт одной записи
     * @param string $remoteEntity
     * @param string $remoteId
     * @param array $item
     * @return BaseModel
     */
    public function importOne($remoteEntity, $remoteId, $item) {

        if (isset(static::$importedCache[$remoteEntity.'_'.$remoteId])) {
            if (isset(static::$importingCache[$remoteEntity.'_'.$remoteId]) && static::$importingCache[$remoteEntity.'_'.$remoteId] !== false) {
                static::$importingCache[$remoteEntity.'_'.$remoteId]->delete();
                unset(static::$importingCache[$remoteEntity.'_'.$remoteId]);
            }
            return static::$importedCache[$remoteEntity.'_'.$remoteId];
        }

        static::$importedCache[$remoteEntity.'_'.$remoteId] = false;

        $insideAnotherTransaction = false;
        if ($this->transaction) {
            $insideAnotherTransaction = true;
        } else {
            $this->transaction = Yii::$app->db->beginTransaction();
        }
        try {
            $method = 'import'.$remoteEntity.'One';

            if (empty($item)) {
                throw new yii\base\InvalidValueException(self::getRemoteEntityLabel($remoteEntity)." с ID $remoteId не найден на внешнем сервере.");
            }

            /** @var BaseModel $result */
            $result = $this->$method($item);

            if (!$insideAnotherTransaction) {
                if ($result) {
                    $this->transaction->commit();
                } else {
                    $this->transaction->rollBack();
                }
                $this->transaction = null;
            }

            if ($result) {
                static::$importedCache[$remoteEntity.'_'.$remoteId] = $result;

                if (isset(static::$importingCache[$remoteEntity.'_'.$remoteId]) && static::$importingCache[$remoteEntity.'_'.$remoteId] !== false) {
                    static::$importingCache[$remoteEntity.'_'.$remoteId]->delete();
                    unset(static::$importingCache[$remoteEntity.'_'.$remoteId]);
                }
                return $result;
            } else {
                return null;
            }

        } catch (\Exception $e) {
            if (!$insideAnotherTransaction) {
                $this->transaction->rollBack();
                $this->transaction = null;
            }
            $error = Error::logError($e, ErrorType::TYPE_IMPORT);
            if (isset(static::$importingCache[$remoteEntity.'_'.$remoteId])) {
                if ($error) {
                    static::$importingCache[$remoteEntity.'_'.$remoteId]->error_id       = $error->id;
                }
                static::$importingCache[$remoteEntity.'_'.$remoteId]->error_message      = $e->getMessage();
                static::$importingCache[$remoteEntity.'_'.$remoteId]->is_error           = true;
                static::$importingCache[$remoteEntity.'_'.$remoteId]->save(false);

                unset(static::$importingCache[$remoteEntity.'_'.$remoteId]);

                static::$importingCache[$remoteEntity.'_'.$remoteId] = false;
                static::$importedCache[$remoteEntity.'_'.$remoteId] = false;
            }
            return null;
        }
    }

    /**
     * @return array
     */
    public function getLabels() {
        return [
            'importEnabled' => 'Разрешен Импорт',
            'exportEnabled' => 'Разрешен Экспорт',
        ];
    }

    /**
     * @param $param
     * @return string|null
     */
    public function getLabel($param) {
        $labels = $this->getLabels();
        if (isset($labels[$param])) {
            return $labels[$param];
        }
        return $param;
    }
}