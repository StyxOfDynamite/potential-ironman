<?php

namespace Invoices;

use \App;
use \Menu;
use \Route;

class Initialize extends \SlimStarter\Module\Initializer{

    public function getModuleName(){
        return 'Invoices';
    }

    public function getModuleAccessor(){
        return 'invoices';
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
        Route::resource('/invoices', 'Invoices\Controllers\InvoiceController');
    }
}