<?php
namespace User;

use \App;
use \Menu;
use \Module;

class BaseController extends \BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->data['menu_pointer'] = '<div class="pointer"><div class="arrow"></div><div class="arrow_border"></div></div>';

        $userMenu = Menu::create('user_sidebar');
        $dashboard = $userMenu->createItem('dashboard', array(
            'label' => 'Dashboard',
            'icon'  => 'dashboard',
            'url'   => 'admin'
        ));

        $userMenu->addItem('dashboard', $dashboard);
        $userMenu->setActiveMenu('dashboard');

        foreach (Module::getModules() as $module) {
            $module->registerUserMenu();
        }

    }
}