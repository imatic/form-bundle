import * as moment from 'moment';

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

        var datepickerOptions = $.extend(
            {
                format: format,
                locale: this.options.defaultLocale,
                showTodayButton: this.options.pickDate,
            },
            this.options.config
        );
        
        for (var locale in this.options.configLocale) {
            moment.updateLocale(locale, this.options.configLocale[locale]);
        }

        $(target).datetimepicker(datepickerOptions);
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
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'auto',
        },
        toolbarPlacement: 'bottom',
        showClose: true,
        showClear: true,
        useCurrent: false,
    },

    // https://momentjs.com/docs/#/customization/
    configLocale: {},

    // http://momentjs.com/docs/#/displaying/format/
    dateFormat: 'YYYY-MM-DD',
    dateTimeFormat: 'YYYY-MM-DD HH:mm',
    timeFormat: 'HH:mm',
};
