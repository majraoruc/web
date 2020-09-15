<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS, PATCH');

require '../vendor/autoload.php';

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
* NOTE: If you are having issues with non working routes in flightPHP you have to enable MOD_REWRITE on Apache
*/

/** TODO
* Create table named car_name_surname where name and surname are your name and surname.
* Table should contain following columns: id, type, model, color, number_of_doors, engine_power, production_year
* Use this table to finish following tasks
*/

Flight::register('db', 'PDO', array('mysql:host=remotemysql.com;dbname=91TeMKc79a', '91TeMKc79a', 'vm3U2Riaus'));


Flight::route('GET /cars', function(){
    /** TODO
    * write a query that will list all data about cars and show them in report table on user interface.
    * following columns should be returned from database: id, type, model, color, number_of_doors, engine_power, production_year, price and car_age
    * price is calculated as: (engine_power * 1000) + (number_of_doors * 1000)
    * car_age is calculated as: production_year - current_year
    *
    * This endpoint should return output in JSON format
    */



    $data = Flight::db()->query("SELECT id, type, model , color, number_of_doors, engine_power,
                                (engine_power * 1000) + (number_of_doors * 1000) * 100 as price, production_year - 2020 as age
                                FROM cars", PDO::FETCH_ASSOC)->fetchAll();




    Flight::json($data);



});

Flight::route('GET /car', function(){



  $id = Flight::request()->query['id'];
  $stmt = Flight::db()->prepare("SELECT * FROM cars WHERE id = :id");
  $stmt->execute([':id' => $id]);
  $car = $stmt->fetch();
  Flight::json($car);


    /** TODO
    * write a query that will list data about car selected from user interface.
    * following columns should be returned from database: id, type, model, color, number_of_doors, engine_power, production_year
    *
    * This endpoint should return output in JSON format
    */
});

Flight::route('POST /cars', function(){




  $request = Flight::request()->data->getData();
  unset($request['psword']);
  $insert = 'INSERT INTO cars (type, model, color, number_of_doors, engine_power, production_year) VALUES (:type, :model, :color, :number_of_doors, engine_power, production_year)';
  $stmt = Flight::db()->prepare($insert);
  $stmt->execute($request);
  Flight::json('Car has been added');

    /** TODO
    * write a query that will add new car to database
    * following columns should be added to database: id, type, model, color, number_of_doors, engine_power, production_year
    *
    * This endpoint should return output in JSON format
    */
});

Flight::route('POST /car', function(){


  $request = Flight::request()->data->getData();
  unset($request['psword']);
  $update = "UPDATE cars SET type = :type, model = :model, color = :color, number_of_doors = :number_of_doors, engine_power = :engine_power, production_year = :production_year WHERE id = :id";
  $stmt = Flight::db()->prepare($update);
  $stmt->execute($request);
  Flight::json('Updated');



    /** TODO
    * write a query that will update data about selected car in database
    * following columns could be updated: type, model, color, number_of_doors, engine_power, production_year
    *
    * This endpoint should return output in JSON format
    */
});

Flight::route('DELETE /car/@id', function($id){


    $delete = 'DELETE FROM cars WHERE id = :id';
    $stmt = Flight::db()->prepare($delete);
    $stmt->execute([':id' => $id]);


});

Flight::start();
?>
