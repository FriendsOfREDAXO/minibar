<?php

/**
 * This file is part of the Minibar package.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

/** TODO: Kann man die boot.php entschlacken und Codeblöcke außerhalb parken (static-Methoden)? Nur compilieren was wirklich notwendig ist. */

$addon = rex_addon::get('minibar');

/** TODO: Muss denn wirklich vor jedem Aufruf die Kompilierung geprüft weden? Reicht es nicht in der install.php? */
if (class_exists('rex_scss_compiler') && $addon->getConfig('compile', false) !== false) {
    $compiler = new rex_scss_compiler();

    $compiler->setRootDir(rex_path::addon('minibar/scss'));
    $compiler->setScssFile([$addon->getPath('scss/styles.scss')]);

    // Compile in backend assets dir
    $compiler->setCssFile($addon->getPath('assets/styles.css'));
    $compiler->compile();
    $addon->setConfig('compile', false);
}

$minibar = Minibar::getInstance();
$minibar->addElement(new System());
$minibar->addElement(new Time());
$minibar->addElement(new Syslog());

rex_api_function::register('mbclrcache', ClearCache::class);
rex_api_function::register('mblzyld', LazyLoader::class);

// URL2/YForm Element für alle Backend-Bereiche verfügbar machen
if (rex::isFrontend()) {
    $minibar->addElement(new Url2Yform());
}
if (rex::isFrontend() || (rex::isBackend() && (rex_be_controller::getCurrentPagePart(1) === 'content' || rex_be_controller::getCurrentPagePart(1) === 'structure'))) {
    $minibar->addElement(new StructureArticle());
}
if (rex::isFrontend() && rex::isDebugMode()) {
    $minibar->addElement(new Debug());
}

if (rex::isBackend()) {
    $minibar->addElement(new Scheme());
    
    if (rex_be_controller::getCurrentPagePart(1) === 'system') {
        rex_system_setting::register(new Scope());
        rex_system_setting::register(new MinibarInPopup());
        rex_system_setting::register(new HideEmptyMetainfos());
    }

    /** TODO: zu zugehörigen Code-Block gemäß aktueller Vorgehensweise in eine Klasse überführen und als static-Methode ausführen */
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
        if (null !==$page && $page->isPopup()) {
            Minibar::getInstance()->setActive(MinibarInPopup::isEnabled());
        }
    });

    if (Minibar::getInstance()->shouldRender()) {
        rex_view::addJsFile($addon->getAssetsUrl('minibar.js'));
        rex_view::addCssFile($addon->getAssetsUrl('styles.css'));
    }

    // XXX vermutlich nicht mehr nötig?
    // TODO: na dann prüfen und dann rauswerfen
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
        rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
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
            if (null !== $minibar) {
                $pjaxResp = $str_lreplace('</section>', "\n". $minibar . '</section>', $ep->getSubject());
                $ep->setSubject($pjaxResp);
            }
        });
    }
}

if (rex::isFrontend()) {
    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) use ($addon) {
        $minibar = Minibar::getInstance()->get();

        if (null !== $minibar) {
            $ep->setSubject(str_ireplace(
                    ['</head>', '</body>'],
                    ['<link rel="stylesheet" type="text/css" href="' . $addon->getAssetsUrl('styles.css') .'" /></head>', $minibar . '</body>'],
                    $ep->getSubject())
            );
        }
    });
}
