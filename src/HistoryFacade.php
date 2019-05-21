<?php

namespace Softlab\History;

use \Illuminate\Support\Facades\Facade;

class HistoryFacade extends Facade {
    protected static function getFacadeAccessor(){
        return 'history';
    }
}
