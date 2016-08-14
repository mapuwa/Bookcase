<?php

namespace App\FrontModule\Components;

use Nette\Application\UI;
use Nette\Application\UI\Form;

class SearchControl extends UI\Control
{
    private $value;
    public $onFormSuccess;
    public function  __construct($val = "")
    {
        $this->value = $val;
    }

    public function render(){
        $this->template->render(__DIR__ . '/SearchControl.latte');
    }
    public function processForm(Form $form)
    {
        $values = $form->getValues();
        $this->onFormSuccess($values);
    }
    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $form = new Form;
        $form->addText('query', '')->setAttribute('value', $this->value);
        $form->addSubmit('submit', 'Hledat');
        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }
}

/** rozhranní pro generovanou továrničku */
interface ISearchControlFactory
{
    /** @return SearchControl */
    function create($val);
}