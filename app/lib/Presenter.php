<?php
namespace App;

use Nette;
use Nette\Application\UI;

abstract class Presenter extends Nette\Application\UI\Presenter
{
    /** @var \ISearchControlFactory @inject */
    public $searchControlFactory;

    protected function createComponentSearch()
    {
        $control = $this->searchControlFactory->create();

        return $control;
    }
}