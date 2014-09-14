<?php

namespace Invoices\Controllers;

use \App;
use \View;
use \Menu;
use \Invoice;
use \UserInvoice;
use \Input;
use \Sentry;
use \Request;
use \Response;
use \Exception;
use \Admin\BaseController;
use \Cartalyst\Sentry\Users\UserNotFoundException;

class InvoiceController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        Menu::get('admin_sidebar')->setActiveMenu('group');
    }

    public function index($page = 1)
    {
        $user = Sentry::getUser();
        $this->data['title'] = 'Invoices List';
        $this->data['Invoices'] = UserInvoice::where('user_id', '=', $user->id)
                               ->get()
                               ->toArray();

        /** load the user.js app */
        $this->loadJs('app/user.js');

        /** publish necessary js  variable */
        $this->publish('baseUrl', $this->data['baseUrl']);

        /** render the template */
        View::display('@invoices/list/list.twig', $this->data);
    }

    public function show()
    {

    }

    public function store()
    {
        $invoice = null;
        $message = '';
        $success = false;

        if(Input::post()) {
            try{
            $input = Input::post();


            $invoice = Sentry::createInvoice(array(
                'clientEmail'       => $input['email'],
                'dueDate'    => $input['password'],
                'reminders'  => $input['first_name'],
                'paid' => false
            ));

            $success = true;
            $message = 'User created successfully';
            }catch (Exception $e){
                $message = $e->getMessage();
            }

            if(Request::isAjax()){
                Response::headers()->set('Content-Type', 'application/json');
                Response::setBody(json_encode(
                    array(
                        'success'   => $success,
                        'data'      => ($user) ? $user->toArray() : $user,
                        'message'   => $message,
                        'code'      => $success ? 200 : 500
                    )
                ));
            }else{
                Response::redirect($this->siteUrl('home'));
            }
        }else{
            View::display('@invoices/new/index.twig');
        }
    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }

    public function paid()
    {
        $user = Sentry::getUser();
        $this->data['title'] = 'Paid Invoices';
        $this->data['invoices'] = UserInvoice::where('user_id', '=', $user->id)->get()->toArray();

        /** load the invoices.js app */
        $this->loadJs('app/invoices.js');

        /** publish necessary js variable */
        $this->publish('baseUrl', $this->data['baseUrl']);

        /** render the template */
        View::display('@invoices/paid/index.twig', $this->data);
    }

}