# Setup

## Database
The quiz requires a database, please execute the SQL found in setup.sql to set up the table structure. (Change the schema name if wished).
Add your questions to the 'question' table (with quizId 1) and your answers to the 'answer' table.

##Config
Add your database connection info to _config/db.php_

## Installing
Run 'composer install' while in the root folder

## Webserver
Run 'php yii serve' while in the root folder, now you can access the quiz via localhost. 
The default URL is http://localhost:8080/ 
