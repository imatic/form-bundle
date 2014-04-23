var imatic = imatic || {};
imatic.form = imatic.form || {};

/**
 * Init AJAX entity choice input field
 *
 * @param {HTMLElement} input
 */
imatic.form.initAjaxEntityChoice = function (input) {
    var config = $(input).data('imaticFormAjaxChoice');

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
                return {
                    filter: {search: {value: term}}
                };
            },
            results: function (data, page) {
                return {results: data};
            }
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
