<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Github\Client;

function authClient(): Client {
    $client = new Client();
    $client->authenticate($_SESSION['access_token'], null,'access_token_header');
    return $client;
}
