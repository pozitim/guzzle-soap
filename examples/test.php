<?php

include_once(realpath(__DIR__ . '/../vendor/autoload.php'));

$wsdl = 'http://lyrics.wikia.com/server.php?wsdl';
$artists = [
    'Dia Frampton',
    'Hoobastank',
    'Train',
    'Duffy',
    'MIKA',
    'The Killers',
    'The Cranberries',
    'The Police',
    'The Enemy',
    'X Ambassadors',
    'Kaiser Chiefs',
    'Adele',
    'The Verve'
];

$requests = array();
foreach ($artists as $artist) {
    $requests[$artist] = array(
        'functionName' => 'searchArtists',
        'arguments' => array($artist)
    );
}
$asyncSender = new \Pozitim\Soap\Async\Client($wsdl);
$responses = $asyncSender->sendAllAsync($requests);

foreach ($responses as $key => $response) {
    echo $key . ' => ' . json_encode($response) . PHP_EOL;
}
