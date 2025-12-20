var preloader = new loader()

function loader() {
    this.load = function (div_append = '') {
        var window_height = $(window).height();
        var loader_height = $("#loader").height();
        var loader_pos = ((window_height / 2) - (loader_height / 2)) - 100
        loaderContainer = $(".loader-container");
        $('#loader').css('margin-top', loader_pos);
        loaderContainer.css('display', 'block');
        if (div_append == '') {
            div_append = 'loader-Container'
        }
        loader(true, div_append)
    }
    this.stop = function (div_append = '') {
        loaderContainer = $(".loader-container");
        loaderContainer.css('display', 'none');
        if (div_append == '') {
            div_append = 'loader-Container'
        }
        loader(false, div_append)
    }
    function loader(is_on, div_append = '') {
        if (is_on == true) {
            if ($('#' + div_append).length == 0) {
                $('#' + div_append).css('opacity', 0.7);
            }
            else {
                $('.' + div_append).css('opacity', 0.7);
            }
        }
        else {
            if ($('#' + div_append).length == 0) {
                $('#' + div_append).css('opacity', 1.0);
            }
            else {
                $('.' + div_append).css('opacity', 1.0);
            }
        }
    }

}
$(document).ready(function () {
    var window_height = $(window).height();
    var window_width = $(window).width();
    var loader_height = $("#loader").height();
    var loader_width = $("#loader").width();
    var loader_pos_top = ((window_height / 2) - (loader_height / 2)) - 50
    var loader_pos_left = ((window_width / 2) - (loader_width / 2)) - 500
    $('#loader').css('margin-top', loader_pos_top);
    $('#loader').css('left', "50%");
});