<?php
/*
The purpose of this file is to route every Ajax call from the front end application 
through this file to prevent XSRF (cross site request forgery). A session token  
will be added to the front end so that every Ajax request will append 
the token and that token will be evaluated here against the original session value.
If the values equate then the request can continue to the API else a response of 
"Unauthoized" will be returned. 
*/

session_start();

$headerToken = $_SERVER['HTTP_CSRF_TOKEN'];
$sessionToken = $_SESSION['XSRF'];

if ($headerToken != $sessionToken) {
  header('HTTP/1.0 401 Unauthorized');
  exit;
}

/* CSRF TOKEN IS LEGIT SO CALL THE API */

$protocol = 'http://';
$ip = '192.168.56.101';
$api = '/ng-login/api/';
$host = 'vm-centos-cc';
$route = $_GET['route'];
$method = $_SERVER['REQUEST_METHOD'];
$params = file_get_contents("php://input");
$userToken = isset($_SERVER['HTTP_USER_TOKEN']) ? $_SERVER['HTTP_USER_TOKEN'] : '';

$url = $protocol . $ip . $api . $route;

$ch = curl_init();

$headers = array('Host: ' . $host, 'USER_TOKEN: ' . $userToken);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);     
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

$response = curl_exec($ch);       
curl_close($ch);

// If this is the login call and successful login then set user cookie
if (isset($response) && strlen($response)) {
	if ($route == 'login') {	
		$json = json_decode($response);
		if ($json->status == 'success') {
			setcookie('user', json_encode($json->data));
		}
	} else if ($route == 'logout') {
		if (isset($_COOKIE['user'])) {
			unset($_COOKIE['user']);
			setcookie('user', null, -1);
		}
	}
}

// return the json response
echo $response;
