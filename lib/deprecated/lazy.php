<?php

/**
 * This file is part of the Minibar package.
 *
 * Shim-Class to access the element-class `rex_minibar_lazy_element`
 * for backward compatibility to `AbstractLazyElement`.
 * This class is marked as deprecated and will be removed in Minibar 4.0.0.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FriendsOfRedaxo\Minibar\Element\AbstractLazyElement;

/**
 * @deprecated 3.0.0 Use namespaced class `FriendsOfRedaxo\Minibar\Element\AbstractLazyElement` instead
 */
abstract class rex_minibar_lazy_element extends AbstractLazyElement {}
