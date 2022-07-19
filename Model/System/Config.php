<?php
/**
 * Copyright 2022 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Mediarox\CheckoutAddressStreetNumber\Model\System;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config implements ConfigInterface
{
    protected StoreManagerInterface $storeManager;
    protected ScopeConfigInterface $scopeConfig;
    protected Json $json;

    public function __construct(StoreManagerInterface $storeManager, ScopeConfigInterface $scopeConfig, Json $json)
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
    }

    public function getConfigValue(string $key, string $basePath = self::SYSTEM_CONFIG_BASE_PATH)
    {
        return $this->scopeConfig->getValue(
            $basePath . $key,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }

    public function getEnable(): int
    {
        return (int)$this->getConfigValue(self::SYSTEM_CONFIG_KEY_ENABLE);
    }
}
