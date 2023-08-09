<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DOMDocument, DOMXPath;
use App\Constants;
use App\Utility\ScraperUtility;

class HomeController extends AbstractController {
    
    private $scraperUtility;

    public function __construct(ScraperUtility $scraperUtility)
    {
        $this->scraperUtility = $scraperUtility;
    }
    
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
        $cookie_consent = 
                <<<EOT
                    curl 'https://rekvizitai.vz.lt/en/company-search/1/' --compressed -X POST -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/116.0' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8' -H 'Accept-Language: en-US,en;q=0.5' -H 'Accept-Encoding: gzip, deflate, br' -H 'Referer: https://rekvizitai.vz.lt/en/company-search/' -H 'Content-Type: multipart/form-data; boundary=---------------------------6170295819296568743677679354' -H 'Origin: https://rekvizitai.vz.lt' -H 'Connection: keep-alive' -H 'Cookie: cf_clearance=L1LOgopAiIiZ_nmiuH9qCrfuWKHfqPGaYiczJIfdg5U-1691577179-0-1-afbe41c0.37dbf413.f8bf1e20-250.2.1691577179; CookieScriptConsent=%7B%22googleconsentmap%22%3A%7B%22ad_storage%22%3A%22targeting%22%2C%22analytics_storage%22%3A%22performance%22%2C%22functionality_storage%22%3A%22functionality%22%2C%22personalization_storage%22%3A%22functionality%22%2C%22security_storage%22%3A%22functionality%22%7D%2C%22action%22%3A%22accept%22%2C%22categories%22%3A%22%5B%5C%22unclassified%5C%22%2C%5C%22targeting%5C%22%5D%22%2C%22key%22%3A%22369b58f2-acac-4d82-b25d-1bc1341aee25%22%7D; _ga_D931ERQW91=GS1.1.1691577171.7.1.1691577187.0.0.0; _ga=GA1.2.18986035.1689089881; visid_incap_1823587=f462hnsfRH6Ee+vE/NX551t3rWQAAAAAQUIPAAAAAACR+VXbsRUl3RV3HhyXFpw0; VzLtLoginHash=0GIy66Wp3ggVkeN0IC; _gid=GA1.2.821224099.1691419940; PHPSESSID=u7ckg1ihgoamsmre9f5nkjarjt; nlbi_1823587=WGd/WR93rlXnyu6Rq9G9pQAAAADQLTs8E6FtuDmADoMu8qwa; _gat_UA-724652-3=1; incap_ses_962_1823587=F2vrCXwHIXpR4eFf7rZZDWNr02QAAAAAvHRw6FC+6l6yxfPKF44GwA==' -H 'Upgrade-Insecure-Requests: 1' -H 'Sec-Fetch-Dest: document' -H 'Sec-Fetch-Mode: navigate' -H 'Sec-Fetch-Site: same-origin' -H 'Sec-Fetch-User: ?1' -H 'TE: trailers' --data-binary $'-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="name"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="word"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="code"\r\n\r\n304422799\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="codepvm"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="city"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="search_terms"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="street"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="employeesMin"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="employeesMax"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="salaryMin"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="salaryMax"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="debtMin"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="debtMax"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="transportMin"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="transportMax"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="salesRevenueMin"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="salesRevenueMax"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="netProfitMin"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="netProfitMax"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="registeredFrom"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="registeredTo"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="search_terms"\r\n\r\n\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="order"\r\n\r\n1\r\n-----------------------------6170295819296568743677679354\r\nContent-Disposition: form-data; name="resetFilter"\r\n\r\n0\r\n-----------------------------6170295819296568743677679354--\r\n'
                EOT;
        
        $cURL = 
                <<<EOT
                    curl 'https://rekvizitai.vz.lt/en/company-search/1/' \
                    -H 'authority: rekvizitai.vz.lt' \
                    -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
                    -H 'accept-language: en-US,en;q=0.9' \
                    -H 'cache-control: max-age=0' \
                    -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundaryLywuq8fPyAKrypIo' \
                    -H 'cookie: CookieScriptConsent=%7B%22googleconsentmap%22%3A%7B%22ad_storage%22%3A%22targeting%22%2C%22analytics_storage%22%3A%22performance%22%2C%22functionality_storage%22%3A%22functionality%22%2C%22personalization_storage%22%3A%22functionality%22%2C%22security_storage%22%3A%22functionality%22%7D%2C%22action%22%3A%22accept%22%2C%22categories%22%3A%22%5B%5C%22unclassified%5C%22%2C%5C%22targeting%5C%22%5D%22%2C%22key%22%3A%223e8df365-6f2c-4c1a-80ad-d5c902d78b97%22%7D; _gid=GA1.2.1146864184.1691324377; PHPSESSID=3enu96ml232aub91i6dkj5htmd; VzLtLoginHash=iKagzxDfJtK9Im0OvZ; cf_clearance=EiKlFEZ9H4wUxrWhbDsC62YXPi_3nfBVDzXYv6cqKek-1691587290-0-1-dd86ce74.644fd689.8a879194-250.2.1691587290; _gat_UA-724652-3=1; _ga_D931ERQW91=GS1.1.1691587224.103.1.1691587336.0.0.0; _ga=GA1.1.1096950485.1688928483' \
                    -H 'origin: https://rekvizitai.vz.lt' \
                    -H 'referer: https://rekvizitai.vz.lt/en/company-search/' \
                    -H 'sec-ch-ua: "Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"' \
                    -H 'sec-ch-ua-mobile: ?0' \
                    -H 'sec-ch-ua-platform: "Linux"' \
                    -H 'sec-fetch-dest: document' \
                    -H 'sec-fetch-mode: navigate' \
                    -H 'sec-fetch-site: same-origin' \
                    -H 'sec-fetch-user: ?1' \
                    -H 'upgrade-insecure-requests: 1' \
                    -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36' \
                    --data-raw $'------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="name"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="word"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="code"\r\n\r\n165235837\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="codepvm"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="city"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="search_terms"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="street"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="employeesMin"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="employeesMax"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="salaryMin"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="salaryMax"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="debtMin"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="debtMax"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="transportMin"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="transportMax"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="salesRevenueMin"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="salesRevenueMax"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="netProfitMin"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="netProfitMax"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="registeredFrom"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="registeredTo"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="search_terms"\r\n\r\n\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="order"\r\n\r\n1\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo\r\nContent-Disposition: form-data; name="resetFilter"\r\n\r\n0\r\n------WebKitFormBoundaryLywuq8fPyAKrypIo--\r\n' \
                    --compressed
                EOT;
        
        $registration_code = 165235837;
        
//        $browser_elemnts = $this->scraperUtility->processCurl($cookie_consent, $registration_code);
        $browser_elemnts = $this->scraperUtility->processCurl($cURL, $registration_code);

        // Set request headers
        $header_elements = [
            $browser_elemnts['content-type'],
            $browser_elemnts['cookie'],
            $browser_elemnts['user-agent']
        ];

        // Options from stream_context
        $options = [
            'http' => [
                'header' => implode(PHP_EOL, $header_elements),
                'method' => 'POST',
                'content' => $browser_elemnts['form-data'],
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
            $browser_elemnts['cookie'],
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
