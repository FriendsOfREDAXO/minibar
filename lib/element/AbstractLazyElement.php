<?php

/**
 * This file is part of the Minibar package.
 * 
 * Abstract class as nucleus for elements with lazy loading ability
 * For details check the documentation-files (*.md)
 *
 * Subclass it to create your custom implementation.
 * 
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfRedaxo\Minibar\Element;

use FriendsOfRedaxo\Minibar\Api\LazyLoader;
use rex_api_function;

abstract class AbstractLazyElement extends AbstractElement
{
    public function render()
    {
        if (self::isFirstView()) {
            return $this->renderFirstView();
        }
        return $this->renderComplete();
    }

    public static function isFirstView()
    {
        $apiFn = rex_api_function::factory();
        return !($apiFn instanceof LazyLoader);
    }

    /**
     * Returns the initial/light-weight html representation of this element.
     *
     * @return string
     */
    abstract protected function renderFirstView();

    /**
     * Returns the full html for this element.
     * This method will be called asynchronously after user starts interacting with the initial element.
     *
     * @return string
     */
    abstract protected function renderComplete();
}
