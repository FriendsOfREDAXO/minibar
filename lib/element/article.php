<?php
/**
 * @package redaxo\structure\content\minibar
 */
class rex_minibar_element_structure_article extends rex_minibar_lazy_element
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
        $article = $this->getArticle();

        if (!$article instanceof rex_article) {
            return '';
        }

        // Return if user have no rights to the site start article
        if (rex::isBackend() && !rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($article->getCategoryId())) {
            return '';
        }

        return
            '<div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                   <i class="rex-minibar-icon--fa rex-minibar-icon--fa-file-lines"></i>
            </span>
            <span class="rex-minibar-value">
                Artikel "' . $article->getName() . '"
            </span>
        </div>';
    }

    protected function renderComplete()
    {
        $articleId = rex_get('article_id');
        $languageId = rex_get('current_lang');
        $article = rex_article::get($articleId, $languageId);

        if (!$article instanceof rex_article) {
            return '';
        }

        // Return if user have no rights to the site start article
        if (rex::isBackend() && !rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($article->getCategoryId())) {
            return '';
        }

        $articleLink = '<a href="' . rex_url::backendPage('content/edit', ['article_id' => $article->getId(), 'category_id' => $article->getCategoryId(), 'clang' => $article->getClangId(), 'mode' => 'edit']) . '">' . rex_i18n::msg('structure_content_minibar_article_edit') . ' </a>';
        if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($article->getCategoryId())) {
            $articleLink = rex_i18n::msg('no_rights_to_edit');
        } elseif (rex::isBackend()) {
            $articleLink = '<a href="' . rex_getUrl($article->getId(), $article->getClangId()) . '">' . rex_i18n::msg('structure_content_minibar_article_show') . '</a>';
        }

        $articlePath = [];
        $tree = $article->getParentTree();
        if (!$article->isStartarticle()) {
            $tree[] = $article;
        }
        foreach ($tree as $parent) {
            $id = $parent->getId();
            $item = rex_escape($parent->getName());
            if (rex::isBackend() && rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($id) && $parent->isStartarticle()) {
                $item = '<a href="' . rex_url::backendPage('structure', ['category_id' => $id, 'clang' => $article->getClangId()]) . '">' . rex_escape($parent->getName()) . '</a>';
            } elseif (!rex::isBackend()) {
                $item = '<a href="' . $parent->getUrl() . '">' . rex_escape($parent->getName()) . '</a>';
            }
            $articlePath[] = $item;
        }

        $groups = rex_extension::registerPoint(new rex_extension_point('MINIBAR_ARTICLE', '', [
            'article' => $article,
        ]));

        return
            '<div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                 <i class="rex-minibar-icon--fa rex-minibar-icon--fa-file-lines"></i>
            </span>
            <span class="rex-minibar-value">
            Artikel "' . $article->getName() . '"
            </span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">' . rex_i18n::msg('structure_info') . '</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">' . rex_i18n::msg('structure_article_name') . '</span>
                    <span>' . rex_escape($article->getName()) . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">' . rex_i18n::msg('structure_status') . '</span>
                    <span class="rex-minibar-status-' . ($article->isOnline() ? 'green' : 'red') . '">' . ($article->isOnline() ? '<i class="rex-icon rex-icon-online"></i>' : '<i class="rex-icon rex-icon-offline"></i>') . ' ' . ($article->isOnline() ? rex_i18n::msg('status_online') : rex_i18n::msg('status_offline')) . '</span>                    
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">' . rex_i18n::msg('structure_path') . '</span>
                    <span>' . implode(' / ', $articlePath) . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title"></span>
                    <span>' . $articleLink . '</span>
                </div>
            </div>
            ' . $groups . '
        </div>
        ';
    }

    private function getArticle()
    {
        $clangId = rex_request('clang', 'int');
        $clangId = rex_clang::exists($clangId) ? $clangId : rex_clang::getStartId();

        if (rex::isBackend()) {
            $article = rex_article::get(rex_request('article_id', 'int'), $clangId);

            if (!$article) {
                $article = rex_article::get(rex_request('category_id', 'int'), $clangId);
            }
        } else {
            $article = rex_article::getCurrent();
        }

        if (!$article) {
            $article = rex_article::getSiteStartArticle();
        }

        return $article;
    }
}
