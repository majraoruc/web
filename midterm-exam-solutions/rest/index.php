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
* NOTE: table that contains all data is named covid_countries
*
* NOTE: If you are having issues with non working routes in flightPHP you have to enable MOD_REWRITE on Apache
*/

Flight::register('db', 'PDO', array('mysql:host=remotemysql.com;dbname=MeMRCQrz0S', 'MeMRCQrz0S', 'H0SZMJvg2n'));

Flight::route('GET /countries', function(){
    /** TODO
    * write a query that will list all data about covid countries and show them in report table on user interface.
    * following columns should be returned from database: id, continent, country_name, total, pending, recovered, deaths, deaths_rate, recovery_rate
    * pending is calculated as:  total - deaths - recovered
    * death_rate is calculated as: deaths / total * 100
    * recovery_rate is calculated as: recovered / total * 100
    *
    * This endpoint should return output in JSON format
    */

    $data = Flight::db()->query("SELECT id, name as country_name, continent, total, recovered, deaths, total-deaths-recovered as pending,
                                deaths/total * 100 as death_rate, recovered / total * 100 as recovery_rate
                                FROM covid_countries", PDO::FETCH_ASSOC)->fetchAll();
    Flight::json($data);
});

Flight::route('GET /graphs', function(){
    /** TODO
    * write a query that will provide aggregated results about death distribution and recovery distribution per continent
    * this query should use MySQL aggregated function. Death distribution is calculated as division of
    * total number of deaths on continent with total number of deaths in the world multiplied with 100. Same formula can be applied to calculate recovery distribution
    *
    * NOTE: The easiest way to implement this is to run 2 queries on MySQL database
    *
    * output should look like this:
    * continent	death_distr	recovery_dist
    * South America	2.2727	4.5512
    * Oceania	0.1017	1.6929
    * North America	25.8255	13.7003
    * Europe	62.1367	50.0688
    * Asia	8.9917	29.0866
    * Africa	0.6716	0.9001
    *
    * This endpoint should return output in JSON format
    */

    $data = Flight::db()->query("SELECT continent,
                                (SUM(deaths) / (SELECT SUM(deaths) FROM covid_countries) * 100) as death_distr,
                                (SUM(recovered) / (SELECT SUM(recovered) FROM covid_countries) * 100) as recovery_dist
                                FROM covid_countries GROUP BY continent", PDO::FETCH_ASSOC)->fetchAll();
    Flight::json($data);
});

Flight::start();
?>
