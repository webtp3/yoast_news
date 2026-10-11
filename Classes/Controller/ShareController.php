<?php

declare(strict_types=1);

namespace WebTp3\YoastNews\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Site\SiteFinder;
use WebTp3\YoastNews\Service\ShareTokenService;

class ShareController
{
    private ShareTokenService $tokens;
    private SiteFinder $siteFinder;

    public function __construct(ShareTokenService $tokens, SiteFinder $siteFinder)
    {
        $this->tokens = $tokens;
        $this->siteFinder = $siteFinder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['error' => 'POST erforderlich.'], 405);
        }
        $body = (array)$request->getParsedBody();
        $uid = (int)($body['uid'] ?? 0);
        $record = BackendUtility::getRecord(ShareTokenService::NEWS_TABLE, $uid);
        $user = $GLOBALS['BE_USER'];
        $page = $record ? BackendUtility::getRecord('pages', (int)$record['pid']) : null;
        if (!$record || !$page || !$user->check('tables_modify', ShareTokenService::NEWS_TABLE)
            || !$user->isInWebMount($page)
            || !$user->doesUserHaveAccess($page, 16)
            || !$user->recordEditAccessInternals(ShareTokenService::NEWS_TABLE, $record)
        ) {
            return new JsonResponse(['error' => 'Keine Berechtigung für diese News.'], 403);
        }
        if ((int)$user->workspace !== 0 || (int)($record['t3ver_wsid'] ?? 0) !== 0) {
            return new JsonResponse(['error' => 'Freigabelinks sind nur für gespeicherte Live-Datensätze verfügbar.'], 400);
        }
        $action = (string)($body['action'] ?? '');
        if ($action === 'revoke') {
            $this->tokens->revoke($uid);
            return new JsonResponse(['revoked' => true]);
        }
        if ($action !== 'create') {
            return new JsonResponse(['error' => 'Unbekannte Aktion.'], 400);
        }
        $config = BackendUtility::getPagesTSconfig((int)$record['pid']);
        $pageUid = (int)($config['TCEMAIN.']['preview.'][ShareTokenService::NEWS_TABLE . '.']['previewPageId'] ?? 0);
        if ($pageUid <= 0) {
            return new JsonResponse(['error' => 'Bitte zuerst die News-Detailseite über previewPageId im Seiten-TSconfig konfigurieren.'], 400);
        }
        $languageUid = max(0, (int)$record['sys_language_uid']);
        try {
            $site = $this->siteFinder->getSiteByPageId($pageUid);
            $language = $site->getLanguageById($languageUid);
        } catch (\RuntimeException $exception) {
            return new JsonResponse(['error' => 'Die Detailseite oder Sprache ist keiner Website zugeordnet.'], 400);
        }
        $grant = $this->tokens->create($uid, $pageUid, $languageUid);
        $url = (string)$site->getRouter()->generateUri($pageUid, [
            '_language' => $language,
            'tx_news_pi1' => ['news_preview' => $uid, 'controller' => 'News', 'action' => 'detail'],
            'yoast_preview' => $grant['token'],
        ]);
        return new JsonResponse(['url' => $url, 'expires' => $grant['expires']]);
    }
}
