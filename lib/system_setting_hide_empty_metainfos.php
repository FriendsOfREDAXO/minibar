<?php

class rex_system_setting_minibar_hide_empty_metainfos extends rex_system_setting
{
    public const SHOW = 1;
    public const HIDE = -1;

    public function getKey()
    {
        return 'minibar_hide_empty_metainfos';
    }

    public function getField()
    {
        $field = new rex_form_select_element();
        $field->setAttribute('class', 'form-control selectpicker');
        $field->setLabel(rex_i18n::msg('minibar_system_setting_hide_empty_metainfos'));
        $select = $field->getSelect();
        $select->addOption(rex_i18n::msg('minibar_metainfo_show'), self::SHOW);
        $select->addOption(rex_i18n::msg('minibar_metainfo_hide'), self::HIDE);
        $select->setSelected(rex_config::get('minibar', 'hide_empty_metainfos', self::SHOW));
        return $field;
    }

    public function setValue($value)
    {
        $value = (int) $value;
        rex_config::set('minibar', 'hide_empty_metainfos', $value);
        return true;
    }
}
