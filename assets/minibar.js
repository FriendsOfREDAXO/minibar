$(document).on('rex:ready', function (event, container) {
    var minibar = $(container).find('.rex-minibar');
    if (minibar.length) {
        $('body > .rex-minibar').replaceWith(minibar);
    }
});
