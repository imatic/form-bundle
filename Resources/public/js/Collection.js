/**
 * Form collection
 *
 * Implements adding and removing items.
 *
 * Also handles JS initialization for all fields.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
export default class Collection
{
    /**
     * @param {jQuery} $rootContainer
     * @param {Array}  initializableFields
     * @param {Object} options
     */
    constructor($rootContainer, initializableFields, options) {
        this.$rootContainer = $rootContainer;
        this.initializableFields = initializableFields;
        this.options = options;
        this.idSequence = 0;
    }

    /**
     * Apply dynamic collection to the root container
     */
    apply() {
        var that = this;

        // init root
        this.initCollection(this.$rootContainer);

        // init children
        this.getChildCollections().each(function () {
            that.initCollection($(this));
        });

        // init fields
        this.initFields(this.$rootContainer);

        // handle button clicks
        this.$rootContainer.on(
            'click',
            '.imatic-form-collection-btn',
            function (e) { that.onButtonClick(e); }
        );
    }

    /**
     * Handle button click
     *
     * @param {MouseEvent} e
     */
    onButtonClick(e) {
        var $button = $(e.target);

        if ($button.is('.imatic-form-collection-delete')) {
            this.deleteItem($($button.parents('.form-group')[0]));
        } else if ($button.is('.imatic-form-collection-add')) {
            this.addItem($($button.parents('.imatic-form-collection')[0]));
        } else {
            throw new Error('Unrecognized button type');
        }

        e.stopPropagation();
        e.preventDefault();
    }

    /**
     * Delete the given item
     *
     * @param {jQuery} $item
     */
    deleteItem($item) {
        $item
            .trigger('delete.imatic.form.collection')
            .remove()
        ;
    }

    /**
     * Add new item to the given container
     *
     * @param {jQuery} $container
     */
    addItem($container) {
        var templateHtml = $container.data('prototype');
        var prototypeName = $container.data('prototypeName');

        // create item from the prototype template
        var $item = $(templateHtml.replace(
            new RegExp(Imatic.View.RegExp.escape(prototypeName), 'g'),
            'new_' + (++this.idSequence)
        ));

        // insert after last item or prepend to the container
        var lastItem = this.getItems($container).last();
        if (lastItem.length > 0) {
            $item.insertAfter(lastItem);
        } else {
            $item.prependTo($container);
        }

        // create delete button
        if ($container.data('allowDelete')) {
            this.addDeleteButtonToChild($item);
        }

        // init fields in the newly created item
        this.initFields($item);

        // trigger event
        $item.trigger('added.imatic.form.collection');
    }

    /**
     * Find all nested collections
     *
     * @returns {jQuery}
     */
    getChildCollections() {
        return this.$rootContainer.find('.imatic-form-collection');
    }

    /**
     * Find items of the given container
     *
     * @param {jQuery} $container
     * @returns {jQuery}
     */
    getItems($container) {
        return $container.children('.form-group');
    }

    /**
     * Initialize a collection
     */
    initCollection($container) {
        var that = this;

        // create add button
        if ($container.data('allowAdd')) {
            $('<button class="imatic-form-collection-btn imatic-form-collection-add">' + Imatic.View.Html.escape(this.options.add_label) + '</button>').appendTo($container);
        }

        // create delete button for existing children
        if ($container.data('allowDelete')) {
            this.getItems($container).each(function () {
                that.addDeleteButtonToChild($(this));
            });
        }
    }

    /**
     * Add delete button to the given child item
     *
     * @param {jQuery} $item
     */
    addDeleteButtonToChild($item) {
        $('<button class="imatic-form-collection-btn imatic-form-collection-delete">' + Imatic.View.Html.escape(this.options.delete_label) + '</button>').appendTo($item);
    }

    /**
     * Initialize all form fields in the given context
     *
     * @param {jQuery} $context
     */
    initFields($context) {
        var results = this.findInitializableFields($context);

        for (var i = 0; i < results.length; ++i) {
            // apply initializable field's initializer to the matched element
            results[i].field.initializer(
                $(results[i].element)
            );
        }
    }

    /**
     * Find initializable form fields in the given context
     *
     * @param {jQuery} $context
     * @returns {Array}
     */
    findInitializableFields($context) {
        var that = this;

        var fields = [];

        $context.find('*[id]').each(function () {
            var field = that.findInitializableField(this);

            if (field) {
                fields.push({element: this, field: field});
            }
        });

        return fields;
    }

    /**
     * Find initializable field for the given element
     *
     * @param {HTMLElement} element
     * @returns {Object|null}
     */
    findInitializableField(element) {
        for (var i = 0; i < this.initializableFields.length; ++i) {
            if (this.getInitializableFieldRegExp(this.initializableFields[i]).test(element.id)) {
                return this.initializableFields[i];
            }
        }

        return null;
    }

    /**
     * Get field ID regex for the given field
     *
     * The generated RegExp object is cached in the field.
     *
     * @param {Object} field
     * @returns {RegExp}
     */
    getInitializableFieldRegExp(field)
    {
        if (!field.regexp) {
            var pattern = Imatic.View.RegExp.escape(field.id);

            pattern = pattern.replace(field.prototypeName, '((?:new_)?\\d+)');

            for (var i = 0; i < field.parentPrototypeNames.length; ++i) {
                pattern = pattern.replace(field.parentPrototypeNames[i], '((?:new_)?\\d+)');
            }

            field.regexp = new RegExp('^' + pattern + '$');
        }

        return field.regexp;
    }
}
