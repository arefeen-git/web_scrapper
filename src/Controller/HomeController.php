<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DOMDocument;
use App\Constants;

class HomeController extends AbstractController {

    #[Route('/home', name: 'app_home')]
    public function index(): Response {
        // Set the URL
        $url = Constants::SCRAP_FROM . 'en/company-search/1/';
        $cookie_consent = 'CookieScriptConsent=%7B%22googleconsentmap%22%3A%7B%22ad_storage%22%3A%22targeting%22%2C%22analytics_storage%22%3A%22performance%22%2C%22functionality_storage%22%3A%22functionality%22%2C%22personalization_storage%22%3A%22functionality%22%2C%22security_storage%22%3A%22functionality%22%7D%2C%22action%22%3A%22accept%22%2C%22categories%22%3A%22%5B%5C%22unclassified%5C%22%2C%5C%22targeting%5C%22%5D%22%2C%22key%22%3A%223e8df365-6f2c-4c1a-80ad-d5c902d78b97%22%7D; PHPSESSID=1lri84b80glrea6c7jfo4pc53o; _gid=GA1.2.857438531.1689414271; cf_clearance=t7MwWSqLcCEwpRYz3NmAqYPgjYP0PyhfIM_aHLDmGjI-1689459386-0-250; __cf_bm=GHTcYoAfD3ehTxqONFaM1TSP8M1UxhmHYlBxFhmbCec-1689459397-0-AbFgEfnp8f6wyhkmHznOWD8JfY+VPRgVd9m48Fq4KTqbBXyCbDC7WoBqmydmxAfZWg==; _gat_UA-724652-3=1; _ga_D931ERQW91=GS1.1.1689459385.21.1.1689459397.0.0.0; _ga=GA1.1.1096950485.1688928483';
        $user_agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36';

        // Set the request headers
        $header_elements = [
            'content-type: multipart/form-data; boundary=----WebKitFormBoundary7DhR5jphY5R9QUgD',
            'cookie: ' . $cookie_consent,
            'user-agent: ' . $user_agent
        ];

//        $registration_code = 304565690;
        // Set the request data
        $data = "------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"word\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"code\"\r\n\r\n304565690\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"codepvm\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"city\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"street\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"employeesMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"employeesMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"salaryMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"salaryMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"debtMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"debtMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"transportMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"transportMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"salesRevenueMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"salesRevenueMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"netProfitMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"netProfitMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"registeredFrom\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"registeredTo\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"order\"\r\n\r\n1\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"resetFilter\"\r\n\r\n0\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD--\r\n";
//        $data = "name=&word=&code=" . $registration_code . "&codepvm=&city=&search_terms=&street=&employeesMin=&employeesMax=&salaryMin=&salaryMax=&debtMin=&debtMax=&transportMin=&transportMax=&salesRevenueMin=&salesRevenueMax=&netProfitMin=&netProfitMax=&registeredFrom=&registeredTo=&search_terms=&order=1&resetFilter=0";
        // Build the stream context options
        $options = [
            'http' => [
                'header' => implode(PHP_EOL, $header_elements),
                'method' => 'POST',
                'content' => $data,
            ],
        ];

        $context = stream_context_create($options);
        $html_chunk = file_get_contents($url, false, $context);
        $html_array = explode(PHP_EOL, $html_chunk);

        foreach ($html_array as $key => $val) {
            if (strpos($val, "company-title d-block")) {
                $index = $key;
                break;
            }
        }


        $dom = new DOMDocument();
        $dom->loadHTML($html_array[$index]);

        $links = $dom->getElementsByTagName('a');
        $firstLink = $links->item(0);
        $href = $firstLink->getAttribute('href');

        $company_profile_header = [
            'cookie: ' . $cookie_consent,
            'user-agent: ' . $user_agent
        ];

        $company_profile_context = stream_context_create([
            'http' => [
                'header' => implode("\r\n", $company_profile_header)
            ]
        ]);

        $company_profile_html_chunk = file_get_contents($href, false, $company_profile_context);
        $company_details = $this->_retrieve_company_details($company_profile_html_chunk);

        print_r($company_details['mobile']);
        die();

        // Companyname, RC, Vat, Address, Mobile Phone.
//        return $this->render('home/index.html.twig', [
//                    'controller_name' => 'HomeController',
//                    'content' => $response
//        ]);
    }

    private function _retrieve_company_details($details): array {
        $company_profile_html_array = explode(PHP_EOL, $details);
        $dom = new DOMDocument();
        $company_details = [];

        foreach (Constants::IDENTIFY_COMPANY_DETAILS as $info => $props) {
            foreach ($company_profile_html_array as $key => $val) {
                if (strpos($val, $props['IDENTIFYING_KEY'])) {
                    $info_index = $key + $props['NODE_TO_TRAVEL'];
                    break;
                }
            }

            if ($props['IDENTIFYING_KEY'] == Constants::ADDRESS) {
                $company_details[strtolower($info)] = $company_profile_html_array[$info_index];
            } else {
                $dom->loadHTML($company_profile_html_array[$info_index]);
                if ($props['IDENTIFYING_KEY'] == Constants::MOBILE) {
                    // Find the img element
                    $info_element = $dom->getElementsByTagName('img')->item(0);
                    $info_obj = $info_element->getAttribute('src');
                    $company_details[strtolower($info)] = Constants::SCRAP_FROM . $info_obj;
                } else {
                    $info_element = $dom->getElementsByTagName($props['IDENTIFYING_NODE']);
                    $info_obj = $info_element->item(0);
                    $company_details[strtolower($info)] = ($info == Constants::NAME) ? str_replace("Company", "", ucwords(strtolower($info_obj->textContent))) : $info_obj->textContent;
                }
            }
        }

        return $company_details;
    }
}
