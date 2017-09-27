<?php

namespace Reports\Application\Controllers;

use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Datatypes\SmartDateTime;
use Reports\Application\WebApplication;

class DashboardController
{
    protected $app = null;

    const MAX_PAST = 10;

    public function __construct(WebApplication $app)
    {
        $this->app = $app;
    }

    protected function calcCurrentMonth(\DateTime $date) : int
    {
        $mod = ((int) $date->format('m') - $this->app->config['fiscal_year']);

        return abs(($mod < 0 ? 12 + $mod : $mod) % 12);
    }

    protected function getFiscalYear(int $year, int $month, int $beg) : int
    {
        if($month < $beg) {
            return $year - 1;
        } 

        return $year;
    }

    protected function getFiscalMonth(int $month) : int
    {
        $mod          = (int) $month - $this->app->config['fiscal_year'];
        return (($mod < 0 ? 12 + $mod : $mod) % 12) + 1;
    }

    protected function getFiscalYearStart(SmartDateTime $date) : SmartDateTime
    {
        $newDate = new SmartDateTime($date->format('Y') . '-' . $date->format('m') . '-01');
        $newDate->smartModify(0, -$this->calcCurrentMonth($date));

        return $newDate;
    }

    protected function getFiscalYearId(\DateTime $date) : int
    {
        $year = $date->format('Y');

        if((int) $date->format('m') >= $this->app->config['fiscal_year']) {
            return (int) $year + 1;
        }

        return $year;
    }
}