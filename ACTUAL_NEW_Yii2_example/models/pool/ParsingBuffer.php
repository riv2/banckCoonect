<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use yii;
use yii\helpers\Json;

/**
 * Class ParsingBuffer
 * @package app\models\pool
 *
 * @property string     buffer
 * @property array      data
 * @property bool       is_error
 * @property string     error_message
 *
 */

class ParsingBuffer extends Pool
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
        return 'Буфер спарсенных цен';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Буфер спарсенных цен';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['error_message','buffer'], 'string'],
                [['is_error'], 'boolean'],
            ]
        );
    }

    public function crudIndexColumns()
    {
        return array_merge(parent::crudIndexColumns(),[
            'created_at',
            'buffer',
            'is_error',
            'error_message'
        ]);
    }

    public function getData() {
        return Json::decode($this->buffer, true);
    }

    public function setData($data) {
        $this->buffer = Json::encode($data);
    }
    
}