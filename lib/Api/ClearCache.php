<?php

/**
 * This file is part of the Minibar package.
 *
 * API class for deleting the complete cache on the fly. Backend to a link
 * provided by "FriendsOfRedaxo\Minibar\Element\System"
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfRedaxo\Minibar\Api;

use rex_api_function;
use rex_response;

class ClearCache extends rex_api_function
{
    // Nur im Backend
    protected $published = false;

    public function execute()
    {
        rex_delete_cache();
        rex_response::cleanOutputBuffers();
        rex_response::setStatus(rex_response::HTTP_OK);
        rex_response::sendContent(rex_response::HTTP_OK);
        exit;
    }
}
