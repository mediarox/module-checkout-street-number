# Checkout street number

## Description
Adds a house number field to the checkout.

## Installation
```bash
composer require mediarox/module-checkout-street-number
bin/magento setup:upgrade
```
## Configuration

```bash
bin/magento config:set checkout_street_number/general/enable 1
```

## Before

![without_extension](https://user-images.githubusercontent.com/32567473/167380501-85011930-86fe-4a73-a86c-9567c9c92cd2.png)

## After

![with_extension](https://user-images.githubusercontent.com/32567473/167380518-b9fd92a0-6074-48a9-8ae9-ed9f5f36100a.png)