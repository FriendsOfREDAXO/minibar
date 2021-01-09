<?php

$mypage = 'minibar';
$addon = rex_addon::get('minibar');

rex_minibar::getInstance()->addElement(new rex_minibar_element_system());
rex_minibar::getInstance()->addElement(new rex_minibar_element_time());
rex_minibar::getInstance()->addElement(new rex_minibar_element_syslog());

if (rex::isFrontend() || (rex::isBackend() && (rex_be_controller::getCurrentPagePart(1) === 'content' || rex_be_controller::getCurrentPagePart(1) === 'structure'))) {
    rex_minibar::getInstance()->addElement(new rex_minibar_element_structure_article());
}
if (rex::isFrontend() && rex::isDebugMode()) {
    rex_minibar::getInstance()->addElement(new rex_minibar_element_debug());
}

if (rex::isBackend()) {
    if (rex_be_controller::getCurrentPagePart(1) == 'system') {
        rex_system_setting::register(new rex_system_setting_minibar());
        rex_system_setting::register(new rex_system_setting_minibar_inpopup());
        rex_system_setting::register(new rex_system_setting_minibar_hide_empty_metainfos());
    }

    require_once __DIR__.'/extensions/extension_metainfo.php';

    rex_extension::register('PAGE_BODY_ATTR', static function (rex_extension_point $ep) {
        if (rex_minibar::getInstance()->isActive() !== false) {
            $body_attr = $ep->getSubject();
            $body_attr['class'][] = 'rex-minibar-is-active';
            return $body_attr;
        }
    });

    rex_extension::register('PAGE_CHECKED', static function (rex_extension_point $ep) {
        $page = rex_be_controller::getCurrentPageObject();
        if ($page && $page->isPopup()) {
            $enabled = rex_config::get('minibar', 'inpopup_enabled', rex_system_setting_minibar_inpopup::DISABLED);
            rex_minibar::getInstance()->setActive($enabled == rex_system_setting_minibar_inpopup::ENABLED);
        }
    });

    if (rex_minibar::getInstance()->shouldRender()) {
        rex_view::addJsFile($addon->getAssetsUrl('minibar.js'));
        rex_view::addCssFile($addon->getAssetsUrl('styles.css'));
    }

    // XXX vermutlich nicht mehr nötig?
    // update body class if minibar has been set inactive
    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
        if (rex_minibar::getInstance()->isActive() === false) {
            $ep->setSubject(preg_replace(
                    '/(<(body|html)[^>]*)rex-minibar-is-active/iU',
                    '$1',
                    $ep->getSubject())
            );
        }
    });

    // minibar aktualisieren bei PJAX requests.
    // in full-page requests wird die minibar via fragments/core/bottom.php gerendert.
    if (rex_request::isPJAXRequest()) {
        rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) use ($addon) {
            // replace last occrance within a string
            // credits to https://stackoverflow.com/questions/3835636/php-replace-last-occurrence-of-a-string-in-a-string
            $str_lreplace = static function ($search, $replace, $subject) {
                $pos = strrpos($subject, $search);

                if ($pos !== false) {
                    $subject = substr_replace($subject, $replace, $pos, strlen($search));
                }

                return $subject;
            };

            $minibar = rex_minibar::getInstance()->get();
            if ($minibar) {
                $pjaxResp = $str_lreplace('</section>', "\n". $minibar . '</section>', $ep->getSubject());
                $ep->setSubject($pjaxResp);
            }
        });
    }
}

if (rex::isFrontend()) {
    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) use ($addon) {
        $minibar = rex_minibar::getInstance()->get();

        if ($minibar) {
            $ep->setSubject(str_ireplace(
                    ['</head>', '</body>'],
                    ['<link rel="stylesheet" type="text/css" href="' . $addon->getAssetsUrl('styles.css') .'" /></head>', $minibar . '</body>'],
                    $ep->getSubject())
            );
        }
    });
}
