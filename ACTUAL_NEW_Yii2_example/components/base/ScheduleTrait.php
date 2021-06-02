<?php
namespace app\components\base;
use yii\db\ActiveQuery;

/**
 * Class ScheduleTrait
 * @package app\components\base
 *
 * @property string scheduleColor
 * @property string primaryKey
 * @method __toString
 * @method ActiveQuery getSchedule
 */
trait ScheduleTrait
{
    protected $_scheduleColor = null;

    public function getScheduleColor($params = []) {
        if ($this->_scheduleColor) {
            return $this->_scheduleColor;
        }
        $this->_scheduleColor = $this->adjustBrightness(substr($this->id, 15, 2).substr($this->id, 11, 2).substr($this->id, 0, 2), -20);
//        $chars = "0123456789ABCDEF";
//        $size = strlen( $chars );
//        $str = "#";
//        for( $j = 0; $j < 6; $j++ ) {
//            $str .=$j % 2 ? $chars[ rand( 0, $size - 1 ) ] : $chars[ rand( 10, $size - 1 ) ];
//        }
//        $this->_scheduleColor = $str;
        return $this->_scheduleColor;
    }
    
    public function getScheduleDuration($params = []) {
        return '00:05:00';
    }
    
    public function getScheduleTitle($params = []) {
        return 'Event';
    }

    public function getScheduleDescription($params = []) {
        return null;
    }

    public function schedule($params = []) {
        
    }

    private function adjustBrightness($hex, $steps) {
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        // Normalize into a six character long hex string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
        }

        // Split into three parts: R, G and B
        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0,min(255,$color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }
}