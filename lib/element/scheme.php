<?php

/**
 * Schaltet die Schemes um:
 *  - Dark-Mode
 *  - Light-Mode
 *  - Automatische Umstaltung gem Systemeinstellung (kann nicht jeder Browser)
 *  - Reset auf Grundeinstellung des Users.
 * 
 * Siehe JS für die konkrete Umschaltung
 *
 * @package redaxo\core\minibar
 */
class rex_minibar_element_scheme extends rex_minibar_element
{
    public function render()
    {
        return '<div class="rex-minibar-item">
                <span class="rex-minibar-value">
                    <span class="rex-js-script-time">&#128161; '.rex_i18n::msg('minibar_scheme_title').'</span>
                </span>
            </div>
            <div class="rex-minibar-info">
                <div class="rex-minibar-info-group">
                    <div class="rex-minibar-info-piece">
                        '.rex_i18n::msg('minibar_scheme_default').': <span id="mb-8d502110-db8a-4355-baaa-a612778fb4aa"></span>
                    </div>
                    <div class="rex-minibar-info-piece">
                        &rArr; <a href="javascript:redaxo.minibar(\'dark\');">'.rex_i18n::msg('minibar_scheme_dark').'</a>
                    </div>
                    <div class="rex-minibar-info-piece">
                        &rArr; <a href="javascript:redaxo.minibar(\'light\');">'.rex_i18n::msg('minibar_scheme_light').'</a>
                    </div>
                    <div class="rex-minibar-info-piece">
                        &rArr; <a href="javascript:redaxo.minibar(\'auto\');">'.rex_i18n::msg('minibar_scheme_auto').'</a>
                    </div>
                    <div class="rex-minibar-info-piece">
                        &rArr; <a href="javascript:redaxo.minibar(\'reset\');">'.rex_i18n::msg('minibar_scheme_reset').'</a>
                    </div>
                </div>
            </div>';
    }

    public function getOrientation()
    {
        return rex_minibar_element::RIGHT;
    }
}
