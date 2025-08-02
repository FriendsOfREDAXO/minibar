<?php
/**
 * @package redaxo\minibar\url2_yform
 */
class rex_minibar_element_url2_yform extends rex_minibar_lazy_element
{
    public function render()
    {
        // create the backend user session, in case it is missing (e.g. in frontend).
        // we do it once beforehand, so we can save the check on each later callsite
        rex_backend_login::createUser();

        return parent::render();
    }

    protected function renderFirstView()
    {
        $url2Info = $this->getUrl2Info();

        if (!$url2Info || !$url2Info['is_yform_table']) {
            return '';
        }

        return
            '<div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                   <i class="rex-minibar-icon--fa rex-minibar-icon--fa-database"></i>
            </span>
            <span class="rex-minibar-value">
                ' . rex_escape($url2Info['table_label']) . ' gefunden
            </span>
        </div>';
    }

    protected function renderComplete()
    {
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
            ' . rex_escape($url2Info['table_label']) . '
            </span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">YForm Datensatz</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">Tabelle</span>
                    <span>' . rex_escape($url2Info['table_label']) . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">Datensatz ID</span>
                    <span>' . rex_escape($url2Info['record_id']) . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title"></span>
                    <span>' . $editButton . '</span>
                </div>
            </div>
        </div>
        ';
    }

    /**
     * Analyze current URL to detect URL2/YForm patterns using proper URL2 API
     */
    private function getUrl2Info(): ?array
    {
        // Only in frontend
        if (rex::isBackend()) {
            return null;
        }

        // Check if url addon is available
        if (!rex_addon::get('url')->isAvailable()) {
            return null;
        }

        try {
            // Use URL2 API to resolve current URL
            $urlManager = \Url\Url::resolveCurrent();
            
            // If no URL manager found, this is not a URL2-managed URL
            if (!$urlManager) {
                return null;
            }

            // Get the profile and dataset information
            $profile = $urlManager->getProfile();
            if (!$profile) {
                return null;
            }

            $dataset = $urlManager->getDataset();
            $tableName = $profile->getTableName();
            
            // Verify this is actually a custom URL2 table, not a standard REDAXO article
            if (empty($tableName) || $tableName === 'rex_article') {
                return null;
            }
            
            // Get YForm table info
            $yformTables = $this->getYFormTables();
            $tableInfo = null;
            
            foreach ($yformTables as $table) {
                if ($table['name'] === $tableName) {
                    $tableInfo = $table;
                    break;
                }
            }
            
            // Only return info if it's a YForm table
            if (!$tableInfo) {
                return null;
            }
            
            return [
                'table' => $tableName,
                'table_label' => $tableInfo['label'],
                'record_id' => $dataset ? $dataset->getId() : $urlManager->getDatasetId(),
                'record_identifier' => $urlManager->getDatasetId(),
                'record_data' => $dataset ? $dataset->getData() : null,
                'profile_id' => $urlManager->getProfileId(),
                'url_manager' => $urlManager,
                'profile' => $profile,
                'is_yform_table' => true
            ];
            
        } catch (\Exception $e) {
            // URL2 couldn't resolve the current URL or other error occurred
            // Don't log this as it's expected behavior for non-URL2 URLs
            return null;
        } catch (\Error $e) {
            // Fatal errors like "Call to a member function on null"
            // Don't log this as it's expected behavior for broken URL2 states
            return null;
        }
    }

    /**
     * Get all YForm tables
     */
    private function getYFormTables(): array
    {
        if (!rex_addon::get('yform')->isAvailable()) {
            return [];
        }

        try {
            // Get YForm tables from database - use correct column names
            $sql = rex_sql::factory();
            $sql->setQuery('SELECT table_name, name, status FROM ' . rex::getTable('yform_table') . ' WHERE status = 1 ORDER BY name, table_name');
            
            $tables = [];
            
            while ($sql->hasNext()) {
                $table = [
                    'name' => $sql->getValue('table_name'), // The actual table name in database
                    'label' => $sql->getValue('name') ?: $sql->getValue('table_name'), // The display name
                    'status' => $sql->getValue('status')
                ];
                
                $tables[] = $table;
                $sql->next();
            }
            
            return $tables;
        } catch (\Exception $e) {
            return [];
        }
    }
}
