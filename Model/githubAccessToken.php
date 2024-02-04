<?php

$githubAuthData = file_get_contents("../githubApiToken.json");
$githubAuthDeserializedData = json_decode($githubAuthData, true);

define("OAUTH2_CLIENT_ID", $githubAuthDeserializedData["client_id"]);
define("OAUTH2_CLIENT_SECRET", $githubAuthDeserializedData["client_secret"]);

$authorizeURL = 'https://github.com/login/oauth/authorize';
$tokenURL = 'https://github.com/login/oauth/access_token';

session_start();

// When GitHub redirects the user back here, there will be a "code" parameter in the query string
if (get('code')) {
    // Exchange the auth code for a token
    $token = apiRequest($tokenURL, array(
        'client_id' => OAUTH2_CLIENT_ID,
        'client_secret' => OAUTH2_CLIENT_SECRET,
        'code' => get('code')
    ));

    $_SESSION['access_token'] = $token->access_token;
    unset($_SESSION['prev_page']);
    header("location: " . $_GET['prev_page']);
} else {
    // Start the login process by sending the user to GitHub's authorization page
    unset($_SESSION['access_token']);

    $params = array(
        'client_id' => OAUTH2_CLIENT_ID,
        'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?prev_page=' . $_SESSION['prev_page'],
        'scope' => 'repo'
    );

    // Redirect the user to GitHub's authorization page
    header('Location: ' . $authorizeURL . '?' . http_build_query($params));
    die();
}

function apiRequest($url, $post = FALSE, $headers = array())
{
    $ch = curl_init($url);

    $agent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2';
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

    $headers[] = 'Accept: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    return json_decode($response);
}

function get($key, $default = NULL)
{
    return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default = NULL)
{
    return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}
