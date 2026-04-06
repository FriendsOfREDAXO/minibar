<?php

/**
 * API-Klasse zum Löschen des Caches
 * 
 * Es wird stets der gesamte Cache gelöscht.
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
