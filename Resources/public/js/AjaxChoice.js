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
     * @param {Object} options
     */
    constructor($field, options) {
        this.$field = $field;
        this.options = options;
    }

    apply() {
        var select2Options = $.extend(
            {
                minimumInputLength: 1,
                placeholder: this.options.defaultPlaceholder,
                ajax: {
                    url: this.options.ajaxPath,
                    datatype: 'json',
                    delay: 300,
                    processResults: processResponseData,
                    data: (params) => {
                        return prepareRequestData(params.term, this.options.requestType);
                    },
                },
            },
            this.options.configs
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
