<?php


namespace FriendsOfRedaxo\Minibar;

use FriendsOfRedaxo\Minibar\Element\AbstractElement;
use FriendsOfRedaxo\Minibar\Settings\Scope;
use rex;
use rex_backend_login;
use rex_config;
use rex_fragment;
use rex_response;
use rex_singleton_trait;

class Minibar
{
    use rex_singleton_trait;

    /**
     * @var bool|null
     */
    private $isActive;

    /** @var rex_minibar_element[] */
    private $elements = [];

    public function addElement(AbstractElement $instance)
    {
        $this->elements[] = $instance;
    }

    /**
     * Identifiziert eine Elementklasse entweder über den Klassennamen im Klartext
     * oder über den als MD5 kodierten Klassennamen.
     * (zur Info, dem Hash steht ein M voran)
     * 
     * @param string $className
     *
     * @return rex_minibar_element|null
     */
    public function elementByClass($className)
    {
        foreach ($this->elements as $element) {
            if ($element::class === $className || $element->jsId() === $className) {
                return $element;
            }
        }
        return null;
    }

    public function get()
    {
        if (!self::shouldRender()) {
            return null;
        }

        if (!count($this->elements)) {
            return null;
        }

        $fragment = new rex_fragment([
            'elements' => $this->elements,
        ]);

        if (rex::isBackend()) {
            return $fragment->parse('minibar/backend.php');
        }

        return $fragment->parse('minibar/frontend.php');
    }

    /**
     * Returns if the minibar should be rendered.
     *
     * @return bool
     */
    public function shouldRender()
    {
        if (is_bool($this->isActive)) {
            return $this->isActive;
        }

        $user = rex_backend_login::createUser();
        if (!$user) {
            return false;
        }

        $enabled = rex_config::get('minibar', 'enabled', Scope::ENABLED_EVERYWHERE);
        if ($enabled === Scope::ENABLED_EVERYWHERE) {
            return true;
        }
        if ($enabled === Scope::ENABLED_BACKEND) {
            return rex::isBackend();
        }
        if ($enabled === Scope::ENABLED_FRONTEND) {
            return rex::isFrontend();
        }
        return false;
    }

    /**
     * Returns if the minibar is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return !rex_cookie('rex_minibar_frontend_hidden', 'bool', false);
    }

    /**
     * Sets the visibility.
     *
     * @param bool $value
     */
    public function setVisibility($value)
    {
        if ($value) {
            rex_response::sendCookie('rex_minibar_frontend_hidden', '');
        } else {
            rex_response::sendCookie('rex_minibar_frontend_hidden', '1', ['expires' => time() + rex::getProperty('session_duration'), 'samesite' => 'strict']);
        }
    }

    /**
     * @param bool $isActive
     */
    public function setActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return bool|null
     */
    public function isActive()
    {
        return $this->isActive;
    }
}
