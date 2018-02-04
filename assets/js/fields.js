jQuery(function ($) {
    'use strict';

    var $document = $(document);

    var file_frames = {};

    $.fn.wpimImagePicker = function () {
        
        var $el = $(this);
        
        var get_ids = function (input_value) {
            var ids = [];
            if (input_value != '') {
                var arr = input_value.split(',');
                for (var i in arr) {
                    var obj = arr[i].split('|');
                    ids.push(obj[0]);
                }
            }
            return ids;
        }
        
        $el.on('click', '.add_images', function (e) {

            e.preventDefault();

            var $this = $(this);
            var $field = $this.closest('.wpim-image_picker');
            var $input = $field.find('input[type="hidden"]');

            if (file_frames[$field.attr('id')]) {
                file_frames[$field.attr('id')].open();
                return;
            }

            file_frames[$field.attr('id')] = wp.media.frames.file_frame = wp.media({
                title: 'Choose Images',
                button: {
                    text: 'Use Images'
                },
                library: {
                    type: 'image'
                },
                multiple: $field.data('multiple')
            });

            file_frames[$field.attr('id')].on('open', function () {

                var ids, selection;
                ids = get_ids($input.val());
                if ('' != ids) {
                    selection = file_frames[$field.attr('id')].state().get('selection');
                    $(ids).each(function (index, element) {
                        var attachment;
                        attachment = wp.media.attachment(element);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });
                }
            });

            file_frames[$field.attr('id')].on('select', function () {

                var result, selection;
                result = [];
                selection = file_frames[$field.attr('id')].state().get('selection');
                var ids = get_ids($input.val());

                var item = '';

                selection.map(function (attachment) {

                    attachment = attachment.toJSON();

                    var src = attachment.sizes.hasOwnProperty('thumbnail') ? attachment.sizes.thumbnail.url : attachment.url;

                    if (ids == '' || $.inArray(attachment.id.toString(), ids) === -1) {

                        item += '<li class="added" data-id="' + attachment.id + '">\n\
                                    <div class="inner">\n\
                                        <img alt="' + attachment.title + '" src="' + src + '"/>\n\
                                    </div>\n\
                                    <a href="#" class="remove"></a>\n\
                                </li>';

                        src = src.replace(wpim_var.upload_url, '');
                        result.push(attachment.id + '|' + encodeURIComponent(src));

                        $el.trigger('wpim_image_picker_selected', [attachment, $field.attr('id')]);
                    }

                });


                if (result.length > 0) {

                    if ($field.data('multiple')) {

                        if (ids != '') {
                            result = ids.concat(result);
                        }

                        $field.find('.image_list').append(item);

                    } else {

                        $field.find('.image_list').html(item);

                    }

                    $input.val(result).change();
                }
            });

            file_frames[$field.attr('id')].open();
        });

        $el.on('click', '.remove', function (e) {
            e.preventDefault();

            var $this = $(this);
            var $input = $this.closest('.wpim-image_picker').find('input[type="hidden"]');
            var ids = $input.val();
            var index = $this.closest('li').index();
            var $field = $this.closest('.wpim-image_picker');

            if (ids != '') {

                ids = ids.split(',');
                delete ids[index];

                ids = ids.filter(function (val) {

                    return val;
                });
            }

            $el.trigger('wpim_image_picker_removed', [index, $field.attr('id')]);

            $input.val(ids).change();
            $this.closest('li').remove();
        });
        
        if ($.fn.sortable) {
            
            $el.find('.image_list').sortable({
                stop: function (e, ui) {
                    var ids = [];
                    var $list = $(ui.item[0]).parent();
                    $list.find('li').each(function () {
                        ids.push($(this).attr('data-id'));
                    });
                    $list.closest('.wpim-image_picker').find('input[type="hidden"]').val(ids);
                }
            });
            
        }
    }

    $.fn.wpimTextGroup = function () {

        $(this).each(function () {

            var $field = $(this);

            $field.on('change', '.label input, .label select', function (e) {
                var $this = $(this);
                var $input = $field.find('input[type="hidden"]');
                var obj = $input.val() != '' ? JSON.parse($input.val()) : {};

                obj[$this.data('name')] = $this.val();
                $input.val(JSON.stringify(obj));
                e.preventDefault();
            });
            
        });

        $(this).find('input[data-name="color"]').wpColorPicker({
            change: function (e, ui) {
                $(e.target).val(ui.color.toString()).change();
            }
        });

    }

    $.fn.wpimLink = function () {

        $document.on('click', '.wpim-link .link_button', function (e) {

            e.preventDefault();
            var $block, $input, $url_label, $title_label, value_object, $link_submit, $wpim_link_submit, $wpim_link_nofollow, dialog;
            $block = $(this).closest(".wpim-link");
            $input = $block.find("input.wpim_value");
            $url_label = $block.find(".url-label");
            $title_label = $block.find(".title-label");
            value_object = $input.data("json");
            $link_submit = $("#wp-link-submit");
            $wpim_link_submit = $('<input type="button" name="wpim_link-submit" id="wpim_link-submit" class="button-primary" value="Set Link">');
            $link_submit.hide();
            $("#wpim_link-submit").remove();
            $wpim_link_submit.insertBefore($link_submit);
            $wpim_link_nofollow = $('<div class="link-target wpim-link-nofollow"><label><span></span> <input type="checkbox" id="wpim-link-nofollow"> Add nofollow option to link</label></div>');
            $("#link-options .wpim-link-nofollow").remove();
            $wpim_link_nofollow.insertAfter($("#link-options .link-target"));
            setTimeout(function () {
                var currentHeight = $("#most-recent-results").css("top");
                $("#most-recent-results").css("top", parseInt(currentHeight) + $wpim_link_nofollow.height())
            }, 200);
            dialog = window.wpLink;
            dialog.open('content');

            if (typeof value_object.url == 'string' && $("#wp-link-url").length) {
                $("#wp-link-url").val(value_object.url);
            } else {
                $("#url-field").val(value_object.url);
            }

            if (typeof value_object.url == 'string' && $("#wp-link-text").length) {
                $("#wp-link-text").val(value_object.title);
            } else {
                $("#link-title-field").val(value_object.title);
            }

            if ($("#wp-link-target").length) {

                $("#wp-link-target").prop("checked", value_object.target.length);
            } else {
                $("#link-target-checkbox").prop("checked", value_object.target.length);
            }

            if ($("#wpim-link-nofollow").length) {
                $("#wpim-link-nofollow").prop("checked", value_object.rel.length);
            }


            $wpim_link_submit.unbind("click.wpimLink").bind("click.wpimLink", function (e) {

                e.preventDefault();
                e.stopImmediatePropagation();
                var string, options = {};
                options.url = $("#wp-link-url").length ? $("#wp-link-url").val() : $("#url-field").val();
                options.title = $("#wp-link-text").length ? $("#wp-link-text").val() : $("#link-title-field").val();
                var $checkbox = $($("#wp-link-target").length ? "#wp-link-target" : "#link-target-checkbox");
                options.target = $checkbox[0].checked ? " _blank" : "";
                options.rel = $("#wpim-link-nofollow")[0].checked ? "nofollow" : "";

                string = $.map(options, function (value, key) {
                    return typeof value == 'string' && 0 < value.length ? key + ":" + encodeURIComponent(value) : void 0
                }).join("|");

                $input.val(string).change();
                $input.data("json", options);
                $url_label.html(options.url + options.target);
                $title_label.html(options.title);
                dialog.close('noReset');
                window.wpLink.textarea = "";
                $link_submit.show();
                $wpim_link_submit.unbind("click.wpimLink");
                $wpim_link_submit.remove();
                $("#wp-link-cancel").unbind("click.wpimLink");
                $checkbox.attr("checked", false);
                $("#most-recent-results").css("top", "");
                $("#wpim-link-nofollow").attr("checked", false);
                return false;
            });
            $("#wp-link-cancel").unbind("click.wpimLink").bind("click.wpimLink", function (e) {
                e.preventDefault();
                dialog.close('noReset');
                $wpim_link_submit.unbind("click.wpimLink");
                $wpim_link_submit.remove();
                $("#wp-link-cancel").unbind("click.wpimLink");
                $("#wp-link-close").unbind("click.wpimCloseLink");
                window.wpLink.textarea = "";
                return false;
            });
            $('#wp-link-close').unbind('click').bind('click.wpimCloseLink', function (e) {
                e.preventDefault();
                dialog.close('noReset');
                $wpim_link_submit.unbind("click.wpimLink");
                $wpim_link_submit.remove();
                $("#wp-link-cancel").unbind("click.wpimLink");
                $("#wp-link-close").unbind("click.wpimCloseLink");
                window.wpLink.textarea = "";
                return false;
            });
        });
    }

    $.fn.wpimDependency = function () {

        $(this).each(function () {

            var dependency = JSON.parse($(this).attr('data-dependency'));

            var param_name = $(this).data('param_name');

            if (dependency != undefined && typeof dependency == 'object') {

                var el = Object.keys(dependency);

                $.each(el, function (index, key) {
                    dependency['#' + key] = dependency[key];
                    delete(dependency[key]);
                });

                var $slave = $('[data-param_name="' + param_name + '"]');

                if ($slave.length) {

                    $slave.wpimDependsOn(dependency);
                }
            }

        });

    }

    $.fn.wpimAutocomplete = function () {

        return $(this).selectize({
            valueField: 'value',
            searchField: 'label',
            labelField: 'label',
            options: [],
            create: false,
            render: {
                option: function (item, escape) {
                    return '<div class="option" data-value="' + item.value + '">#' + item.value + ' - ' + escape(item.label) + '</div>';
                }
            },
            load: function (query, callback) {

                var $container = $(this)[0].$wrapper.closest('.wpim-field');

                var min_length = $container.data('min_length');

                if (query.length < parseInt(min_length))
                    return callback();

                var type = $container.attr('data-ajax_type');

                var values = $container.attr('data-ajax_value');

                $.ajax({
                    url: ajaxurl,
                    type: 'GET',
                    data: {
                        action: 'wpim_autocomplete_' + type,
                        types: values,
                        s: query
                    },
                    error: function () {
                        callback();
                    },
                    success: function (data) {
                        console.log(data);
                        callback(data);
                    }
                });
            }
        });
    };

    /**
     * Group Tabs
     */
    $document.on('click', '.wpim_group .group_nav a', function (e) {

        var $this = $(this);
        var id = $this.attr('href');

        $this.closest('ul').find('.active').removeClass('active');
        $this.addClass('active');

        $('.wpim_group .group_item.active').removeClass('active');

        var $panel = $('.wpim_group ' + id);
        $panel.addClass('active');

        $document.trigger('wpim_group_active', [$panel]);

        e.preventDefault();
    });

    $.fn.wpimTextareaHtml = function () {

        var $element = $(this);

        var id = $element.attr('id');

        if (tinymce.get(id)) {
            wp.editor.remove(id);
        }

        wp.editor.initialize(id, {
            tinymce: {
                wpautop: true
            },
            quicktags: true
        });


    }

    $.fn.wpimSlider = function () {

        var $el = $(this);

        $el.each(function () {

            var $this = $(this);

            var $label = $this.find('.wpim-slider__label');

            var $bar = $this.find('.wpim-slider__bar');

            var $input = $this.find('input.wpim_value');

            var data = $this.data('options');

            $bar.slider({
                range: data.range,
                min: data.min,
                max: data.max,
                value: data.value,
                step: data.step,
                slide: function (event, ui) {
                    $label.text(data.label + ui.value + data.unit);
                },
                change: function (e, ui) {
                    $label.text(data.label + ui.value + data.unit);
                    $input.val(ui.value).change();
                }
            });
        });
    }
    
    $.fn.wpimRadio = function () {

        var $el = $(this);

        $el.on('change', 'input[type="radio"]', function (e) {
            var $this = $(this);
            var $ul = $this.closest('ul');
            $ul.find('input').removeAttr('checked');
            $this.attr('checked', 'checked');

            $ul.closest('.wpim-field').find('.wpim_value').val($this.val()).trigger('change');
            e.preventDefault();
        });
        
    }
});