<?php
namespace Magegate;


class BaseJsonController extends \BaseController {

    protected function jsonResponse($data)
    {
        return \Response::make(json_encode($data,JSON_PRETTY_PRINT),200)
            ->header('Content-Type','application/json');
    }

    protected function jsonResponseFlush($data)
    {
        ignore_user_abort(true);
        set_time_limit(0);
        $response = $this->jsonResponse($data);
        $response->header('Connection','close');
        $response->send();
        ob_end_flush();
        flush();
    }

    protected function callMethod($method, $parameters)
    {
        try {
            return $this->jsonResponse(call_user_func_array(array($this, $method), $parameters));
        }
        catch(\Exception $e) {
            return \App::abort($e->getCode()?:500, $e->getMessage());
        }
    }

}