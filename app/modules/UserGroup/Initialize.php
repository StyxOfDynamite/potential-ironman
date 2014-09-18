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

    public function registerAdminMenu()
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
            'icon'  => 'envelope-o',
            'url'   => '/invoices/new'
        ));

        $pendingInvoices = $userMenu->createItem('pending-invoices', array(
            'label' => 'Pending Invoices',
            'icon'  => 'exchange',
            'url'   => '/invoices/pending'
        ));
        
        $paidInvoices = $userMenu->createItem('paid-invoices', array(
            'label' => 'Paid Invoices',
            'icon'  => 'money',
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