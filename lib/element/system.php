<?php

/**
 * @package redaxo\core\minibar
 */
class rex_minibar_element_system extends rex_minibar_element
{
    public function render()
    {
        $database = rex::getProperty('db');

        $links = '';
        if (rex::getUser() && rex::getUser()->isAdmin()) {
            $links .= '<a href="https://redaxo.org/doku/master" target="_blank" rel="help noreferrer noopener">'.rex_i18n::msg('minibar_documentation_link_label').'</a>';
        }

        $logo = str_replace('<svg ', '<svg class="rex-redaxo-logo" ', (string) rex_file::get(rex_url::coreAssets('redaxo-logo.svg')));
        return
        '<div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                '.$logo.'
            </span>
            <span class="rex-minibar-value">
                '.rex::getVersion().'
            </span>
        </div>
        <div class="rex-minibar-info">
        <div class="rex-minibar-info-header">'. rex_i18n::msg('minibar_system_info') .'</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">REDAXO</span>
                    <span>'.rex::getVersion().' '.(rex::getUser() && rex::getUser()->isAdmin() ? '<br><a href="' . rex_url::backendPage('system/log') . '" title="'.rex_escape(rex_i18n::msg('logfiles')).'">'.rex_i18n::msg('logfiles').'</a> <br><a href="' . rex_url::backendPage('system/report') . '" title="'.rex_escape(rex_i18n::msg('system_report')).'">'.rex_i18n::msg('system_report').'</a>' : '') .'</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">PHP Version</span>
                    <span>'.PHP_VERSION.' '.(rex::isBackend() && rex::getUser() && rex::getUser()->isAdmin() ? '<a href="' . rex_url::backendPage('system/phpinfo') . '" title="phpinfo" onclick="newWindow(\'phpinfo\', this.href, 1000,800,\',status=yes,resizable=yes\');return false;">phpinfo()</a>' : '') .'</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">MySQL</span>
                    <span>'.rex_sql::getServerVersion().'</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">'.rex_i18n::msg('minibar_database').'</span>
                    <span>'.rex_escape($database[1]['name']).'</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">Host</span>
                    <span>'.$database[1]['host'].'</span>
                </div>
            </div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">'.rex_i18n::msg('minibar_help').'</span>
                    <span>
                        '.$links.'<br />
                        <a href="https://redaxo.org/slack/" target="_blank" rel="help noreferrer noopener">'.rex_i18n::msg('minibar_slack_link_label').'</a><br />
                        <a href="https://github.com/redaxo/redaxo/issues" target="_blank" rel="help noreferrer noopener">'.rex_i18n::msg('minibar_errorreport').'</a>
                    </span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title"></span>
                    <span>
                        <a href="https://redaxo.org" target="_blank" rel="help noreferrer noopener">redaxo.org</a>
                        / 
                        <a href="'.(rex::getUser() ? rex_url::backendPage('credits') : 'https://www.redaxo.org/" target="_blank" rel="noreferrer noopener').'">'.rex_i18n::msg('footer_credits').'</a>
                        <br />
                        <a href="https://www.yakamara.de" target="_blank" rel="help noreferrer noopener">yakamara.de</a>
                    </span>
                </div>
            </div>
        </div>';
    }

    /**
     * {@inheritdoc}
     */
    public function getOrientation()
    {
        return rex_minibar_element::RIGHT;
    }

    /**
     * {@inheritdoc}
     */
    public function isPrimary()
    {
        return true;
    }
}
