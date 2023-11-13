<?php

if (!function_exists('is_closure')) {
    function is_closure($t)
    {
        return $t instanceof \Closure;
    }
}


if (!function_exists('escape_like')) {
    /**
     * @param $string
     * @return mixed
     */
    function escape_like($string)
    {
        $search = array('%', '_');
        $replace   = array('\%', '\_');
        return str_replace($search, $replace, $string);
    }
}
