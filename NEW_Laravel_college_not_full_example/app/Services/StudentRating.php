<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 20.12.18
 * Time: 14:45
 */

namespace App\Services;


class StudentRating
{
    private static function getGraduate()
    {
        return [
            [
                'percent' => [
                    'from' => 0,
                    'to'   => 24
                ],
                'points' => 0,
                'letter' => 'F'
            ],
            [
                'percent' => [
                    'from' => 25,
                    'to'   => 49
                ],
                'points' => 0.5,
                'letter' => 'FX'
            ],
            [
                'percent' => [
                    'from' => 50,
                    'to'   => 54
                ],
                'points' => 1,
                'letter' => 'D-'
            ],
            [
                'percent' => [
                    'from' => 55,
                    'to'   => 59
                ],
                'points' => 1.33,
                'letter' => 'D+'
            ],
            [
                'percent' => [
                    'from' => 60,
                    'to'   => 64
                ],
                'points' => 1.67,
                'letter' => 'C-'
            ],
            [
                'percent' => [
                    'from' => 65,
                    'to'   => 69
                ],
                'points' => 2,
                'letter' => 'C'
            ],
            [
                'percent' => [
                    'from' => 70,
                    'to'   => 74
                ],
                'points' => 2.33,
                'letter' => 'C+'
            ],
            [
                'percent' => [
                    'from' => 75,
                    'to'   => 79
                ],
                'points' => 2.67,
                'letter' => 'B-'
            ],
            [
                'percent' => [
                    'from' => 80,
                    'to'   => 84
                ],
                'points' => 3,
                'letter' => 'B'
            ],
            [
                'percent' => [
                    'from' => 85,
                    'to'   => 89
                ],
                'points' => 3.33,
                'letter' => 'B+'
            ],
            [
                'percent' => [
                    'from' => 90,
                    'to'   => 94
                ],
                'points' => 3.67,
                'letter' => 'A-'
            ],
            [
                'percent' => [
                    'from' => 95,
                    'to'   => 100
                ],
                'points' => 4,
                'letter' => 'A'
            ]
        ];
    }

    /**
     * @param int $valuePercent
     * @return float|null
     */
    static function getFinalResultPoints(int $valuePercent) : ?float
    {
        if ($valuePercent < 0 || $valuePercent > 100) {
            return null;
        }

        $graduate = self::getGraduate();

        foreach ($graduate as $item) {
            if ($valuePercent >= $item['percent']['from'] && $valuePercent <= $item['percent']['to']) {
                return (float)$item['points'];
            }
        }

        return null;
    }

    /**
     * @param $valuePercent
     * @return bool|string|null
     */
    static function getClassicString($valuePercent)
    {
        if($valuePercent < 0 || $valuePercent > 100)
        {
            return null;
        }

        $points = self::getFinalResultPoints($valuePercent);
        $points = (int) round($points);
        
        if($points == 4) return 'Өте жақсы / Отлично';
        if($points == 3) return 'Жақсы / Хорошо';
        if($points == 2) return 'Орташа / Удовлетворительно';
        if($points == 1) return 'Жеткіліксіз / Неудовлетворительно';

        return false;
    }

    /**
     * @param $valuePercent
     * @return bool|string|null
     */
    static function getClassicString3Lang($valuePercent)
    {
        if($valuePercent < 0 || $valuePercent > 100)
        {
            return null;
        }

        $points = self::getFinalResultPoints($valuePercent);
        //$points = (int) round($points);
        
        if($points >= 3.67) return 'Өте жақсы/ Excellent/ Отлично';
        if($points >= 2.33) return 'Жақсы/ Good/ Хорошо';
        if($points >= 1) return 'Қанағаттанарлық/ Fair/ Удовлетворительно';
        if($points >= 0) return 'Жеткіліксіз/ Fail/ Неудовлетворительно';

        return false;
    }

    /**
     * @param $valuePercent
     * @return string|null
     */
    static function getLetter(int $valuePercent) : ?string
    {
        if ($valuePercent < 0 || $valuePercent > 100) {
            return null;
        }

        $graduate = self::getGraduate();

        foreach ($graduate as $item) {
            if ($valuePercent >= $item['percent']['from'] && $valuePercent <= $item['percent']['to']) {
                return $item['letter'];
            }
        }

        return null;
    }

    /**
     * @param $valuePercent
     * @param $credits
     * @return float|null
     */
    static function getDisciplineGpa(int $valuePercent, int $credits) : ?float
    {
        if ($valuePercent < 0 || $valuePercent > 100) {
            return null;
        }

        $points = self::getFinalResultPoints($valuePercent);

        return round($points * $credits, 2);
    }
}