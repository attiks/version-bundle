<?php

/**
 * NetBrothers VersionBundle
 *
 * @author Stefan Wessel, NetBrothers GmbH
 */

namespace NetBrothers\VersionBundle\Services\Sql;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Execute SQL on connection.
 * 
 * @package NetBrothers\VersionBundle\Services\Sql
 */
class ExecuteService
{
    private const SUPPORTED_PLATFORMS = [
        MariaDBPlatform::class,
        MySQLPlatform::class,
    ];

    private Connection $connection;

    /**
     * ExecuteService constructor.
     * @param EntityManagerInterface $entityManager
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $platform = $entityManager->getConnection()->getDatabasePlatform();
        if (! in_array(get_class($platform), self::SUPPORTED_PLATFORMS)) {
            echo sprintf(
                'WARNING: Unsupported database platform: %s',
                get_class($platform)
            ) . PHP_EOL;
        }
        $this->connection = $entityManager->getConnection();
    }

    /**
     * Executes the assembled queries.
     * 
     * @param array $sql SQL queries priorly generated by the `GenerateService`.
     * @return void 
     * @throws Exception 
     */
    public function execute(array $sql = []): void
    {
        if (empty($sql)) {
            return;
        }
        foreach ($sql as $query) {
            $this->connection->prepare($query)->executeQuery();
        }
    }
}
