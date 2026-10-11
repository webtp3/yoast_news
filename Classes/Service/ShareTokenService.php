<?php

declare(strict_types=1);

namespace WebTp3\YoastNews\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

class ShareTokenService
{
    public const TABLE = 'tx_yoastnews_preview';
    public const NEWS_TABLE = 'tx_news_domain_model_news';
    public const LIFETIME = 7 * 24 * 60 * 60;

    private ConnectionPool $connectionPool;

    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    public function create(int $newsUid, int $pageUid, int $languageUid): array
    {
        $token = bin2hex(random_bytes(32));
        $expires = time() + self::LIFETIME;
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE);
        $connection->transactional(function () use ($connection, $newsUid, $pageUid, $languageUid, $token, $expires): void {
            $connection->delete(self::TABLE, ['news_uid' => $newsUid]);
            $connection->insert(self::TABLE, [
                'token_hash' => hash('sha256', $token),
                'news_uid' => $newsUid,
                'page_uid' => $pageUid,
                'language_uid' => $languageUid,
                'expires' => $expires,
            ]);
        });
        return ['token' => $token, 'expires' => $expires];
    }

    public function revoke(int $newsUid): void
    {
        $this->connectionPool->getConnectionForTable(self::TABLE)->delete(self::TABLE, ['news_uid' => $newsUid]);
    }

    public function validate(string $token, int $pageUid, int $languageUid, int $newsUid): ?array
    {
        if (!preg_match('/^[a-f0-9]{64}$/D', $token)) {
            return null;
        }
        $grant = $this->connectionPool->getConnectionForTable(self::TABLE)
            ->select(['*'], self::TABLE, ['token_hash' => hash('sha256', $token)])
            ->fetchAssociative();
        if (!$grant || (int)$grant['expires'] <= time()
            || (int)$grant['page_uid'] !== $pageUid
            || (int)$grant['language_uid'] !== $languageUid
            || (int)$grant['news_uid'] !== $newsUid
        ) {
            return null;
        }
        // A valid grant allows previewing hidden or scheduled news. Connection::select()
        // applies default TCA restrictions, so use an explicitly unrestricted query here.
        $query = $this->connectionPool->getQueryBuilderForTable(self::NEWS_TABLE);
        $query->getRestrictions()->removeAll();
        $record = $query
            ->select('*')
            ->from(self::NEWS_TABLE)
            ->where(
                $query->expr()->eq('uid', $query->createNamedParameter($newsUid, Connection::PARAM_INT)),
                $query->expr()->eq('deleted', 0),
                $query->expr()->eq('t3ver_wsid', 0)
            )
            ->executeQuery()
            ->fetchAssociative();
        if (!$record || ((int)$record['sys_language_uid'] !== -1 && (int)$record['sys_language_uid'] !== $languageUid)) {
            return null;
        }
        return $record;
    }
}
