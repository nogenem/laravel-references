<?php

namespace App\Utils;

abstract class QueryStringUtils
{
    public static function sortQueryStringToOrderByObj($sortQueryString)
    {
        if(empty($sortQueryString)) {
            return [];
        }

        $dir = "asc";
        $field = $sortQueryString;
        if (str_starts_with($sortQueryString, "-")) {
            $dir = "desc";
            $field = \substr($field, 1);
        } elseif (str_starts_with($sortQueryString, "+")) {
            $field = \substr($field, 1);
        }

        return [
            'field' => $field,
            'dir' => $dir,
        ];
    }
}
