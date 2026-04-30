<?php

/**
 * This file is part of the Minibar package.
 *
 * Shim-Class to access the settings-class `rex_system_setting_minibar`
 * for backward compatibility to `Scope`.
 * This class is marked as deprecated and will be removed in Minibar 4.0.0.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FriendsOfRedaxo\Minibar\Settings\Scope;

/**
 * @deprecated 3.0.0 Use namespaced class `FriendsOfRedaxo\Minibar\Settings\Scope` instead
 */
class rex_system_setting_minibar extends Scope {}
