<?php
namespace app\models\register;

use app\components\base\Entity;
use app\components\base\type\Register;
use app\models\enum\ErrorType;
use app\models\enum\Status;
use yii;

/**
 * Class Error
 * @package app\models\register
 *
 * @property string name
 * @property string remote_id
 * @property string remote_entity
 * @property boolean is_error
 * @property string message
 * @property string error_id
 *
 *
 */
class Error extends Register
{

    public $labelAttribute = 'message';

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Ошибка';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Ошибки';
    }

    /**
     * @inheritdoc
     */
    public static function noCount() {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [[['name','message','hash','code','kind','entity_row_id','entity_type_id','error_type_id','file','line','info','created_at','updated_at','created_user_id','updated_user_id','backtrace'], 'safe']];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'name'              => 'Класс ошибки',
                'message'           => 'Сообщение',
                'hash'              => 'Хэш',
                'code'              => 'Код',
                'kind'              => 'Вид',
                'entity_row_id'     => 'ID сущности',
                'entity_type_id'    => 'Тип сущности',
                'error_type_id'     => 'Тип ошибки',
                'file'              => 'Файл',
                'line'              => 'Строка',
                'backtrace'         => 'Бектрейс',
                'info'              => 'Инфо',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'entityType',
            'errorType',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            
            'created_at',
            'message',
            'entityType',
            'entity_row_id',
            'errorType',
        ]);
    }

    public function __toString()
    {
        return $this->message;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getErrorType() {
        return $this->hasOne(ErrorType::className(), ['id' => 'error_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntityType() {
        return $this->hasOne(Entity::className(), ['id' => 'entity_type_id']);
    }

    public static function extractMessage($message) {
        $matches = [];
        preg_match('/^(.*?)\[(.*?)\]$/', $message, $matches);
        if (count($matches) == 3 && isset($matches[2])) {
            return $matches[1];
        }
        return $message;
    }
    public static function extractInfo($message) {
        $matches = [];
        preg_match('/^(.*?)\[(.*?)\]$/', $message, $matches);
        if (count($matches) == 3 && isset($matches[2])) {
            $data['message'] =  $matches[1];
            return $matches[2];
        }
        return null;
    }
    /**
     * @param $exception
     * @param int $errorTypeId
     * @param int $entityTypeId
     * @param string $entityId
     * @return Error|null
     */
    public static function logError($e, $errorTypeId = 0, $entityTypeId = null, $entityId = null) {

        $data = [];

        if (is_array($e)) {
            $data = $e;
        } else if ($e instanceof \Exception) {
            $data = [
                'message'           => $e->getMessage(),
                'name'              =>$e->getMessage(),
                'kind'              => 'Exception',
                'error_type_id'     => $errorTypeId,
                'entity_type_id'    => $entityTypeId,
                'entity_row_id'     => $entityId,
                'file'              => $e->getFile(),
                'line'              => $e->getLine(),
                'code'              => $e->getCode(),
                'backtrace'         => $e->getTraceAsString(),
            ];
        } else {
            $exception = new \Exception($e);
            $data = [
                'message'           => $exception->getMessage(),
                'name'              => $exception->getMessage(),
                'kind'              => 'ErrorMessage',
                'error_type_id'     => $errorTypeId,
                'entity_type_id'    => $entityTypeId,
                'entity_row_id'     => $entityId,
                'file'              => $exception->getFile(),
                'line'              => $exception->getLine(),
                'code'              => $exception->getCode(),
                //'backtrace'         => $exception->getTraceAsString(),
            ];
        }

        $data = array_merge([
            'name'              => 'Error',
            'message'           => 'Unknown',
            'file'              => null,
            'line'              => 0,
            'error_type_id'     => 0,
            'entity_type_id'    => 0,
            'entity_row_id'     => '',
            'code'              => 0,
            'kind'              => 'Error',
            'info'              => null,
            'backtrace'         => null,
        ], $data, [
            'error_type_id'     => $errorTypeId,
            'entity_type_id'    => $entityTypeId,
            'entity_row_id'     => $entityId,
        ]);
        
        $data['message'] =  static::extractMessage($data['message']);
        $data['info']    =  static::extractInfo($data['message']);

        $data['code'] = (string)$data['code'];

        if (!$data['file']) {
            $tail = $data['message'];
        } else {
            $tail = $data['file'].'-'.$data['line'];
        }
        $data['hash'] = md5($data['name'].'-'.$data['kind'].'-'.$tail);

        if (!isset($data['info']) || !$data['info']) {
            $data['info'] = [];
            if (isset($_SERVER)) {
                $data['info']['_SERVER'] = $_SERVER;
            }
            if (isset($_POST)) {
                $data['info']['_POST'] = $_POST;
            }
            if (isset($_GET)) {
                $data['info']['_GET'] = $_GET;
            }
            if (isset($_COOKIES)) {
                $data['info']['_COOKIES'] = $_COOKIES;
            }
            $data['info'] = yii\helpers\Json::encode($data['info']);
        }

        if (Yii::$app->db->schema->getTableSchema('{{%error}}')) {
            $error = new Error();
            $error->setAttributes($data);
            if ($errorTypeId == ErrorType::TYPE_PARSED_PRICE_REFINING) {
                $error->status_id = Status::STATUS_DISABLED;
            }
            $error->save();
            return $error;
        }
        return null;
    }
}