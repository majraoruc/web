<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS, PATCH');


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
* Table users contains following columns: id, username and password
*
* NOTE: If you are having issues with non working routes in flightPHP you have to enable MOD_REWRITE on Apache
*/

Flight::register('db', 'PDO', array('mysql:host=remotemysql.com;dbname=MeMRCQrz0S','MeMRCQrz0S','H0SZMJvg2n'));

Flight::route('GET /users', function(){
  $users = Flight::db()->query('SELECT * FROM users ', PDO::FETCH_ASSOC)->fetchAll();
  Flight::json($users);
});




/** TODO
  * implement midleware function to protect API from users that are not logged in.
  * If unlogged user tries to open any other endpoint than rest/login return message 'LOG IN PLEASE'
  * If logged in user tries to open any other endpoint return message 'Enpont is not available for now, go to home'
 */



Flight::route('POST /login', function(){
  /** TODO
  * write a query to check if user exists in database
  * check if password is valid
  * create JWT which will encode id, username, and hashed user password
  * JWT should be valid 24 hours
  * if user does not exists or password is not correct you should stop flightPHP framework
  * This endpoint should return output in JSON format
  */
  $user = Flight::request()->data->getData();
  $db_user =Flight::db()->query('SELECT * FROM users WHERE id = :id');

  if($db_user){
    if($db_user['password'] == $user['psword']){
      $token_data = [
        'id' => $db_user['id'],
        'username' => $db_user['username'],

      ];

      $jwt = Auth::encode_jwt($token_data);
      Flight::json(['user_token' => $jwt]);
    } else {
      Flight::halt(404, 'Password is not correct');
    }
  } else {
    Flight::halt(404, 'User not found');
  }
});


Flight::start();
?>
