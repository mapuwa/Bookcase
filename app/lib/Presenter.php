<?php
namespace App\FrontModule\Presenters;

use Nette;
use Nette\Application\UI;

abstract class Presenter extends Nette\Application\UI\Presenter
{
    /** @var \App\FrontModule\Components\ISearchControlFactory @inject */
    public $searchControlFactory;

    protected function createComponentSearch()
    {
        $control = $this->searchControlFactory->create("");
        $control->onFormSuccess[] = function ($values) {
            $this->redirect('Search:book', $values['query']);
        };
        return $control;
    }
}