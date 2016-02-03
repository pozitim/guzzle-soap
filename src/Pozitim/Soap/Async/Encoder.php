<?php

namespace Pozitim\Soap\Async;

use GuzzleHttp\Psr7\Request;

class Encoder extends \SoapClient
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @param $functionName
     * @param array $arguments
     * @param array|null $options
     * @param null $inputHeaders
     * @return Request
     */
    public function encode(
        $functionName,
        array $arguments,
        array $options = null,
        $inputHeaders = null
    ) {
        $this->__soapCall($functionName, $arguments, $options, $inputHeaders);
        $request = $this->request;
        $this->request = null;
        return $request;
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param int $one_way
     * @return string
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $headers = array(
            'SOAPAction' => $action,
            'Content-Type' => 'text/xml; charset=utf-8',
            'Content-Length' => strlen($request)
        );
        $this->request = new Request('POST', $location, $headers, (string)$request);
        return '';
    }
}
