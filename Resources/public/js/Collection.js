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
        this.options = processOptions($.extend({}, this.constructor.defaults, options));
        this.id = getUniqueCollectionId();
        this.idSequence = 0;
    }

    /**
     * Apply dynamic collection to the root container
     */
    apply() {
        // init root
        this.initCollection(this.$rootContainer);

        // init children
        this.getChildCollections(this.$rootContainer).each((i, elem) => {
            this.initCollection($(elem));
        });

        // init fields
        this.initFields(this.$rootContainer);

        // handle button clicks
        this.$rootContainer.on(
            'click',
            '.imatic-form-collection-btn',
            e => this.onButtonClick(e)
        );

        this.$rootContainer.on(
            'mouseenter mouseleave',
            '.imatic-form-collection-btn',
            e => this.onButtonHover(e)
        );
    }

    /**
     * Handle button click
     *
     * @param {MouseEvent} e
     */
    onButtonClick(e) {
        var $button = $(e.currentTarget);

        if (this.isAddButton($button)) {
            this.addItem(this.getCollectionForAddButton($button));
        } else if (this.isDeleteButton($button)) {
            this.deleteItem(this.getItemForDeleteButton($button));
        } else {
            throw new Error('Unrecognized button type');
        }

        e.stopPropagation();
        e.preventDefault();
    }

    /**
     * Handle button hover
     *
     * @param {MouseEvent} e
     */
    onButtonHover(e) {
        var $button = $(e.currentTarget);
        var $target;

        if (this.isAddButton($button)) {
            $target = this.getCollectionForAddButton($button)
        } else if (this.isDeleteButton($button)) {
            $target = this.getItemForDeleteButton($button);
        } else {
            throw new Error('Unrecognized button type');
        }

        $target['mouseenter' === e.type ? 'addClass' : 'removeClass']('imatic-form-collection-marker');
    }

    /**
     * @param {jQuery} $button
     * @returns {Boolean}
     */
    isAddButton($button) {
        return $button.is('.imatic-form-collection-add');
    }

    /**
     * @param {jQuery} $button
     * @returns {Boolean}
     */
    isDeleteButton($button) {
        return $button.is('.imatic-form-collection-delete');
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
            'new_' + this.id + (++this.idSequence)
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

        // init child collections
        this.getChildCollections($item).each((i, elem) => {
            this.initCollection($(elem));
        });

        // trigger event
        $item.trigger('added.imatic.form.collection');
    }

    /**
     * Find all nested collections
     *
     * @param {jQuery} $context
     * @returns {jQuery}
     */
    getChildCollections($context) {
        return $context.find('.imatic-form-collection');
    }

    /**
     * Find parent collection of the given add button
     *
     * @param {jQuery} $button
     * @returns {jQuery}
     */
    getCollectionForAddButton($button) {
        return $($button.parents('.imatic-form-collection')[0]);
    }

    /**
     * Find items of the given container
     *
     * @param {jQuery} $container
     * @returns {jQuery}
     */
    getItems($container) {
        return $container.children('.form-group:not(.imatic-form-collection-ambient)');
    }

    /**
     * Find parent item of the given delete button
     *
     * @param {jQuery} $container
     * @returns {jQuery}
     */
    getItemForDeleteButton($button) {
        return $($button.parents('.form-group')[0]);
    }

    /**
     * Initialize a collection
     *
     * @param {jQuery} $container
     */
    initCollection($container) {
        // create add button
        if ($container.data('allowAdd')) {
            Imatic.View.Html.render(this.options.addButtonTemplate, {
                classes: 'imatic-form-collection-btn imatic-form-collection-add',
                label: this.options.addButtonLabel,
            }).appendTo($container);
        }

        // create delete button for existing children
        if ($container.data('allowDelete')) {
            this.getItems($container).each((i, elem) => {
                this.addDeleteButtonToChild($(elem));
            });
        }
    }

    /**
     * Add delete button to the given child item
     *
     * @param {jQuery} $item
     */
    addDeleteButtonToChild($item) {
        Imatic.View.Html.render(this.options.deleteButtonTemplate, {
            classes: 'imatic-form-collection-btn imatic-form-collection-delete',
            label: this.options.deleteButtonLabel,
        }).appendTo($item);
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
        var fields = [];

        $context.find('*[id]').each((i, elem) => {
            var field = this.findInitializableField(elem);

            if (field) {
                fields.push({element: elem, field: field});
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

// default options
Collection.defaults = {
    addButtonLabel: 'Add',
    addButtonTemplate: '<a class="{{classes}} btn btn-default"><i class="fas fa-plus"></i> {{label}}</a>',
    deleteButtonLabel: 'Delete',
    deleteButtonTemplate: '<a class="{{classes}} btn btn-default"><i class="fas fa-trash-alt"></i> {{label}}</a>',
    buttonWrapperStyle: 'boostrap-horizontal',
};

/**
 * @param {Object} options
 * @returns {Object}
 */
function processOptions(options)
{
    // apply button wrapper style
    switch (options.buttonWrapperStyle) {
        // horizontal boostrap forms
        case 'bootstrap-horizontal':
            options.addButtonTemplate = '<div class="form-group row imatic-form-collection-ambient"><div class="col-sm-12">' + options.addButtonTemplate + '</div></div>';
            options.deleteButtonTemplate = '<div class="col-sm-2"></div><div class="col-sm-10 imatic-form-collection-inline-control">' + options.deleteButtonTemplate + '</div>';
            break;

        // generic bootstrap forms
        case 'bootstrap':
            options.addButtonTemplate = '<div class="form-group imatic-form-collection-ambient">' + options.addButtonTemplate + '</div>';
            options.deleteButtonTemplate = '<div class="imatic-form-collection-inline-control">' + options.deleteButtonTemplate + '</div>';
            break;
    }

    return options;
}

/**
 * @returns {Number}
 */
function getUniqueCollectionId()
{
    return new Date().getTime() + (++collectionIdSeq);
}

var collectionIdSeq = 0;
