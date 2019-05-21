<?php

namespace Softlab\History;

use AdminSection;

class History {
    private static $_instance = null;

    private function __construct(){
    }

    protected function __clone() {}

    public static function getInstance() {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function fireDisplay( $params = [] ){
        $model = $params['model'];
        return AdminSection::getModel(\Panoscape\History\History::class)->fireDisplay(['model_id'=>$model->id]);
    }

}
