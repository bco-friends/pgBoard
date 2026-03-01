<?php

function number($str)
{
    return str_replace(["$", ","], "", $str);
}
