<?php

/*
 * This file is part of the web-tp3/yoast_news.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$llPrefix = 'LLL:EXT:yoast_news/Resources/Private/Language/TCA.xlf:';
$seoLlPrefix = 'LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:';
$socialColumns = [];
foreach (['og_title', 'twitter_title'] as $fieldName) {
    $socialColumns[$fieldName] = [
        'exclude' => true,
        'l10n_mode' => 'prefixLangTitle',
        'label' => $seoLlPrefix . 'pages.' . $fieldName,
        'config' => [
            'type' => 'input',
            'size' => 40,
            'max' => 255,
            'eval' => 'trim',
        ],
    ];
}
foreach (['og_description', 'twitter_description'] as $fieldName) {
    $socialColumns[$fieldName] = [
        'exclude' => true,
        'l10n_mode' => 'prefixLangTitle',
        'label' => $seoLlPrefix . 'pages.' . $fieldName,
        'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 3,
        ],
    ];
}
$socialColumns['twitter_card'] = [
    'exclude' => true,
    'label' => $seoLlPrefix . 'pages.twitter_card',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'default' => 'summary',
        'items' => [
            [$seoLlPrefix . 'pages.twitter_card.summary', 'summary'],
            [$seoLlPrefix . 'pages.twitter_card.summary_large_image', 'summary_large_image'],
        ],
    ],
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tx_news_domain_model_news',
    $socialColumns
);

$socialImageColumns = [];
foreach (['og_image', 'twitter_image'] as $fieldName) {
    $socialImageColumns[$fieldName] = [
        'exclude' => true,
        'label' => 'LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:pages.' . $fieldName,
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            $fieldName,
            [
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '--palette--;;imageoverlayPalette, --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '--palette--;;imageoverlayPalette, --palette--;;filePalette',
                        ],
                    ],
                    'columns' => [
                        'crop' => [
                            'config' => [
                                'cropVariants' => [
                                    'default' => ['disabled' => true],
                                    'social' => [
                                        'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.crop_variant.social',
                                        'coverAreas' => [],
                                        'cropArea' => [
                                            'x' => 0.0,
                                            'y' => 0.0,
                                            'width' => 1.0,
                                            'height' => 1.0,
                                        ],
                                        'allowedAspectRatios' => [
                                            '1.91:1' => [
                                                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.191_1',
                                                'value' => 1200 / 630,
                                            ],
                                            'NaN' => [
                                                'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                                                'value' => 0.0,
                                            ],
                                        ],
                                        'selectedRatio' => '1.91:1',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        ),
    ];
}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tx_news_domain_model_news',
    $socialImageColumns
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tx_news_domain_model_news',
    [
        'tx_yoastnews_share_preview' => [
            'label' => 'Freigabelink',
            'exclude' => true,
            'displayCond' => 'REC:NEW:false',
            'config' => [
                'type' => 'none',
                'renderType' => 'yoastNewsSharePreview',
            ],
        ],
        'tx_yoastseo_snippetpreview' => [
            'label' => $llPrefix . 'news.field.snippetPreview',
            'exclude' => true,
            'displayCond' => 'REC:NEW:false',
            'config' => [
                'type' => 'text',
                'renderType' => 'snippetPreview',
                'settings' => [
                    'titleField' => 'alternative_title',
                    'descriptionField' => 'description'
                ]
            ]
        ],
        'tx_yoastseo_readability_analysis' => [
            'label' => $llPrefix . 'news.field.analysis',
            'exclude' => true,
            'displayCond' => 'REC:NEW:false',
            'config' => [
                'type' => 'text',
                'renderType' => 'readabilityAnalysis'
            ]
        ],
        'tx_yoastseo_focuskeyword' => [
            'label' => $llPrefix . 'news.field.seoFocusKeyword',
            'exclude' => true,
            'config' => [
                'type' => 'input',
            ]
        ],
        'tx_yoastseo_focuskeyword_analysis' => [
            'label' => $llPrefix . 'news.field.analysis',
            'exclude' => true,
            'displayCond' => 'REC:NEW:false',
            'config' => [
                'type' => 'input',
                'renderType' => 'focusKeywordAnalysis',
                'settings' => [
                    'focusKeywordField' => 'tx_yoastseo_focuskeyword',
                ]
            ]
        ],

    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tx_news_domain_model_news',
    'yoast-metadata',
    '
    --linebreak--, tx_yoastseo_snippetpreview,
    --linebreak--, alternative_title,
    --linebreak--, description,
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tx_news_domain_model_news',
    'yoast-focuskeyword',
    '
    --linebreak--, tx_yoastseo_focuskeyword,
    --linebreak--, tx_yoastseo_focuskeyword_analysis
    '
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tx_news_domain_model_news',
    'yoast-readability',
    '
    --linebreak--, tx_yoastseo_readability_analysis
    '
);

$GLOBALS['TCA']['tx_news_domain_model_news']['palettes']['metatags']['showitem'] =
    preg_replace('/description(.*,|.*$)/', '', $GLOBALS['TCA']['tx_news_domain_model_news']['palettes']['metatags']['showitem']);

$GLOBALS['TCA']['tx_news_domain_model_news']['palettes']['alternativeTitles']['showitem'] =
    preg_replace('/alternative_title(.*,|.*$)/', '', $GLOBALS['TCA']['tx_news_domain_model_news']['palettes']['alternativeTitles']['showitem']);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tx_news_domain_model_news',
    'yoast-opengraph',
    'og_title, --linebreak--, og_description, --linebreak--, og_image'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tx_news_domain_model_news',
    'yoast-twittercards',
    'twitter_title, --linebreak--, twitter_description, --linebreak--, twitter_image, --linebreak--, twitter_card'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_news_domain_model_news',
    '
    --div--;' . $llPrefix . 'news.tabs.seo,
        --palette--;' . $llPrefix . 'news.palettes.metadata;yoast-metadata,
        --palette--;' . $llPrefix . 'news.palettes.readability;yoast-readability,
        --palette--;' . $llPrefix . 'news.palettes.seo;yoast-focuskeyword,
        --palette--;' . $seoLlPrefix . 'pages.palettes.opengraph;yoast-opengraph,
        --palette--;' . $seoLlPrefix . 'pages.palettes.twittercards;yoast-twittercards,
        tx_yoastnews_share_preview,
    ',
    '',
    'after:bodytext'
);
