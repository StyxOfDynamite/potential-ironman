<?php

namespace Invoices\Controllers;

use \App;
use \View;
use \Menu;
use \Invoice;
use \User;
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
            /* Invoice details */
            $client_email = Input::post('email');
            $due_date = Input::post('due_date');
            $reminders = true;
            $paid = false;
            $client_name = Input::post('client_name');
            $clientAddress1 = Input::post('clientAddress1');
            $clientAddress2 = Input::post('clientAddress2');
            $clientAddress3 = Input::post('clientAddress3');
            $clientAddress4 = Input::post('clientAddress4');
            $item_name = Input::post('item_name');
            $item_qty = Input::post('item_qty');
            $item_price = Input::post('item_price');
            $item_total = Input::post('item_total');
            $invoice_message = Input::post('message');
            $invoice_total = Input::post('total');
            $items = array();

            for ($i=0; $i < count($item_name); $i++) { 
                $items[]['name'] = $item_name[$i];
                $items[]['quantity'] = $item_qty[$i];
                $items[]['price'] = $item_price[$i];
                $items[]['total'] = $item_total[$i]; 
            }

            try{

                $user = Sentry::getUser();

                $invoice = new Invoice;
                $invoice->user_id = $user->id;
                $invoice->clientEmail = $client_email;
                $invoice->dueDate = $due_date;
                $invoice->reminders = $reminders;
                $invoice->paid = $paid;
                $invoice->save();

                function generatePdf($template, $array)
                {

                    define("APIKEY", "f3a72695-d9b3-4984-8b77-290a4d88131a");

                    define("SERVERURL", "pdfnow.com:8080/pdfnow/generate");


                    $xml = array_to_xml($array);
                    $xml = str_replace("&", "%26", $xml);
                    $fields_string = "apiKey=" . APIKEY . "&" . "templateName=" . $template . "&" . "xmlString=" . $xml;
                    $ch = curl_init();
                    $options = array(CURLOPT_URL => SERVERURL, CURLOPT_RETURNTRANSFER => 1, CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $fields_string);
                    curl_setopt_array($ch, $options);
                    $response = curl_exec($ch);
                    $responsedecoded = (array)json_decode($response);
                    curl_close($ch);
                    return $responsedecoded;
                }

                function array_to_xml($array, $xml = NULL)
                {
                    $isRootNode = false;
                    if (!isset($xml)) {
                        $isRootNode = true;
                        $xml = new \SimpleXMLElement("<root/>");
                    }
                    foreach ($array as $key => $value) {
                        if (is_array($value)) {
                            if (!is_numeric($key)) {
                                $subnode = $xml->addChild("$key");
                                array_to_xml($value, $subnode);
                            } else {
                                array_to_xml($value, $xml);
                            }
                        } else {
                            $xml->addChild("$key", "$value");
                        }
                    }
                    if ($isRootNode) {
                        $innerXml = ($xml->xpath("/root"));
                        $innerXml = $innerXml[0]->children();
                        return $innerXml[0]->asXml();
                    }
                    return $xml;
                }

                

                $template = "invoice";

                $invoice_details = array(
                    'type' => "invoice",
                    'subject' => "Invoice from " . $due_date,
                    'recipient' => array(
                        'field1' => $clientAddress1,
                        'field2' => $clientAddress2,
                        'field3' => $clientAddress3,
                        'field4' => $clientAddress4
                    ),
                    'invoice' => array(
                        'id' => $invoice->id,
                        'clientname' => $client_name,
                        'duedate' => $due_date,
                        'item' => $items,
                        'total' => $invoice_total
                    ),
                    'text' => $invoice_message
                );

                $invoice_details = array("doc" => $invoice_details);
                $result = generatePdf($template, $invoice_details);
                
                $contents = file_get_contents($result['pdf']);

                file_put_contents('invoice.pdf', $contents);

                $email = new \PHPMailer;
                $email->From      = $user->email;
                $email->FromName  = $user->first_name . ' ' . $user->last_name;
                $email->Subject   = 'Invoice from' . $user->first_name . ' ' . $user->last_name;
                $email->Body      = 'Please find invoice attached';
                $email->AddAddress($client_email);

                $file_to_attach = 'invoice.pdf';


                $email->AddAttachment($file_to_attach, 'Invoice.pdf' );
                $email->Send();

                App::flash('message', 'Invoice sent!');
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