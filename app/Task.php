<?php

namespace App;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Task extends Eloquent
{
  protected $fillable = ['title','description','due_date','completed'];

  public static function boot()
    {
        parent::boot();
     
        self::creating(function($model){
            $model->completed = false;
        });
    }

}
