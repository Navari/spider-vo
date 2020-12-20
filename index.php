<?php

include "vendor/autoload.php";
use Navari\Spider\Spider;
use GuzzleHttp\Client;


$spider = new Spider((new Client()));

$spider->setAgencyId(62);

print_r($spider->getAllPage());