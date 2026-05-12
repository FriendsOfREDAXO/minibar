</div><!-- END .rex-page -->
<?php

/**
 * This file is part of the Minibar package.
 *
 * replace the core-fragment bottom.php to add the minibar output before the
 * closing body-tag.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FriendsOfRedaxo\Minibar\Minibar;

$page = rex_be_controller::getCurrentPageObject();
if (null !== $page && 'login' !== $page->getFullKey() && !$page->isPopup()): ?>
    <button type="button" class="navbar-toggle rex-js-nav-main-toggle" data-toggle="collapse" data-target=".rex-nav-main > .navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
<?php endif ?>
<?= Minibar::getInstance()->get() ?>
</body>
</html>
