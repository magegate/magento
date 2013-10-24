<?php

class EventControllerTest extends MagegateMagentoTestCase {

    public function testGetEventsMagegateMagento4711()
    {
        $response = $this->call('GET',$url='/api/magegate/events/magegate/magento/4711');

        $this->assertTrue(is_a($response,'\Illuminate\Http\Response'),
            "Invalid response from $url");
        $this->assertEquals(200,$code=$response->getStatusCode(),
            "Response status code $code != 200 from $url");
        $this->assertTrue(is_string($content=$response->getContent()),
            "Response content invalid: $content");

        $event = json_decode($content,true);

        $this->assertTrue(array_key_exists($k='resourceName',$event),
            "Invalid event missing attribute '$k'");
        $this->assertEquals($e='magegate/magento',$v=$event[$k],
            "Invalid event $k '$v' expected '$e'");
        $this->assertTrue(array_key_exists($k='objectId',$event),
            "Invalid event missing attribute '$k'");
        $this->assertEquals($e=4711,$v=$event[$k],
            "Invalid event $k '$v' expected '$e'");
        $this->assertTrue(array_key_exists($k='objectDiff',$event),
            "Invalid event missing attribute '$k'");
        $this->assertTrue(is_array($v=$event[$k]),
            "Invalid event attribute type '$k' array expected");

        return $event;
    }

    /**
     * @depends testGetEventsMagegateMagento4711
     */
    public function testPostEvents_Hello()
    {
        $event = $this->testGetEventsMagegateMagento4711();
        $event['eventName'] = \App::environment();
        $event['objectDiff'] = array(
            "HelloWorld" => "Hello, World!",
        );

        $response = $this->call('POST',$url='/api/magegate/events',array(
            'events' => json_encode(array($event)),
        ));

        $this->assertTrue(is_a($response,'\Illuminate\Http\Response'),
            "Invalid response from $url");
        $this->assertEquals(200,$code=$response->getStatusCode(),
            "Response status code $code != 200 from $url");

    }

    /**
     * @depends testPostEvents_Hello
     */
    public function testGetEventsMagegateMagento4711_afterPostHello()
    {
        $event = $this->testGetEventsMagegateMagento4711();

        $this->assertTrue(array_key_exists($k='eventName',$event),
            "Invalid event missing attribute '$k'");
        $this->assertEquals($e=\App::environment(),$v=$event[$k],
            "Invalid event $k '$v' expected '$e'");

        $diff = $event['objectDiff'];

        $this->assertTrue(array_key_exists($k='HelloWorld',$diff),
            "Invalid object missing attribute '$k'");
        $this->assertEquals($e='Hello, World!',$v=$diff[$k],
            "Invalid object $k '$v' expected '$e'");
    }

    /**
     * @depends testGetEventsMagegateMagento4711_afterPostHello
     */
    public function testPostEvents_Hallo()
    {
        $event = $this->testGetEventsMagegateMagento4711();
        $event['eventName'] = \App::environment();
        $event['objectDiff'] = array(
            "HelloWorld" => "Hallo, Welt?",
        );

        $response = $this->call('POST',$url='/api/magegate/events',array(
            'events' => json_encode(array($event)),
        ));

        $this->assertTrue(is_a($response,'\Illuminate\Http\Response'),
            "Invalid response from $url");
        $this->assertEquals(200,$code=$response->getStatusCode(),
            "Response status code $code != 200 from $url");

    }

    /**
     * @depends testPostEvents_Hallo
     */
    public function testGetEventsMagegateMagento4711_afterPostHallo()
    {
        $event = $this->testGetEventsMagegateMagento4711();

        $this->assertTrue(array_key_exists($k='eventName',$event),
            "Invalid event missing attribute '$k'");
        $this->assertEquals($e=\App::environment(),$v=$event[$k],
            "Invalid event $k '$v' expected '$e'");

        $diff = $event['objectDiff'];

        $this->assertTrue(array_key_exists($k='HelloWorld',$diff),
            "Invalid object missing attribute '$k'");
        $this->assertEquals($e='Hallo, Welt?',$v=$diff[$k],
            "Invalid object $k '$v' expected '$e'");
    }

    /**
     * @depends testPostEvents_Hello
     */
    public function testGetEvents()
    {
        $response = $this->call('GET',$url='/api/magegate/events');

        $this->assertTrue(is_a($response,'\Illuminate\Http\Response'),
            "Invalid response from $url");
        $this->assertEquals(200,$code=$response->getStatusCode(),
            "Response status code $code != 200 from $url");
        $this->assertTrue(is_string($content=$response->getContent()),
            "Response content invalid: $content");

        $events = array();

        foreach(json_decode($content,true) as $event)
        {
            $events[$event['model'].'/'.$event['id']] = $event;
        }

        $this->assertArrayHasKey($k='magegate/magento/4711',$events,
            "Event '$k' not found in event list");
        $this->assertEquals($e=\App::environment(),$v=$events[$k]['event'],
            "Event '$k' wrong event name '$v' expecting '$e'");
    }

}