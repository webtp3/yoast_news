<?php

declare(strict_types=1);

namespace WebTp3\YoastNews\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use WebTp3\YoastNews\Service\PreviewAccess;
use WebTp3\YoastNews\Service\ShareTokenService;

class SharePreview implements MiddlewareInterface
{
    private ShareTokenService $tokens;
    private PreviewAccess $access;

    public function __construct(ShareTokenService $tokens, PreviewAccess $access)
    {
        $this->tokens = $tokens;
        $this->access = $access;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $query = $request->getQueryParams();
        if (!array_key_exists('yoast_preview', $query)) {
            return $handler->handle($request);
        }
        $news = $query['tx_news_pi1'] ?? [];
        $routing = $request->getAttribute('routing');
        $language = $request->getAttribute('language');
        $record = null;
        if (is_string($query['yoast_preview']) && is_array($news) && $routing && $language
            && !isset($news['news']) && ($news['action'] ?? '') === 'detail'
            && ($news['controller'] ?? '') === 'News' && is_scalar($news['news_preview'] ?? null)
            && (int)$routing->getPageType() === 0
        ) {
            $record = $this->tokens->validate(
                $query['yoast_preview'],
                $routing->getPageId(),
                $language->getLanguageId(),
                (int)$news['news_preview']
            );
        }
        $headers = [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
            'Referrer-Policy' => 'no-referrer',
        ];
        if (!$record) {
            return new HtmlResponse('Dieser Freigabelink ist ungültig oder abgelaufen.', 403, $headers);
        }
        $controller = $request->getAttribute('frontend.controller') ?? $GLOBALS['TSFE'];
        $controller->set_no_cache('Shared news preview', true);
        $this->access->record = $record;
        try {
            $response = $handler->handle($request);
            foreach ($headers as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
            return $response;
        } finally {
            $this->access->record = null;
        }
    }
}
