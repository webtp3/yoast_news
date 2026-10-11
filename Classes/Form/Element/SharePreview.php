<?php

declare(strict_types=1);

namespace WebTp3\YoastNews\Form\Element;

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class SharePreview extends AbstractNode
{
    public function render(): array
    {
        $result = $this->initializeResultArray();
        $record = $this->data['databaseRow'];
        $uid = (int)$record['uid'];
        $endpoint = (string)GeneralUtility::makeInstance(UriBuilder::class)->buildUriFromRoute('ajax_yoast_news_share');
        $escape = static function (string $value): string {
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };
        $result['html'] = '<div class="panel panel-default yoast-news-share" data-uid="' . $uid
            . '" data-endpoint="' . $escape($endpoint) . '" data-title="' . $escape((string)$record['title']) . '">'
            . '<div class="panel-body">'
            . '<p><strong>Vorschau zur Abnahme teilen</strong><br>'
            . 'Ohne Login · 7 Tage gültig · aktueller gespeicherter Stand</p>'
            . '<p class="text-muted">Änderungen vor dem Teilen speichern. Ein neuer Link ersetzt den bisherigen Link.</p>'
            . '<div class="form-group"><button type="button" class="btn btn-primary" data-share-action="create">Freigabelink erstellen</button> '
            . '<button type="button" class="btn btn-default" data-share-action="revoke">Link widerrufen</button></div>'
            . '<div data-share-result hidden>'
            . '<label>Freigabelink<input type="text" class="form-control" readonly data-share-url aria-label="Freigabelink"></label>'
            . '<p data-share-expiry class="text-muted"></p>'
            . '<button type="button" class="btn btn-default" data-share-action="copy">Link kopieren</button> '
            . '<a class="btn btn-default" data-share-mail>Per E-Mail teilen</a> '
            . '<a class="btn btn-default" data-share-open target="_blank" rel="noopener noreferrer">Vorschau öffnen</a>'
            . '</div><p data-share-status role="status" aria-live="polite"></p>'
            . '</div></div>';
        if (method_exists(JavaScriptModuleInstruction::class, 'create')) {
            $result['javaScriptModules'][] = JavaScriptModuleInstruction::create(
                '@yoast-news/share/share-preview.js'
            );
        } else {
            // TYPO3 11 has no importmap support; resolve the public URL before importing.
            $moduleUrl = PathUtility::getPublicResourceWebPath(
                'EXT:yoast_news/Resources/Public/JavaScript/SharePreview.js'
            );
            $result['additionalJavaScriptPost'][] = 'import('
                . json_encode($moduleUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
                . ');';
        }
        return $result;
    }
}
