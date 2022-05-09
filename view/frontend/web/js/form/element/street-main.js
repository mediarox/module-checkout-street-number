/**
 * Copyright 2022 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * See LICENSE for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/form/element/abstract'
], function (_, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            modules: {
                street: '${ $.parentName }.street'
            },
            imports: {
                streetNumberIsChanged: '${ $.parentName }.street_number:value',
                streetNumber: '${ $.parentName }.street_number:value'
            }
        },

        /**
         * Method called every time street_number value gets changed.
         * @param {String} streetNumber - new street number.
         */
        streetNumberIsChanged: function (streetNumber) {
            this.updateFirstStreetLine(streetNumber);
        },
        
        updateFirstStreetLine: function (streetNumber) {
            this.street().elems.each(function (street, key) {
                let isFirstLine = key === 0;
                if(isFirstLine) {
                    this.street().elems()[key].value(this.value() + ' ' + streetNumber);
                }
            }.bind(this));
        },

        /**
         * Defines if value has changed.
         * 
         * @override
         * @returns {Boolean}
         */
        hasChanged: function () {
            var notEqual = this.value() !== this.initialValue;
            
            if(notEqual) {
                this.updateFirstStreetLine(this.streetNumber);
            }
            return !this.visible() ? false : notEqual;
        }
    });
});
