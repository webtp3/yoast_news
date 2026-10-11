<?php

return [
    'dependencies' => ['backend'],
    'imports' => [
        // Der Key ist der Name, unter dem du es im JS importierst
        '@yoast-news/share/share-preview.js' => 'EXT:yoast_news/Resources/Public/JavaScript/SharePreview.js',
    ],
];
