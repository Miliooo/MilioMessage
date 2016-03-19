<?php

namespace Read\Projector;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DbalProjectorFunctionalTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $connection = DriverManager::getConnection(array('driver' => 'pdo_sqlite', 'memory' => true));
        $schemaManager = $connection->getSchemaManager();
        $schema = $schemaManager->createSchema();
    }

    /**
     * @test
     */
    public function it_exists()
    {

    }

}
