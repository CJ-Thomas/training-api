<?php
$DB_NUCLEUS_HOST = "localhost";
$DB_NUCLEUS_USER = "root";
$DB_NUCLEUS_PASSWD = "root";
$DB_NUCLEUS_NAME = "db_nucleus";

$GLOBALS['RABBITMQ_HOST']     = 'localhost';
$GLOBALS['RABBITMQ_USER']     = 'guest';
$GLOBALS['RABBITMQ_PASSWORD'] = 'guest';

$GLOBALS['JWT_SECRET'] = 'thisisasecret';

$GLOBALS['GENERAL_SALT'] = '7deb2a92e95689fb3bce0a96eca0592f';

putenv("DEBUG=true");

// For proper working of  helper functions in base testcase
$DB_HOST = $DB_NUCLEUS_HOST;
$DB_USER = $DB_NUCLEUS_USER;
$DB_PASSWD = $DB_NUCLEUS_PASSWD;
$DB_NAME = $DB_NUCLEUS_NAME;

