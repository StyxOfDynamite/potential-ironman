<?php

namespace UserGroup;

use \App;
use \Menu;
use \Route;

class Initialize extends \SlimStarter\Module\Initializer{

    public function getModuleName(){
        return 'UserGroup';
    }

    public function getModuleAccessor(){
        return 'usergroup';
    }

    public function registerAdminMenu(){

        $adminMenu = Menu::get('admin_sidebar');

        $userGroup = $adminMenu->createItem('usergroup', array(
            'label' => 'User and Group',
            'icon'  => 'group',
            'url'   => '#'
        ));
        $userGroup->setAttribute('class', 'nav nav-second-level');

        $userMenu = $adminMenu->createItem('user', array(
            'label' => 'User',
            'icon'  => 'user',
            'url'   => 'admin/user'
        ));

        $groupMenu = $adminMenu->createItem('group', array(
            'label' => 'Group',
            'icon'  => 'group',
            'url'   => 'admin/group'
        ));

        $userGroup->addChildren($userMenu);
        $userGroup->addChildren($groupMenu);

        $adminMenu->addItem('usergroup', $userGroup);
    }

    public function registerUserMenu()
    {
        $userMenu = Menu::get('user_sidebar');

        $invoiceGroup = $userMenu->createItem('invoices', array(
            'label' => 'Invoices',
            'icon' => 'envelope',
            'url' => '#'
            ));
        $invoiceGroup->setAttribute('class', 'nav nav-second-level');

        $newInvoice = $userMenu->createItem('new-invoice', array(
            'label' => 'New Invoice',
            'icon'  => 'user',
            'url'   => '/invoices/new'
        ));

        $pendingInvoices = $userMenu->createItem('pending-invoices', array(
            'label' => 'Pending Invoices',
            'icon'  => 'user',
            'url'   => '/invoices/pending'
        ));
        
        $paidInvoices = $userMenu->createItem('paid-invoices', array(
            'label' => 'Paid Invoices',
            'icon'  => 'user',
            'url'   => '/invoices/paid'
        ));

        $invoiceGroup->addChildren($newInvoice);
        $invoiceGroup->addChildren($pendingInvoices);
        $invoiceGroup->addChildren($paidInvoices);

        $userMenu->addItem('invoices', $invoiceGroup);

    }

    public function registerAdminRoute(){
        Route::resource('/user', 'UserGroup\Controllers\UserController');
        Route::resource('/group', 'UserGroup\Controllers\GroupController');
    }
}