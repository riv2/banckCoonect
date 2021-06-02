<?php
namespace app\components\base;

/**
 * Class ScheduleInterface
 * @package app\components\base
 *
 */
interface ScheduleInterface
{
    public function getScheduleColor($params = []);

    public function getScheduleDuration($params = []);

    public function getScheduleTitle($params = []);

    public function getScheduleDescription($params = []);

    public function schedule($params = []);
}