<?php

/**
 * This file is part of the Minibar package.
 *
 * Time elapsed to open the current page
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
                <span class="rex-js-script-time"><!--DYN-->' . rex_i18n::msg('footer_scripttime', rex::getProperty('timer')->getFormattedDelta(rex_timer::SEC)) . '<!--/DYN--></span>
            </span>
        </div>';
    }

    public function getOrientation()
    {
        return self::RIGHT;
    }
}
