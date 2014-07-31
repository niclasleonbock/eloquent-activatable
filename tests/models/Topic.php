<?php
use Illuminate\Database\Eloquent\Model;

use niclasleonbock\Eloquent\ActivatableTrait;

class Topic extends Model
{
    use ActivatableTrait;

    protected $table = 'topics';

    public $timestamps = false;

    protected $fillable = [ 'title' ];
}
