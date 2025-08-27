<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\AaEndpointForSc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use PrestaShop\Module\LinkList\Adapter\ObjectModelHandler;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class CarrierMappingRepository
 */
class CarrierMappingRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var TranslatorInterface
     */
    private $translator;


    /**
     * ColumnRepository constructor.
     *
     * @param Connection $connection
     * @param string $dbPrefix
     * @param array $languages
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        array $languages,
        TranslatorInterface $translator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->languages = $languages;
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        $errors = [];
        $engine = _MYSQL_ENGINE_;
        $this->dropTables();

        $queries = [
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}sendcloud_carrier`(
    		    `id_sc_carrier` int(10) unsigned NOT NULL auto_increment,
                `code` varchar(128) NOT NULL default '',
    			PRIMARY KEY (`id_sc_carrier`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}sendcloud_carrier_mapping`(
    		    `id_sc_carrier` int(10) unsigned NOT NULL,
                `id_ps_reference_carrier` int(10) unsigned
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
        ];

        foreach ($queries as $query) {

            try {
                $this->connection->executeQuery($query);
            } catch (DBALException $e) {
                $errors[] = [
                    'key' => json_encode($e->getMessage()),
                    'parameters' => [],
                    'domain' => 'Admin.Modules.Notification',
                ];
            }
        }

        return $errors;
    }


    /**
     * @return array
     */
    public function dropTables()
    {
        $errors = [];
        $tableNames = [
            'sendcloud_carrier',
            'sendcloud_carrier_mapping',
        ];
        foreach ($tableNames as $tableName) {
            $sql = 'DROP TABLE IF EXISTS ' . $this->dbPrefix . $tableName;
            try {
                $this->connection->executeQuery($sql);
            } catch (DBALException $e) {
                $errors[] = [
                    'key' => json_encode($e->getMessage()),
                    'parameters' => [],
                    'domain' => 'Admin.Modules.Notification',
                ];
            }
        }

        return $errors;
    }

    /**
     * @return array
     */
    public function getCarrierMapping()
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('sc.id_sc_carrier, sc.code, scm.id_ps_reference_carrier')
            ->from($this->dbPrefix . 'sendcloud_carrier', 'sc')
            ->innerJoin('sc', $this->dbPrefix . 'sendcloud_carrier_mapping', 'scm', 'sc.id_sc_carrier = scm.id_sc_carrier')
        ;

        $mapping = $qb->execute()->fetchAll();
        //var_dump($qb->getSQL());die;
        return $mapping;
    }

    /**
     * @return array
     */
    public function getPsCarrierName($idCarrierMapping)
    {

    }

    /**
     * @param array $data
     *
     * @return string
     *
     * @throws DatabaseException
     */
    public function createMappingRecord($id_sc_carrier, $id_ps_reference_carrier)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->insert($this->dbPrefix . 'sendcloud_carrier_mapping')
            ->values([
                'id_sc_carrier' => ':idScCarrier',
                'id_ps_reference_carrier' => ':idPsReferenceCarrier',
            ])
            ->setParameters([
                'idScCarrier' => $id_sc_carrier,
                'idPsReferenceCarrier' =>  $id_ps_reference_carrier
            ]);

        $this->executeQueryBuilder($qb, 'Mapping record creation error');

        return ;
    }

    /**
     * @return array
     */
    public function deleteMappingRecord($id_sc_carrier, $id_ps_reference_carrier)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->delete($this->dbPrefix .'sendcloud_carrier_mapping')
            ->andWhere('id_sc_carrier = :idScCarrier')
            ->andWhere('id_ps_reference_carrier = :idPsReferenceCarrier')
            ->setParameters([
                'idScCarrier' => $id_sc_carrier,
                'idPsReferenceCarrier' =>  $id_ps_reference_carrier
            ]);
        ;
        $this->executeQueryBuilder($qb, 'Mapping record Deletion error');
    }


    public function createSendCloudCarriers($sendCloudCarriers)
    {
        $qb = $this->connection->createQueryBuilder();
        foreach ($sendCloudCarriers as $sendCloudCarrier) {
            $qb
                ->insert($this->dbPrefix . 'sendcloud_carrier')
                ->values([
                    'id_sc_carrier' => ':idScCarrier',
                    'code' => ':code',
                ])
                ->setParameters([
                    'idScCarrier' => $sendCloudCarrier['id_sc_carrier'],
                    'code' =>  $sendCloudCarrier['code'],
                ]);
            $this->executeQueryBuilder($qb, 'Mapping creation error');

//            $qb
//                ->insert($this->dbPrefix . 'sendcloud_carrier_mapping')
//                ->values([
//                    'id_sc_carrier' => ':idScCarrier',
//                ])
//                ->setParameters([
//                    'idScCarrier' => $sendCloudCarrier['id_sc_carrier'],
//                ]);
//            $this->executeQueryBuilder($qb, 'Mapping creation error');
        }



        return;
    }

    /**
     * @return array
     */
    public function deleteSendCloudCarriers()
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->delete($this->dbPrefix .'sendcloud_carrier')
            ;
        ;
        $this->executeQueryBuilder($qb, 'Mapping Deletion error');
//        $qb
//            ->delete($this->dbPrefix .'sendcloud_carrier_mapping')
//        ;
//        ;
//        $this->executeQueryBuilder($qb, 'Mapping Deletion error');
    }

    /**
     * @param QueryBuilder $qb
     * @param string $errorPrefix
     *
     * @return Result|int|string
     *
     * @throws DatabaseException
     */
    private function executeQueryBuilder(QueryBuilder $qb, $errorPrefix = 'SQL error')
    {
        try {
            $statement = $qb->execute();
        } catch (DBALException $e) {
            throw new DatabaseException($errorPrefix . ': ' . var_export($e->getMessage(), true));
        }

        return $statement;
    }

}
