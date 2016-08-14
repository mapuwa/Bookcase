<?php

namespace App\FrontModule\Presenters;

use App;

class SearchPresenter extends App\FrontModule\Presenters\Presenter
{
    /** @var \App\Model\PageWrapper*/
    private $pageWrapper;

    private $value;

    public function __construct(App\Model\PageWrapper $manager)
    {
        $this->pageWrapper = $manager;
        $this->value = "";
    }

    public function renderBook($id)
    {
        $this->template->query = $id;
        $this->template->books = $this->pageWrapper->search($id);
    }

    public function renderPreview($link)
    {
        $this->template->book = $this->pageWrapper->getPage($link);
    }
    public function actionBook($id)
    {
        $this->value = $id;
    }
    protected function createComponentSearch()
    {
        $control = $this->searchControlFactory->create($this->value);
        $control->onFormSuccess[] = function ($values) {
            $this->redirect('book', $values['query']);
        };
        return $control;
    }
}