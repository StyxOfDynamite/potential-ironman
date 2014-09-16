<?php

namespace Invoices\Controllers;

use \App;
use \View;
use \Menu;
use \Invoice;
use \User;
use \UsersInvoice;
use \Input;
use \Sentry;
use \Request;
use \Response;
use \Exception;
use \Admin\BaseController;
use \Cartalyst\Sentry\Users\UserNotFoundException;

class InvoiceController extends \User\BaseController
{

    public function __construct()
    {
        parent::__construct();
        Menu::get('user_sidebar')->setActiveMenu('invoices');
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
        App::render('@invoices/list/list.twig', $this->data);
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
            $email = Input::post('email');
            $due_date = Input::post('due_date');
            $reminders = Input::post('reminders') ? true : false;
            $paid = Input::post('paid') ? true : false;
            $redirect = Input::post('redirect');
            $redirect = ($redirect) ? $redirect : 'home';

            try{

                $user = Sentry::getUser();

                $invoice = new Invoice;
                $invoice->user_id = $user->id;
                $invoice->clientEmail = $email;
                $invoice->dueDate = $due_date;
                $invoice->reminders = $reminders;
                $invoice->paid = $paid;
                $invoice->save();


                Response::redirect($this->siteUrl('invoices/pending'));

                

            }catch(\Exception $e){
                App::flash('message', $e->getMessage());
                App::flash('email', $email);

                Response::redirect($this->siteUrl('invoices/new'));
            }
        }else{
            $this->loadJs('app/new-invoice.js', ['position' => 'after:jquery-1.10.2.js']);
            App::render('@invoices/new/index.twig', $this->data);
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

    }

    public function pending()
    {
        $user = Sentry::getUser();
        $invoices = User::find($user->id)->invoices;



        $this->data['title'] = 'Invoices';
        $this->data['invoices'] = $invoices;


        /** render the template */
        App::render('@invoices/pending/index.twig', $this->data);
    }

}