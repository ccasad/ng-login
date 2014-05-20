<?php
/*
TO DO:

1. Add transactions to inserts
2. Clean up 

*/

session_start();

require 'vendor/slim/slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->post('/login', 'login');
$app->post('/logout', 'logout');
$app->post('/register', 'register');
$app->get('/users', 'getUsers');  // 'AuthenticateUserToken', 

$app->run();

function AuthenticateUserToken(\Slim\Route $route) {
  if (!isset($_SERVER['HTTP_USER_TOKEN']) || !verifyUserToken()) {
    appFailure("You need a valid API key.", 400);
  }
}

function verifyUserToken() {

  $isKeyValid = false;

  try {
    $db = PdoMysql::getConnection();

    if ($db) {

      $sql = <<<EOF
          SELECT *
          FROM user
          WHERE MD5(CONCAT(id, signature)) = :token
EOF;
      
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':token', $_SERVER['HTTP_USER_TOKEN'], PDO::PARAM_STR);

      $stmt->execute();
      $result = $stmt->fetch();

      if ($result && isset($result['id']) && strlen($result['id'])) {
        $isKeyValid = true;
      }
    }

  } catch( PDOException $e ) {
    throw new PDOException($e); 
  } catch (Exception $e) {
    throw new Exception($e); 
  }

  return $isKeyValid;
}

function login() {

  $app = \Slim\Slim::getInstance();

  $request = $app->request();
  $data = json_decode($request->getBody());

  $response = '{"status": "error", "message": "A problem occurred."}';

  if ($data && isset($data->email) && isset($data->password)) {
    $email = mysql_real_escape_string($data->email);
    $password = mysql_real_escape_string($data->password);

    if (strlen($email) && strlen($password)) {

      try {
        $db = PdoMysql::getConnection();

        if ($db) {

          $sql = <<<EOF
              SELECT id
              FROM user
              WHERE email = :email
              AND password = :password
EOF;
          
          $stmt = $db->prepare($sql);

          $stmt->bindParam(':email', $email, PDO::PARAM_STR);
          $stmt->bindParam(':password', $password, PDO::PARAM_STR);

          $stmt->execute();

          $result = $stmt->fetch();

          if ($result && isset($result['id'])) {
            $user = getUser($result['id']);
          }
          
          $response = '{"status": "success", "data": ' . $user . '}';     
        }

      } catch( PDOException $e ) {
        throw new PDOException($e); 
      } catch (Exception $e) {
        throw new Exception($e);
      } 
    }
  }

  echo $response;
}

function logout() {
  echo '{"status": "success", "data": ""}';
}

function register() {
  
  $app = \Slim\Slim::getInstance();

  $request = $app->request();
  $data = json_decode($request->getBody());
  $response = '{"status": "error", "message": "A problem occurred."}';

  if ($data) {
    $firstName = mysql_real_escape_string($data->firstName);
    $lastName = mysql_real_escape_string($data->lastName);
    $email = mysql_real_escape_string($data->email);
    $password = mysql_real_escape_string($data->password);
    $bitMask = mysql_real_escape_string($data->bitMask);

    if (strlen($firstName) && strlen($lastName) && strlen($email) && strlen($password) && strlen($bitMask)) {
      try {
        $db = PdoMysql::getConnection();

        if ($db) {
          $sql = <<<EOF
              INSERT INTO user (first_name, last_name, email, password, signature)
              VALUES (:firstName, :lastName, :email, :password, :signature)      
EOF;

          $stmt = $db->prepare($sql);
          
          // create a random signature
          $signature = substr(md5(rand()), 5, 10) . strtoupper(substr(md5(rand()), 5, 10));
          
          $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);         
          $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
          $stmt->bindParam(':email', $email, PDO::PARAM_STR);
          $stmt->bindParam(':password', $password, PDO::PARAM_STR);
          $stmt->bindParam(':signature', $signature, PDO::PARAM_STR);

          $stmt->execute();
          
          $newUserId = $db->lastInsertId();

          // set the user's role
          $sql = <<<EOF
              INSERT INTO user_role (user_id, role_id)
                SELECT :userId, id
                FROM role
                WHERE bit_mask = :bitMask      
EOF;

          $stmt = $db->prepare($sql);
          
          $stmt->bindParam(':userId', $newUserId, PDO::PARAM_INT);         
          $stmt->bindParam(':bitMask', $bitMask, PDO::PARAM_INT);

          $stmt->execute();

          // get the new user 
          $user = getUser($newUserId);

          $response = '{"status": "success", "data": ' . $user . '}';     
        }

        $db = null;
      } catch( PDOException $e ) {
        throw new PDOException($e); 
      } catch (Exception $e) {
        throw new Exception($e);
      }      
    }
  }

  echo $response;
}

