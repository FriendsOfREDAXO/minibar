<?php

/**
 * This file is part of the Minibar package.
 *
 * Fragment for rendering the debug element in the minibar. The element is rendered
 * by calling its render() method.
 *
 * @author (c) Friends Of REDAXO
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** @var rex_fragment $this */

$adminLinks = '';

if ($this->getVar('adminLinks', false)) {
    $adminLinks .= sprintf(
        '<span><a href="%s">%s</a></span><br>',
        rex_url::backendPage('system'),
        rex_i18n::msg('minibar_debug_system_settings'),
    );
    if ($this->getVar('debugLink', false)) {
        $adminLinks .= sprintf('<span><a href="%s" target="_blank">%s</a></span>',
            rex_url::backendPage('debug'),
            rex_i18n::msg('minibar_debug_start_debug'),
        );
    }
        $adminLinks = sprintf(
            '<div class="rex-minibar-info-piece"><span class="title">%s</span>%s</div>',
            rex_i18n::msg('minibar_debug_links'),
            $adminLinks,
        );
}

?>
<style>

.rex-minibar-debug {
    animation:rex-pulse 5s ease infinite;
	color: #f09000;
}

@keyframes rex-pulse {
    0% {
        transform:scale(1)
    }

    5% {
        transform:scale(1.15)
    }

    20% {
        transform:scale(1)
    }

    30% {
        transform:scale(1)
    }

    35% {
        transform:scale(1.15)
    }

    50% {
        transform:scale(1)
    }

    55% {
        transform:scale(1.25)
    }

    70% {
        transform:scale(1)
    }
}

</style>
<div class="rex-minibar-item">
    <span class="rex-minibar-icon">
        <i class="rex-minibar-debug rex-minibar-icon--fa rex-minibar-icon--fa-heartbeat"></i> 
    </span>
    <span class="rex-minibar-value">
        <?= rex_i18n::msg('debug_mode') ?>
    </span>
</div>
<div class="rex-minibar-info">
    <div class="rex-minibar-info-header">
        <i class="rex-minibar-debug rex-minibar-icon--fa rex-minibar-icon--fa-heartbeat"></i>
        <?= rex_i18n::msg('minibar_debug_header')?>
    </div>
    <div class="rex-minibar-info-group">
        <div class="rex-minibar-info-piece">
            <span class="title"><?= rex_i18n::msg('minibar_debug_info') ?></span>
            <span>
                <?= rex_i18n::msg('minibar_debug_info_text') ?>
            </span>
        </div>
        <?= $adminLinks ?>
    </div>
</div>
