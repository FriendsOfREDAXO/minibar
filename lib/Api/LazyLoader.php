<?php

/**
 * This file is part of the Minibar package.
 *
 * API class for lazy loading content of elements which are a subclass to
 * "FriendsOfRedaxo\Minibar\Element\AbstractLazyElement"
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfRedaxo\Minibar\Api;

use FriendsOfRedaxo\Minibar\Minibar;
use rex;
use rex_api_function;
use rex_fragment;
use rex_response;
use rex_url;

class LazyLoader extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        $visibility = rex_get('visibility', 'bool', null);
        if (null !== $visibility) {
            Minibar::getInstance()->setVisibility($visibility);

            if (rex::isBackend()) {
                rex_response::sendRedirect(rex_url::currentBackendPage([], false));
            }

            rex_response::sendRedirect(rex_getUrl('', '', [], '&'));
        }

        $lazyElement = rex_get('lazy_element', 'string');
        if ($lazyElement) {
            $minibar = Minibar::getInstance();
            $element = $minibar->elementByClass($lazyElement);
            if ($element) {
                $fragment = new rex_fragment([
                    'element' => $element,
                ]);
                rex_response::setStatus(rex_response::HTTP_OK);
                rex_response::sendContent($fragment->parse('minibar/element.php'));
                exit;
            }
        }
    }

    protected function requiresCsrfProtection()
    {
        return true;
    }
}
