<?php
session_start();

require 'vendor/slim/slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/*
$headerToken = $_SERVER['HTTP_CSRF_TOKEN'];
$sessionToken = $_SESSION['XSRF'];

if ($headerToken != $sessionToken) {
  appFailure("Token is not authenticated.", 400);
}
*/

$app->post('/login', 'login');
$app->post('/logout', 'logout');
$app->post('/register', 'AuthenticateUserToken', 'register');
$app->get('/users', 'AuthenticateUserToken', 'getUsers');

$app->run();

function AuthenticateUserToken(\Slim\Route $route) {
  if (!isset($_SERVER['HTTP_USER_TOKEN']) || !verifyUserToken()) {
    appFailure("You need a valid API key.", 400);
  }
}

function verifyUserToken() {

  $isKeyValid = false;

  /*
  try { 
    $sql = "select * from apiKeys where apiKey = :apiKey";
    $s = $this->dbh->prepare($sql);
    $s->bindParam("apiKey", $key);
    $s->execute();
    $keyVerification = $s->fetch(\PDO::FETCH_OBJ);
  } catch(\PDOException $e) {
    $app->status(500);
    $result = array("status" => "error", "message" => 'No likey');
    echo json_encode($result);
    $app->stop();
  }
  */

  $isKeyValid = ($_SERVER['HTTP_USER_TOKEN'] == '1234') ? true : false;

  return $isKeyValid;
}

function appFailure($message, $code) {

  $app = \Slim\Slim::getInstance();

  $app->status($code);
  $result = array("status" => "error", "message" => $message);
  echo json_encode($result);
  $app->stop();
}

function login() {

  $app = \Slim\Slim::getInstance();

  $request = $app->request();
  $data = json_decode($request->getBody());
  $response = '';

  if ($data) {
    $username = mysql_real_escape_string($data->username);
    $password = mysql_real_escape_string($data->password);

    $user = '';
    if ($username == 'admin' && $password == '123') {
      $user = '{"role": {"bitMask": 4, "title": "admin"}, "username": "administrator", "token": "1234"}';
    } else if ($username == 'user' && $password == '123') {
      $user = '{"role": {"bitMask": 2, "title": "user"}, "username": "jimmyjo", "token": "5678"}';
    }

    $response = '{"status": "success", "data": ' . $user . '}';
  } else {
    $response = '{"status": "error", "message": "Login unsuccessful"}';
  }

  echo $response;
}

function logout() {
  echo '{"status": "success", "data": ""}';
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

