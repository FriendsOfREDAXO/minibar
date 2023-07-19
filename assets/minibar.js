$(document).on('rex:ready', function (event, container) {
    var minibar = $(container).find('.rex-minibar');
    if (minibar.length) {
        $('body > .rex-minibar').replaceWith(minibar);
    }

    /** 
     * Für den Scheme-Umschalter (Light/Dark-Mode)
     */
    if (typeof rex === 'object' && rex.theme && redaxo.theme && !redaxo.minibar ) {
        redaxo.minibar = function (theme) {
            if( theme == 'reset' ){
                theme = rex.theme;
            }
            if( theme == redaxo.theme.current ){
                return;
            }
            if( theme === 'auto' ) {
                document.body.classList.remove('rex-theme-dark');
                document.body.classList.remove('rex-theme-light');
                return;
            }
            document.body.classList.remove('rex-theme-'+redaxo.theme.current);
            document.body.classList.add('rex-theme-'+theme)
        };
        let node = document.getElementById('mb-8d502110-db8a-4355-baaa-a612778fb4aa');
        if( node ) {
            node.innerHTML = rex.theme;
        }
    }
});
