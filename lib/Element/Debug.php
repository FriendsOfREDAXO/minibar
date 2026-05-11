<?php

/**
 * This file is part of the Minibar package.
 *
 * Provide debug-support via Minibar toolbar
 * Available only in debug-mode
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** TODO: Code sollte in Fragmente ausgelagert werden */
/** TODO: JS/CSS besser in JS/CSS-Dateien? */

namespace FriendsOfRedaxo\Minibar\Element;

use rex;
use rex_addon;
use rex_fragment;

class Debug extends AbstractElement
{
    /**
     * Returns the html bar item.
     *
     * @api
     * @return string
     */
    public function render()
    {
        $fragment = new rex_fragment();
        $fragment->setVar('adminLinks', false, false);
        if (null !== rex::getUser() && rex::getUser()->isAdmin()) {
            $fragment->setVar('adminLinks', true, false);
            $fragment->setVar('debugLink', rex_addon::get('debug')->isAvailable(), false);
        }
        return $fragment->parse('minibar/debug.php');
    }

    /**
     * Returns the orientation in the minibar.
     *
     * @api
     * @return string 'right'
     */
    public function getOrientation()
    {
        return self::RIGHT;
    }
}
