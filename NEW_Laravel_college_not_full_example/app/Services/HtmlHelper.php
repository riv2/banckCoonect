<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.05.19
 * Time: 10:55
 */

namespace App\Services;


class HtmlHelper
{
    /**
     * @param $tagName
     * @param $content
     * @return string|string[]|null
     */
    static function stripTag($tagName, $content)
    {
        if(is_string($tagName))
        {
            $tagName = [$tagName];
        }

        $result = $content;

        foreach ($tagName as $tag)
        {
            $result = preg_replace('/<' . $tag . '[^>]*>([\s\S]*?)<\/' . $tag . '[^>]*>/', '', $result);
            $result = strip_tags($result, $tag);
        }

        return $result;
    }
}