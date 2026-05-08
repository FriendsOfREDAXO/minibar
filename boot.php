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
use FriendsOfRedaxo\Minibar\Backend;
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
if (class_exists('rex_scss_compiler') && false !== $addon->getConfig('compile', false)) {
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
if (rex::isFrontend() || (rex::isBackend() && ('content' === rex_be_controller::getCurrentPagePart(1) || 'structure' === rex_be_controller::getCurrentPagePart(1)))) {
    $minibar->addElement(new StructureArticle());
}
if (rex::isFrontend() && rex::isDebugMode()) {
    $minibar->addElement(new Debug());
}

if (rex::isBackend()) {
    $minibar->addElement(new Scheme());

    if ('system' === rex_be_controller::getCurrentPagePart(1)) {
        rex_system_setting::register(new Scope());
        rex_system_setting::register(new MinibarInPopup());
        rex_system_setting::register(new HideEmptyMetainfos());
    }

    rex_extension::register('MINIBAR_ARTICLE', Backend::epMinibarArticle(...));
    rex_extension::register('MINIBAR_CLANG', Backend::epMinibarClang(...));
    rex_extension::register('PAGE_BODY_ATTR', Backend::epPageBodyAttr(...));
    rex_extension::register('PAGE_CHECKED', Backend::epPageChecked(...));

    if (Minibar::getInstance()->shouldRender()) {
        rex_view::addJsFile($addon->getAssetsUrl('minibar.js'));
        rex_view::addCssFile($addon->getAssetsUrl('styles.css'));
    }

    // XXX vermutlich nicht mehr nötig?
    // TODO: na dann prüfen und dann rauswerfen
    // update body class if minibar has been set inactive
    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
        if (false === Minibar::getInstance()->isActive()) {
            $ep->setSubject(preg_replace(
                '/(<(body|html)[^>]*)rex-minibar-is-active/iU',
                '$1',
                $ep->getSubject()),
            );
        }
    });

    // minibar aktualisieren bei PJAX requests.
    // in full-page requests wird die minibar via fragments/core/bottom.php gerendert.
    if (rex_request::isPJAXRequest()) {
        rex_extension::register('OUTPUT_FILTER', Backend::epOutputFilterPjax(...));
    }
}

if (rex::isFrontend()) {
    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) use ($addon) {
        $minibar = Minibar::getInstance()->get();

        if (null !== $minibar) {
            $ep->setSubject(str_ireplace(
                ['</head>', '</body>'],
                ['<link rel="stylesheet" type="text/css" href="' . $addon->getAssetsUrl('styles.css') . '" /></head>', $minibar . '</body>'],
                $ep->getSubject()),
            );
        }
    });
}
