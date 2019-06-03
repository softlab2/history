<?php
namespace Softlab\History\Traits;

use View;

trait Historiable
{
    use \Panoscape\History\HasHistories;
        
  //   public function histories()
  //   {
  //       return $this->morphMany(\Softlab\Metatag\Models\Metatag::class, 'metatagable');
  //   }

  //   public function meta()
  //   {
  //       return $this->morphMany(\Softlab\Metatags\Models\MetatagValue::class, 'metable');
  //   }

    public function history($title, $meta){
        event(new \Panoscape\History\Events\ModelChanged ($this, $title, $meta));
    }

    public function diff($old, $new){
      return ['add'=>array_diff($new, $old), 'remove'=>array_diff($old, $new)];
    }
}