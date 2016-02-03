<?php

namespace Pozitim\Soap\Async;

class Decoder extends \SoapClient
{
    private $response;

    public function __construct()
    {
        parent::__construct(null, array('location' => '1', 'uri' => '2'));
    }

    public function decode($response)
    {
        $this->response = $response;
        $ret = $this->pseudoCall();
        $this->response = null;
        return $ret;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        return $this->response;
    }
}
