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
        this.$field.select2(this.select2Options);
    }
}
