<?php
/**
 * Copyright 2022 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Mediarox\CheckoutAddressStreetNumber\Model\System;

interface ConfigInterface
{
    const SYSTEM_CONFIG_BASE_PATH = 'checkout/options/';
    const SYSTEM_CONFIG_KEY_ENABLE = 'split_street_into_name_and_number';
}
