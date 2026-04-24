<?php

/**
 * @package redaxo\core\minibar
 */

namespace FriendsOfRedaxo\Minibar\Element;

use rex;
use rex_i18n;
use rex_timer;

class Time extends AbstractElement
{
    public function render()
    {
        return
        '<div class="rex-minibar-item">
            <span class="rex-minibar-value">
                <span class="rex-js-script-time"><!--DYN-->'.rex_i18n::msg('footer_scripttime', rex::getProperty('timer')->getFormattedDelta(rex_timer::SEC)).'<!--/DYN--></span>
            </span>
        </div>';
    }

    public function getOrientation()
    {
        return self::RIGHT;
    }
}
