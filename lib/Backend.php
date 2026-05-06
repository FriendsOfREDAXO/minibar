<?php

/**
 * This file is part of the Minibar package.
 *
 * Boot-Support: EP-Funktions for several purposes.
 * Backend-Funktions only.
 *
 * Enhances the readebility of the boot.php and keeps the EP-Functions in one place.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfRedaxo\Minibar;

use FriendsOfRedaxo\Minibar\Settings\HideEmptyMetainfos;
use FriendsOfRedaxo\Minibar\Settings\MinibarInPopup;
use rex;
use rex_article;
use rex_be_controller;
use rex_clang;
use rex_config;
use rex_extension;
use rex_extension_point;
use rex_formatter;
use rex_i18n;
use rex_sql;
use rex_url;

use function count;
use function in_array;
use function sprintf;
use function strlen;

class Backend
{

    /**
     * @param rex_extension_point<string> $ep
     * @return string|null
     */
    public static function epMinibarArticle(rex_extension_point $ep)
    {
        $sqlFields = rex_sql::factory();
        $showMetaInfo = rex_config::get('minibar', 'hide_empty_metainfos');
        // $sqlFields->setDebug();
        $fields = $sqlFields->getArray('
            SELECT  `t`.`label`, 
                    `f`.`title`, 
                    `f`.`name` 
            FROM    ' . rex::getTable('metainfo_field') . ' AS f
                LEFT JOIN ' . rex::getTable('metainfo_type') . ' AS t
                    ON  `f`.`type_id` = `t`.`id`
            WHERE `f`.`name` LIKE :art OR `f`.`name` LIKE :cat 
            ORDER BY LEFT(`f`.`name`, 4), priority', ['art' => 'art_%', 'cat' => 'cat_%']);

        if (0 === count($fields)) {
            return null;
        }

        /** @var rex_article $article */
        $article = $ep->getParam('article');

        $items = [];
        foreach ($fields as $field) {
            // Durch das unterschiedliche Erstellen der Optionen (Pipe, Sql) können die dazugehörigen Labels nicht ganz so einfach aufgelöst werden
            // Ein Admin sieht daher die gespeicherten Werte, ein Redakteur kann damit weniger anfangen
            if (!rex::getUser()->isAdmin() && in_array($field['label'], ['checkbox', 'radio', 'select'], true)) {
                continue;
            }
            if (in_array($field['label'], ['legend'], true)) {
                continue;
            }

            $value = $article->getValue($field['name']);
            if ('' !== trim($value)) {
                switch ($field['label']) {
                    case 'REX_MEDIA_WIDGET':
                        $value = sprintf('<a href="%s" target="_blank">%s</a>', rex_url::media($value), rex_escape($value));
                        break;
                    case 'REX_MEDIALIST_WIDGET':
                        $values = explode(',', $value);
                        $value = [];
                        foreach ($values as $fileName) {
                            $value[] = sprintf('<a href="%s" target="_blank">%s</a>', rex_url::media($fileName), rex_escape($fileName));
                        }
                        $value = implode(' | ', $value);
                        break;
                    case 'REX_LINK_WIDGET':
                        $linkedArticle = rex_article::get($value);
                        if (null === $linkedArticle) {
                            break;
                        }
                        $value = sprintf('<a href="%s" target="_blank">%s</a>', $linkedArticle->getUrl(), rex_escape($linkedArticle->getName()));
                        break;
                    case 'REX_LINKLIST_WIDGET':
                        $values = explode(',', $value);
                        $value = [];
                        foreach ($values as $articleId) {
                            $linkedArticle = rex_article::get((int) $articleId);
                            if (null === $linkedArticle) {
                                continue;
                            }
                            $value[] = sprintf('<a href="%s">%s</a>', $linkedArticle->getUrl(), rex_escape($linkedArticle->getName()));
                        }
                        $value = implode(' | ', $value);
                        break;
                    case 'date':
                        $value = rex_formatter::intlDate($value);
                        break;
                    case 'datetime':
                        $value = rex_formatter::intlDateTime($value);
                        break;
                    case 'time':
                        $value = rex_formatter::intlTime($value);
                        break;
                }
            }

            if ('' === trim($value) && HideEmptyMetainfos::HIDE === $showMetaInfo) {
                continue;
            }

            $item = '
                <div class="rex-minibar-info-piece">
                    <span class="title">' . rex_i18n::translate($field['title']) . '</span>
                    <span>' . $value . '</span>                    
                </div>';

            $items[] = $item;
        }

        if (0 === count($items) && HideEmptyMetainfos::HIDE === $showMetaInfo) {
            return null;
        }

        return
            '<div class="rex-minibar-info-group">
                <div class="rex-minibar-info-group-title">' . rex_i18n::msg('metainfo_minibar_article_title') . '</div>
                ' . implode('', $items) . '
            </div>';
    }

    /**
     * @param rex_extension_point<string> $ep
     * @return string|null
     */
    public static function epMinibarClang(rex_extension_point $ep)
    {
        if (!rex::getUser()->isAdmin()) {
            return null;
        }

        $sqlFields = rex_sql::factory();
        // $sqlFields->setDebug();
        $fields = $sqlFields->getArray('SELECT `title`, `name` FROM ' . rex::getTable('metainfo_field') . ' WHERE `name` LIKE :prefix ORDER BY priority', ['prefix' => 'clang_%']);

        if (0 === count($fields)) {
            return null;
        }

        /** @var rex_clang $clang */
        $clang = $ep->getParam('clang');
        $items = [];
        foreach ($fields as $field) {
            $item = '
                <div class="rex-minibar-info-piece">
                    <span class="title">' . rex_i18n::translate($field['title']) . '</span>
                    <span>' . rex_escape($clang->getValue($field['name'])) . '</span>                    
                </div>';

            $items[] = $item;
        }

        return
            '<div class="rex-minibar-info-group">
                <div class="rex-minibar-info-group-title">' . rex_i18n::msg('metainfo_minibar_clang_title') . '</div>
                ' . implode('', $items) . '
            </div>';
    }

    /** @param rex_extension_point<array<string,array<string>>> $ep */
    public static function epPageBodyAttr(rex_extension_point $ep): void
    {
        if (false !== Minibar::getInstance()->isActive()) {
            $body_attr = $ep->getSubject();
            $body_attr['class'][] = 'rex-minibar-is-active';
            $ep->setSubject($body_attr);
        }
    }

    /** @param rex_extension_point<string> $ep */
    public static function epPageChecked(rex_extension_point $ep): void
    {
        $page = rex_be_controller::getCurrentPageObject();
        if (null !== $page && $page->isPopup()) {
            Minibar::getInstance()->setActive(MinibarInPopup::isEnabled());
        }
    }

    /** @param rex_extension_point<string> $ep */
    public static function epOutputFilterPjax(rex_extension_point $ep): void
    {
        $minibar = Minibar::getInstance()->get();
        if (null === $minibar) {
            return;
        }

        // replace last occrance within a string
        // credits to https://stackoverflow.com/questions/3835636/php-replace-last-occurrence-of-a-string-in-a-string
        $str_lreplace = static function ($search, $replace, $subject) {
            $pos = strrpos($subject, $search);

            if (false !== $pos) {
                $subject = substr_replace($subject, $replace, $pos, strlen($search));
            }

            return $subject;
        };

        $pjaxResp = $str_lreplace('</section>', "\n" . $minibar . '</section>', $ep->getSubject());
        $ep->setSubject($pjaxResp);
    }
}
