<?php
namespace Magegate;

class ImportCatalogCategory extends Import {

    protected $host;
    protected $list;

    static public function create(array $attributes)
    {
        if(!array_key_exists($k='host',$attributes))
            throw(new \Exception("array key '$k' missing",404));
        $host = $attributes[$k];

        if(!array_key_exists($k='list',$attributes))
            throw(new \Exception("array key'$k' missing",404));
        $list = $attributes[$k];

        $import = parent::create(array(
            'host' => $host,
            'name' => __CLASS__,
            'status' => 'init',
            'number' => count($list),
        ));

        $import->host = $host;
        $import->list = $list;

        return $import;
    }

    public function queueWork()
    {
        if(!$this->queueNext()) return false;

        $this->status = 'work';
        $this->queued+=1;
        $this->save();

        if(\Magegate\EntityCatalogCategory::create(array(
                'host' => $this->host,
                'data' => $this->list[$this->queued-1],
            ))->count()>0)
        {
            $this->status = $this->queueNext()?'wait':'done';
            $this->finish+=1;
            $this->save();
            return true;
        }
        else
        {
            $this->status = $this->queueNext()?'wait':'done';
            $this->failed+=1;
            $this->save();
            return false;
        }
    }
}