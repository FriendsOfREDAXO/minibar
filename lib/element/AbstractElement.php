<?php

/**
 * This file is part of the Minibar package.
 * 
 * Abstract class as nucleus for each element in the
 * Minibar toolbar.
 *
 * Subclass it to create your custom implementation.
 * 
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfRedaxo\Minibar\Element;

use rex_string;

abstract class AbstractElement
{
    public const LEFT = 'LEFT';
    public const RIGHT = 'RIGHT';

    /**
     * Returns the html bar item.
     *
     * @return string
     */
    abstract public function render();

    /**
     * liefert einen CSS-Namen, über den Elemente dieser Klasse
     * individuell konfiguriert werden können.
     * 
     * Für eigene Klassen: optional überschreiben
     * 
     * Default: normalisierter Klassenname
     * 
     * @api
     */
    public function cssClass() : string
    {
        static $cache = [];
        return $cache[static::class] ??= rex_string::normalize(static::class, '-');
    }

    /**
     * Liefert einen anonymisierten Identifier zur Verwendung in API-Aufrufen
     * MD5-kodierter Klassenname
     * 
     * Dem Hash ist ein M vorangestellt, da JS-Identifier mit einem Buchstaben
     * beginnen müssen/sollten
     * 
     * @api
     */
    public function jsId() : string
    {
        static $cache = [];
        return $cache[static::class] ??= 'M' . md5(static::class);
    }

    /**
     * Returns the orientation in the minibar.
     *
     * @return string `rex_minibar_element::LEFT` or `rex_minibar_element::RIGHT`
     */
    public function getOrientation()
    {
        return self::LEFT;
    }

    /**
     * Returns the danger status.
     *
     * @return bool
     */
    public function isDanger()
    {
        return false;
    }

    /**
     * Returns the primary status.
     *
     * @return bool
     */
    public function isPrimary()
    {
        return false;
    }

    /**
     * Returns the warning status.
     *
     * @return bool
     */
    public function isWarning()
    {
        return false;
    }
}
