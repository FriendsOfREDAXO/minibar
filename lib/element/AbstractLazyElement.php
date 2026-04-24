<?php

/**
 * minibar element base class which provides lazy loading abilities 
 * for resource extensive use-cases.
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
