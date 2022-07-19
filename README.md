# Checkout street number

## Description
### Short
Divides the first street line into street & house number.
### Long
We use a checkout [LayoutProcessor](https://devdocs.magento.com/guides/v2.4/howdoi/checkout/checkout_custom_checkbox.html) to find all street fields and insert the new fields "street_main" and "street_number" in front of them. 
Matching knockout components [street-main](https://github.com/mediarox/module-checkout-street-number/blob/main/view/frontend/web/js/form/element/street-main.js) and [street-number](https://github.com/mediarox/module-checkout-street-number/blob/main/view/frontend/web/js/form/element/street-number.js) exist, where [street-main](https://github.com/mediarox/module-checkout-street-number/blob/main/view/frontend/web/js/form/element/street-main.js) contains the logic for updating the original street field.
CSS is then used to hide the original first street field.

## Installation
```bash
composer require mediarox/module-checkout-street-number
bin/magento setup:upgrade
```

## Configuration

### Backend
SHOPS > Configuration > MEDIAROX > Checkout Street Number

### Shell
```bash
bin/magento config:set customer/address/split_street_into_name_and_number 1
```

## Before
![without_extension](https://user-images.githubusercontent.com/32567473/167380501-85011930-86fe-4a73-a86c-9567c9c92cd2.png)

## After
![with_extension](https://user-images.githubusercontent.com/32567473/167380518-b9fd92a0-6074-48a9-8ae9-ed9f5f36100a.png)

## Compatible with

* [amasty/module-single-step-checkout](https://amasty.com/one-step-checkout-for-magento-2.html), tested: 3.1.3, 4.0.0
* [mediarox/module-checkout-placeholder](https://github.com/mediarox/module-checkout-placeholder), tested: 0.4.2

## Notes
### General
The extension was created from various live projects with themes based on Luma. It is to be considered as "beta" and does not claim to be perfect. The main reason of the release is to share this module with others and improve it together.
### Helpful posts on the topic
* Magento 2 Doc [Add custom fields that influence other checkout fields](https://devdocs.magento.com/guides/v2.4/howdoi/checkout/checkout_custom_checkbox.html)
  * Different handling of shipping & billing addresses (dataScope's)
  * Understanding that the billing address can appear multiple times and in different places
* Magento 2 Doc [Linking properties of UI components](https://devdocs.magento.com/guides/v2.4/ui_comp_guide/concepts/ui_comp_linking_concept.html)
  * Understanding to import values from other components
* Magento 2 Doc [About the uiElement class](https://devdocs.magento.com/guides/v2.4/ui_comp_guide/concepts/ui_comp_uielement_concept.html)
  * Understanding linking objects from other components (modules: {})