<?php

/**
 * @package redaxo\core\minibar
 */
class rex_minibar_element_syslog extends rex_minibar_element
{

    public function __construct() {
        if (rex_be_controller::getCurrentPage() == 'system/log/redaxo') {
            rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
                rex_set_session('rex_syslog_last_seen', filemtime(rex_logger::getPath()) );
            });
        }
    }

    public function render()
    {
        $status = 'rex-syslog-ok';

        $sysLogFile = rex_logger::getPath();
        clearstatcache( true, $sysLogFile );
        $lastModified = filemtime($sysLogFile);
        // "last-seen" will be updated, when the user looks into the syslog
        $lastSeen = rex_session('rex_syslog_last_seen');

        // when the user never looked into the file (e.g. after login), we dont have a timely reference point.
        // therefore we check for changes in the file within the last 24hours
        if (!$lastSeen) {
            if ($lastModified > strtotime('-24 hours')) {
                $status = 'rex-syslog-changed';
            }
        } elseif ($lastModified && $lastModified > $lastSeen) {
            $status = 'rex-syslog-changed';
        }

        return
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
    }

    public function getOrientation()
    {
        return rex_minibar_element::RIGHT;
    }
}
