<?php

return [
    'frontend' => [
        'yoast-news/share-preview' => [
            'target' => \WebTp3\YoastNews\Middleware\SharePreview::class,
            'after' => ['typo3/cms-frontend/tsfe'],
            'before' => ['typo3/cms-frontend/prepare-tsfe-rendering'],
        ],
    ],
];
