<?php
/**
 * This file is part of the Quick Navigation package.
 *
 * @author (c) Friends Of REDAXO
 * @author <friendsof@redaxo.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class rex_minibar_element_debug extends rex_minibar_element
{
    public function render()
    {
	$links = '';    
	if (rex::getUser()->isAdmin()) {
	    $links = '	    
                <div class="rex-minibar-info-piece">
                    <span class="title">'.rex_i18n::msg('minibar_debug_links').'</span>
                    <span>
                        <a href="/redaxo/index.php?page=system">'.rex_i18n::msg('minibar_debug_system_settings').'</a>
                    </span>
                    <br>';
if (rex_addon::get('debug')->isAvailable())	
{
	 $links .= '<span>
                 <a href="/redaxo/index.php?page=debug" target="_blank">'.rex_i18n::msg('minibar_debug_start_debug').'</a>
         </span>
';}

 $links .= '</div>';
}    
        return
        '
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
            '.rex_i18n::msg('debug_mode').'
            </span>
        </div>
<div class="rex-minibar-info">
        <div class="rex-minibar-info-header"><i class="rex-minibar-debug rex-minibar-icon--fa rex-minibar-icon--fa-heartbeat"></i>  '.rex_i18n::msg('minibar_debug_header').'</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">'.rex_i18n::msg('minibar_debug_info').'</span>
                    <span>
                        '.rex_i18n::msg('minibar_debug_info_text').'
                    </span>
                </div>
             '.$links.'
            </div>
        </div>
        ';
    }

    public function getOrientation()
    {
        return rex_minibar_element::RIGHT;
    }
}

