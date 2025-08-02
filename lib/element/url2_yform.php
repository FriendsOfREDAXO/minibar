<?php
/**
 * @package redaxo\minibar\url2_yform
 */
class rex_minibar_element_url2_yform extends rex_minibar_element
{
    public function render()
    {
        // create the backend user session, in case it is missing (e.g. in frontend).
        // we do it once beforehand, so we can save the check on each later callsite
        rex_backend_login::createUser();

        $url2Info = $this->getUrl2Info();

        // Check permissions first
        $user = rex_backend_login::createUser();
        if (!$user) {
            return '';
        }

        // Das Widget ist für alle eingeloggten Backend-Benutzer sichtbar
        // Die spezifischen Berechtigungen werden bei den Buttons geprüft

        // Für spezifische YForm-Tabellenberechtigung prüfen wir später pro Tabelle

        // Always show the basic item
        $status = 'rex-minibar-url2-none';
        $value = rex_i18n::msg('minibar_url2_yform_title');
        $itemStyle = '';
        
        if ($url2Info && $url2Info['is_yform_table']) {
            $status = 'rex-minibar-url2-found';
            $value = $url2Info['table_label'];
            // Noch dunklerer grüner Hintergrund wenn YForm-Daten gefunden
            $itemStyle = ' style="background-color: #14532d; color: white; border-radius: 4px; padding: 2px 6px;"';
        }

        $item = 
            '<div class="rex-minibar-item"' . $itemStyle . '>
                <span class="rex-minibar-icon">
                    <i class="rex-minibar-icon--fa rex-minibar-icon--fa-database ' . $status . '"></i>
                </span>
                <span class="rex-minibar-value">
                    ' . rex_escape($value) . '
                </span>
            </div>';

        $info = '';
        if ($url2Info && $url2Info['is_yform_table']) {
            // Get CSRF token for YForm operations
            $csrf_token = null;
            if (rex::isFrontend() && rex_backend_login::hasSession()) {
                rex::setProperty('redaxo', true);
                try {
                    $table = rex_yform_manager_table::get($url2Info['table']);
                    if ($table) {
                        $_csrf_key = $table->getCSRFKey();
                        $_csrf_params = rex_csrf_token::factory($_csrf_key)->getUrlParams();
                        $csrf_token = $_csrf_params['_csrf_token'];
                    }
                } catch (\Exception $e) {
                    // CSRF token generation failed, continue without token
                }
                rex::setProperty('redaxo', false);
            }

            // Build edit URL
            $editUrl = '';
            $tableUrl = '';
            if ($url2Info['record_id']) {
                $recordParams = [
                    'table_name' => $url2Info['table'],
                    'data_id' => $url2Info['record_id'],
                    'func' => 'edit'
                ];
                if ($csrf_token) {
                    $recordParams['_csrf_token'] = $csrf_token;
                }
                
                $editUrl = rex_url::backendPage('yform/manager/data_edit', $recordParams);
            }
            
            // Build table overview URL
            $tableParams = [
                'table_name' => $url2Info['table']
            ];
            $tableUrl = rex_url::backendPage('yform/manager/data_edit', $tableParams);

            // Check YForm table permissions for this specific table
            $table = rex_yform_manager_table::get($url2Info['table']);
            $canEditTable = false;
            $canViewTable = false;
            
            if ($table && $user) {
                // Admin kann alles
                if ($user->isAdmin()) {
                    $canEditTable = true;
                    $canViewTable = true;
                } else {
                    // Prüfen der spezifischen YForm-Tabellenberechtigung
                    $tablePermKey = 'yform_manager_table_edit[' . $url2Info['table'] . ']';
                    $tableViewPermKey = 'yform_manager_table_view[' . $url2Info['table'] . ']';
                    
                    $canEditTable = $user->hasPerm($tablePermKey);
                    $canViewTable = $user->hasPerm($tablePermKey) || 
                                   $user->hasPerm($tableViewPermKey);
                }
            }

            $editButton = ($editUrl && $canEditTable) ? 
                '<a href="' . $editUrl . '" target="_blank" style="background-color: #22c55e; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: 500; margin-right: 8px;">' . rex_i18n::msg('minibar_url2_yform_edit_record') . '</a>' :
                '<span style="color: #888; font-size: 11px; margin-right: 8px;">' . ($editUrl ? rex_i18n::msg('minibar_url2_yform_no_permission') : rex_i18n::msg('minibar_url2_yform_no_record')) . '</span>';
                
            $tableButton = $canViewTable ? 
                '<a href="' . $tableUrl . '" target="_blank" style="background-color: #3b82f6; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: 500;">' . rex_i18n::msg('minibar_url2_yform_open_table') . '</a>' :
                '<span style="color: #888; font-size: 11px;">' . rex_i18n::msg('minibar_url2_yform_no_permission') . '</span>';

            $info = 
                '<div class="rex-minibar-info">
                    <div class="rex-minibar-info-header">' . rex_i18n::msg('minibar_url2_yform_header_found') . '</div>
                    <div class="rex-minibar-info-group">
                        <div class="rex-minibar-info-piece">
                            <span class="title">' . rex_i18n::msg('minibar_url2_yform_table') . '</span>
                            <span>' . rex_escape($url2Info['table_label']) . '</span>
                        </div>
                        <div class="rex-minibar-info-piece">
                            <span class="title">' . rex_i18n::msg('minibar_url2_yform_record_id') . '</span>
                            <span>' . rex_escape($url2Info['record_id'] ?: rex_i18n::msg('minibar_url2_yform_not_found')) . '</span>
                        </div>
                        <div class="rex-minibar-info-piece">
                            <span class="title"></span>
                            <span>' . $editButton . $tableButton . '</span>
                        </div>
                    </div>
                </div>';
        } else {
            // Info für den Fall, wenn keine URL2/YForm-Daten gefunden wurden
            $info = 
                '<div class="rex-minibar-info">
                    <div class="rex-minibar-info-header">' . rex_i18n::msg('minibar_url2_yform_header_info') . '</div>
                    <div class="rex-minibar-info-group">
                        <div class="rex-minibar-info-piece">
                            <span class="title">Info</span>
                            <span>' . rex_i18n::msg('minibar_url2_yform_no_data') . '</span>
                        </div>
                    </div>
                </div>';
        }

        return $item . $info;
    }

