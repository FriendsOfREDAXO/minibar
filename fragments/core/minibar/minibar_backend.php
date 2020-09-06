<div class="rex-minibar rex-minibar-backend"></div>

<script>
var minibarContent = `
<?php
$addon = rex_addon::get('minibar');
$styles = $addon->getAssetsUrl('styles.css').'?v='. $addon->getVersion();
echo '<link rel="stylesheet" type="text/css" media="all" href="'. $styles .'">';
?>
<div class="rex-minibar-elements">
    <?php
    foreach ($this->elements as $element) {
        $subFragment = $this->getSubfragment('core/minibar/minibar_element.php', [
            'element' => $element,
        ]);
        echo str_replace('\\', '\\\\', $subFragment);
    }
    ?>
</div>`;

var shadow = document.querySelector('.rex-minibar').attachShadow({mode: 'open'});
shadow.innerHTML = minibarContent;
</script>
