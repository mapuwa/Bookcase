<?php

namespace App\FrontModule\Presenters;

use App;

class HomepagePresenter extends App\FrontModule\Presenters\Presenter
{
    /** @var \App\Model\BookManager */
    private $bookManager;

    public function __construct(App\Model\BookManager $manager)
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