    public function getOrientation()
    {
        return rex_minibar_element::LEFT;
    }

    private function getUrl2Info(): ?array
    {
        try {
            // Check if URL addon is available
            if (!rex_addon::get('url')->isAvailable()) {
                return null;
            }

            // Try to resolve current URL with URL2
            $url = \Url\Url::resolveCurrent();
            if (!$url) {
                return null;
            }

            // Get the resolved URL data (this is a YForm dataset object, not array)
            $dataset = $url->getDataset();
            
            // Get table name from URL profile
            $profile = $url->getProfile();
            if (!$profile) {
                return null;
            }

            $tableName = $profile->getTableName();
            if (!$tableName) {
                return null;
            }

            // Check if it's a YForm table
            $table = rex_yform_manager_table::get($tableName);
            if (!$table) {
                return null;
            }

            // Get record ID from dataset (object method, not array access)
            $recordId = null;
            if ($dataset && method_exists($dataset, 'getId')) {
                $recordId = $dataset->getId();
            }

            // Use table description (name field) instead of technical table name
            $tableLabel = $table->getName() ?: $tableName;

            return [
                'url' => $_SERVER['REQUEST_URI'] ?? '',
                'is_yform_table' => true,
                'table' => $tableName,
                'table_label' => $tableLabel, // Use human-readable table description from name field
                'record_id' => $recordId,
                'dataset' => $dataset
            ];
            
        } catch (\Exception $e) {
            // URL2 not available or error occurred
            return null;
        }
    }
}
