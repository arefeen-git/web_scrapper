<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DOMDocument, DOMXPath;
use App\Constants;

class HomeController extends AbstractController {
    
    #[Route('/home/test', name: 'app_home_test')]
    public function test(): Response {
        $number = "5";

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
    
    #[Route('/home', name: 'app_home')]
    public function index(): Response {

        // Set URL
        $url = Constants::SCRAP_FROM . 'en/company-search/1/';
        $cookie_consent = 'VzLtLoginHash=Ir5m8qAvlyaGfg0dbH; _gid=GA1.2.1022143044.1691396522; cf_clearance=wDvZaWd_cr3SgQZqK6FRlvnzTreGOSQWV2INUsu2sAw-1691439000-0-1-37c3310c.776000b8.60c26eec-250.2.1691439000; CookieScriptConsent=%7B%22googleconsentmap%22%3A%7B%22ad_storage%22%3A%22targeting%22%2C%22analytics_storage%22%3A%22performance%22%2C%22functionality_storage%22%3A%22functionality%22%2C%22personalization_storage%22%3A%22functionality%22%2C%22security_storage%22%3A%22functionality%22%7D%2C%22action%22%3A%22accept%22%2C%22categories%22%3A%22%5B%5C%22unclassified%5C%22%2C%5C%22targeting%5C%22%5D%22%2C%22key%22%3A%2298b5c6bf-eebe-454f-a7fa-dcf9580b6b64%22%7D; PHPSESSID=53auht7ictbpnq96ar114beln2; _gat_UA-724652-3=1; _ga_D931ERQW91=GS1.1.1691439000.3.1.1691439703.0.0.0; _ga=GA1.1.303498819.1691396522';
        
        $url_decoded = urldecode($cookie_consent);
        $url_decoded_array = explode(";", $url_decoded);
        
        $browser_elemnts = [];
        
        if (is_array($url_decoded_array) && !empty($url_decoded_array[0])){
            if (str_contains($url_decoded_array[0], Constants::LINUX_CHROME['identifier'])){
                $browser_elemnts = Constants::LINUX_CHROME;
            }
            else if(str_contains($url_decoded_array[0], Constants::LINUX_MOZILLA['identifier'])){
                $browser_elemnts = Constants::LINUX_MOZILLA;
            }
            else if(str_contains($url_decoded_array[0], Constants::MAC_WINDOWS_COMMONER)){
                if (!empty($url_decoded_array[2])){
                    if (str_contains($url_decoded_array[2], Constants::MAC_CHROME['identifier'])){
                        $browser_elemnts = Constants::MAC_CHROME;
                    }
                    else if(str_contains($url_decoded_array[2], Constants::WINDOWS_CHROME['identifier'])){
                        $browser_elemnts = Constants::WINDOWS_CHROME;
                    }
                }
            }
        }

        // Set request headers
        $header_elements = [
            $browser_elemnts['content-type'],
            'cookie: ' . $cookie_consent,
            $browser_elemnts['user-agent']
        ];

        // Set request data
        $registration_code = 306373468;
        $data = $browser_elemnts['form-data'];
        $form_data = str_replace("PUT_REGISTRATION_CODE_HERE", $registration_code, $data);

        // Options from stream_context
        $options = [
            'http' => [
                'header' => implode(PHP_EOL, $header_elements),
                'method' => 'POST',
                'content' => $form_data,
            ],
        ];

        $context = stream_context_create($options);        
        $html_chunk = file_get_contents($url, false, $context);
        $html_array = explode(PHP_EOL, $html_chunk);

        foreach ($html_array as $key => $val) {
            if (strpos($val, "No records found. Please refine the search criteria") !== false) {
                print_r("No records found. Please provide valid Registration Code");
                die();
            }
            if (strpos($val, "company-title d-block") !== false) {
                $index = $key;
                break;
            }
        }


        $dom = new DOMDocument();
        $dom->loadHTML($html_array[$index]);

        $links = $dom->getElementsByTagName('a');
        $firstLink = $links->item(0);
        $company_profile_url = $firstLink->getAttribute('href');
        $company_turnover_url = $company_profile_url . "turnover";

        $company_profile_header = [
            'cookie: ' . $cookie_consent,
            $browser_elemnts['user-agent']
        ];

        $company_profile_context = stream_context_create([
            'http' => [
                'header' => implode("\r\n", $company_profile_header)
            ]
        ]);

        // Extracting Company Details from Company Profile URL
        $company_profile_html_chunk = file_get_contents($company_profile_url, false, $company_profile_context);
        $company_details = $this->_retrieve_company_details($company_profile_html_chunk);

        // Extraction 2 : Sending Chris Hemsworth for turnover details ...
        $company_turnover_html_chunk = file_get_contents($company_turnover_url, false, $company_profile_context);
        
        if (strpos($company_turnover_html_chunk, Constants::IDENTIFY_COMPANY_TURNOVER) == false) {
            $company_turnover = "Not Available";
        }
        else{
            $company_turnover = $this->_retrieve_company_turnover($company_turnover_html_chunk);
        }

        return $this->render('home/index.html.twig', [
                    'controller_name' => 'HomeController',
                    'content' => $company_details,
                    'turnover' => $company_turnover
        ]);
    }

    private function _retrieve_company_details($details): array {
        $company_profile_html_array = explode(PHP_EOL, $details);
        $dom = new DOMDocument();
        $company_details = [];

        foreach (Constants::IDENTIFY_COMPANY_DETAILS as $info => $props) {
            
            // Set default value.
            $company_details[strtolower($info)] = Constants::MESSAGE_NA;
            
            foreach ($company_profile_html_array as $key => $val) {
                if (strpos($val, $props['IDENTIFYING_KEY']) !== false) {
                    $info_index = $key + $props['NODE_TO_TRAVEL'];
                    $company_details[strtolower($info)] = true; // Set Later.
                    break;
                }
            }
            
            /*
             * Arefeen : Required info should be in this element. 
             * Some info might be missing, so use single condition block for each individual info.
             * Don't use only else as some companies doesn't show vat, and use different formats regarding mobile.
             */
            
            $dom->loadHTML($company_profile_html_array[$info_index]);

            if ($info == Constants::NAME && ($company_details[strtolower($info)] !== Constants::MESSAGE_NA)) {
                $info_element = $dom->getElementsByTagName($props['IDENTIFYING_NODE']);
                if (!empty($info_element)) {
                    $info_obj = $info_element->item(0);
                    $company_details[strtolower($info)] = str_replace("Company", "", ucwords(strtolower($info_obj->textContent)));
                }
            }
            
            if ($info == Constants::ADDRESS && ($company_details[strtolower($info)] !== Constants::MESSAGE_NA)) {
                $company_details[strtolower($info)] = $company_profile_html_array[$info_index];
            }
            
            if ($info == Constants::MOBILE && ($company_details[strtolower($info)] !== Constants::MESSAGE_NA)) {
                // Find the img element that contains mobile no image.
                $info_element = $dom->getElementsByTagName('img')->item(0);
                if (!empty($info_element)) {
                    $info_obj = $info_element->getAttribute('src');
                    $company_details[strtolower($info)] = Constants::SCRAP_FROM . $info_obj;
                }
            }
            
            if ($info == Constants::RC && ($company_details[strtolower($info)] !== Constants::MESSAGE_NA)) {
                $info_element = $dom->getElementsByTagName($props['IDENTIFYING_NODE']);
                if (!empty($info_element)) {
                    $info_obj = $info_element->item(0);
                    $company_details[strtolower($info)] = $info_obj->textContent;
                }
            }

            if ($info == Constants::VAT && ($company_details[strtolower($info)] !== Constants::MESSAGE_NA)) {
                $info_element = $dom->getElementsByTagName($props['IDENTIFYING_NODE']);
                if (!empty($info_element)) {
                    $info_obj = $info_element->item(0);
                    $company_details[strtolower($info)] = $info_obj->textContent;
                }
            }
        }

        return $company_details;
    }

    private function _retrieve_company_turnover($turnover_details): string {
        $company_turnover_html_array = explode(PHP_EOL, $turnover_details);
        $dom = new DOMDocument();
        $company_turnover_details = [];

        $turnover_table_start_index = 0;
        $turnover_table_end_index = 0;
        $search_table_end_index = false;

        foreach ($company_turnover_html_array as $key => $val) {
            if (strpos($val, Constants::IDENTIFY_COMPANY_TURNOVER) !== false) {
                $turnover_table_start_index = $key;
                $search_table_end_index = true;
            } else {
                // Delete only till search index is found.
                if (!$search_table_end_index) {
                    unset($company_turnover_html_array[$key]);
                }
            }
            if ($search_table_end_index) {
                if (strpos($val, '</table>') !== false) {
                    $turnover_table_end_index = $key;
                    break;
                }
            }
        }

        $slice_length = ($turnover_table_end_index - $turnover_table_start_index) + 1; // As array index start from 0.
        $tmp_array = array_values($company_turnover_html_array);

        $turnover_table_array = array_splice($tmp_array, 0, $slice_length);
        $company_turnover_details = $this->_prepare_array_from_table($turnover_table_array);

        return $company_turnover_details;
    }

    private function _prepare_array_from_table($turnover_table_array) {
        $non_table_identifiers = ['<div ', '</div>', '<rect ', '<path ', '<svg ', '</svg>', '<span ', '</span>'];

        foreach ($non_table_identifiers as $non_table_elem) {
            foreach ($turnover_table_array as $index => $examine_val) {
                if (strpos($examine_val, $non_table_elem) !== false) {
                    unset($turnover_table_array[$index]);
                }
            }
        }

        $turnover_table = implode(PHP_EOL, $turnover_table_array);

        $dom = new DOMDocument();
        $dom->loadHTML($turnover_table);
        
        $xpath = new DOMXPath($dom);
        $table = $xpath->query('//table')->item(0);

        // Remove the 'style' & 'class' attribute 
        $table->removeAttribute('style');
        $table->removeAttribute('class');

        // Get all elements within the table
        $elements = $xpath->query('//table//*');

        // Remove 'style' and 'class' from Childs
        foreach ($elements as $element) {
            $element->removeAttribute('style');
            $element->removeAttribute('class');
        }

        // Modified HTML content
        $mod_html = '<table id="financial_details" class="table table-striped table-bordered">';
        foreach ($table->childNodes as $node) {
            $mod_html .= $dom->saveHTML($node);
        }
        
        $result = trim($mod_html . '</table>');

        return trim($result);
    }
}
