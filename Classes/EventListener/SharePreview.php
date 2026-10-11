<?php

declare(strict_types=1);

namespace WebTp3\YoastNews\EventListener;

use GeorgRinger\News\Domain\Repository\NewsRepository;
use GeorgRinger\News\Event\NewsDetailActionEvent;
use WebTp3\YoastNews\Service\PreviewAccess;

class SharePreview
{
    private PreviewAccess $access;
    private NewsRepository $newsRepository;

    public function __construct(PreviewAccess $access, NewsRepository $newsRepository)
    {
        $this->access = $access;
        $this->newsRepository = $newsRepository;
    }

    public function __invoke(NewsDetailActionEvent $event): void
    {
        if ($this->access->record === null) {
            return;
        }
        // Bypass enable fields exclusively for the record authorized by the token.
        $news = $this->newsRepository->findByUid((int)$this->access->record['uid'], false);
        $values = $event->getAssignedValues();
        $values['newsItem'] = $news;
        $event->setAssignedValues($values);
    }
}
