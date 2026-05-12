
<?php

/**
 * This file is part of the Minibar package.
 *
 * Fragment: provide the basic backend-structure of the minibar and render all
 * elements. The elements are rendered by calling their render() method via
 * sub-fragment.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** @var rex_fragment $this */

$elements = $this->getVar('elements', []);

?>
<div class="rex-minibar rex-minibar-backend">
    <div class="rex-minibar-elements">
        <?php
        foreach ($elements as $element) {
            $this->subfragment('minibar/element.php', [
                'element' => $element,
            ]);
        }
        ?>
    </div>
</div>
