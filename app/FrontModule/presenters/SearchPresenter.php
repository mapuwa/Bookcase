<?php

namespace App\FrontModule\Presenters;

use App;

class SearchPresenter extends App\FrontModule\Presenters\Presenter
{
    /** @var \App\Model\BookManager*/
    private $bookManager;


    public function __construct(App\Model\BookManager $manager)
    {
        $this->bookManager = $manager;
    }

    public function renderBook($id)
    {
        $this->template->posts = $this->bookManager->getPublicBooks();
    }
}