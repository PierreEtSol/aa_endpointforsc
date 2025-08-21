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
     * @var bool
     */
    private $isMultiStoreUsed;

    /**
     * @var Context
     */
    private $multiStoreContext;

    /**
     * @var ObjectModelHandler
     */
    private $objectModelHandler;

    /**
     * @var idLang
     */
    private $idLang;

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
        TranslatorInterface $translator,
        bool $isMultiStoreUsed,
        Context $multiStoreContext,
        $idLang,
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->languages = $languages;
        $this->translator = $translator;
        $this->isMultiStoreUsed = $isMultiStoreUsed;
        //$this->objectModelHandler = $objectModelHandler;
        $this->multiStoreContext = $multiStoreContext;
        $this->idLang = $idLang;
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
    		    `id_sc_carrier` int(10) unsigned NOT NULL,
                `code` varchar(128) NOT NULL default '',
    			PRIMARY KEY (`id_sc_carrier`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->dbPrefix}sendcloud_carrier_mapping`(
    		    `id_sc_carrier` int(10) unsigned NOT NULL,
                `id_ps_reference_carrier` int(10) unsigned NOT NULL,
    			PRIMARY KEY (`id_sc_carrier` ,  `id_ps_reference_carrier`)
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
    public function getCategoryCrossSellingProducts($idCategory)
    {

        $qb = $this->connection->createQueryBuilder();

        $qb->select('ccp.id_product, pl.name, ccp.is_primary ')
            ->from($this->dbPrefix . 'crossselling_category_product', 'ccp')
            ->innerJoin('ccp', $this->dbPrefix . 'product_lang', 'pl', 'ccp.id_product = pl.id_product')
            ->andWhere('ccp.id_category = :categoryId')
            ->andWhere('pl.id_lang = :langId')
            ->setParameter('categoryId', $idCategory)
            ->setParameter('langId', $this->idLang)
        ;

        $products = $qb->execute()->fetchAll();
        //var_dump($products); die;
        return $products;
    }

}
