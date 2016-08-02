<?php

use Nette\Security\Permission;

class AuthorizatorFactory
{

    /**
     * @return \Nette\Security\IAuthorizator
     */

    static public function create()
    {
        $permission = new Permission();

        /* seznam uživatelských rolí */
        $permission->addRole('guest');
        $permission->addRole('user', 'guest');
        $permission->addRole('admin', 'user');

        /* seznam zdrojů */
        $permission->addResource('book');
        $permission->addResource('user');
        $permission->addResource('comment');

        /* seznam pravidel oprávnění */
        $permission->allow('guest', ['book', 'comment'], 'list');
        $permission->allow('user', ['book', 'comment'], 'create');

        /* admin má práva na všechno */
        $permission->allow('admin', Permission::ALL, Permission::ALL);

        return $permission;
    }

}