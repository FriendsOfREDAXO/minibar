<?php

use FriendsOfRedaxo\Minibar\Api\ClearCache;
use FriendsOfRedaxo\Minibar\Api\LazyLoader;
use FriendsOfRedaxo\Minibar\Element\Debug;
use FriendsOfRedaxo\Minibar\Element\Scheme;
use FriendsOfRedaxo\Minibar\Element\StructureArticle;
use FriendsOfRedaxo\Minibar\Element\Syslog;
use FriendsOfRedaxo\Minibar\Element\System;
use FriendsOfRedaxo\Minibar\Element\Time;
use FriendsOfRedaxo\Minibar\Element\Url2Yform;
use FriendsOfRedaxo\Minibar\Minibar;
use FriendsOfRedaxo\Minibar\Settings\HideEmptyMetainfos;
use FriendsOfRedaxo\Minibar\Settings\MinibarInPopup;
use FriendsOfRedaxo\Minibar\Settings\Scope;

$addon = rex_addon::get('minibar');


if (class_exists('rex_scss_compiler') && $addon->getConfig('compile')) {
    $compiler = new rex_scss_compiler();

    $compiler->setRootDir(rex_path::addon('minibar/scss'));
    $compiler->setScssFile([$addon->getPath('scss/styles.scss')]);

    // Compile in backend assets dir
    $compiler->setCssFile($addon->getPath('assets/styles.css'));
    $compiler->compile();
    $addon->setConfig('compile', false);
}

$mypage = 'minibar';
$addon = rex_addon::get('minibar');

Minibar::getInstance()->addElement(new System());
Minibar::getInstance()->addElement(new Time());
Minibar::getInstance()->addElement(new Syslog());
rex_api_function::register('mbclrcache', ClearCache::class);
rex_api_function::register('minibar', LazyLoader::class);

// URL2/YForm Element für alle Backend-Bereiche verfügbar machen
if (rex::isFrontend()) {
Minibar::getInstance()->addElement(new Url2Yform());
}
if (rex::isFrontend() || (rex::isBackend() && (rex_be_controller::getCurrentPagePart(1) === 'content' || rex_be_controller::getCurrentPagePart(1) === 'structure'))) {
    Minibar::getInstance()->addElement(new StructureArticle());
}
if (rex::isFrontend() && rex::isDebugMode()) {
    Minibar::getInstance()->addElement(new Debug());
}

if (rex::isBackend()) {
    Minibar::getInstance()->addElement(new Scheme());
    
    if (rex_be_controller::getCurrentPagePart(1) == 'system') {
        rex_system_setting::register(new Scope());
        rex_system_setting::register(new MinibarInPopup());
        rex_system_setting::register(new HideEmptyMetainfos());
    }

    require_once __DIR__.'/extensions/extension_metainfo.php';

    rex_extension::register('PAGE_BODY_ATTR', static function (rex_extension_point $ep) {
        if (Minibar::getInstance()->isActive() !== false) {
            $body_attr = $ep->getSubject();
            $body_attr['class'][] = 'rex-minibar-is-active';
            return $body_attr;
        }
    });

    rex_extension::register('PAGE_CHECKED', static function (rex_extension_point $ep) {
        $page = rex_be_controller::getCurrentPageObject();
        if ($page && $page->isPopup()) {
            $enabled = rex_config::get('minibar', 'inpopup_enabled', MinibarInPopup::DISABLED);
            Minibar::getInstance()->setActive($enabled == MinibarInPopup::ENABLED);
        }
    });

    if (Minibar::getInstance()->shouldRender()) {
        rex_view::addJsFile($addon->getAssetsUrl('minibar.js'));
        rex_view::addCssFile($addon->getAssetsUrl('styles.css'));
    }

    // XXX vermutlich nicht mehr nötig?
    // update body class if minibar has been set inactive
    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
        if (Minibar::getInstance()->isActive() === false) {
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

            $minibar = Minibar::getInstance()->get();
            if ($minibar) {
                $pjaxResp = $str_lreplace('</section>', "\n". $minibar . '</section>', $ep->getSubject());
                $ep->setSubject($pjaxResp);
            }
        });
    }
}

if (rex::isFrontend()) {
    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) use ($addon) {
        $minibar = Minibar::getInstance()->get();

        if ($minibar) {
            $ep->setSubject(str_ireplace(
                    ['</head>', '</body>'],
                    ['<link rel="stylesheet" type="text/css" href="' . $addon->getAssetsUrl('styles.css') .'" /></head>', $minibar . '</body>'],
                    $ep->getSubject())
            );
        }
    });
}
