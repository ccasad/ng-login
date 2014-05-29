<?php

require 'vendor/slim/slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->post('/login', array('Auth', 'login'));
$app->post('/logout', array('Auth', 'logout'));
$app->post('/register', array('Auth', 'register'));
$app->get('/users', array('UserDataService', 'getUsers')); 

// Example of needing to have a secure route
// $app->get('/users', array('Auth', 'authenticateUserToken'), 'getUsers');


class User implements JsonSerializable {

  public $id;
  public $firstName;
  public $lastName;
  public $email;
  public $password;
  public $token;
  public $role;

  public function __construct($arr) {
    if (isset($arr) && is_array($arr)) {
      $this->populate($arr);
    }
  }

  public function jsonSerialize() {
    return [
      'id' => $this->id,
      'firstName' => $this->firstName,
      'lastName' => $this->lastName,
      'email' => $this->email,
      'token' => $this->token,
      'role' => [
        'bitMask' => $this->role->bitMask,
        'title' => $this->role->title
      ]
    ];
  }

  public function populate($arr) {
    $this->id = (isset($arr['user_id']) && strlen($arr['user_id'])) ? $arr['user_id'] : '';
    $this->firstName = (isset($arr['first_name']) && strlen($arr['first_name'])) ? $arr['first_name'] : '';
    $this->lastName = (isset($arr['last_name']) && strlen($arr['last_name'])) ? $arr['last_name'] : '';
    $this->email = (isset($arr['email']) && strlen($arr['email'])) ? $arr['email'] : '';
    $this->token = (isset($arr['token']) && strlen($arr['token'])) ? $arr['token'] : '';

    $role = new Role();
    $role->id = $arr['role_id'];
    $role->title = $arr['role_title'];
    $role->bitMask = $arr['role_bit_mask'];

    $this->role = $role;
  }
}

class Role {

  public $id;
  public $title;
  public $bitMask;

}

class UserDataService {

  function getUsers() {

    $users = '';

    try {
      $db = PdoMysql::getConnection();

      if ($db) {
        $sql = <<<EOF
            SELECT u.id AS user_id, 
                   first_name, 
                   last_name, 
                   email, 
                   r.id AS role_id,
                   r.role AS role_title, 
                   r.bit_mask AS role_bit_mask
            FROM user u
            JOIN user_role ur ON u.id = ur.user_id
            JOIN role r ON ur.role_id = r.id
EOF;
        
        $stmt = $db->prepare($sql);

        $stmt->execute();
       
        $usersArr = array();
        while ($row = $stmt->fetch()) {
          $user = new User($row);
          $usersArr[] = $user;
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

}

function getUser($id) {

  $user = '';

  if (isset($id) && strlen($id) && $id > 0) {

    try {
      $db = PdoMysql::getConnection();

      if ($db) {

        $sql = <<<EOF
            SELECT *, MD5(CONCAT(id, UNIX_TIMESTAMP(created_date))) AS token
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

final class Auth {

  public static function login() {

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

  public static function logout() {
    echo '{"status": "success", "data": ""}';
  }

  public static function register() {
    
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
                INSERT INTO user (first_name, last_name, email, password)
                VALUES (:firstName, :lastName, :email, :password)      
EOF;

            $stmt = $db->prepare($sql);
            
            $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);         
            $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);

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

  public static function authenticateUserToken() {

    if (!isset($_SERVER['HTTP_USER_TOKEN']) || !self::verifyUserToken()) {
      appFailure("You need a valid API key.", 400);
    }

  }

  private static function verifyUserToken() {

    $isKeyValid = false;

    try {
      $db = PdoMysql::getConnection();

      if ($db) {

        $sql = <<<EOF
            SELECT *
            FROM user
            WHERE MD5(CONCAT(id, UNIX_TIMESTAMP(created_date))) = :token
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


$app->run();

