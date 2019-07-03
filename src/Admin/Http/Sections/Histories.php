<?php

namespace Softlab\History\Admin\Http\Sections;

use AdminColumn;
use AdminColumnEditable;
use AdminDisplay;
use AdminForm;
use AdminFormElement;
use SleepingOwl\Admin\Contracts\DisplayInterface;
use SleepingOwl\Admin\Contracts\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Section;
use SleepingOwl\Admin\Form\FormElements;
use History;
use URL;
use \Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class Entities
 *
 * @property \Softlab\Catalog\Models\Entity $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class Histories extends Section implements Initializable
{
    /**
     * @see http://sleepingowladmin.ru/docs/model_configuration#ограничение-прав-доступа
     *
     * @var bool
     */
    protected $checkAccess = true;

    /**
     * @var string
     */
    protected $title = "История изменений";

    /**
     * @var string
     */
    protected $alias;

    public function initialize()
    {
    }

    /**
     * @return DisplayInterface
     */
    public function onDisplay($payload = [])
    {
        $table = AdminDisplay::table()
            //->setName('history')
            ->setModelClass(\Softlab\History\Models\History::class)
           ->setHtmlAttribute('class', 'table-primary')
           ->setColumns([
                    AdminColumn::link('performed_at', 'Дата')->setWidth('200px'),
                    AdminColumn::link('message', 'Событие'),
                    AdminColumn::custom('Изменения:', function($instance){
                        $relations = [
                            'union_id' => ['field'=>'Объединение', 'model'=>\Softlab\Catalog\Models\Union::class],
                            'user_id' => ['field'=>'Владелец', 'model'=>\App\User::class],
                            'types' => ['field'=>'Типы', 'model'=>\Softlab\Catalog\Models\Type::class],
                            'images' => ['field'=>'Изображения', 'model'=>\Softlab\Catalog\Models\Type::class],
                        ];
                        $attributes = [
                            'name' => 'Название',
                            'slug' => 'Ссылка',
                            'visible' => 'Включено',
                        ];
                        $diffString = '';
                        $meta = $instance->meta;                    
                        if(!empty($meta['attribute_id'])){
                            //eav
                            $attribute = \Softlab\Catalog\Models\Attribute::find($meta['attribute_id']);
                            if($attribute->is_collection || isset($meta['is_filter'])){
                                if(isset($meta['diff'])){
                                    $diffIds = $meta['diff'];
                                    if(count($diffIds['add'])){
                                        $diffString .= 'Добавлено: '.implode(', ', \Softlab\Catalog\Models\Category::find($diffIds['add'])->pluck('name')->toArray());
                                    }
                                    if(count($diffIds['remove'])){
                                        $diffString .= 'Удалено: '.implode(', ', \Softlab\Catalog\Models\Category::find($diffIds['remove'])->pluck('name')->toArray());
                                    }
                                }
                            // }elseif(isset($meta['is_filter'])){
                            //     $old = "";
                            //     $new = "";
                            //     if(isset($meta['old'])){
                            //         $old = implode(', ', \Softlab\Catalog\Models\Category::find($meta['old'])->pluck('name')->toArray());
                            //     }
                            //     if(isset($meta['new'])){
                            //         $new = implode(', ', \Softlab\Catalog\Models\Category::find($meta['new'])->pluck('new')->toArray());
                            //     }
                            //     $diffString = 'Было: "'.$old.'", Стало: "'.$new.'"';
                            }else{
                                $diffString = 'Было: "'.(isset($meta['old'])?$meta['old']:'').'", Стало: "'.(!empty($meta['new'])?$meta['new']:'').'"';
                            }
                        }elseif(!empty($meta['filter_id'])){
                            if(isset($meta['diff'])){
                                $diffIds = $meta['diff'];
                                if(count($diffIds['add'])){
                                    $diffString .= 'Добавлено: '.implode(', ', \Softlab\Catalog\Models\Category::find($diffIds['add'])->pluck('name')->toArray());
                                }
                                if(count($diffIds['remove'])){
                                    $diffString .= 'Удалено: '.implode(', ', \Softlab\Catalog\Models\Category::find($diffIds['remove'])->pluck('name')->toArray());
                                }
                            }
                        }else{
                            //attribute or relation\
                            $diffs = [];
                            if(isset($meta['key'])){
                                if(isset($meta['attached'])){
                                    $operation = 'Добавлено';
                                    $ids = $meta['attached'];
                                }
                                if(isset($meta['detached'])){
                                    $operation = 'Удалено';
                                    $ids = $meta['detached'];
                                }
                                if(isset($relations[$meta['key']])){
                                    $relation = $relations[$meta['key']];
                                    $field = $relation['field'];
                                    $diffString = 'Поле: '.$field.'. '.$operation.': '.implode(', ', (new $relation['model'])->find($ids)->pluck('name')->toArray());
                                }
                            }else{
                                $meta = array($meta);
                                foreach ($meta as $row) {
                                    if(isset($row['key'])){
                                        if(isset($relations[$row['key']])){
                                            $relation = $relations[$row['key']];
                                            $field = $relation['field'];
                                            $model = (new $relation['model']);
                                            
                                            if(!empty($row['old'])){
                                                if(is_array($row['old']))
                                                    $old = implode(', ', $row['old']);
                                                else{
                                                    if(is_integer($row['old']))
                                                        $old = $model->find($row['old'])->toString();
                                                    else
                                                        $old = $row['old'];
                                                }
                                            }else{
                                                $old = '';
                                            }

                                            if(!empty($row['new'])){
                                                if(is_array($row['new']))
                                                    $new = implode(', ', $row['new']);
                                                else{
                                                    if(is_integer((int)$row['new']))
                                                        $new = $model->find($row['new'])->toString();
                                                    else
                                                        $new = $row['new'];
                                                }
                                            }else{
                                                $new = '';
                                            }
                                        }else{
                                            $field = (isset($attributes[$row['key']])?$attributes[$row['key']]:$row['key']);
                                            $old = (isset($row['old'])?$row['old']:'');
                                            $new = (!empty($row['new'])?$row['new']:'');
                                        }
                                        $diffs[] = 'Поле: '.$field.'. Было: "'.$old.'", Стало: "'.$new.'"';
                                    }else{
                                        $diffs[] = '';
                                    }
                                }
                                $diffString = implode("</br>", $diffs);
                            }
                        }
                        // dd($instance);  
                        // dd($instance->model()->attribute($instance->meta['attribute_id']), $instance);
                        // //\Softlab\Catalog\Models\Attribute::whereSlug($instance->meta['key'])
                        // dd($instance);
                        // $instance->diff()
                        // return $instance->diff();
                        return $diffString;
                    }),
                    AdminColumn::link('author.name', 'Автор')->setWidth('100px'),
           ])->paginate(20);    

            $table->setApply(function($query) use($payload) {
                $query->where('model_id', $payload['model_id'])->orderBy('id', 'desc');
            });

            // $table->getActions()->setView(view('metatags::toolbar', ['metatagable_id'=>$payload['owner_id'], 'metatagable_type'=>$class]))->setPlacement('panel.heading.actions');


            //$table->setParameter('model_id', $payload['model_id'])->setParameter('owner_class', $payload['owner_class']);
        //}

        return $table;

    }

    /**
     * @param int $id
     *
     * @return FormInterface
     */
    public function onEdit($id)
    {
    }

    /**
     * @return FormInterface
     */
    public function onCreate()
    {
    }

    /**
     * @return void
     */
    public function onDelete($id)
    {
        // remove if unused
    }

    /**
     * @return void
     */
    public function onRestore($id)
    {
        // remove if unused
    }
}
