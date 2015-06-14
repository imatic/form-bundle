/**
 * AJAX choice module
 *
 * Dependencies: jQuery, Select2.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */

"use_strict";

var Imatic;
(function (Imatic, $) {
    (function (Form) {
        (function (AjaxChoice) {

            AjaxChoice.defaults = {
                multiple: false,
                placeholder: null,
                allowClear: false,
                initialValue: null,
                ajaxPath: '',
                ajaxRequestFormat: 'simple',
                formatSearching: 'Searching...',
                formatInputTooShort: 'Input too short',
                formatNoMatches: 'No matches'
            };

            /**
             * @param {String} term
             * @param {String} format
             * @returns {Object}
             */
            function prepareRequestData(term, format) {
                var data;

                switch (format) {
                    case 'filter':
                        data = {
                            filter: {search: {value: term}}
                        };
                        break;
                    case 'simple':
                        data = {search: term};
                        break;
                    default:
                        throw new Error('Invalid format');
                }

                return data;
            }

            /**
             * @param {Array}  data
             * @param {Number} page
             * @returns {Object}
             */
            function select2AjaxResults(data, page) {
                return {results: data};
            }

            /**
             * Init hidden input field
             *
             * @param {HTMLElement} input
             * @param {Object}      options
             */
            AjaxChoice.init = function (input, options) {
                // resolve configuration
                var config = $.extend(
                    {},
                    AjaxChoice.defaults,
                    options,
                    $(input).data('imaticFormAjaxChoice')
                );

                // apply select2
                $(input).select2({
                    multiple: config.multiple,
                    placeholder: config.placeholder,
                    allowClear: config.allowClear,
                    minimumInputLength: 1,
                    initSelection: function(element, callback) {
                        if(null !== config.initialValue) {
                            callback(config.initialValue);
                        }
                    },
                    ajax: {
                        url: config.ajaxPath,
                        datatype: 'json',
                        quietMillis: 300,
                        data: function(term, page) {
                            return prepareRequestData(
                                term,
                                config.ajaxRequestFormat
                            );
                        },
                        results: select2AjaxResults
                    },
                    formatSearching: function () {
                        return config.formatSearching;
                    },
                    formatInputTooShort: function (term, minLen) {
                        return config.formatInputTooShort.replace('%minlen%', minLen);
                    },
                    formatNoMatches: function (term) {
                        return config.formatNoMatches;
                    }
                });
            };

        })(Form.AjaxChoice || (Form.AjaxChoice = {}));
    })(Imatic.Form || (Imatic.Form = {}));
})(Imatic || (Imatic = {}), jQuery);
