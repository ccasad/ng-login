<?php

$users = <<<EOF
	[
		{"id": 1, "role": {"bitMask": 4, "title": "admin"}, "username": "admin"}, 
		{"id": 2, "role": {"bitMask": 2, "title": "user"}, "username": "bill"},
		{"id": 3, "role": {"bitMask": 3, "title": "user"}, "username": "chris"},
		{"id": 4, "role": {"bitMask": 4, "title": "admin"}, "username": "admin"}
	]
EOF;

echo $users;

