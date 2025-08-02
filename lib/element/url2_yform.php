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

        // Check if user has permissions for YForm/Backend access
        $hasPermissions = false;
        if (rex::isBackend()) {
            $hasPermissions = $user->isAdmin() || $user->hasPerm('yform');
        } else {
            // Frontend: Extended permission check for backend users
            $hasPermissions = $user->isAdmin() || 
                            $user->hasPerm('yform') || 
                            $user->hasPerm('structure') || 
                            $user->hasPerm('content');
        }

        if (!$hasPermissions) {
            return '';
        }

        // Always show the basic item, even if no URL2 data found
        $status = 'rex-minibar-url2-none';
        $value = 'URL2/YForm';
        
        if ($url2Info && $url2Info['is_yform_table']) {
            $status = 'rex-minibar-url2-found';
            $value = $url2Info['table_label'];
        }

        $item = 
            '<div class="rex-minibar-item">
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

            $editButton = $editUrl ? 
                '<a href="' . $editUrl . '" target="_blank" style="background-color: #22c55e; color: white; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: 500;">Datensatz bearbeiten</a>' :
                '<span style="color: #888; font-size: 11px;">Kein Datensatz gefunden</span>';

            $info = 
                '<div class="rex-minibar-info">
                    <div class="rex-minibar-info-header">YForm Datensatz</div>
                    <div class="rex-minibar-info-group">
                        <div class="rex-minibar-info-piece">
                            <span class="title">Tabelle</span>
                            <span>' . rex_escape($url2Info['table_label']) . '</span>
                        </div>
                        <div class="rex-minibar-info-piece">
                            <span class="title">Datensatz ID</span>
                            <span>' . rex_escape($url2Info['record_id'] ?: 'Nicht gefunden') . '</span>
                        </div>
                        <div class="rex-minibar-info-piece">
                            <span class="title"></span>
                            <span>' . $editButton . '</span>
                        </div>
                    </div>
                </div>';
        }

        return $item . $info;
    }

    public function getOrientation()
    {
        return rex_minibar_element::RIGHT;
    }

    private function getUrl2Info(): ?array
    {
        try {
            // Check if URL2 addon is available
            if (!rex_addon::get('url2')->isAvailable()) {
                return null;
            }

            // Simple test: Check if we're on a URL that could have YForm data
            $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
            
            // For testing purposes, return some data if we have URL parameters
            // This is a simplified approach for now
            if (empty($_GET)) {
                return null;
            }

            // Look for common YForm table patterns in URL
            $possibleTables = [];
            
            // Check for table parameter
            if (isset($_GET['table'])) {
                $possibleTables[] = $_GET['table'];
            }
            
            // Check for other common patterns
            foreach (['products', 'news', 'events', 'categories'] as $commonTable) {
                if (rex_yform_manager_table::get($commonTable)) {
                    $possibleTables[] = $commonTable;
                }
            }

            // Find the first valid YForm table
            foreach ($possibleTables as $tableName) {
                $table = rex_yform_manager_table::get($tableName);
                if ($table) {
                    // Try to find record ID
                    $recordId = null;
                    $idFields = ['id', 'data_id', $tableName . '_id'];
                    foreach ($idFields as $field) {
                        if (isset($_GET[$field]) && is_numeric($_GET[$field])) {
                            $recordId = (int)$_GET[$field];
                            break;
                        }
                    }

                    return [
                        'url' => $currentUrl,
                        'is_yform_table' => true,
                        'table' => $tableName,
                        'table_label' => $tableName, // Simplified - just use table name
                        'record_id' => $recordId
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
