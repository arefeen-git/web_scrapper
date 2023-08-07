<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App;

class Constants {
    
    public const SCRAP_FROM = 'https://rekvizitai.vz.lt/';
    
    public const LINUX_CHROME = [
        'identifier' => 'CookieScriptConsent', // First url decoded array element should contain this string.
        'user-agent' => 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
        'content-type' => 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7DhR5jphY5R9QUgD',
        'form-data' => "------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"word\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"code\"\r\n\r\nPUT_REGISTRATION_CODE_HERE\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"codepvm\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"city\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"street\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"employeesMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"employeesMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"salaryMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"salaryMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"debtMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"debtMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"transportMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"transportMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"salesRevenueMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"salesRevenueMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"netProfitMin\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"netProfitMax\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"registeredFrom\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"registeredTo\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"order\"\r\n\r\n1\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD\r\nContent-Disposition: form-data; name=\"resetFilter\"\r\n\r\n0\r\n------WebKitFormBoundary7DhR5jphY5R9QUgD--\r\n",
    ];
    
    public const LINUX_MOZILLA = [
        'identifier' => 'cf_clearance', // First url decoded array element should contain this string.
        'user-agent' => 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/116.0',
        'content-type' => 'content-type: multipart/form-data; boundary=----WebKitFormBoundaryHqzv6pMD1IpACYGv',
        'form-data' => "-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"word\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"code\"\r\n\r\nPUT_REGISTRATION_CODE_HERE\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"codepvm\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"city\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"street\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"employeesMin\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"employeesMax\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"salaryMin\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"salaryMax\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"debtMin\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"debtMax\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"transportMin\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"transportMax\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"salesRevenueMin\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"salesRevenueMax\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"netProfitMin\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"netProfitMax\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"registeredFrom\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"registeredTo\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"order\"\r\n\r\n1\r\n-----------------------------418446951218208717741252343331\r\nContent-Disposition: form-data; name=\"resetFilter\"\r\n\r\n0\r\n-----------------------------418446951218208717741252343331--\r\n"
    ];
    
    public const MAC_WINDOWS_COMMONER = 'VzLtLoginHash';
    
    public const MAC_CHROME = [
        'identifier' => 'PHPSESSID', // This identifier is different between mac and windows on index 2.
        'user-agent' => 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
        'content-type' => 'Content-Type: multipart/form-data; boundary=---------------------------418446951218208717741252343331',
        'form-data' => "------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"word\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"code\"\r\n\r\nPUT_REGISTRATION_CODE_HERE\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"codepvm\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"city\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"street\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"employeesMin\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"employeesMax\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"salaryMin\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"salaryMax\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"debtMin\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"debtMax\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"transportMin\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"transportMax\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"salesRevenueMin\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"salesRevenueMax\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"netProfitMin\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"netProfitMax\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"registeredFrom\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"registeredTo\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"order\"\r\n\r\n1\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv\r\nContent-Disposition: form-data; name=\"resetFilter\"\r\n\r\n0\r\n------WebKitFormBoundaryHqzv6pMD1IpACYGv--\r\n"
    ];
    
    public const WINDOWS_CHROME = [
        'identifier' => 'cf_clearance', // This identifier is different between mac and windows on index 2.
        'user-agent' => 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
        'content-type' => 'content-type: multipart/form-data; boundary=----WebKitFormBoundary9BSDZUmXzgP5gMAp',
        'form-data' => "------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"word\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"code\"\r\n\r\nPUT_REGISTRATION_CODE_HERE\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"codepvm\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"city\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"street\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"employeesMin\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"employeesMax\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"salaryMin\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"salaryMax\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"debtMin\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"debtMax\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"transportMin\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"transportMax\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"salesRevenueMin\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"salesRevenueMax\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"netProfitMin\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"netProfitMax\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"registeredFrom\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"registeredTo\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"search_terms\"\r\n\r\n\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"order\"\r\n\r\n1\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp\r\nContent-Disposition: form-data; name=\"resetFilter\"\r\n\r\n0\r\n------WebKitFormBoundary9BSDZUmXzgP5gMAp--\r\n"
    ];


    public const ADDRESS    = 'ADDRESS';
    public const MOBILE     = 'MOBILE';
    public const NAME       = 'NAME';
    public const VAT        = 'VAT';
    public const RC         = 'REGISTRATION_CODE';
    
    public const MESSAGE_NA = 'Not Available';

    public const IDENTIFY_COMPANY_TURNOVER = "postCodes table currency-table finances-table";
    
    public const IDENTIFY_COMPANY_DETAILS = [
        'NAME' => [
            'IDENTIFYING_KEY'   => 'top-title',
            'IDENTIFYING_NODE'  => 'h2',
            'NODE_TO_TRAVEL'    => 1
        ],
        'REGISTRATION_CODE' => [
            'IDENTIFYING_KEY'   => 'Registration code',
            'IDENTIFYING_NODE'  => 'td',
            'NODE_TO_TRAVEL'    => 1
        ],
        'VAT' => [
            'IDENTIFYING_KEY'   => 'VAT',
            'IDENTIFYING_NODE'  => 'td',
            'NODE_TO_TRAVEL'    => 1
        ],
        'ADDRESS' => [
            'IDENTIFYING_KEY'   => 'Address',
            'IDENTIFYING_NODE'  => 'td',
            'NODE_TO_TRAVEL'    => 2
        ],
        'MOBILE' => [
            'IDENTIFYING_KEY'   => 'id="filter0_b_27_1875"',
            'IDENTIFYING_NODE'  => 'td',
            'NODE_TO_TRAVEL'    => 9
        ],
    ];
    
    public const RESPONSE_LIMITER = 5;
    public const REDIS_KEY_PREFIX = "redis_list_";
    public const REDIS_PAGINATE_KEY_PREFIX = "redis_pagination_";
}
