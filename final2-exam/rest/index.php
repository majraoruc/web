<?php
require_once 'config.php';
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;
/** TODO
* Use PDO connection to connect to MySQL Database
*
* connect to MySQL database with following credentials
* host: remotemysql.com
* username: MeMRCQrz0S
* password: H0SZMJvg2n
* database: MeMRCQrz0S
* connection string: mysql:host=remotemysql.com;dbname=MeMRCQrz0S
*
* Table students contains following columns: id, name, surname, gender, class
* Table users contains following columns: id, username and password, type
*
* NOTE: If you are having issues with non working routes in flightPHP you have to enable MOD_REWRITE on Apache
*/
Flight::register('db', 'PDO', array('host: remotemysql.com;dbname=MeMRCQrz0S', 'MeMRCQrz0S', 'H0SZMJvg2n'), function ($db) {


Flight::route('POST /login', function(){
  /** TODO
  * write a query to check if user exists in database
  * check if password is valid
  * create JWT which will encode id, username, and hashed user password and user type
  * JWT should be valid 30 minutes
  * if user does not exists or password is not correct you should stop flightPHP framework
  * This endpoint should return output in JSON format
*/

$username = Flight::request()->data->username;
  $password = Flight::request()->data->password;

  $database_user = Flight::db()->execute_query("SELECT * FROM users WHERE username = :username AND password = :password",
  [
      ':username' => $username,
      ':password' => $password
  ]);

  if($database_user){
    $expiration = time() + (1000 * 60 * 60 * 24);
    $payload = array(
      'id' => $database_user[0]['id'],
      'username' => $database_user[0]['username'],
      'password' => hash('sha256', $database_user[0]['password']),
      'exp' =>  $expiration
    );

    $jwt = JWT::encode($payload, JWT_KEY);
    Flight::json(
      array(
          'success' => TRUE,
          'name' => $database_user[0]['username'],
          'jwt' => $jwt
      )
    );
    die();
  } else {
    Flight::json(
      array(
          'success' => FALSE,
          'message' => 'There is no user with that credentials'
      )
    );
    die();
  }
});

});

Flight::route('GET /students', function(){
    /** TODO
    * write a query that will list all data about students.
    * following columns should be returned from database: id, name, surname, gender, class
    *
    * This endpoint should return output in JSON format
    */

    $data = Flight::db()->query("SELECT id, name, surname, gender, class FROM students", PDO::FETCH_ASSOC)->fetchAll();
    Flight::json($data);
});


Flight::route('GET /graphs', function(){
    /** TODO
    * write a query that will provide results about class distribution
    *
    * output should look like this:
    * class	distribution
    * First	50
    * Second	50
    *
    * This endpoint should return output in JSON format
    */
    $data = Flight::db()->query("SELECT class,
                                (SUM(first) / (SELECT SUM(name) FROM students)*100) as first,
                                (SUM(second) / (SELECT SUM(class) FROM students)*100) as second
                                FROM student GROUP BY class", PDO::FETCH_ASSOC)->fetchAll();
    Flight::json($data);

    $data = Flight::db()->query("SELECT users,
                                (SUM(manager) / (SELECT SUM(username) FROM users)*100) as manager,
                                (SUM(executive) / (SELECT SUM(username) FROM users)*100) as executive
                                FROM users GROUP BY type", PDO::FETCH_ASSOC)->fetchAll();
    Flight::json($data);
});


Flight::start();
?>
