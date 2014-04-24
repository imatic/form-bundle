var imatic = imatic || {};
imatic.form = imatic.form || {};

/**
 * AJAX choice module
 * Depends on jQuery and Select2.
 * 
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
imatic.form.ajaxChoice = {
    /**
     * Default options
     */
    defaults: {
        multiple: false,
        placeholder: null,
        allowClear: false,
        initialValue: null,
        ajaxPath: '',
        ajaxRequestFormat: 'simple',
        formatSearching: 'Searching...',
        formatInputTooShort: 'Input too short',
        formatNoMatches: 'No matches'
    },

    /**
     * Init hidden input field
     *
     * @param {HTMLElement} input
     * @param {Object}      options
     */
    init: function (input, options) {
        // resolve configuration
        var config = $.extend(
            {},
            imatic.form.ajaxChoice.defaults,
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
                    return imatic.form.ajaxChoice._prepareRequestData(
                        term,
                        config.ajaxRequestFormat
                    );
                },
                results: imatic.form.ajaxChoice._select2AjaxResults
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
    },

    /**
     * @param {String} term
     * @param {String} format
     * @returns {Object}
     */
    _prepareRequestData: function(term, format) {
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
    },

    /**
     * @param {Array}  data
     * @param {Number} page
     * @returns {Object}
     */
    _select2AjaxResults: function (data, page) {
        return {results: data};
    }
};
