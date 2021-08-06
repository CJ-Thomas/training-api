#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";
use com\linways\nucleus\core\service\CollegeService;
use \Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
error_reporting(E_ALL & ~E_WARNING ); // prevent showing warnings

//Path to nucleus DB conf
$nucleusTestDBConfPath = __DIR__ . '/../test/db_conf/nucleus_db_conf.php';
putenv("NUCLEUS_CONF=$nucleusTestDBConfPath");

$dbs = [];
$target = null;
$seedName = null;
$command = null;
$dbUsername = 'root';
$dbPassword = 'root';
$dbHost = 'localhost';
$commandArgs = [];
$migrationFile = null;
if(count($argv)<2){
  // If no operations specified
  $argv[1] = 'help';
}
else{
  $args = $argv;
  $command = $args[1];
  $flagStartIndex = null;
  for($i=3; $i<$argc; $i++){
    if(($args[$i] == '-s')|| ($args[$i] == '-S')){
      $seedName = $args[$i+1];
      $flagStartIndex = $flagStartIndex? $flagStartIndex: $i;
    }
    elseif(($args[$i] == '-t') || ($args[$i] == '-T')){
      $target = $args[$i+1];
      $flagStartIndex = $flagStartIndex? $flagStartIndex: $i;
    }
    elseif(($args[$i] == '-f') || ($args[$i] == '-F')){
      $migrationFile = $args[$i+1];
      $flagStartIndex = $flagStartIndex? $flagStartIndex: $i;
    }
    elseif(strtolower($args[$i]) == '-dbuser'){
      $dbUsername = $args[$i+1];
      $flagStartIndex = $flagStartIndex? $flagStartIndex: $i;
    }
    elseif(strtolower($args[$i]) == '-dbpass'){
      $dbPassword = $args[$i+1];
      $flagStartIndex = $flagStartIndex? $flagStartIndex: $i;
    }
    elseif(strtolower($args[$i]) == '-dbhost'){
      $dbHost = $args[$i+1];
      $flagStartIndex = $flagStartIndex? $flagStartIndex: $i;
    }
  }
  $flagStartIndex = $flagStartIndex? $flagStartIndex: 3; // for taking a single available arg
  if($argc > 2){
    for($i=2; $i<$flagStartIndex; $i++){
      $commandArgs[] = $args[$i];
    }
  }

}

switch ($command){
    case 'migrate':     //  MIGRATE
        $dbs = prepareDBArray($commandArgs, $command);
        foreach($dbs as $dbDetails){
          $app = new PhinxApplication();
          $wrap = setPhinxVariables('Migrating',$app, $dbDetails);
          try{
            if($target)
              $response = $wrap->getMigrate(null, $target);
            else
              $response = $wrap->getMigrate();
            print_r($response);
          }catch(Exception $e){
            echo "\033[31m ERROR: ";
            echo $e->getMessage();
            echo "\033[0m\n";
            exit(1);
          }

          echo "\n";
          unset($wrap);
          unset($app);
          unset($response);
      }
      break;
    case 'seed':         // SEED
        $dbs = prepareDBArray($commandArgs, $command);
        foreach($dbs as $dbDetails){
          $app = new PhinxApplication();
          $wrap = setPhinxVariables('Seeding', $app, $dbDetails);
          try{
            $response = $wrap->getSeed();
            print_r($response);
          }catch(Exception $e){
            echo "\033[31m ERROR: ";
            echo $e->getMessage();
            echo "\033[0m\n";
            exit(1);
          }
          echo "<br/>";
          unset($wrap);
          unset($app);
          unset($response);
        }
        break;
    case 'migrate-fake':    // MIGRATE FAKE
      if(empty($migrationFile)){
        echo "Name of the migration file is required\n";
        echo "USAGE: manger.php migrate-fake [ALL] / [[DB1 [DB2]] -f 1231234_migration_file\n";
        exit(1);
      }
      list($version, $migarationName) = explode ('_', $migrationFile, 2);
      $migarationName .= '-faked';
      $dbs = prepareDBArray($commandArgs, $command);
      foreach($dbs as $dbDetails){
         if(fakeMigration($version, $migarationName, $dbDetails))
            echo "Faked Migration $migrationFile for " . $dbDetails['dbName'] + "\n";
      }
      break;
    case 'create-migration':       // CREATE MIGRATION
      $app = new PhinxApplication();
      $command = ['create'];
      if(empty($commandArgs)){
        echo "Not enough arguments (missing: 'name')\n";
        echo "USAGE: manager.php create-migration NameOfMigration\n";
        exit(1);
      }
      $command += ["name" => $commandArgs[0]];
      try{
        $response = executeRun($command, $app);
        print_r($response);
      }catch(Exception $e){
        echo "\033[31m ERROR: ";
        echo $e->getMessage();
        echo "\033[0m\n";
        exit(1);
      }
      break;
    case 'create-seed':          // CREATE SEED
      $app = new PhinxApplication();
      $command = ['seed:create'];
      if(empty($commandArgs)){
        echo "Not enough arguments (missing: 'name')\n";
        echo "USAGE: manager.php create-seed NameOfSeedFile\n";
        exit(1);
      }
      $command += ["name" => $commandArgs[0]];
      try{
        $response = executeRun($command, $app);
        print_r($response);
      }catch(Exception $e){
        echo "\033[31m ERROR: ";
        echo $e->getMessage();
        echo "\033[0m\n";
        exit(1);
      }
      break;
    case 'help':          // HELP
      echo "\n";
      $mask = "%-20s %-s \n";
      printf($mask, 'migrate', 'To run the migrations');
      printf($mask, 'migrate-fake', 'To fake a migration (mark migration as done)');
      printf($mask, 'seed', 'To add seed values to the database');
      printf($mask, 'create-migration', 'To create a new migration file');
      printf($mask, 'create-seed', 'To create a new seed file');
      print("Refer https://devdocs.linways.com/books/linways-ams-developer-docs/chapter/database-migrator\n");
      break;
    default :
      echo "invalid option. Use `php manager.php help`";
}
/**
 * Prepare the array of databases according to the command params
 * @param  array $commandArgs commandline arguments
 * @param  array $command name of the command
 * @return array       array of database names;
 */
