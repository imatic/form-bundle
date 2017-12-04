/**
 * Ajax choice
 *
 * Uses https://select2.github.io/
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
export default class AjaxChoice
{
    /**
     * @param {jQuery} $field
     * @param {String} ajaxPath
     * @param {String} requestType
     * @param {Object} select2Options
     */
    constructor($field, ajaxPath, requestType, select2Options) {
        this.$field = $field;
        this.ajaxPath = ajaxPath;
        this.requestType = requestType;
        this.select2Options = select2Options;
    }

    apply() {
        var select2Options = $.extend(
            {
                minimumInputLength: 1,
                ajax: {
                    url: this.ajaxPath,
                    datatype: 'json',
                    delay: 300,
                    processResults: processResponseData,
                    data: (params) => {
                        return prepareRequestData(params.term, this.requestType);
                    },
                },
                language: document.documentElement.getAttribute('lang') === 'cs'
                    ? require('select2/src/js/select2/i18n/cs.js')
                    : require('select2/src/js/select2/i18n/en.js')
            },
            this.select2Options
        );

        this.$field.select2(select2Options);
    }
}

/**
 * @param {String} term
 * @param {String} type
 * @returns {Object}
 */
function prepareRequestData(term, type) {
    var data;

    switch (type) {
        case 'filter':
            data = {filter: {search: {value: term}}};
            break;

        case 'simple':
            data = {search: term};
            break;

        default:
            throw new Error('Unsupported request type');
    }

    return data;
}

/**
 * @param {Array} data
 * @returns {Object}
 */
function processResponseData(data)
{
    return {results: data};
}
