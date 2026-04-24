<?php
/** @var AbstractElement $element */

use FriendsOfRedaxo\Minibar\Api\LazyLoader;
use FriendsOfRedaxo\Minibar\Element\AbstractElement;
use FriendsOfRedaxo\Minibar\Element\AbstractLazyElement;

$element = $this->element;

$class = 'rex-minibar-element ';
$class .= $element->cssClass();
$class .= (AbstractElement::RIGHT == $element->getOrientation() ? ' rex-minibar-element-right' : '');
$class .= ($element->isDanger() ? ' rex-minibar-status-danger' : '');
$class .= ($element->isWarning() ? ' rex-minibar-status-warning' : '');
$class .= ($element->isPrimary() ? ' rex-minibar-status-primary' : '');

$onmouseover = '';
if ($element instanceof AbstractLazyElement && AbstractLazyElement::isFirstView()) {
    $elementId = $element->jsId();
    $context = rex_context::restore();
    $url = $context->getUrl(['lazy_element' => $elementId, 'article_id' => rex_article::getCurrentId(), 'current_lang' => rex_clang::getCurrentId()] + LazyLoader::getUrlParams());
    $onmouseover = <<<EOD
            var that = this;
            window._rex_minibar_req = window._rex_minibar_req || {};
            if (window._rex_minibar_req.$elementId) return;
            
            window._rex_minibar_req.$elementId = true;
            window.fetch('$url', {
                method: 'post',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('HTTP error, status = ' + response.status);
                }
                return response.text();
            })
            .then(function(text) {
                that.outerHTML = text;
                window._rex_minibar_req.$elementId = false;
            })
            .catch(function(error) {
                console.error(error)
                window._rex_minibar_req.$elementId = false;
            });
        EOD;
}

?>
<div class="<?= $class ?>" onmouseover="<?= $onmouseover ?>"><?= $element->render() ?></div>
