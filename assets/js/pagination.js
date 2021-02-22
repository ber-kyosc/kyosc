const $ = require('jquery');

function resize() {
    const viewportWidth = $(window).width();
    if (viewportWidth < 600) {
        $('.pagination.paginationCustom').removeClass('pagination-lg').addClass('pagination-sm');
    } else {
        $('.pagination.paginationCustom').removeClass('pagination-sm').addClass('pagination-lg');
    }
}

$(window).ready(resize);
$(window).resize(resize);
