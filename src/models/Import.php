<?php
namespace Magegate;

class Import extends \Eloquent {

    public $table = 'import';
    public $fillable = array('host','name','status','number');
    public $softDelete = true;

    public function queueNext()
    {
        return $this->queued < $this->number;
    }

}