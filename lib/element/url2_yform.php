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

        if (!$url2Info || !$url2Info['is_yform_table']) {
            return '';
        }

        // Check permissions
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

        return
            '<div class="rex-minibar-item">
                <span class="rex-minibar-icon">
                    <i class="rex-minibar-icon--fa rex-minibar-icon--fa-database"></i>
                </span>
                <span class="rex-minibar-value">
                    ' . $editButton . '
                </span>
            </div>';
    }

    private function getUrl2Info(): ?array
    {
        try {
            // Check if URL2 addon is available
            if (!rex_addon::get('url2')->isAvailable()) {
                return null;
            }

            // Get current URL
            $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
            
            // Try to resolve URL with URL2
            $url = \Url\Url::resolveCurrent();
            if (!$url) {
                return null;
            }

            // Get URL data
            $urlData = $url->getData();
            if (empty($urlData)) {
                return null;
            }

            // Check if this URL has YForm table data
            $tableData = $this->analyzeUrl2Url($url);
            if (!$tableData) {
                return null;
            }

            return [
                'url' => $currentUrl,
                'is_yform_table' => true,
                'table' => $tableData['table_name'],
                'table_label' => $tableData['table_label'],
                'record_id' => $tableData['record_id'],
                'url_data' => $urlData
            ];
        } catch (\Exception $e) {
            // URL2 not available or error occurred
            return null;
        }
    }

    private function analyzeUrl2Url($url): ?array
    {
        try {
            if (!$url) {
                return null;
            }

            $urlData = $url->getData();
            if (empty($urlData)) {
                return null;
            }

            // Look for YForm table references in URL data
            $possibleTables = [];
            
            // Check for table_name in URL data
            if (isset($urlData['table_name'])) {
                $possibleTables[] = $urlData['table_name'];
            }

            // Check for other common YForm patterns in URL data
            foreach ($urlData as $key => $value) {
                if (is_string($value) && rex_yform_manager_table::get($value)) {
                    $possibleTables[] = $value;
                }
            }

            // Find the first valid YForm table
            foreach ($possibleTables as $tableName) {
                $table = rex_yform_manager_table::get($tableName);
                if ($table) {
                    // Try to find record ID in URL data
                    $recordId = null;
                    
                    // Common ID field patterns
                    $idFields = ['id', 'data_id', 'dataset_id', $tableName . '_id'];
                    foreach ($idFields as $field) {
                        if (isset($urlData[$field]) && is_numeric($urlData[$field])) {
                            $recordId = (int)$urlData[$field];
                            break;
                        }
                    }

                    return [
                        'table_name' => $tableName,
                        'table_label' => $table->getLabel() ?: $tableName,
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
