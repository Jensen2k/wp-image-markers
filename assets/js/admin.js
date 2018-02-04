jQuery(function ($) {

    'use strict';



    /**
     * Appcode
     */
    var wpim = {};

    var Marker = Backbone.Model.extend({
        defaults: {
            id: 1,
            valueType: 'char',
            value: '1',
            custom_hover: 0,
            radius: 100,
            image_hover: '',
            rotate: 0,
            available: '',
            background: {
                color: '#3F51B5',
                color_hover: '#888888',
            },
            position: {
                top: 10,
                left: 10
            },
            font: {
                size: 14,
                weight: 'bold',
                style: 'normal',
                color: '#fff',
                color_hover: '#fff',
            },
            size: {
                width: 30,
                height: 30,
                'line-height': 30
            },
            border: {
                width: 2,
                style: 'solid',
                color: '#fff',
                color_hover: '#fff'
            },
            shadow: {
                h: 0,
                v: 0,
                blur: 0,
                color: ''
            },
            infowindow: {
                datasource: 'paragraph',
                content: '',
                style: 'default',
                position: 'top',
                event: 'hover'
            }
        },
        idAttribute: 'id',
        initialize: function () {
            this.set('id', this.cid);
        }
    });

    var ImageModel = Backbone.Model.extend({
        defaults: {
            src: '',
            width: 'auto',
            height: 'auto',
            id: 0
        }
    });

    var MarkerCollection = Backbone.Collection.extend({
        model: Marker
    });

    var MarkerView = Backbone.View.extend({
        template: _.template($('#wpim_template_marker').html()),
        initialize: function () {},
        bindEvents: function () {

            this.$el = $('#wpim_marker_' + this.model.id);
            this.$icon = this.$el.find('.wpim-marker__icon');

            this.$edit = this.$el.find('.wpim-marker__edit');
            this.$copy = this.$el.find('.wpim-marker__copy');
            this.$remove = this.$el.find('.wpim-marker__remove');

            this.$el.draggable({
                containment: 'parent',
                handle: '.wpim-marker__icon',
                stop: _.bind(this.onDragStop, this)
            });

            this.$edit.on('click', _.bind(this.onEdit, this));
            this.$copy.on('click', _.bind(this.onCopy, this));
            this.$remove.on('click', _.bind(this.onRemove, this));
        },
        render: function () {
            return this.template(this.model.toJSON());
        },
        cssUnit: function (value) {

            if (value == '' || value == undefined) {
                value = 0;
            }

            var regex = /^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/g;
            var matches = regex.exec(value);
            if (matches != null) {

                value = matches[1];
                var unit = matches[2] == undefined ? 'px' : matches[2];
                value = value + unit;

            } else {
                value = parseFloat(value) + 'px';
            }

            return value;
        },
        renderCss: function () {

            var css = {
                'width': this.cssUnit(this.model.get('size').width),
                'height': this.cssUnit(this.model.get('size').height),
                'line-height': this.cssUnit(this.model.get('size')['line-height']),
                'border-radius': parseFloat(this.model.get('radius')) + '%',
                'border-width': this.cssUnit(this.model.get('border').width),
                'border-style': this.borderStyle(this.model.get('border').style),
                'border-color': this.model.get('border').color,
                'transform': 'rotate(' + this.model.get('rotate') + 'deg)'
            };

            if (this.model.get('valueType') !== 'image') {

                css['background-color'] = this.model.get('background').color;
                css['color'] = this.model.get('font').color;
                css['font-size'] = this.cssUnit(this.model.get('font').size);
                css['font-weight'] = this.cssUnit(this.model.get('font').weight);
                css['font-style'] = this.cssUnit(this.model.get('font').style);

            }

            if (this.model.get('shadow').color) {
                css['box-shadow'] = this.cssUnit(this.model.get('shadow').h) + ' ' + this.cssUnit(this.model.get('shadow').v) + ' ' + this.cssUnit(this.model.get('shadow').blur) + ' ' + this.model.get('shadow').color;
            }

            this.$icon.removeAttr('style');
            this.$icon.css(css);
            this.$icon.removeClass('wpim-marker__icon--zigzag');
            this.$icon.addClass('wpim-marker__icon--' + this.model.get('border').style);
            this.renderCssBorderArrow();

            this.$el.css({
                'left': this.cssUnit(this.model.get('position').left),
                'top': this.cssUnit(this.model.get('position').top)
            });

        },
        isArrow: false,
        borderStyle: function (style) {

            if (style == 'zigzag') {
                style = 'none';
            } else if ($.inArray(style, ['solid-arrow-top', 'solid-arrow-right', 'solid-arrow-bottom', 'solid-arrow-left']) > -1) {
                style = 'solid';
                this.isArrow = true;
            }

            return style;
        },
        renderCssBorderArrow: function () {
            if (this.isArrow) {

                var color = this.model.get('border').color;
                var size = parseFloat(this.model.get('border').width) * 3;

                switch (this.model.get('border').style) {
                    case 'solid-arrow-bottom':
                        this.$icon.find('.icon__arrow').css({
                            'border-left': size + 'px solid transparent',
                            'border-right': size + 'px solid transparent',
                            'border-top': size + 'px solid ' + color,
                            'left': 'calc(50% - ' + size + 'px)',
                            'top': '100%'
                        });
                        break;
                    case 'solid-arrow-top':
                        this.$icon.find('.icon__arrow').css({
                            'border-left': size + 'px solid transparent',
                            'border-right': size + 'px solid transparent',
                            'border-bottom': size + 'px solid ' + color,
                            'left': 'calc(50% - ' + size + 'px)',
                            'bottom': '100%'
                        });
                        break;
                    case 'solid-arrow-left':
                        this.$icon.find('.icon__arrow').css({
                            'border-top': size + 'px solid transparent',
                            'border-bottom': size + 'px solid transparent',
                            'border-right': size + 'px solid ' + color,
                            'top': 'calc(50% - ' + size + 'px)',
                            'right': '100%'
                        });
                        break;
                    case 'solid-arrow-right':
                        this.$icon.find('.icon__arrow').css({
                            'border-top': size + 'px solid transparent',
                            'border-bottom': size + 'px solid transparent',
                            'border-left': size + 'px solid ' + color,
                            'top': 'calc(50% - ' + size + 'px)',
                            'left': '100%'
                        });
                        break;
                }
            }
        },
        change: function () {

            var $newmarker = $(this.render());

            this.$icon.html($newmarker.find('.wpim-marker__icon').html());
            this.renderCss();

        },
        onEdit: function (e) {
            e.preventDefault();
            wpim.dialog.open(this);
        },
        onCopy: function (e) {

            e.preventDefault();

            var clone = this.model.clone();
            var position = clone.get('position');

            clone.set('position', {
                top: parseFloat(position.top) + 30,
                left: parseFloat(position.left) + 30
            });

            var markerView = new MarkerView({model: clone, panel: wpim.panel});
            wpim.panel.drawMarker(markerView);
        },
        onRemove: function (e) {

            e.preventDefault();

            wpim.collection.remove(this.model);
            wpim.panel.change();
            this.$el.remove();

        },
        onDragStop: function (e, ui) {
            this.model.set('position', ui.position);
            wpim.collection.add(this.model, {merge: true});
            wpim.panel.change();
        }
    });

    var DialogView = Backbone.View.extend({
        events: {},
        spinner: '<div class="dialog-empty"><span class="spinner is-active"></span></div>',
        initialize: function (args) {

            this.$el = $('#wpim_dialog').dialog({
                autoOpen: false,
                modal: true,
                width: 650,
                position: {my: "center", at: "center top", of: window},
                buttons: {
                    'Cancel': _.bind(this.onCancel, this),
                    'Save Changes': _.bind(this.onSave, this),
                },
                open: _.bind(this.onOpen, this),
                close: _.bind(this.onClose, this)
            });
        },
        bindEvents: function () {
            /**
             * Field Image Picker
             */
            this.$el.find('.wpim-image_picker').wpimImagePicker();


            /**
             * Field select multiple
             */

            this.$el.find('.wpim-select-multiple').selectize({
                plugins: ['remove_button', 'drag_drop']
            });

            this.$el.on('change', '.wpim-select-multiple', function () {
                $(this).closest('div').find('.wpim_value').val($(this).val()).trigger('change');
            });

            /**
             * Field icon picker
             */
            if (this.$el.find('div.simple-iconfonts-picker').length) {
                this.$el.find('div.simple-iconfonts-picker').trigger('simple-iconfonts-picker:update');
            }

            /**
             * Field Color
             */
            this.$el.find('.wpim-field-type-color_picker').wpColorPicker();


            /**
             * Text Group
             */
            this.$el.find('.wpim-text_group').wpimTextGroup();

            /**
             * Field Autocomplete
             */
            this.$el.find('.wpim-autocomplete select').wpimAutocomplete();

            this.$el.on('change', '.wpim-autocomplete select', function () {
                $(this).closest('div').find('.wpim_value').val($(this).val()).trigger('change');
            });

            /*
             * Triger selected
             */
            var $dialog = this.$el;
            this.$el.on('change', '#datasource', function () {
                $dialog.find('[data-param_name="post_type"] label').text('Select ' + $(this).val());
                $dialog.find('[data-param_name="post_type"] .wpim-autocomplete').attr('data-ajax_value', $(this).val());
            });

            this.$el.find('#datasource').change();

            /*
             * Textarea html
             */
            this.$el.find('.wpim-textarea_html textarea').wpimTextareaHtml();

            /*
             * Slider
             */
            this.$el.find('.wpim-slider').wpimSlider();

            /*
             * Radio
             */
            this.$el.find('.wpim-radio').wpimRadio();
            this.$el.find('.wpim-radio_image').wpimRadio();

            /**
             * Trigger image selected
             */

            var $size = this.$el.find('div[data-param_name="size"]');

            this.$el.find('div[data-param_name="image"] .wpim-image_picker').on('wpim_image_picker_selected', function (e, attachment, id) {
                var width, height = 0;

                if ($(e.target).attr('id') == id) {
                    if (attachment.sizes.hasOwnProperty('thumbnail')) {
                        width = attachment.sizes.thumbnail.width;
                        height = attachment.sizes.thumbnail.height;
                    } else if (attachment.sizes.hasOwnProperty('medium')) {
                        width = attachment.sizes.medium.width;
                        height = attachment.sizes.medium.height;
                    } else if (attachment.sizes.hasOwnProperty('full')) {
                        width = attachment.sizes.full.width;
                        height = attachment.sizes.full.height;
                    }

                    if (width && height) {
                        $size.find('[data-name="width"]').val(width).change();
                        $size.find('[data-name="height"]').val(height).change();
                    }
                }

            });

            this.$el.find('div[data-param_name="image"] .wpim-image_picker').on('wpim_image_picker_removed', function (e, index, id) {

                if ($(e.target).attr('id') == id) {
                    $size.find('[data-name="width"]').val(30).change();
                    $size.find('[data-name="height"]').val(30).change();
                }

            });

            var $avaiables = this.$el.find('div[data-param_name="available"] .wpim-radio_image li');

            this.$el.find('div[data-param_name="valueType"] .wpim-radio').on('change', 'input[type="radio"]', function (e) {

                var $this = $(this);

                var value = $this.val();

                $avaiables.hide();

                $avaiables.each(function () {
                    var group = $(this).data('group');
                    if (group.includes(value)) {
                        $(this).show();
                    }

                });

                e.preventDefault();
            });
            this.$el.find('div[data-param_name="valueType"] .wpim-radio li input:checked').change();

            var importMarker = _.bind(this.import, this);
            $avaiables.on('change', 'input[type="radio"]', function () {
                var $this = $(this);

                if (wpim_var.marker_availables.hasOwnProperty($this.val())) {
                    var data = wpim_var.marker_availables[$this.val()];
                    importMarker(data);
                }
            });



        },
        open: function (marker) {
            this.marker = marker;
            this.marker.$el.addClass('current-edit');
            $('#wp_image_markers_content .inside').addClass('locked');
            this.$el.dialog('open');
        },
        onClose: function (e) {
            $('#wp_image_markers_content .inside').removeClass('locked');
            this.marker.$el.removeClass('current-edit');
            this.$el.find('.wpim_dialog_inner').empty();
        },
        onSave: function (e) {

            var $dialog = this.$el;

            this.marker.model.set('valueType', $dialog.find('#valueType').val());
            this.marker.model.set('custom_hover', $dialog.find('#custom_hover').is(':checked') ? 1 : 0);
            this.marker.model.set('available', $dialog.find('#available').val());

            if (this.marker.model.get('valueType') === 'icon') {
                var $icon = $dialog.find('#icon');
                var value = $icon.find('#icon-type').val() + ' ' + $icon.find('#icon-icon').val();
                this.marker.model.set('value', value);
            } else {
                this.marker.model.set('value', $dialog.find('#' + this.marker.model.get('valueType')).val());
            }

            this.marker.model.set('image_hover', $dialog.find('#image_hover').val());

            this.marker.model.set('background', {
                color: $dialog.find('#color__background').val(),
                color_hover: $dialog.find('#color__background_hover').val(),
            });

            var border = JSON.parse($dialog.find('#border').val());
            border.color_hover = $dialog.find('#color__border_hover').val();

            this.marker.model.set('border', border);
            this.marker.model.set('shadow', JSON.parse($dialog.find('#box_shadow').val()));
            this.marker.model.set('size', JSON.parse($dialog.find('#size').val()));
            this.marker.model.set('position', JSON.parse($dialog.find('#position').val()));
            this.marker.model.set('radius', $dialog.find('#border_radius').val());
            this.marker.model.set('rotate', $dialog.find('#rotate').val());


            var font = JSON.parse($dialog.find('#font').val());
            font.color_hover = $dialog.find('#color__text_hover').val();
            this.marker.model.set('font', font);

            var datasource = $dialog.find('#datasource').val();
            var content = wp.editor.getContent('paragraph');

            if (datasource !== 'paragraph') {

                var $content = $dialog.find('[data-ajax_value="' + datasource + '"]');
                if ($content.length) {
                    content = $content.find('.wpim_value').val();
                }
            }

            this.marker.model.set('infowindow', {
                datasource: datasource,
                content: content,
                style: 'default',
                position: $dialog.find('#window_position').val(),
                event: $dialog.find('#window_event').val()
            });

            wpim.collection.add(this.marker.model, {merge: true});
            wpim.panel.change();

            this.marker.change();
            this.onCancel();


        },
        onOpen: function (e) {

            var $dialog = this.$el;

            var model = this.marker.model;

            var spinner = this.spinner;

            var event = _.bind(this.bindEvents, this);

            Backbone.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'wpim_dialog_open',
                    data: model.attributes,
                    nonce: $('#_wpim_nonce').val()
                },
                beforeSend: function () {
                    $dialog.find('.wpim_dialog_inner').html(spinner);
                },
                success: function (html) {

                    $dialog.find('.wpim_dialog_inner').html(html);

                    /**
                     * Bind event
                     */
                    event();

                    /**
                     * Init Dependency
                     */
                    if ($dialog.find('[data-dependency]').length) {
                        $dialog.find('[data-dependency]').wpimDependency();
                    }
                }
            });

        },
        onCancel: function (e) {
            this.$el.dialog('close');
        },
        import: function (data) {

            var type = this.$el.find('#valueType').val();
            if (type == 'char') {
                var $char = this.$el.find('#char');
                if ($char.val() == '') {
                    $char.val(data.value).change();
                }
            } else if (type == 'icon' && data.value) {
                var $icon = this.$el.find('#icon');
                var value = data.value.split(' ');
                if (value.length == 2) {
                    $icon.find('#icon-type').val(value[0]);
                    $icon.find('#icon-icon').val(value[1]);
                    if (this.$el.find('div.simple-iconfonts-picker').length) {
                        this.$el.find('div.simple-iconfonts-picker').trigger('simple-iconfonts-picker:update');
                    }
                }


            }

            this.$el.find('#custom_hover').prop('checked', data.custom_hover).change();

            this.$el.find('#color__background').val(data.background.color).change();
            this.$el.find('#color__background_hover').val(data.background.color_hover).change();


            this.$el.find('div[data-param_name="border_radius"] .wpim-slider__bar').slider('value', data.radius);
            this.$el.find('div[data-param_name="rotate"] .wpim-slider__bar').slider('value', data.rotate);

            var $border = this.$el.find('div[data-param_name="border"]');
            $border.find('[data-name="width"]').val(data.border.width).change();
            $border.find('[data-name="style"]').val(data.border.style).change();
            $border.find('[data-name="color"]').val(data.border.color).change();
            this.$el.find('#color__border_hover').val(data.border.color_hover).change();

            var $boxshadow = this.$el.find('div[data-param_name="box_shadow"]');
            $boxshadow.find('[data-name="h"]').val(data.shadow.h).change();
            $boxshadow.find('[data-name="v"]').val(data.shadow.v).change();
            $boxshadow.find('[data-name="blur"]').val(data.shadow.blur).change();
            $boxshadow.find('[data-name="color"]').val(data.shadow.color).change();

            var $size = this.$el.find('div[data-param_name="size"]');
            $size.find('[data-name="width"]').val(data.size.width).change();
            $size.find('[data-name="height"]').val(data.size.height).change();
            $size.find('[data-name="line-height"]').val(data.size['line-height']).change();

            var $font = this.$el.find('div[data-param_name="font"]');
            $font.find('[data-name="size"]').val(data.font.size).change();
            $font.find('[data-name="weight"]').val(data.font.weight).change();
            $font.find('[data-name="style"]').val(data.font.style).change();
            $font.find('[data-name="color"]').val(data.font.color).change();
            this.$el.find('#color__text_hover').val(data.font.color_hover).change();

        }

    });

    var ToolbarView = Backbone.View.extend({
        el: $('.wpim-toolbar'),
        events: {
            'click .js-wpim-add-marker': 'onAddMarker',
            'click .js-wpim-savechanges': 'onSaveChanges',
            'click .js-wpim-add-image': 'onAddImage',
            'click .wpim-shortcode input': 'onSelectShortcode',
            'click .wpim-shortcode button': 'onButtonSelectShortcode'
        },
        initialize: function () {

        },
        onAddMarker: function (e) {

            var index = wpim.collection.length + 1;

            var position = 60;

            if (index == 2) {
                position = 80;
            } else if (index > 2) {
                position = 25 * (index + 1);
            }

            var model = new Marker({
                value: index,
                position: {
                    top: position,
                    left: position
                }
            });

            var markerView = new MarkerView({model: model, panel: wpim.panel});
            wpim.panel.drawMarker(markerView);

            e.preventDefault();
        },
        onSaveChanges: function (e) {
            $('#publish').trigger('click');
            e.preventDefault();
        },
        onAddImage: function (e) {

            if (this.imageDialog) {
                this.imageDialog.open();
                return;
            }

            var imageDialog = this.imageDialog;

            this.imageDialog = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Use Image'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });

            this.imageDialog.on('select', _.bind(this.onSelectImage, this));
            this.imageDialog.open();

            e.preventDefault();
        },
        onSelectImage: function (e) {

            var selection = this.imageDialog.state().get('selection');

            var data = selection.toJSON();

            var imageModel = new ImageModel({
                src: data[0].url,
                width: data[0].width,
                height: data[0].height,
                id: data[0].id
            });

            wpim.image = new ImageView({model: imageModel});
            wpim.panel.drawImage(wpim.image);

        },
        onSelectShortcode: function (e) {
            var $this = $(e.target);
            $this.select();
        },
        onButtonSelectShortcode: function (e) {
            var $this = $(e.target);
            $this.prev('input').select();
        }
    });

    var PanelView = Backbone.View.extend({
        el: $('.wpim-layer'),
        initialize: function (args) {

            $('#adv-settings .columns-prefs-2 input').removeAttr('checked').change();
            $('#adv-settings .columns-prefs-1 input').prop('checked', true).change();
            $('#post-body').removeClass('columns-2').addClass('columns-1');

            this.$value = $('#_wpim_markers');

            if (this.$value.val() != '') {

                var panel = this;
                var data = JSON.parse(this.$value.val());

                if (data.hasOwnProperty('markers')) {
                    _.each(data.markers, function (item) {
                        var view = new MarkerView({model: new Marker(item)});
                        panel.drawMarker(view);
                    });
                }

                if (data.hasOwnProperty('image')) {
                    wpim.image = new ImageView({model: new ImageModel(data.image), panel: panel});
                    panel.drawImage(wpim.image);
                }
            }

        },
        drawImage: function (view) {
            this.$el.prepend(view.render());
            this.change();
        },
        drawMarker: function (view) {
            wpim.collection.add(view.model);
            this.$el.append(view.render());
            view.bindEvents();
            view.renderCss();
            this.change();
        },
        change: function () {
            var data = {
                image: wpim.hasOwnProperty('image') ? wpim.image.model.attributes : {},
                markers: wpim.collection.toJSON()
            };
            this.$value.val(JSON.stringify(data));
        },
    });

    var ImageView = Backbone.View.extend({
        type: 'image',
        initialize: function () {},
        render: function () {
            $('.wpim-image').remove();
            $('.wpim-layer').css({
                'width': this.model.get('width') + 'px',
                'height': this.model.get('height') + 'px'
            });


            return '<div class="wpim-image"><img src="' + this.model.get('src') + '" alt=""/></div>';
        }
    });

    wpim.collection = new MarkerCollection();
    wpim.panel = new PanelView();
    wpim.toolbar = new ToolbarView();
    wpim.dialog = new DialogView({panel: wpim.panel});

});