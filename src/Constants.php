<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App;

class Constants {
    
    public const SCRAP_FROM     = 'https://rekvizitai.vz.lt/';
    public const SCRAPDO_URL    = "https://api.scrape.do?";
    public const SCRAPDO_TOKEN  = "2b5c016e99bd4a7eab63526aeb5abca88d310a99879";
    public const SCRAPDO_ERROR  = 'StatusCode';

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
