<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '@import "EXT:yoast_news/Configuration/TSconfig/Page.tsconfig"'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
    'options.saveDocView.tx_news_domain_model_news = 1'
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1791500001] = [
    'nodeName' => 'yoastNewsSharePreview',
    'priority' => 40,
    'class' => \WebTp3\YoastNews\Form\Element\SharePreview::class,
];
$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['yoast_news_share'] = [
    'path' => '/yoast-news/share',
    'target' => \WebTp3\YoastNews\Controller\ShareController::class . '::handle',
];
