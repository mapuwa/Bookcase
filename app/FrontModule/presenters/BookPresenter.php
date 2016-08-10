<?php
namespace App\FrontModule\Presenters;

use Nette;
use Nette\Application\UI\Form;


class BookPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderShow($id)
    {
        $book = $this->database->table('books')->get($id);
	    if (!$book) {
	        $this->error('Stránka nebyla nalezena');
	    }
	    $this->template->book = $book;
        $this->template->comments = $book->related('comment')->order('created_at');
        $this->template->user = $this->getUser();
    }
    public function actionEdit($id)
    {
        if (!$this->getUser()->isAllowed('book', 'create')) {
            $this->redirect('Sign:in');
        }
        $book = $this->database->table('books')->get($id);
        if (!$book) {
            $this->error('Příspěvek nebyl nalezen');
        }
        $this['bookForm']->setDefaults($book->toArray());
    }
    public function actionCreate()
    {
        if (!$this->getUser()->isAllowed('book', 'create')) {
            $this->redirect('Sign:in');
        }
    }

    protected function createComponentBookForm()
    {
        $form = new Form;
        $form->addText('title', 'Titulek:')
            ->setRequired();
        $form->addTextArea('desc', 'Obsah:')
            ->setRequired();

        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = [$this, 'bookFormSucceeded'];

        return $form;
    }
    public function bookFormSucceeded($form, $values)
    {
        if (!$this->getUser()->isAllowed('book', 'create')) {
            $this->error('Pro vytvoření, nebo editování příspěvku se musíte přihlásit.');
        }
        $bookId = $this->getParameter('id');

        if ($bookId) {
            $book = $this->database->table('books')->get($bookId);
            $book->update($values);
        } else {
            $book = $this->database->table('books')->insert($values);
        }
        $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        $this->redirect('show', $book->id);
    }

    protected function createComponentCommentForm()
	{
	    $form = new Form();
	    $form->addTextArea('content', 'Komentář:')
	        ->setRequired();
	    $form->addSubmit('send', 'Publikovat komentář');
        $form->onSuccess[] = [$this, 'commentFormSucceeded'];
	    return $form;
	}
	public function commentFormSucceeded($form, $values)
	{
        if (!$this->getUser()->isAllowed('comment', 'create')) {
            $this->error('Pro komentování se musíte přihlásit.');
        }

	    $bookId = $this->getParameter('id');
	    $this->database->table('comments')->insert([
	        'book_id' => $bookId,
	        'content' => $values->content,
	    ]);
	    $this->flashMessage('Děkuji za komentář', 'success');
	    $this->redirect('this');
	}
}