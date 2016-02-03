<?php

namespace Pozitim\Soap\Async;

use GuzzleHttp\Psr7\Response;

class Client
{
    /**
     * @var Decoder
     */
    protected $decoder;

    /**
     * @var Encoder
     */
    protected $encoder;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzleClient;

    /**
     * @var string
     */
    protected $wsdl;

    /**
     * @var array
     */
    protected $promises = array();

    /**
     * @param $wsdl
     * @param Encoder|null $encoder
     * @param Decoder|null $decoder
     */
    public function __construct($wsdl, Encoder $encoder = null, Decoder $decoder = null)
    {
        $this->wsdl = $wsdl;
        $this->guzzleClient = new \GuzzleHttp\Client();
        if ($encoder == null) {
            $this->encoder = new Encoder($this->wsdl);
        }
        if ($decoder == null) {
            $this->decoder = new Decoder();
        }
    }

    /**
     * @param array $requests
     * @return array
     */
    public function sendAllAsync(array $requests)
    {
        foreach ($requests as $key => $request) {
            $request['key'] = $key;
            $this->sendAsync($request);
        }
        return $this->getResponses();
    }

    /**
     * @param array $params
     */
    protected function sendAsync(array $params) {
        $params = $this->fixRequestParams($params);
        $request = $this->getEncoder()->encode(
            $params['functionName'],
            $params['arguments'],
            $params['options'],
            $params['inputHeaders']
        );
        $this->promises[$params['key']] = $this->getClient()->sendAsync($request);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function fixRequestParams(array $params)
    {
        if (!array_key_exists('key', $params)
            || !array_key_exists('functionName', $params)
            || !array_key_exists('arguments', $params)
        ) {
            throw new \BadMethodCallException();
        }
        if (!array_key_exists('options', $params)) {
            $params['options'] = null;
        }
        if (!array_key_exists('inputHeaders', $params)) {
            $params['inputHeaders'] = null;
        }
        return $params;
    }

    /**
     * @return array
     */
    protected function getResponses()
    {
        /**
         * @var Response $response
         */
        $responses = \GuzzleHttp\Promise\unwrap($this->promises);
        $temp = [];
        foreach ($responses as $key => $response) {
            $temp[$key] = $this->getDecoder()->decode($response->getBody()->__toString());
        }
        return $temp;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getClient()
    {
        return $this->guzzleClient;
    }

    /**
     * @return Encoder
     */
    protected function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * @return Decoder
     */
    protected function getDecoder()
    {
        return $this->decoder;
    }
}
