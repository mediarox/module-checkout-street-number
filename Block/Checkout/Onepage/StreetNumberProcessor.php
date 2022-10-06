<?php
/**
 * Copyright 2022 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Mediarox\CheckoutAddressStreetNumber\Block\Checkout\Onepage;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Mediarox\CheckoutAddressStreetNumber\Model\System\Config;

class StreetNumberProcessor implements LayoutProcessorInterface
{
    public const ADDRESS_TYPE_BILLING = 'billing';
    public const ADDRESS_TYPE_SHIPPING = 'shipping';

    public const KEY_MAIN = 'main';
    public const KEY_NUMBER = 'number';
    public const KEY_STREET = 'street';
    public const KEY_STREET_MAIN = self::KEY_STREET . '_' . self::KEY_MAIN;
    public const KEY_STREET_NUMBER = self::KEY_STREET . '_' . self::KEY_NUMBER;
    public const SORT_ORDER_POSITION_BEFORE = 'before';
    public const SORT_ORDER_POSITION_AFTER = 'after';

    public const KEY_SORT_ORDER = 'sortOrder';
    public const KEY_SORT_ORDER_DEPENDS_ON = 'sortOrderDependsOn';
    public const KEY_SORT_ORDER_POSITION = 'sortOrderPosition';
    public const KEY_SORT_ORDER_STEP_SIZE = 'sortOrderStepSize';
    public const KEY_LABEL = 'label';
    public const KEY_COMPONENT = 'component';
    public const KEY_PROVIDER = 'provider';
    public const KEY_VISIBLE = 'visible';
    public const KEY_INITIAL_VALUE = 'initialValue';
    public const KEY_VALIDATION = 'validation';
    public const KEY_CONFIG = 'config';
    public const KEY_TEMPLATE = 'template';
    public const KEY_DATA_SCOPE = 'dataScope';
    public const KEY_DATA_SCOPE_PREFIX = 'dataScopePrefix';
    public const KEY_CUSTOM_SCOPE = 'customScope';
    public const KEY_CHILDREN = 'children';
    public const KEY_ADDITIONAL_CLASSES = 'additionalClasses';

    public const FIELD_COMPONENT_PREFIX = 'Mediarox_CheckoutAddressStreetNumber/js/form/element/';
    public const FIELD_DEFAULT_TEMPLATE = 'ui/form/field';
    public const FIELD_DEFAULT_COMPONENT = 'Magento_Ui/js/form/element/abstract';
    public const FIELD_DEFAULT_PROVIDER = 'checkoutProvider';
    public const FIELD_DEFAULT_VISIBLE = true;
    public const FIELD_DEFAULT_INITIAL_VALUE = false;
    public const FIELD_DEFAULT_VALIDATION = ['required-entry' => true];
    public const FIELD_DEFAULT_SORT_ORDER = 100;
    public const FIELD_DEFAULT_SORT_ORDER_DEPENDS_ON = self::KEY_STREET;
    public const FIELD_DEFAULT_SORT_ORDER_POSITION = self::SORT_ORDER_POSITION_BEFORE;
    public const FIELD_DEFAULT_SORT_ORDER_STEP_SIZE = 1;

    public const LABEL_STREET_MAIN = 'Street';
    public const LABEL_STREET_NUMBER = 'Street Number';

    public const CSS_CLASS_HIDE_STREET_FIELDSET = 'hide-street-fieldset';

    public const COMPONENT_STREET_MAIN = self::FIELD_COMPONENT_PREFIX . self::KEY_STREET . '-' . self::KEY_MAIN;
    public const COMPONENT_STREET_NUMBER = self::FIELD_COMPONENT_PREFIX . self::KEY_STREET . '-' . self::KEY_NUMBER;

    public const STREET_PATH = ArrayManager::DEFAULT_PATH_DELIMITER . self::KEY_STREET;
    public const DATA_SCOPE_ADDRESS_SHIPPING = 'shippingAddress';

    public const ADDITIONAL_FIELDS = [
        self::KEY_STREET_MAIN => [
            self::KEY_LABEL => self::LABEL_STREET_MAIN,
            self::KEY_COMPONENT => self::COMPONENT_STREET_MAIN,
            self::KEY_SORT_ORDER_STEP_SIZE => 2
        ],
        self::KEY_STREET_NUMBER => [
            self::KEY_LABEL => self::LABEL_STREET_NUMBER,
            self::KEY_COMPONENT => self::COMPONENT_STREET_NUMBER,
            self::KEY_SORT_ORDER_DEPENDS_ON => self::KEY_STREET_MAIN,
            self::KEY_SORT_ORDER_POSITION => self::SORT_ORDER_POSITION_AFTER
        ]
    ];

    public const FIELD_DEFAULT_DATA = [
        self::KEY_CONFIG => [
            self::KEY_TEMPLATE => self::FIELD_DEFAULT_TEMPLATE
        ],
        self::KEY_COMPONENT => self::FIELD_DEFAULT_COMPONENT,
        self::KEY_PROVIDER => self::FIELD_DEFAULT_PROVIDER,
        self::KEY_VISIBLE => self::FIELD_DEFAULT_VISIBLE,
        self::KEY_INITIAL_VALUE => self::FIELD_DEFAULT_INITIAL_VALUE,
        self::KEY_VALIDATION => self::FIELD_DEFAULT_VALIDATION
    ];

    /**
     * Possible address path's
     *
     * 'steps/children/shipping-step/children/shippingAddress/children/shipping-address-fieldset/children'
     * 'steps/children/billing-step/children/payment/children/afterMethods/children/billing-address-form/children/form-fields/children'
     */
    public const ADDRESS_LIST_SEARCH_KEYS = [
        self::ADDRESS_TYPE_BILLING => 'form-fields/children',
        self::ADDRESS_TYPE_SHIPPING => 'shipping-address-fieldset/children'
    ];

    public const BILLING_REMOVE_PATH = '/children/form-fields/children';

    protected ArrayManager $arrayManager;
    protected Config $config;

    public function __construct(
        ArrayManager $arrayManager,
        Config $config
    ) {
        $this->arrayManager = $arrayManager;
        $this->config = $config;
    }

    /**
     * Search and handle all street field's.
     */
    private function injectAdditionalStreetFields(array $jsLayout): array
    {
        foreach ($this->arrayManager->findPaths(self::KEY_STREET, $jsLayout) as $streetPath) {
            $addressFieldsetPath = $this->removeFromStringEnd($streetPath, self::STREET_PATH);
            foreach ($this->detectAddressType($addressFieldsetPath) as $type) {
                foreach ($this->detectDataScope(
                    $addressFieldsetPath,
                    $type,
                    $jsLayout
                ) as $dataScope => $addressFieldset) {
                    foreach (self::ADDITIONAL_FIELDS as $fieldName => $fieldData) {
                        $addressFieldset = $this->addFieldToFieldset(
                            $fieldName,
                            $fieldData,
                            $addressFieldset,
                            $dataScope
                        );
                    }
                    $jsLayout = $this->arrayManager->merge($addressFieldsetPath, $jsLayout, $addressFieldset);
                }
            }
        }
        return $jsLayout;
    }

    public function detectDataScope(string $path, string $type, array $jsLayout): \Generator
    {
        switch ($type) {
            case self::ADDRESS_TYPE_BILLING:
                $billingFieldset = $this->arrayManager->get($path, $jsLayout);
                $billingControl = $this->arrayManager->get(
                    $this->removeFromStringEnd($path, self::BILLING_REMOVE_PATH),
                    $jsLayout
                );
                if ($dataScope = $billingControl[self::KEY_DATA_SCOPE_PREFIX] ?? false) {
                    yield $dataScope => $billingFieldset;
                }
                break;
            case self::ADDRESS_TYPE_SHIPPING:
                yield self::DATA_SCOPE_ADDRESS_SHIPPING => $this->arrayManager->get($path, $jsLayout);
                break;
            default:
                break;
        }
    }

    public function detectAddressType(string $path): \Generator
    {
        foreach (self::ADDRESS_LIST_SEARCH_KEYS as $type => $key) {
            if ($this->stringContains($path, $key)) {
                yield $type;
            }
        }
    }

    public function stringContains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }

    public function removeFromStringEnd(string $path, string $part)
    {
        return substr($path, 0, (0 - strlen($part)));
    }

    private function addFieldToFieldset(
        string $fieldName,
        array $fieldData,
        array $addressFieldset,
        string $dataScope
    ): array {
        $fieldData = array_replace_recursive(self::FIELD_DEFAULT_DATA, $fieldData);
        $fieldData[self::KEY_SORT_ORDER] = $this->getSortOrder($fieldData, $addressFieldset);
        $fieldData[self::KEY_CONFIG][self::KEY_CUSTOM_SCOPE] = $dataScope;
        $fieldData[self::KEY_DATA_SCOPE] = $dataScope . '.' . $fieldName;
        if ($fieldLabel = $fieldData[self::KEY_LABEL] ?? false) {
            $fieldData[self::KEY_LABEL] = __($fieldLabel);
        }
        if ($this->hasOnlyOneChild($addressFieldset[self::KEY_STREET][self::KEY_CHILDREN])) {
            $fieldData[self::KEY_ADDITIONAL_CLASSES] = self::CSS_CLASS_HIDE_STREET_FIELDSET;
        }
        $addressFieldset[$fieldName] = $fieldData;
        return $addressFieldset;
    }

    private function hasOnlyOneChild(array $children): bool
    {
        return 1 === (int)count($children);
    }

    private function getSortOrder(array $fieldData, array $addressFieldset): int
    {
        $dependsOn = $fieldData[self::KEY_SORT_ORDER_DEPENDS_ON] ?? self::FIELD_DEFAULT_SORT_ORDER_DEPENDS_ON;
        $parent = $addressFieldset[$dependsOn][self::KEY_SORT_ORDER] ?? self::FIELD_DEFAULT_SORT_ORDER;
        $position = $fieldData[self::KEY_SORT_ORDER_POSITION] ?? self::FIELD_DEFAULT_SORT_ORDER_POSITION;
        $stepSize = $fieldData[self::KEY_SORT_ORDER_STEP_SIZE] ?? self::FIELD_DEFAULT_SORT_ORDER_STEP_SIZE;
        $moveBefore = $position === self::SORT_ORDER_POSITION_BEFORE;
        return $moveBefore ? $parent - $stepSize : $parent + $stepSize;
    }

    /**
     * Processor init.
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        return $this->config->getEnable() ? $this->injectAdditionalStreetFields($jsLayout) : $jsLayout;
    }
}