function getUser($id) {

  $user = '';

  if (isset($id) && strlen($id) && $id > 0) {

    try {
      $db = PdoMysql::getConnection();

      if ($db) {

        $sql = <<<EOF
            SELECT *, MD5(CONCAT(id, signature)) AS token
            FROM user
            WHERE id = :id
EOF;
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        $result1 = $stmt->fetch();

        // get user role
        $sql = <<<EOF
            SELECT role_id, role, bit_mask
            FROM user_role ur
            JOIN role r ON ur.role_id = r.id AND ur.user_id = :userId
EOF;
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userId', $id, PDO::PARAM_INT);

        $stmt->execute();
        $result2 = $stmt->fetch();

        $user = '{"id": ' . $result1['id'] . ', "firstName": "' . $result1['first_name'] . '", "lastName": "' . $result1['last_name'] . '", "email": "' . $result1['email'] . '", "token": "' . $result1['token'] . '", "role": {"bitMask": ' . $result2['bit_mask'] . ', "title": "' . $result2['role'] . '"}}';    
      }

    } catch( PDOException $e ) {
      throw new PDOException($e); 
    } catch (Exception $e) {
      throw new Exception($e); 
    }
  }

  return $user;
}

function getUsers() {

  $users = '';

  try {
    $db = PdoMysql::getConnection();

    if ($db) {

      $sql = <<<EOF
          SELECT * 
          FROM user u
          JOIN user_role ur ON u.id = ur.user_id
          JOIN role r ON ur.role_id = r.id
EOF;
      
      $stmt = $db->prepare($sql);

      $stmt->execute();
      
      $usersArr = array();
      while ($row = $stmt->fetch()) {
        $userArr = array('id' => $row['user_id'], 
                         'firstName' => $row['first_name'], 
                         'lastName' => $row['last_name'], 
                         'email' => $row['email'], 
                         'token' => '', 
                         'role' => array('bitMask' => $row['bit_mask'], 'title' => $row['role']), 
                         );

        $usersArr[] = $userArr;
      }
      $users = json_encode($usersArr);  
    }

  } catch( PDOException $e ) {
    throw new PDOException($e); 
  } catch (Exception $e) {
    throw new Exception($e); 
  }

  $response = '{"status": "success", "data": ' . $users . '}';

  echo $response;
}

final class PdoMysql {

  const HOST = 'localhost';
  const USER = 'ccasad';
  const PASSWORD = 'ccasad';
  const DBNAME = 'test1';

  public static function getConnection() {

    try {

      $dsn = 'mysql:dbname=' . self::DBNAME . ';host=' . self::HOST;
      $conn = new PDO($dsn, self::USER, self::PASSWORD);

    } catch (PDOException $e) {
      return false;
    }
    
    return $conn;
  }
}

function appFailure($message, $code) {

  $app = \Slim\Slim::getInstance();

  $app->status($code);
  $result = array("status" => "error", "message" => $message);
  echo json_encode($result);
  $app->stop();
}
