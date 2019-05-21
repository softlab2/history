<?php

if (!function_exists('history')) {
    function history( $params = [] )
    {
        return History::getInstance();
    }
}

