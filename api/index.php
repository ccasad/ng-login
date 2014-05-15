<?php
require 'vendor/slim/slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->post('/login', 'login');
$app->post('/logout', 'logout');
$app->post('/register', 'register');
$app->get('/users', 'getUsers');

$app->run();

function login() {

  $app = \Slim\Slim::getInstance();

  $request = $app->request();
  $data = json_decode($request->getBody());
  $user = '';

  if ($data) {
    $username = mysql_real_escape_string($data->username);
    $password = mysql_real_escape_string($data->password);

    if ($username == 'admin' && $password == '123') {
      $user = '{"role": {"bitMask": 4, "title": "admin"}, "username": "administrator"}';
    } else if ($username == 'user' && $password == '123') {
      $user = '{"role": {"bitMask": 2, "title": "user"}, "username": "jimmyjo"}';
    }
  }

  echo $user;
}

function logout() {
  echo '';
}

function register() {
  echo '';
}

function getUsers() {

	$users = <<<EOF
		[
			{"id": 1, "role": {"bitMask": 4, "title": "admin"}, "username": "admin"}, 
			{"id": 2, "role": {"bitMask": 2, "title": "user"}, "username": "chris"},
			{"id": 3, "role": {"bitMask": 3, "title": "user"}, "username": "payal"},
			{"id": 4, "role": {"bitMask": 4, "title": "admin"}, "username": "amit"}
		]
EOF;

	echo $users;
	
	/*
    $sql = "select * FROM wine ORDER BY name";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $wines = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"wine": ' . json_encode($wines) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    */
}





//With custom settings
/*
$app = new Slim(array(
    'log.enable' => true,
    'log.path' => './logs',
    'log.level' => 4,
    'view' => 'MyCustomViewClassName'
));
*/

/*
$app->get('/wines/:id',  'getWine');
$app->get('/wines/search/:query', 'findByName');
$app->post('/wines', 'addWine');
$app->put('/wines/:id', 'updateWine');
$app->delete('/wines/:id',   'deleteWine');
*/

//GET route
//$app->get('/hello/:name', function ($name) {
//    echo "Hello, $name";
//});

