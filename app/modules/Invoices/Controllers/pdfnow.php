<?php namespace Pdfnow\Pdfnow;
/**
 * PDFnow library, use it in your own application
 *
 * This file is for calling the API of PDFnow.com
 *
 * Requirements:
 * - PHP 5.3.10
 * - Curl-Library of PHP
 *
 * @author Tobias Haupenthal
 * @version 2014-07-01
 */

/**
 * !!! Please modify this
 * You have to set here the right API key.
 * It can be seen after login at our website at
 * http://www.pdfnow.com/de/userarea
 */
define("APIKEY", "f3a72695-d9b3-4984-8b77-290a4d88131a");

/**
 * This constant defines the interface to the API.
 * No modification is here necessary.
 */
define("SERVERURL", "pdfnow.com:8080/pdfnow/generate");

/**
 * Creates a pdf out of a selected template
 * @param String $template - a template, which has been previously uploaded
 * @param Array $array - predefined variables of the template
 * @return array - Status of the request
 *
 * Example:
 * $adresse = array();
 * $adresse[] = array("adresse" => array("name"=>"Thorsten Horn",
 *    "strasse"=>"Meinestr. 26", "plz"=>"52072", "ort"=>"Aachen"));
 *
 * generatePdf('rechnung1', array('addressen' => $addresse));
 *
 *
 * Output
 * On success - temporary URL of the created PDF:
 *   array('status'=> 'OK', 'url' => 'http://')
 * On errror - some error info:
 *   array('status'=> 'NOK', 'error' => 'Template tmpl does not exist.')
 *
 */
function generatePdf($template, $array) {
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

/**
 * Changes a PHP-Array into an xml construct
 * 
 * @param Array $array - PHP Array
 * @param Array $array - predefined variable settings of the template
 * @return xml - umgewandeltes XML
 *
 * Example:
 * array_to_xml(array('hallo'=> 'welt'));
 *
 * Output:
 * <hallo>welt</hallo>
 * 
 **/
function array_to_xml($array, $xml = NULL) {
    $isRootNode = false;
    if (!isset($xml)) {
        $isRootNode = true;
        $xml = new SimpleXMLElement("<root/>");
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
?>


