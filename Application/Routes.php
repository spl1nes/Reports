<?php

use phpOMS\Router\RouteVerb;

return [
	'^(\/*\?.*)*$' => [
        [
            'dest' => 'Reports\Application\Controllers\OverviewController:showOverview',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^report/DentistNewLostCustomers$' => [
        [
            'dest' => 'Reports\Application\Controllers\ReportsController:DentistNewLostCustomers',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^report/TotalActiveCustomers$' => [
        [
            'dest' => 'Reports\Application\Controllers\ReportsController:TotalActiveCustomers',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^report/DentistNewLostCustomers\?export=.*$' => [
        [
            'dest' => 'Reports\Application\Controllers\ReportsController:DentistNewLostCustomersExport',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^report/FederalStatesSalesNormalized$' => [
        [
            'dest' => 'Reports\Application\Controllers\ReportsController:FederalStatesSalesNormalized',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^report/ServiceSalesEvaluation$' => [
        [
            'dest' => 'Reports\Application\Controllers\ReportsController:ServiceSalesEvaluation',
            'verb' => RouteVerb::GET,
        ],
    ],
];