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

    public function show($id)
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
            $due_date = str_replace('/', '-', Input::post('due_date'));
            $due_date = date("Y-m-d", strtotime($due_date));
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
                $item = array( 
                    'name' => $item_name[$i],
                    'quantity' => $item_qty[$i],
                    'price' => $item_price[$i],
                    'total' => $item_total[$i]
                );
                array_push($items, array('item' => $item)); 
            }



            try{

                $user = Sentry::getUser();

                $invoice = new Invoice;
                $invoice->user_id = $user->id;
                $invoice->clientEmail = $client_email;
                $invoice->dueDate = $due_date;
                $invoice->reminders = $reminders;
                $invoice->paid = $paid;
                $invoice->total = $invoice_total;
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
                        'items' => $items,
                        'total' => $invoice_total
                    ),
                    'text' => $invoice_message
                );

                $invoice_details = array("doc" => $invoice_details);
                $result = generatePdf($template, $invoice_details);
                /* fetch pdf */
                $contents = file_get_contents($result['pdf']);
                
                $random = rand();
                /* store pdf */
                file_put_contents('invoice-' . $random . '.pdf', $contents);

                $email = new \PHPMailer;
                $email->From = $user->email;
                $email->FromName  = $user->first_name . ' ' . $user->last_name;
                $email->Sender = $user->email;
                $email->Subject = 'Invoice from ' . $user->first_name . ' ' . $user->last_name;
                $email->Body = $invoice_message;
                $email->AddAddress($client_email);

                $file_to_attach = 'invoice-' . $random . '.pdf';
                $email->AddAttachment($file_to_attach, 'Invoice.pdf' );
                $email->Send();

                /* remove pdf */
                unlink('invoice-' . $random . '.pdf');

                App::flash('message', 'Invoice sent!');
                Response::redirect($this->siteUrl('invoices/pending'));

                

            }catch(\Exception $e){
                App::flash('message', $e->getMessage());
                App::flash('email', $client_email);

                Response::redirect($this->siteUrl('invoices/new'));
            }
        }else{
            $this->loadCss('jquery-ui.min.css');
            $this->loadCss('new-invoice.css');
            $this->loadJs('jquery-ui.min.js');
            $this->loadJs('app/new-invoice.js');
            App::render('@invoices/new/index.twig', $this->data);
        }
    }

    public function edit()
    {

    }

    public function update($id)
    {
        $success = false;
        $message = '';
        $invoice = null;
        $code = 0;

        try {
            $input = Input::put();
            /** in case request come from post http form */
            $input = is_null($input) ? Input::post() : $input;


            /** get invoice by id */
            $invoice = Invoice::find($id);
            $invoice->paid = $input['paid'];

            $success = $invoice->save();
            $code    = 200;
            $message = 'Invoice updated sucessfully';
        } catch (Exception $e) {
            $message = $e->getMessage();
            $code    = 500;
        }

        if(Request::isAjax()){
            Response::headers()->set('Content-Type', 'application/json');
            Response::setBody(json_encode(
                array(
                    'success' => $success,
                    'data' => ($invoice) ? $invoice->toArray() : $invoice,
                    'message' => $message,
                    'code' => $code
                )
            ));
        }
    }

    public function destroy($id)
    {

    }

    public function paid()
    {
        $user = Sentry::getUser();
        $invoices = User::find($user->id)->invoices()->where('paid', '=', '1')->get();

        $this->data['title'] = 'Paid Invoices';
        $this->data['invoices'] = $invoices;


        /** render the template */
        App::render('@invoices/paid/index.twig', $this->data);
    }

    public function pending()
    {
        $user = Sentry::getUser();
        $invoices = User::find($user->id)->invoices()->where('paid', '=', '0')->get();

        $this->data['title'] = 'Pending Invoices';
        $this->data['invoices'] = $invoices;
        $this->loadJs('jquery-ui.min.js');
        $this->loadJs('app/pending-invoice.js');

        /** publish necessary js  variable */
        $this->publish('baseUrl', $this->data['baseUrl']);


        /** render the template */
        App::render('@invoices/pending/index.twig', $this->data);
    }

}
