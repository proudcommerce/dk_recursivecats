<?php
/**
 * @package ProudCommerce
 * @subpackage oxid/dk/recursivecats
 * @author Florian Palme <florian@proudcommerce.com>
 */

namespace ProudCommerce\OXID\DK\RecursiveCats\Application\Controller;


class ArticleListController extends ArticleListController_parent
{
    /**
     * We need to overwrite the default function.. how sad this is
     *
     * @param \OxidEsales\Eshop\Application\Model\Category $category
     * @return object|\oxArticleList|\OxidEsales\Eshop\Application\Model\ArticleList
     */
    protected function _loadArticles($category)
    {
        $config = $this->getConfig();

        $numberOfCategoryArticles = (int) $config->getConfigParam('iNrofCatArticles');
        $numberOfCategoryArticles = $numberOfCategoryArticles ? $numberOfCategoryArticles : 1;

        // load only articles which we show on screen
        $articleList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $articleList->setSqlLimit($numberOfCategoryArticles * $this->_getRequestPageNr(), $numberOfCategoryArticles);
        $articleList->setCustomSorting($this->getSortingSql($this->getSortIdent()));

        if ($category->isPriceCategory()) {
            $priceFrom = $category->oxcategories__oxpricefrom->value;
            $priceTo = $category->oxcategories__oxpriceto->value;

            $this->_iAllArtCnt = $articleList->loadPriceArticles($priceFrom, $priceTo, $category);
        } else {
            $sessionFilter = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('session_attrfilter');

            /**
             * Changes by Module
             */
            $activeCategoryId = $category->getId();
            $this->_iAllArtCnt = $articleList->loadCategoryArticles($activeCategoryId, $sessionFilter);

            $aCatIds = array();
            foreach ($category->getSubCats() as $subCat) {
                $aCatIds[] = $subCat->oxcategories__oxid->value;
            }
            $aCatIds[] = $activeCategoryId;
            $this->_iAllArtCnt = $articleList->loadCategoriesArticles($aCatIds, $sessionFilter);
            /**
             * End of Changes by Module
             */
        }

        $this->_iCntPages = ceil($this->_iAllArtCnt / $numberOfCategoryArticles);

        return $articleList;
    }
}