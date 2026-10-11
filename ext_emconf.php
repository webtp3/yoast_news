<?php

/*
 * This file is part of the web-tp3/yoast_news.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$EM_CONF['yoast_news'] = [
    'title' => 'Yoast SEO for TYPO3 - EXT:news',
    'description' => 'Integrate Yoast SEO for TYPO3 in EXT:news',
    'category' => 'fe',
    'state' => 'stable',
    'author' => 'Richard Haeser',
    'author_email' => 'richardhaeser@gmail.com',
    'version' => '4.0.1',
    'constraints' => [
        'depends' => [
            'news' => '',
            'yoast_seo' => '',
        ],
        'conflicts' => [],
        'suggests' => []
    ],
];
