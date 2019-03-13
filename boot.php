<?php

$mypage = 'minibar';
$addon = rex_addon::get('minibar');

rex_minibar::getInstance()->addElement(new rex_minibar_element_system());
rex_minibar::getInstance()->addElement(new rex_minibar_element_time());

/* Addon Parameter */
if (rex::isBackend()) {
    require_once __DIR__.'/extensions/extension_metainfo.php';

    rex_extension::register('PAGE_BODY_ATTR', function (rex_extension_point $ep) {
        if (rex_minibar::getInstance()->isActive() !== false) {
            $body_attr = $ep->getSubject();
            $body_attr['class'][] = 'rex-minibar-is-active';
            return $body_attr;
        }
    });

    rex_extension::register('PAGE_CHECKED', function (rex_extension_point $ep) {
        // e.g. mediapool/linkmap should not have a minibar
        $page = rex_be_controller::getCurrentPageObject();
        if ($page && $page->isPopup()) {
            rex_minibar::getInstance()->setActive(false);
        }
    });

    rex_extension::register('BE_STYLE_SCSS_COMPILE', function (rex_extension_point $ep) use ($addon) {
        $scss_files = rex_extension::registerPoint(new rex_extension_point('BE_STYLE_SCSS_FILES', []));

        $subject[] = [
            'scss_files' => array_merge($scss_files, [$addon->getPath('scss/master_minibar.scss')]),
            'css_file' => $addon->getPath('assets/css/minibar.css'),
            'copy_dest' => $addon->getAssetsPath('css/minibar.css'),
        ];
        return $subject;
    });

    if (rex_minibar::getInstance()->shouldRender()) {
        rex_view::addCssFile($addon->getAssetsUrl('css/minibar.css'));
    }

    // XXX vermutlich nicht mehr nötig?
    // update body class if minibar has been set inactive
    rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
        if (rex_minibar::getInstance()->isActive() === false) {
            $ep->setSubject(preg_replace(
                    '/(<(body|html)[^>]*)rex-minibar-is-active/iU',
                    '$1',
                    $ep->getSubject())
            );
        }
    });
}

if (rex::isFrontend()) {
    rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
        $minibar = rex_minibar::getInstance()->get();
        if ($minibar) {
            $ep->setSubject(str_replace(
                    ['</head>', '</body>'],
                    ['<link rel="stylesheet" type="text/css" href="' . rex_addon::get('be_style')->getAssetsUrl('css/minibar.css') .'" /></head>', $minibar . '</body>'],
                    $ep->getSubject())
            );
        }
    });
}

if (rex::isFrontend() || (rex::isBackend() && (rex_be_controller::getCurrentPagePart(1) === 'content' || rex_be_controller::getCurrentPagePart(1) === 'structure'))) {
    rex_minibar::getInstance()->addElement(new rex_minibar_element_structure_article());
}

