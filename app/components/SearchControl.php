<?php

use Nette\Application\UI;
use Nette\Application\UI\Form;

class SearchControl extends UI\Control
{

    public function render(){
        echo $this['form'];
    }
    public function processForm(Form $form)
    {
        $values = $form->getValues();

    }
    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $form = new Form;
        $form->addText('username', 'Uživ. jméno')->setRequired();
        $form->addPassword('password', 'Heslo')->setRequired();
        $form->addSubmit('submit', 'Přihlásit se');
        $form->onSuccess[] = $this->processForm;
        return $form;
    }
}

/** rozhranní pro generovanou továrničku */
interface ISearchControlFactory
{
    /** @return SearchControl */
    function create();
}