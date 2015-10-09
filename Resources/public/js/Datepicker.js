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
     * @param {jQuery} $field
     * @param {Object} options
     */
    constructor($field, options) {
        this.$field = $field;
        this.options = $.extend(true, {}, this.constructor.defaults, options);
    }

    apply() {
        var target = this.$field.parent('.input-group.date')[0] || this.$field[0];

        var format;
        if (this.options.pickDate && this.options.pickTime) {
            format = this.options.dateTimeFormat;
        } else if (this.options.pickDate) {
            format = this.options.dateFormat;
        } else if (this.options.pickTime) {
            format = this.options.timeFormat;
        }

        $(target).datetimepicker($.extend(
            {
                format: format,
                locale: this.options.defaultLocale,
                useCurrent: false,
                toolbarPlacement: 'bottom',
                showClose: true,
                showClear: true,
                showTodayButton: this.options.pickDate,
                widgetPositioning: {
                    horizontal: 'left',
                    vertical: 'auto',
                },
            },
            this.options.config
        ));
    }
}

// default options
Datepicker.defaults = {
    pickTime: false,
    pickDate: true,

    // https://eonasdan.github.io/bootstrap-datetimepicker/Options/
    config: {
        allowInputToggle: true,
        sideBySide: true,
    },

    // http://momentjs.com/docs/#/displaying/format/
    dateFormat: 'YYYY-MM-DD',
    dateTimeFormat: 'YYYY-MM-DD HH:mm',
    timeFormat: 'HH:mm',
};
