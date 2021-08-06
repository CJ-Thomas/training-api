<?php

namespace test\unit;

use com\linways\base\test\BaseTestCase;
use Faker\Factory;

class HelloWorldAPITestCase extends BaseTestCase
{
    protected $faker = null;
    protected function setUp()
    {

        $nucleusTestDBConfPath = __DIR__ . '/../db_conf/nucleus_db_conf.php';
        $amsDbForTest = __DIR__ . '/../db_conf/ams_db_conf.php';
        putenv("TEST_DB_CONFIG=$amsDbForTest");
        putenv("DB_PROFESIONAL_CONFIG=$amsDbForTest");
        putenv("NUCLEUS_CONF=$nucleusTestDBConfPath");
        $GLOBALS['userId'] = '100';
        $this->faker = Factory::create();
    }
}
