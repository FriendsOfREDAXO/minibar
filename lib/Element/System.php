<?php

/**
 * This file is part of the Minibar package.
 *
 * System overview with helpfull links
 *
 * Subclass it to create your custom implementation.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** TODO: Code sollte in Fragmente ausgelagert werden */
/** TODO: JS/CSS besser in JS/CSS-Dateien? */

namespace FriendsOfRedaxo\Minibar\Element;

use FriendsOfRedaxo\Minibar\Api\ClearCache;
use rex;
use rex_file;
use rex_i18n;
use rex_sql;
use rex_url;

use function sprintf;

use const PHP_VERSION;

class System extends AbstractElement
{

    /**
     * Returns the html bar item.
     *
     * @api
     * @return string
     */
    public function render()
    {
        $database = rex::getProperty('db');

        $links = '';
        if (null !==rex::getUser() && rex::getUser()->isAdmin()) {
            $links .= '<a href="https://redaxo.org/doku/master" target="_blank" rel="help noreferrer noopener">' . rex_i18n::msg('minibar_documentation_link_label') . '</a>';
        }

        $clearCache = '';
        if (null !== rex::getUser() && rex::getUser()->isAdmin() && rex::isDebugMode()) {
            $clearCache = sprintf(
                '<br><a href="javascript:(fetch(\'%s\'))">%s</a>',
                rex_url::currentBackendPage(ClearCache::getUrlParams(), false),
                rex_i18n::msg('delete_cache'),
            );
        }

        $logo = str_replace('<svg ', '<svg class="rex-redaxo-logo" ', (string) rex_file::get(rex_url::coreAssets('redaxo-logo.svg')));
        return
        '<div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                ' . $logo . '
            </span>
            <span class="rex-minibar-value">
                ' . rex::getVersion() . '
            </span>
        </div>
        <div class="rex-minibar-info">
        <div class="rex-minibar-info-header">' . rex_i18n::msg('minibar_system_info') . '</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">REDAXO</span>
                    <span>' . rex::getVersion() . ' ' . (null !== rex::getUser() && rex::getUser()->isAdmin() ? '<br><a href="' . rex_url::backendPage('system/log') . '" title="' . rex_i18n::msg('logfiles') . '">' . rex_i18n::msg('logfiles') . '</a> <br><a href="' . rex_url::backendPage('system/report') . '" title="' . rex_i18n::msg('system_report') . '">' . rex_i18n::msg('system_report') . '</a>' . $clearCache : '') . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">PHP Version</span>
                    <span>' . PHP_VERSION . ' ' . (rex::isBackend() && null !== rex::getUser() && rex::getUser()->isAdmin() ? '<a href="' . rex_url::backendPage('system/phpinfo') . '" title="phpinfo" onclick="newWindow(\'phpinfo\', this.href, 1000,800,\',status=yes,resizable=yes\');return false;">phpinfo()</a>' : '') . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">MySQL</span>
                    <span>' . rex_sql::getServerVersion() . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">' . rex_i18n::msg('minibar_database') . '</span>
                    <span>' . rex_escape($database[1]['name']) . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">Host</span>
                    <span>' . $database[1]['host'] . '</span>
                </div>
            </div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">' . rex_i18n::msg('minibar_help') . '</span>
                    <span>
                        ' . $links . '<br />
                        <a href="https://redaxo.org/slack/" target="_blank" rel="help noreferrer noopener">' . rex_i18n::msg('minibar_slack_link_label') . '</a><br />
                        <a href="https://github.com/redaxo/redaxo/issues" target="_blank" rel="help noreferrer noopener">' . rex_i18n::msg('minibar_errorreport') . '</a>
                    </span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title"></span>
                    <span>
                        <a href="https://redaxo.org" target="_blank" rel="help noreferrer noopener">redaxo.org</a>
                        / 
                        <a href="' . (null !== rex::getUser() ? rex_url::backendPage('credits') : 'https://www.redaxo.org/" target="_blank" rel="noreferrer noopener') . '">' . rex_i18n::msg('footer_credits') . '</a>
                        <br />
                        <a href="https://www.yakamara.de" target="_blank" rel="help noreferrer noopener">yakamara.de</a>
                    </span>
                </div>
            </div>
        </div>';
    }

    /**
     * Returns the orientation in the minibar.
     *
     * @api
     * @return string 'right'
     */
    public function getOrientation()
    {
        return self::RIGHT;
    }

    /**
     * Returns the primary status.
     *
     * @api
     * @return bool
     */
    public function isPrimary()
    {
        return true;
    }
}
