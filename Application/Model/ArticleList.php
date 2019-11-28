<?php
/**
 * @package ProudCommerce
 * @subpackage oxid/dk/recursivecats
 * @author Florian Palme <florian@proudcommerce.com>
 */

namespace ProudCommerce\OXID\DK\RecursiveCats\Application\Model;


use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

class ArticleList extends ArticleList_parent
{
    /**
     * Loads articles for the given Categories
     *
     * @param array $aCatIds Category tree IDs
     * @param array $aSessionFilter Like array ( catid => array( attrid => value,...))
     * @param int $iLimit Limit
     * @return integer total Count of Articles in this Category
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    public function loadCategoriesArticles( $aCatIds, $aSessionFilter, $iLimit = null ): int
    {
        $sArticleFields = $this->getBaseObject()->getSelectFields();

        $sSelect = $this->_getCategoriesSelect( $sArticleFields, $aCatIds, $aSessionFilter );

        $sCntSelect = $this->_getCategoriesCountSelect( $aCatIds, $aSessionFilter );

        $db = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $iArticleCount = $db->getOne( $sCntSelect );

        if ($iLimit = (int) $iLimit) {
            $sSelect .= " LIMIT $iLimit";
        }

        $this->selectString( $sSelect );

        return $iArticleCount;
    }

    /**
     * Creates SQL Statement to load Articles from multiple categories, etc.
     *
     * @param string $sFields Fields which are loaded e.g. "oxid" or "*" etc.
     * @param $aCatIds
     * @param array $aSessionFilter Like array ( catid => array( attrid => value,...))
     *
     * @return string SQL
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    protected function _getCategoriesSelect( $sFields, $aCatIds, $aSessionFilter = null ): string
    {
        /** @var TableViewNameGenerator $tableViewNameGenerator */
        $tableViewNameGenerator = Registry::get(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName( 'oxarticles' );
        $sO2CView      = $tableViewNameGenerator->getViewName( 'oxobject2category' );

        // ----------------------------------
        // sorting
        $sSorting = '';
        if ( $this->_sCustomSorting ) {
            $sSorting = " {$this->_sCustomSorting} , ";
        }

        // ----------------------------------
        // filtering ?
        $sFilterSql = '';
        $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $sCatId = $aCatIds[count($aCatIds) - 1];
        if ($aSessionFilter && isset($aSessionFilter[$sCatId][$iLang])) {
            $sFilterSql = $this->_getFilterSql($sCatId, $aSessionFilter[$sCatId][$iLang]);
        }

        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);

        $inCategoriesSql = implode(',', array_map(function($v){
            return "'$v'";
        }, $aCatIds));

        $sSelect = "SELECT $sFields, $sArticleTable.oxtimestamp FROM $sO2CView as oc left join $sArticleTable
                    ON $sArticleTable.oxid = oc.oxobjectid
                    WHERE " . $this->getBaseObject()->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''
                    and oc.oxcatnid IN($inCategoriesSql) $sFilterSql ORDER BY $sSorting oc.oxpos, oc.oxobjectid ";

        return $sSelect;
    }


    /**
     * Creates SQL Statement to load Articles Count for multiple categories, etc.
     *
     * @param array $aCatIds Category tree IDs
     * @param array $aSessionFilter Like array ( catid => array( attrid => value,...))
     *
     * @return string SQL
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    protected function _getCategoriesCountSelect( $aCatIds, $aSessionFilter = null ): string
    {
        /** @var TableViewNameGenerator $tableViewNameGenerator */
        $tableViewNameGenerator = Registry::get(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName( 'oxarticles' );
        $sO2CView      = $tableViewNameGenerator->getViewName( 'oxobject2category' );


        // ----------------------------------
        // filtering ?
        $sFilterSql = '';
        $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $sCatId = $aCatIds[count($aCatIds) - 1];
        if ($aSessionFilter && isset($aSessionFilter[$sCatId][$iLang])) {
            $sFilterSql = $this->_getFilterSql($sCatId, $aSessionFilter[$sCatId][$iLang]);
        }

        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);

        $inCategoriesSql = implode(',', array_map(function($v){
            return "'$v'";
        }, $aCatIds));

        $sSelect = "SELECT COUNT(*) FROM $sO2CView as oc left join $sArticleTable
                    ON $sArticleTable.oxid = oc.oxobjectid
                    WHERE " . $this->getBaseObject()->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''
                    and oc.oxcatnid IN($inCategoriesSql) $sFilterSql ";

        return $sSelect;
    }
}