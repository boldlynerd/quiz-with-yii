# Setup

## Database
The quiz requires a database, please execute the SQL found in setup.sql to set up the table structure.
Add your questions to the 'question' table (with quizId 1) and your answers to the 'answer' table.

##Config
Add your database connection info to _basic/config/db.php_

## Installing
Run 'composer install' while in the folder _basic_

## Webserver
Run 'php yii serve' while in the folder _basic_, now you can access the quiz via localhost. 
The default URL is http://localhost:8080/ 
