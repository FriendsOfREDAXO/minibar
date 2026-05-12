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
use rex_request;
use rex_response;
use rex_url;

class LazyLoader extends rex_api_function
{
    // Backend and Frontend
    protected $published = true;

    /**
     * API-Anfgare ausführen. Es werden entweder die Sichtbarkeit der Minibar
     * gesetzt oder Inhalte von Lazy-Elementen geladen.
     *
     * @api
     * @return never
     */
    public function execute()
    {
        $visibility = rex_request::get('visibility', 'bool', null);
        if (null !== $visibility) {
            Minibar::getInstance()->setVisibility($visibility);

            if (rex::isBackend()) {
                rex_response::sendRedirect(rex_url::currentBackendPage([], false));
            }

            rex_response::sendRedirect(rex_getUrl('', '', [], '&'));
        }

        $lazyElement = rex_request::get('lazy_element', 'string');
        if ('' < $lazyElement) {
            $minibar = Minibar::getInstance();
            $element = $minibar->elementByClass($lazyElement);
            if (null !== $element) {
                $fragment = new rex_fragment([
                    'element' => $element,
                ]);
                rex_response::setStatus(rex_response::HTTP_OK);
                rex_response::sendContent($fragment->parse('minibar/element.php'));
                exit;
            }
        }
        rex_response::setStatus(rex_response::HTTP_BAD_REQUEST);
        rex_response::sendContent('Invalid request');
        exit;
    }

    /**
     * CSFR-Protection aktivieren.
     *
     * @return bool
     */
    protected function requiresCsrfProtection()
    {
        return true;
    }
}
