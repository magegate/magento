<?php

/**
 * Class Magegate_Observer_Model_Observer
 *
 * Listen to Magento Events model_save_commit_after, model_delete_commit_after
 * and send events to Magegate Runtime directly by call or via POST-Request.
 *
 * We need this to synchronize Magegate with Magento and to inform Magegate
 * clients about changes.
 */
class Magegate_Observer_Model_Observer
{
    /**
     * @var Magegate\MagentoEventsController
     *
     * Use the MagentoEventController if inside Laravel or
     * an array to collect events if inside Magento.
     */
    protected $events;

    /**
     * Create the $this->events member according to Laravel or Magento.
     */
    function __construct()
    {
        if(!defined('LARAVEL_START'))
        {
            /**
             * If not used inside Laravel, we collect events until __destruct
             */
            $this->events = array();
        }
        else
        {
            /**
             * If used inside Laravel, we call the MagentoEventController directly
             */
            $this->events = new \Magegate\MagentoEventsController();
        }
    }

    /**
     * Send events to Laravel if it's used inside a Magento-Request
     */
    function __destruct()
    {
        if(!defined('LARAVEL_START') && !empty($this->events))
        {
            /**
             * If not used inside Laravel send all collected events to the MagentoEventController
             * within one fast curl POST request
             */
            $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
            $url.= dirname($_SERVER['SCRIPT_NAME']);

            /**
             * Doing the curl stuff: Simple RESTfull POST reguest the MagentoEventController
             */
            $url = curl_init("$url/api/magegate/events");
            curl_setopt_array($url,array(
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CONNECTTIMEOUT_MS => 10,
                CURLOPT_TIMEOUT_MS => 100,
                CURLOPT_POSTFIELDS => array('events'=>json_encode(array_values($this->events))),
            ));
            curl_exec($url);
            curl_close($url);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * Listen to Magento events to collect and send all events the end of Magento-Request
     * via HTTP-POST or call directly to Magegate's MagentoEventController via php.
     */
    public function listen(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();

        /**
         * We want only events from Mage_Core_Model_Abstract objects.
         * Register events: model_save_commit_after, model_delete_commit_after
         */
        if(is_a($object = $event->getData('object'),'Mage_Core_Model_Abstract'))
        {
            /**
             * The array_dot function flattens a multi-dimensional array into
             * a single level array that uses "dot" notation to indicate depth.
             *
             * The function is defined inside Laravel, so use if function_exists!
             */
            $array_dot = function_exists('array_dot')?'array_dot'
                :function ($array, $prepend = '') use (&$array_dot)
                {
                    $results = array();

                    foreach ($array as $key => $value)
                    {
                        if (is_array($value))
                        {
                            $results = array_merge($results, $array_dot($value, $prepend.$key.'.'));
                        }
                        else
                        {
                            $results[$prepend.$key] = $value;
                        }
                    }

                    return $results;
                };

            /**
             * Collect the difference between OrigData and Data into $objectDiff
             * Use only values, which are present in OrigData and no Arrays!
             */
            $objectDiff = array();
            $objectData = $array_dot($object->getData()?:array());
            $objectOrig = $array_dot($object->getOrigData()?:array());
            foreach(array_keys($objectOrig) as $k)
            {
                if(is_array($objectOrig[$k])) continue;
                if(!array_key_exists($k,$objectData)) continue;
                if(is_array($objectData[$k])) continue;
                if($objectData[$k] == $objectOrig[$k]) continue;
                $objectDiff[$k] = $objectData[$k];
            }

            /**
             * Remove 'updated_at' member, because it always changes.
             * If nothing changed left, return. Nothing to do.
             */
            if(array_key_exists($k='updated_at',$objectDiff)) unset($objectDiff[$k]);
            if(empty($objectDiff)) return;

            /**
             * Build the Magegate event structure ...
             */
            $event = array(
                'eventName' => $event->getName(),
                'resourceName' => $object->getResourceName(),
                'objectId' => $object->getId(),
                'objectDiff' => $objectDiff,
            );

            if(!defined('LARAVEL_START'))
            {
                /**
                 * ... store the structure until __destruct if not Laravel.
                 */
                $this->events[$event['eventName'].$event['resourceName'].$event['objectId']]
                    = $event;
            }
            else
            {
                /**
                 * ... send it immediately to Laravel if not inside Magento.
                 */
                $this->events->create(array($event));
            }
        }
    }
}