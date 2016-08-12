<?php

namespace App\FrontModule\Presenters;

use App;
use App\Model\BookManager;

class HomepagePresenter extends App\Presenter
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
    protected function createComponentSearchControl()
    {
        $control = $this->searchControlFactory->create();

        return $control;
    }
}
