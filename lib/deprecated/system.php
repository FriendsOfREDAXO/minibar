<?php

/**
 * This file is part of the Minibar package.
 *
 * Shim-Class to access the element-class `rex_minibar_element_system`
 * for backward compatibility to `System`.
 * This class is marked as deprecated and will be removed in Minibar 4.0.0.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FriendsOfRedaxo\Minibar\Element\System;

/**
 * @deprecated 3.0.0 Use namespaced class `FriendsOfRedaxo\Minibar\Element\System` instead
 */
class rex_minibar_element_system extends System {}
