<?php

/**
 * This file is part of the Minibar package.
 *
 * Add an entry to the system configuration
 * -> Toggle visibility of Minibar toolbar in popup windows
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfRedaxo\Minibar\Settings;

use rex_config;
use rex_form_select_element;
use rex_i18n;
use rex_system_setting;

class MinibarInPopup extends rex_system_setting
{
    /** @api */
    public const ENABLED = 1;
    /** @api */
    public const DISABLED = -1;

    /**
     * Returns the key for this system setting.
     *
     * @return string
     */
    public function getKey()
    {
        return 'minibar_inpopup_enabled';
    }

    /**
     * Returns the form field for this system setting.
     *
     * @return rex_form_select_element
     */
    public function getField()
    {
        $field = new rex_form_select_element();
        $field->setAttribute('class', 'form-control selectpicker');
        $field->setLabel(rex_i18n::msg('minibar_system_setting_inpopup'));
        $select = $field->getSelect();
        $select->addOption(rex_i18n::msg('minibar_inpopup_enabled'), self::ENABLED);
        $select->addOption(rex_i18n::msg('minibar_inpopup_disabled'), self::DISABLED);
        $select->setSelected(rex_config::get('minibar', 'inpopup_enabled', self::DISABLED));
        return $field;
    }

    /**
     * Write the value for this field to the config-table.
     *
     * @param mixed $value
     * @return bool
     */
    public function setValue($value)
    {
        $value = (int) $value;
        rex_config::set('minibar', 'inpopup_enabled', $value);
        return true;
    }

    public static function isEnabled()
    {
        return self::ENABLED === rex_config::get('minibar', 'inpopup_enabled', self::DISABLED);
    }
}
