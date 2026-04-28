<?php

/**
 * This file is part of the Minibar package.
 *
 * Quick toggle between different view schemes.
 * - dark mode
 * - light mode
 * - automatic mode switch (depends on browser capabilities)
 * - reset to user preset
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

use rex_i18n;

class Scheme extends AbstractElement
{
    public function render()
    {
        return '<div class="rex-minibar-item">
                <span class="rex-minibar-value">
                    <span class="rex-js-script-time">&#128161; ' . rex_i18n::msg('minibar_scheme_title') . '</span>
                </span>
            </div>
            <div class="rex-minibar-info">
                <div class="rex-minibar-info-group">
                    <div class="rex-minibar-info-piece">
                        ' . rex_i18n::msg('minibar_scheme_default') . ': <span id="mb-8d502110-db8a-4355-baaa-a612778fb4aa"></span>
                    </div>
                    <div class="rex-minibar-info-piece">
                        &rArr; <a href="javascript:redaxo.minibar(\'dark\');">' . rex_i18n::msg('minibar_scheme_dark') . '</a>
                    </div>
                    <div class="rex-minibar-info-piece">
                        &rArr; <a href="javascript:redaxo.minibar(\'light\');">' . rex_i18n::msg('minibar_scheme_light') . '</a>
                    </div>
                    <div class="rex-minibar-info-piece">
                        &rArr; <a href="javascript:redaxo.minibar(\'auto\');">' . rex_i18n::msg('minibar_scheme_auto') . '</a>
                    </div>
                    <div class="rex-minibar-info-piece">
                        &rArr; <a href="javascript:redaxo.minibar(\'reset\');">' . rex_i18n::msg('minibar_scheme_reset') . '</a>
                    </div>
                </div>
            </div>';
    }

    public function getOrientation()
    {
        return self::RIGHT;
    }
}
