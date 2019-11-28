<?php
/**
 * @package Proudcommerce
 * @subpackage oxid/dk/recursivecats
 * @author Florian Palme <florian.palme@internetfabrik.de>
 */


/**
 * Metadata version
 */
$sMetadataVersion = '2.0';


$aModule = [
    'id'           => 'dk_recursivecats',
    'title' => '[ProudCommerce] Recursive Cats',
    'description'        => 'List products of category and its subcategories.<br>
    <hr style="margin-top: 15px;">
    <img src="https://www.proudcommerce.com/module/img/icon_link.png" border="0" style="width: 10px; height: 11px;">&nbsp; <a href="https://github.com/proudcommerce/dk_recursivecats" target="_blank">Modul-Info</a>',
    'thumbnail'    => 'logo_pc-os.jpg',
    'version'      => '2.0.2',
    'author'       => 'ProudCommerce',
    'url'          => 'https://www.proudcommerce.com/',
    'email'        => 'welcome@proudcommerce.com',
    'extend'       => [
        \OxidEsales\Eshop\Application\Controller\ArticleListController::class
            => \ProudCommerce\OXID\DK\RecursiveCats\Application\Controller\ArticleListController::class,

        \OxidEsales\Eshop\Application\Model\ArticleList::class
            => \ProudCommerce\OXID\DK\RecursiveCats\Application\Model\ArticleList::class,
    ],
    'controllers' => [
    ],
    'events'       => [
    ],
    'templates'   => [
    ],
    'blocks' => [
    ],
    'settings' => [
    ],
];
