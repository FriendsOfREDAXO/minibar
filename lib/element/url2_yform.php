<?php
/**
 * @package redaxo\minibar\url2_yform
 */
class rex_minibar_element_url2_yform extends rex_minibar_element
{
    public function render()
    {
        // Einfachste Version - immer anzeigen
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-database"></i>
            </span>
            <span class="rex-minibar-value">TEST ELEMENT</span>
        </div>';
    }

    public function getOrientation()
    {
        return rex_minibar_element::RIGHT;
    }

    private function getUrl2Info(): ?array
    {
        try {
            // Check if URL2 addon is available
            if (!rex_addon::get('url')->isAvailable()) {
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
