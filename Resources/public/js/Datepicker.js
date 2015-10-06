/**
 * Datepicker
 *
 * Uses https://eonasdan.github.io/bootstrap-datetimepicker/
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
export default class Datepicker
{
    /**
     * @param {jQuery}  $field
     * @param {String}  locale
     * @param {Boolean} pickDate
     * @param {Boolean} pickTime
     * @param {Object}  options
     */
    constructor($field, locale, pickDate, pickTime, options) {
        if (!pickDate && !pickTime) {
            throw new Error('pickDate and pickTime cannot be both false');
        }

        this.$field = $field;
        this.locale = locale;
        this.pickDate = pickDate;
        this.pickTime = pickTime;
        this.options = options;
    }

    apply() {
        var target = this.$field.parent('.input-group.date')[0] || this.$field[0];

        var format;
        if (this.pickDate && this.pickTime) {
            format = dateTimeFormat;
        } else if (this.pickDate) {
            format = dateFormat;
        } else {
            format = timeFormat;
        }

        var options = {
            locale: this.locale,
            format: format,
            allowInputToggle: true,
            sideBySide: true
        };

        if (this.options) {
            $.extend(options, this.options);
        }

        $(target).datetimepicker(options);
    }
}

// http://momentjs.com/docs/#/displaying/format/
var dateFormat = 'YYYY-MM-DD';
var dateTimeFormat = 'YYYY-MM-DD HH:mm';
var timeFormat = 'HH:mm';
