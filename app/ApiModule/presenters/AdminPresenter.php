<?php

namespace App\ApiModule\Presenters;

use Nette;
use App\Model\ArticleManager;

class AdminPresenter extends Nette\Application\UI\Presenter
{
    /** @var ArticleManager */
    private $articleManager;

    public function __construct(ArticleManager $articleManager)
    {
        $this->articleManager = $articleManager;
    }

    public function renderDefault()
    {
        $this->template->posts = $this->articleManager->getPublicArticles()->limit(5);
    }
}
