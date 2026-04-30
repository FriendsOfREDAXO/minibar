<?php

/**
 * This file is part of the Minibar package.
 *
 * Shim-Class to access the element-class `rex_minibar_element_structure_article`
 * for backward compatibility to `StructureArticle`.
 * This class is marked as deprecated and will be removed in Minibar 4.0.0.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FriendsOfRedaxo\Minibar\Element\StructureArticle;

/**
 * @deprecated 3.0.0 Use namespaced class `FriendsOfRedaxo\Minibar\Element\StructureArticle` instead
 */
class rex_minibar_element_structure_article extends StructureArticle {}
