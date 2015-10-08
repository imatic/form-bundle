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
        var options = this.options;

        this.$field.select2({
            multiple: options.multiple,
            allowClear: options.allowClear,
            placeholder: options.placeholder,
            minimumInputLength: 1,
            ajax: {
                url: options.ajaxPath,
                datatype: 'json',
                delay: 300,
                processResults: processResponseData,
                data: function (params) {
                    return prepareRequestData(params.term, options.requestType);
                },
            }
        });
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
