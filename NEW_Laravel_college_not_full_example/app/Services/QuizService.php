<?php


namespace App\Services;


class QuizService
{
    /**
     * @param $text
     * @return mixed
     */
        static function textSafety($text)
        {
            $afterTrim = trim(strip_tags($text, '<img><table><tbody><thead><tfoot><tr><th><td>'));
            return str_replace(['`','\\'], '\'', $afterTrim);
        }
}