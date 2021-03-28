<?php

/**
 * @package redaxo\core\minibar
 */
class rex_minibar_element_syslog extends rex_minibar_element
{

    public function render()
    {
        $status = 'rex-syslog-ok';

        $sysLogFile = rex_logger::getPath();
        $login = rex::getProperty('login');

        // in case someone else aready read the filemtime() and the file was changed afterwards within the same request
        clearstatcache( true, $sysLogFile );
        $lastModified = filemtime($sysLogFile);

        // "last-seen" will be updated, when the user looks into the syslog
        if (rex::isBackend() && rex_be_controller::getCurrentPage() == 'system/log/redaxo') {
            // use the backend-session instead of rex_session() to make it work consistently across frontend/backend.
            // the frontend should reflect when we look into the log in the backend.
            $login->setSessionVar('rex_syslog_last_seen', $lastModified );
            $lastSeen = $lastModified;
        } else {
            $lastSeen = $login->getSessionVar('rex_syslog_last_seen');
        }

        // when the user never looked into the file (e.g. after login), we dont have a timely reference point.
        // therefore we check for changes in the file within the last 24hours
        if (!$lastSeen) {
            if ($lastModified > strtotime('-24 hours')) {
                $status = 'rex-syslog-changed';
            }
        } elseif ($lastModified && $lastModified > $lastSeen) {
            $status = 'rex-syslog-changed';
        }

        $item =
            '<div class="rex-minibar-item">
                <a href="'. rex_url::backendPage('system/log/redaxo') .'">
                    <span class="rex-minibar-icon">
                        <i class="rex-minibar-icon--fa rex-minibar-icon--fa-flag '. $status .'"></i>
                    </span>
                    <span class="rex-minibar-value">
                        System Log
                    </span>
                </a>
        </div>';

        $logFile = rex_logger::getPath();
        $editor = rex_editor::factory();
        $url = $editor->getUrl($logFile, 1);

        $info = '';
        if ($url) {
            $info =
                '<div class="rex-minibar-info">
                    <div class="rex-minibar-info-group">
                        <div class="rex-minibar-info-piece">
                            <a href="'. $url .'">' . rex_i18n::msg('system_editor_open_file', basename($logFile)) . '</a>
                        </div>
                    </div>
            </div>';
        }

        return $item . $info;
    }

    public function getOrientation()
    {
        return rex_minibar_element::RIGHT;
    }
}
