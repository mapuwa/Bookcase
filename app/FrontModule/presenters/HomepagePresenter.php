<?php

namespace App\FrontModule\Presenters;

use Nette;
use App\Model\BookManager;

class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /** @var BookManager */
    private $bookManager;

    public function __construct(BookManager $manager)
    {
        $this->bookManager = $manager;
    }

    public function renderDefault()
    {
        $this->template->posts = $this->bookManager->getPublicBooks()->limit(5);
    }
}
