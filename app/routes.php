<?php

/**
 * Sample group routing with user check in middleware
 */
Route::group(
    '/admin',
    function(){
        if(!Sentry::check()){

            if(Request::isAjax()){
                Response::headers()->set('Content-Type', 'application/json');
                Response::setBody(json_encode(
                    array(
                        'success'   => false,
                        'message'   => 'Session expired or unauthorized access.',
                        'code'      => 401
                    )
                ));
                App::stop();
            }else{
                $redirect = Request::getResourceUri();
                Response::redirect(App::urlFor('login').'?redirect='.base64_encode($redirect));
            }
        }
    },
    function() use ($app) {
        /** sample namespaced controller */
        Route::get('/', 'Admin\AdminController:index')->name('admin');

        foreach (Module::getModules() as $module) {
            $module->registerAdminRoute();
        }
    }
);

/** Routes to admin functions */
Route::get('/admin/login', 'Admin\AdminController:login')->name('admin-login');
Route::get('/admin/logout', 'Admin\AdminController:logout')->name('admin-logout');
Route::post('/admin/login', 'Admin\AdminController:doLogin');

/** Route to signup page */
Route::get('/signup', 'User\UserController:signup')->name('signup');
Route::post('/signup', 'User\UserController:doSignup');

/** Route to user home */
Route::get('/home', 'User\UserController:home')->name('home');
Route::get('/login', 'User\UserController:login')->name('login');
Route::get('/logout', 'User\UserController:logout')->name('logout');
Route::post('/login', 'User\UserController:doLogin');

/** Route to invoices */
Route::get('/invoices', 'Invoices\Controllers\InvoiceController:index')->name('all-invoices');
Route::get('/invoices/new', 'Invoices\Controllers\InvoiceController:store')->name('new-invoice');
Route::post('/invoices/new', 'Invoices\Controllers\InvoiceController:store');
Route::get('/invoices/pending', 'Invoices\Controllers\InvoiceController:pending')->name('pending-invoices');
Route::get('/invoices/paid', 'Invoices\Controllers\InvoiceController:paid')->name('paid-invoices');

foreach (Module::getModules() as $module) {
    $module->registerPublicRoute();
}

/** default routing */
Route::get('/', 'HomeController:welcome');