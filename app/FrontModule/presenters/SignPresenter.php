<?php

namespace App\FrontModule\Presenters;

use Nette;
use App;
use Nette\Application\UI\Form;
use Nette\Security as NS;

class SignPresenter extends App\Presenter
{

    protected function createComponentSignInForm()
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Prosím vyplňte své uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte své heslo.');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $form;
    }
    public function signInFormSucceeded($form, $values)
    {
        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Homepage:');

        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
        }
    }
    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('Odhlášení bylo úspěšné.');
        $this->redirect('Homepage:');
    }
    public function actionIn()
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->flashMessage('Již jste přihlášen.');
            $this->redirect('Homepage:');
        }
    }

}