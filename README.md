# Yoast SEO for TYPO3 - EXT:news
With this simple plugin, you integrate Yoast SEO for TYPO3 in EXT:news.

## Installation and configuration
You can install the plugin by composer or by the Extension manager. Not many configuration is needed.

* Install extension by composer / Extension manager
* Make sure the TypoScript is included in your project by using `<INCLUDE_TYPOSCRIPT: source="FILE:EXT:yoast_news/Configuration/TypoScript/setup.typoscript">`.
* Include PageTs config `<INCLUDE_TYPOSCRIPT: source="FILE:EXT:yoast_news/Configuration/TSconfig/Page.tsconfig">`
* Set your detail page id in your own Page TSconfig file. `TCEMAIN.preview.tx_news_domain_model_news.previewPageId = x`.
Where x is the id of your detail page!

## Backend preview (TYPO3 11)

Set the detail page in the Page TSconfig of each site root (or news storage folder):

```typoscript
TCEMAIN.preview.tx_news_domain_model_news.previewPageId = 123
```

Replace `123` with the page containing the news detail plugin. Save the news record
and use **View** in the backend editor toolbar. The preview opens the detail page
with `tx_news_pi1[news_preview]` and also supports translated records.

Include the extension's TypoScript setup as described above to preview hidden news
while logged in as a backend user. Hidden-record preview is enabled only for that
session. In the detail plugin, set hidden-record preview to **Defined in TypoScript**.

## Share preview JavaScript

SharePreview.js is an ES module. TYPO3 12 and newer load it through the
`@yoast-news/share/share-preview.js` importmap entry in Configuration/JavaScriptModules.php.
TYPO3 11 loads the same module with a dynamic import of its resolved public resource URL.
The share AJAX route is registered in Configuration/Backend/AjaxRoutes.php.
After deploying these changes, flush the TYPO3 system caches and reload the backend.
