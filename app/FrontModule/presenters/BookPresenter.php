<?php
namespace App\FrontModule\Presenters;

use Nette;
use App;
use Nette\Application\UI\Form;


class BookPresenter extends App\FrontModule\Presenters\Presenter
{
    /** @var Nette\Database\Context */
    private $database;
    /** @var \App\Model\PageWrapper*/
    private $pageWrapper;

    public function __construct(Nette\Database\Context $database, App\Model\PageWrapper $manager)
    {
        $this->database = $database;
        $this->pageWrapper = $manager;
    }

    public function renderShow($id)
    {
        $book = $this->database->table('books')->get($id);
	    if (!$book) {
	        $this->error('Stránka nebyla nalezena');
	    }
	    $this->template->book = $book;
        $this->template->comments = $book->related('comment')->order('created_at');
    }
    public function renderCreate($link)
    {
        if ($link) {
            $book = $this->pageWrapper->getPage($link);
            $book['authors'] =
            $this['bookForm']->setDefaults($book);
        }

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
    public function actionCreate($link)
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
        $form->addTextArea('description', 'Obsah:')
            ->setRequired();
        $form->addInteger('datePublished', 'Rok vydani');
        $form->addInteger('pages', 'Pocet stran');
        $form->addText('authors', 'Autor');
        $form->addText('image', 'URL obrazku');
        $form->addText('genre', 'Zanr');
        $form->addText('publisher', 'vydavatel');
        $form->addText('isbn', 'ISBN');
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
    protected function createComponentWishForm()
    {
        $form = new Form();
        $form->addTextArea('content', 'Komentář:')
            ->setRequired();
        $form->addSubmit('send', 'Pridat do wishlistu');
        $form->onSuccess[] = [$this, 'wishFormSucceeded'];
        return $form;
    }
    public function wishFormSucceeded($form, $values)
    {
        if (!$this->getUser()->isAllowed('comment', 'create')) {
            $this->error('Pro komentování se musíte přihlásit.');
        }
        $this->database->table('wishlist')->insert([
            'user_id' => $this->getUser()->id,
            'book_id' => $this->getParameter('id'),
            'content' => "wish",
        ]);
        $this->flashMessage('Kniha byla přidana', 'success');
        $this->redirect('this');
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