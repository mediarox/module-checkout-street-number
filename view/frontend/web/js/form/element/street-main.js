/**
 * Copyright 2022 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * See LICENSE for license details.
 */

define([
    'Magento_Ui/js/form/element/abstract',
    'underscore'
], function (Abstract, _) {
    'use strict';

    return Abstract.extend({
        defaults: {
            modules: {
                originFirstStreetLine: '${ $.parentName }.street.0'
            },
            imports: {
                updateFirstStreetLine: '${ $.parentName }.street_number:value',
                streetNumber: '${ $.parentName }.street_number:value'
            }
        },
        
        updateFirstStreetLine: function (streetNumber) {
            if ('' !== this.value()) {
                this.originFirstStreetLine().value(this.value() + ' ' + streetNumber);
            }
        },

        /**
         * Defines if value has changed.
         *
         * @override
         * @returns {Boolean}
         */
        hasChanged: function () {
            var notEqual = this.value() !== this.initialValue;

            if (notEqual) {
                this.updateFirstStreetLine(this.streetNumber);
            }
            return !this.visible() ? false : notEqual;
        }
    });
});
