<?php


namespace app\components;


use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class BigJsonSerialize
{
    private $startChar = '[';
    private $endChar = ']';

    private $started = false;
    private $isAssoc = false;

    public $toFile = false;


    private function start($array) {
        if ($this->started) {
            return;
        }
        $this->started = true;
        $this->isAssoc = ArrayHelper::isAssociative($array);
        if ($this->isAssoc) {
            $this->startChar = '{';
            $this->endChar = '}';
        }
        $this->output($this->startChar);
    }

    public function serializeChunk($chunk) {
        foreach ($chunk as $id => $object) {
            if ($this->started) {
                $this->output(',');
            } else {
                $this->start($chunk);
            }
            if ($this->isAssoc) {
                $this->output('"'.$id.'":');
            }
            if ($object) {
                $this->output(Json::encode($object));
            } else {
                $this->output('null');
            }
        }
    }

    public function end() {
        if ($this->started) {
            $this->output($this->endChar);
            $this->started = false;
        }
    }

    private function output($string) {
        if ($this->toFile) {

        } else {
            echo $string;
        }
    }

}