<?php

// src/Utility/AppUtility.php

namespace App\Utility;

use App\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use DOMDocument;
use DOMXPath;

class ScraperUtility extends AbstractController {

    /**
     * Generates scrapped data and return company details and finances if available.
     *
     * @param int $rcCode 9 digit registration code of the company.
     * @param string $cookieConsent Cookie Consent Value Constants::SCRAP_FROM website after passing capcha.
     *
     * @return array Should return company details which can be used to create a company.
     */
    public function start_scraping($rcCode, $cookieConsent): array {

        // Set URL
        $url = Constants::SCRAP_FROM . 'en/company-search/1/';
        $cookie_consent = $cookieConsent;

        // This will try to get the appropriate form data boundary, user-agent etc. for the requests.
        $browser_elements = $this->_get_browser_elemetns($cookie_consent);

        // Set request headers
        $header_elements = [
            $browser_elements['content-type'],
            'cookie: ' . $cookie_consent,
            $browser_elements['user-agent']
        ];

        // Set request data
        $registration_code = $rcCode;
        $data = $browser_elements['form-data'];
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
                $message = [];
                $message['error_message'] = "No records found. Please provide valid Registration Code";
                return $message;
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
            $browser_elements['user-agent']
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
        } else {
            $company_turnover = $this->_retrieve_company_turnover($company_turnover_html_chunk);
        }

        $company_details['finances'] = $company_turnover;

        return $company_details;
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

    // Limiting support for 4 browsers
    private function _get_browser_elemetns($cookie_consent): array {
        $url_decoded = urldecode($cookie_consent);
        $url_decoded_array = explode(";", $url_decoded);

        if (!is_array($url_decoded_array) || empty($url_decoded_array[0])) {
            return [];
        }

        $browser_elements = [];

        $firstElement = $url_decoded_array[0];
        $secondElement = $url_decoded_array[2] ?? null;

        if (str_contains($firstElement, Constants::LINUX_CHROME['identifier'])) {
            $browser_elements = Constants::LINUX_CHROME;
        } elseif (str_contains($firstElement, Constants::LINUX_MOZILLA['identifier'])) {
            $browser_elements = Constants::LINUX_MOZILLA;
        } elseif (str_contains($firstElement, Constants::MAC_WINDOWS_COMMONER)) {
            if (!empty($secondElement)) {
                $browser_elements = str_contains($secondElement, Constants::MAC_CHROME['identifier']) ? Constants::MAC_CHROME : Constants::WINDOWS_CHROME;
            }
        }

        return $browser_elements;
    }
}
