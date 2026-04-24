<?php

use FriendsOfRedaxo\Minibar\Api\LazyLoader;
use FriendsOfRedaxo\Minibar\Minibar;

/** @var rex_fragment $this */

?>
<div class="rex-minibar" data-minibar="<?= Minibar::getInstance()->isVisible() ? 'true' : 'false' ?>">
    <a class="rex-minibar-opener" href="<?= rex_getUrl('', '', ['visibility' => true] + LazyLoader::getUrlParams()) ?>">
        <i class="rex-minibar-opener-icon"></i>
    </a>
    <a class="rex-minibar-close" href="<?= rex_getUrl('', '', ['visibility' => false] + LazyLoader::getUrlParams()) ?>">
        <i class="rex-minibar-close-icon"></i>
    </a>
    <div class="rex-minibar-elements">
        <?php
        foreach ($this->elements as $element) {
            $this->subfragment('minibar/minibar_element.php', [
                'element' => $element,
            ]);
        }
        ?>
    </div>
</div>
