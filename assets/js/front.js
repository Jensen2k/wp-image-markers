jQuery(function ($) {
    'use strict';

    if ($('.wpim').length) {

        $('.wpim').on('click', '.wpim-marker--click .wpim-marker__icon', function (e) {

            var $marker = $(this).closest('.wpim-marker');
            var $parent = $marker.closest('.wpim');

            if ($marker.hasClass('focus')) {
                return;
            }

            $parent.find('.focus').removeClass('focus');
            $marker.addClass('focus');

            e.preventDefault();
        });

        $('.wpim').on('mouseover', '.wpim-marker--hover .wpim-marker__icon', function (e) {

            var $marker = $(this).closest('.wpim-marker');
            var $parent = $marker.closest('.wpim');

            if ($marker.hasClass('focus')) {
                return;
            }

            $parent.find('.focus').removeClass('focus');
            $marker.addClass('focus');

            e.preventDefault();
        });

        $('.wpim').on('click', '.wpim-infowindow__close', function (e) {

            var $this = $(this);

            $this.closest('.wpim-marker').removeClass('focus');
            $this.closest('.wpim-infowindow--hover').hide();
            setTimeout(function () {
                $this.closest('.wpim-infowindow--hover').removeAttr('style');
            }, 200);

            e.preventDefault();
        });


        var showMarker = function (i) {
            setTimeout(function () {
                $('.wpim-marker').eq(i).addClass('show bounceIn');
            }, (i * 300));
        }

        $('.wpim-marker').each(function (index) {
            showMarker(index);
        });


    }

});