function prepareDBArray($commandArgs, $command){
  if(empty($commandArgs)){
    echo "Database name or 'ALL' should be specified as second argument\n";
    echo "use \n `manager.php $command DB_NAME_1 [DB_NAME_2 ... DB_NAME_N] [-t target] [-s seedFileName] [-f migrationFile]` \nor\n `manager.php $command ALL [-t target] [-s seedVersion]` \n";
    $mask = "%-20s %s \n";
    printf($mask, 'DB_NAME', 'To run the migrations against specified database');
    printf($mask, 'ALL', 'To run migrations against all databases');
    printf($mask, '-t', '(optional) To pass the target version of the migration. (The timestamp in the file name is the version)');
    printf($mask, '-s', '(optional) To pass the name of the seed file. (Name of the seed file)');
    printf($mask, '-dbhost', '(optional) Database hostname, Default: localhost');
    printf($mask, '-dbuser', '(optional) Database username, Default: root');
    printf($mask, '-dbpass', '(optional) Database password, Default: root');
    print("Refer https://devdocs.linways.com/books/linways-ams-developer-docs/chapter/database-migrator\n");
    exit();
  }
  if($commandArgs == ['ALL'] || $commandArgs == ['all']){
    $allColleges = CollegeService::getInstance()->getAllColleges();
    foreach ($allColleges as $college) {
      $dbs[] = [
          'dbName' => $college->collegeDb,
          'dbUsername' => $college->dbUsername,
          'dbPassword' => $college->dbPassword,
          'dbHost' => $college->dbHost
        ];
    }
    if(empty($dbs)){
      echo "ERROR: No entries found on college database.\n";
      exit(1);
    }
  }else{
    foreach($commandArgs as $dbName){
      global $dbUsername,$dbPassword,$dbHost;
      $dbs[] = [
        'dbName' => $dbName,
        'dbUsername' => $dbUsername,
        'dbPassword' => $dbPassword,
        'dbHost' => $dbHost
      ];
    }
  }
  return $dbs;
}
/**
 * Return true if $haystack starts with $needle, else false
 * @param  string $haystack
 * @param  string $needle   key to be searched
 * @return boolean         True if $haystack starts with $needle else False
 */
function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

/**
 * sets server env variables and returns the wrapper object
 * @param string $action    Name of the action for displaying only
 * @param string $dbDetails Database Details
 * @param object $app       Object of PhinxApplication
 * @return object           Phinx wrapper object
 */
function setPhinxVariables($action, $app, $dbDetails){
  $_SERVER['PHINX_DBNAME'] = $dbDetails['dbName'];
  $_SERVER['PHINX_DBUSER'] = $dbDetails['dbUsername'];
  $_SERVER['PHINX_DBPASS'] = $dbDetails['dbPassword'];
  $_SERVER['PHINX_DBHOST'] = $dbDetails['dbHost'];
  $_SERVER['PHINX_CONFIG_DIR'] = __DIR__ . '/../';
  echo "============= $action :".$dbDetails['dbName']." ==========\n";
  $wrap = new \Phinx\Wrapper\TextWrapper($app, array(
              // 'config_path' => __DIR__. '/../../../phinx.yml',
              'parser' => 'yaml'
  ));
  return $wrap;
}

/**
 * To execue a phinx command
 * @param  array            $command [description]
 * @param  PhinxApplication $app     [description]
 * @return [type]                    [description]
 */
function executeRun(array $command, PhinxApplication $app)
{
    // Output will be written to a temporary stream, so that it can be
    // collected after running the command.
    $stream = fopen('php://temp', 'w+');
    // Execute the command, capturing the output in the temporary stream
    // and storing the exit code for debugging purposes.
    $exit_code = $app->doRun(new ArrayInput($command), new StreamOutput($stream));
    // Get the output of the command and close the stream, which will
    // destroy the temporary file.
    $result = stream_get_contents($stream, -1, 0);
    fclose($stream);
    return $result;
}
/**
 * creates an entry in migration table with $version and $name
 * @param  string $version       version of the migration (timestamp prefix of the migration file)
 * @param  string $migrationName name of the migration
 * @param  array  $dbDetails     database connection details
 * @return boolean
 */
function fakeMigration($version, $migrationName, $dbDetails){
  $db = new mysqli($dbDetails['dbHost'], $dbDetails['dbUsername'], $dbDetails['dbPassword'], $dbDetails['dbName']);
  if($db->connect_errno > 0){
      die('Unable to connect to database [' . $db->connect_error . ']');
  }

  $createTable = "CREATE TABLE IF NOT EXISTS `db_migrations` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`))";
  $db->query($createTable);

  //TODO read migration table from .yml configuration
  $migrationTableName = 'db_migrations';
  $sql = "INSERT INTO `$migrationTableName`
  (`version`, `migration_name`, `start_time`, `end_time`)
  VALUES ('$version', '$migrationName', NOW(), NOW())";

  if(!$result = $db->query($sql)){
      echo("There was an error running the query [$db->error]\n");
      exit(0);
  }
  else
    return true;
}
