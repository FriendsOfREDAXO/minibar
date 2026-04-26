<?php

/**
 * This file is part of the Minibar package.
 * 
 * Add an entry to the system configuration
 * -> Toggle availability of Minibar toolbar depending on the environment
 * 
 * - Frontend only
 * - Backend only
 * - both front- and backend
 * - toolbar disabled
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

class Scope extends rex_system_setting
{
    public const DISABLED = -1;
    public const ENABLED_EVERYWHERE = 1;
    public const ENABLED_FRONTEND = 2;
    public const ENABLED_BACKEND = 3;

    public function getKey()
    {
        return 'minibar_enabled';
    }

    public function getField()
    {
        $field = new rex_form_select_element();
        $field->setAttribute('class', 'form-control selectpicker');
        $field->setLabel(rex_i18n::msg('minibar_system_setting'));
        $select = $field->getSelect();
        $select->addOption(rex_i18n::msg('minibar_enabled_everywhere'), self::ENABLED_EVERYWHERE);
        $select->addOption(rex_i18n::msg('minibar_enabled_frontend'), self::ENABLED_FRONTEND);
        $select->addOption(rex_i18n::msg('minibar_enabled_backend'), self::ENABLED_BACKEND);
        $select->addOption(rex_i18n::msg('minibar_disabled'), self::DISABLED);
        $select->setSelected(rex_config::get('minibar', 'enabled', self::ENABLED_EVERYWHERE));
        return $field;
    }

    public function setValue($value)
    {
        $value = (int) $value;
        rex_config::set('minibar', 'enabled', $value);
        return true;
    }
}
