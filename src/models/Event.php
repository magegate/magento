<?php
namespace Magegate;


class Event extends \Eloquent {

    public $table = 'event';
    public $fillable = array('event','model','id');
    public $incrementing = false;

    static protected $translateEvent = array(
        'model_save_after' => 'save',
        'model_save_commit_after' => 'save',
        'model_delete_after' => 'delete',
        'model_delete_commit_after' => 'delete',
    );

    static public function getEventIdent($name)
    {
        return array_key_exists($name,self::$translateEvent)
            ? self::$translateEvent[$name]
            : self::$translateEvent[$name] = $name;
    }

    static public function getCacheIdent($model, $id)
    {
        return "event://$model/$id";
    }

    static public function newCacheEvent($model, $id, $event = 'init')
    {
        return array(
            'eventName' => self::getEventIdent($event),
            'resourceName' => $model,
            'objectId' => $id,
            'objectDiff' => array(),
        );
    }

    static public function getCacheEvent($model,$id)
    {
        return \Cache::driver('memcached')->get(
            self::getCacheIdent($model,$id),
            self::newCacheEvent($model,$id));
    }

    static public function putCacheEvent($model, $id, $event, $data, $minutes = 30)
    {
        $cache = self::getCacheEvent($model,$id);
        $cache['eventName'] = $event = self::getEventIdent($event);
        $cache['objectDiff'] = array_merge($cache['objectDiff'],$data);
        \Cache::driver('memcached')->put($ident=self::getCacheIdent($model,$id),$cache,$minutes);

        if($store=\Magegate\Event
            ::where('event','=',$event)
            ->where('model','=',$model)
            ->where('id','=',$id)
            ->first())
        {
            $store->touch();
        }
        else {
            $store = \Magegate\Event::create(array(
                'event' => $event,
                'model' => $model,
                'id' => $id,
            ));
        }

        return $cache;
    }

}