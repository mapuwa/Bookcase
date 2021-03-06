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
        $this->template->wish = false;
        $this->template->read = false;
        $user = $this->getUser();
        if ($user->loggedIn) {
            $w = $this->database->table('wishlist')->get(array($id, $user->id));
            $r = $this->database->table('readlist')->get(array($id, $user->id));
            if ($w)
                $this->template->wish = $w->content;
            if ($r)
                $this->template->read = $r->content;
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
    public function renderWishlist()
    {
        $list = $this->database->table('wishlist')->where('user_id = ?', $this->getUser()->id)->order('added_at DESC');
        $this->template->books = [];
        foreach ($list as $book) {
            $w = $book->ref('book')->toArray();
            $w['wish'] = $book->content;
            $this->template->books[] = $w;
        }
    }
    public function renderReadlist()
    {
        $list = $this->database->table('readlist')->where('user_id = ?', $this->getUser()->id)->order('added_at DESC');
        $this->template->books = [];
        foreach ($list as $book) {
            $r = $book->ref('book')->toArray();
            $r['read'] = $book->content;
            $this->template->books[] = $r;
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
    public function actionWishlist()
    {
        if (!$this->getUser()->isAllowed('book', 'create')) {
            $this->redirect('Sign:in');
        }
    }
    public function actionReadlist()
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
            'content' => $values->content,
        ]);
        $this->flashMessage('Kniha byla přidana', 'success');
        $this->redirect(':wishlist');
    }
    protected function createComponentReadForm()
    {
        $form = new Form();
        $form->addTextArea('content', 'Komentář:')
            ->setRequired();
        $form->addSubmit('send', 'Pridat do prectenych knih');
        $form->onSuccess[] = [$this, 'readFormSucceeded'];
        return $form;
    }
    public function readFormSucceeded($form, $values)
    {
        if (!$this->getUser()->isAllowed('comment', 'create')) {
            $this->error('Pro komentování se musíte přihlásit.');
        }
        $userId = $this->getUser()->id;
        $bookId = $this->getParameter('id');
        $isWish = $this->database->table('wishlist')->get(array($bookId, $userId));
        if ($isWish)
            $isWish->delete();
        $this->database->table('readlist')->insert([
            'user_id' => $userId,
            'book_id' => $bookId,
            'content' => $values->content,
        ]);
        $this->flashMessage('Kniha byla přidana', 'success');
        $this->redirect(':readlist');
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