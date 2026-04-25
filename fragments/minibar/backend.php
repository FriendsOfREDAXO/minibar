
<?php

/** rex_fragment $this */

?>
<div class="rex-minibar rex-minibar-backend">
    <div class="rex-minibar-elements">
        <?php
        foreach ($this->elements as $element) {
            $this->subfragment('minibar/element.php', [
                'element' => $element,
            ]);
        }
        ?>
    </div>
</div>
