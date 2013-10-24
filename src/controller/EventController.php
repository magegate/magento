<?php
namespace Magegate;

use Magegate\Event;

class MagentoEventsController extends BaseJsonController {

    public function show($model,$id)
    {
        return Event::getCacheEvent($model,$id);
    }


    public function index()
    {
        return Event::all()->toArray();
    }

    public function create($events=null)
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $events = $events?:\Input::get('events',array());
        $events = json_decode($events,true);

        foreach($events as $event)
        {
            Event::putCacheEvent(
                $event['resourceName'],$event['objectId'],$event['eventName'],$event['objectDiff']);
        }
        return true;
    }

}