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

use Context;
use Db;
use Doctrine\DBAL\Exception as DBALException;
use Hook;
use Language;
use Configuration;
use Shop;
use FrontController;
use Product;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

/**
 * Class LegacyRepository.
 */
class LegacyRepository
{
    const LIMIT_FACTOR = 50;
    /**
     * @var Db
     */
    private $db;

    /**
     * @var Shop
     */
    private $shop;

    /**
     * @var string
     */
    private $db_prefix;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var Translator
     */
    private $idLang;

    /**
     * @param Db $db
     * @param Shop $shop
     * @param Translator $translator
     */
    public function __construct(Db $db, Shop $shop, Translator $translator, $idLang)
    {
        $this->db = $db;
        $this->shop = $shop;
        $this->db_prefix = $db->getPrefix();
        $this->translator = $translator;
        $this->idLang = $idLang;
    }

    /**
     * @return bool
     */
    public function createTables()
    {

        $engine = _MYSQL_ENGINE_;
        $success = true;
        $this->dropTables();
        $queries = [
            "CREATE TABLE IF NOT EXISTS `{$this->db_prefix}sendcloud_carrier`(
    		    `id_sc_carrier` int(10) unsigned NOT NULL auto_increment,
                `code` varchar(128) NOT NULL default '',
    			PRIMARY KEY (`id_sc_carrier`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->db_prefix}sendcloud_carrier_mapping`(
    		    `id_sc_carrier` int(10) unsigned NOT NULL,
                `id_ps_reference_carrier` int(10) unsigned NOT NULL,
    			PRIMARY KEY (`id_sc_carrier` ,  `id_ps_reference_carrier`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
        ];
        foreach ($queries as $query) {
            $success &= $this->db->execute($query);
        }

        return (bool) $success;
    }

    public function dropTables()
    {

        $sql = "DROP TABLE IF EXISTS
			`{$this->db_prefix}sendcloud_carrier`, `{$this->db_prefix}sendcloud_carrier_mapping`
			";

        return $this->db->execute($sql);
    }

}
