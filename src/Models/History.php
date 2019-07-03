<?php

namespace Softlab\History\Models;

class History extends \Panoscape\History\History
{
    public function author()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}