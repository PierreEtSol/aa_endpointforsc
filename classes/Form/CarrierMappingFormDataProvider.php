<?php

namespace PrestaShop\Module\AaEndpointForSc\Form;

use PrestaShop\Module\AaEndpointForSc\Cache\LinkBlockCacheInterface;
use PrestaShop\Module\AaCarriersFooter\Model\CarrierFooter;
use PrestaShop\Module\AaEndpointForSc\Repository\CarrierMappingRepository;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class CarrierMappingFormDataProvider
 */
class CarrierMappingFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var int|null
     */
    private $idCarrierFooter;

    /**
     * @var CarrierFooterRepository
     */
    private $repository;

    /**
     * @var LinkBlockCacheInterface
     */
    private $moduleRepository;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var int
     */
    private $shopId;

    /**
     * LinkBlockFormDataProvider constructor.
     *
     * @param CarrierMappingRepository $repository
     * @param LinkBlockCacheInterface $cache
     * @param ModuleRepository $moduleRepository
     * @param array $languages
     * @param int $shopId
     */
    public function __construct(
        CarrierMappingRepository $repository,
        LinkBlockCacheInterface $cache,
        array $languages,
        $shopId
    ) {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->languages = $languages;
        $this->shopId = $shopId;
    }

    /**
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getData()
    {

        if (null === $this->idCarrierFooter) {
            return [];
        }

        $carrierFooter = new CarrierFooter($this->idCarrierFooter);

        $arrayCarrierFooter = $carrierFooter->toArray();

        return ['crossselling' => [
            'id_carrier_footer' => $arrayCarrierFooter['id_carrier_footer'],
            'name' => $arrayCarrierFooter['name'],
            'active' => $arrayCarrierFooter['active'],
        ]];
    }

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShop\PrestaShop\Adapter\Entity\PrestaShopDatabaseException
     */
    public function setData(array $data)
    {
        $carrierFooter = $data['carrier_footer'];
        $errors = $this->validateCarrierFooter($carrierFooter);
        if (!empty($errors)) {
            return $errors;
        }

        if (empty($carrierFooter['id_carrier_footer'])) {
            $carrierFooterId = $this->repository->create($carrierFooter);
            $this->setIdCarrierFooter($carrierFooterId);
        } else {
            $carrierFooterId = $carrierFooter['id_carrier_footer'];
            $this->repository->update($carrierFooterId, $carrierFooter);
        }

        $this->cache->clearModuleCache();
        return [];
    }

    /**
     * @return int
     */
    public function getIdCarrierFooter()
    {
        return $this->idCarrierFooter;
    }

    /**
     * @param int $idCarrierFooter
     *
     * @return CarrierFooterFormDataProvider
     */
    public function setIdCarrierFooter($idCarrierFooter)
    {
        $this->idCarrierFooter = $idCarrierFooter;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function validateCarrierFooter(array $data)
    {
        $errors = [];

        if (!isset($data['name'])) {
            $errors[] = [
                'key' => 'Missing block_name',
                'domain' => 'Admin.Catalog.Notification',
                'parameters' => [],
            ];
        }

        return $errors;
    }

}
