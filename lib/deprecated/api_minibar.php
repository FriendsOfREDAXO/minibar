<?php

/**
 * This file is part of the Minibar package.
 *
 * API class for lazy loading content of elements which are a subclass to
 * "FriendsOfRedaxo\Minibar\Element\AbstractLazyElement"
 *
 * Shim-Class to access the API via `rex_api_minibar` for backward compatibility.
 * This class is marked as deprecated and will be removed in Minibar 4.0.0.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FriendsOfRedaxo\Minibar\Api\LazyLoader;

/**
 * @deprecated 3.0.0 Use namespaced class `FriendsOfRedaxo\Minibar\Api\LazyLoader` instead
 */
class rex_api_minibar extends LazyLoader {}
