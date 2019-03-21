<?php

$addon = rex_addon::get('minibar');

// kommt mit be_style addon
if (class_exists('rex_scss_compiler')) {
    $compiler = new rex_scss_compiler();

    $compiler->setRootDir(rex_path::addon('minibar/scss'));
    $compiler->setScssFile([$addon->getPath('scss/styles.scss')]);

    // Compile in backend assets dir
    $compiler->setCssFile($addon->getPath('assets/styles.css'));
    $compiler->compile();
}
