<?php

namespace App\Model;

use Nette;

class BookManager
{
    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getPublicBooks()
    {
        return $this->database->table('books')
            ->where('created_at < ', new \DateTime())
            ->order('created_at DESC');
    }
}