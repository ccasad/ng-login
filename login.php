<?php

$data = json_decode(file_get_contents("php://input"));
$user = '{}';

if ($data) {
	$username = mysql_real_escape_string($data->username);
	$password = mysql_real_escape_string($data->password);

	if ($username == 'admin' && $password == '123') {
		$user = '{"role": {"bitMask": 4, "title": "admin"}, "username": "adminyo"}';
	} else if ($username == 'user' && $password == '123') {
		$user = '{"role": {"bitMask": 2, "title": "user"}, "username": "billybob"}';
	}
}

echo $user;
