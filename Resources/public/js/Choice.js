/**
 * Choice
 *
 * Uses https://select2.github.io/
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
export default class Choice
{
    /**
     * @param {jQuery} $field
     * @param {Object} select2Options
     */
    constructor($field, select2Options) {
        this.$field = $field;
        this.select2Options = select2Options;
    }

    apply() {
        var select2Options = $.extend(
            {
                language: document.documentElement.getAttribute('lang') === 'cs'
                    ? require('select2/src/js/select2/i18n/cs.js')
                    : require('select2/src/js/select2/i18n/en.js')
            },
            this.select2Options
        )
        this.$field.select2(select2Options);
    }
}
