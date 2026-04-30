<?php

/**
 * This file is part of the Minibar package.
 *
 * Shim-Class to access the class `rex_minibar` for backward compatibility to `Minibar`.
 * This class is marked as deprecated and will be removed in Minibar 4.0.0.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FriendsOfRedaxo\Minibar\Minibar;

/**
 * @deprecated 3.0.0 Use namespaced class `FriendsOfRedaxo\Minibar\Minibar` instead
 */
class rex_minibar extends Minibar {}
