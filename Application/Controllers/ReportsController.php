<?php

namespace Reports\Application\Controllers;

use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;
use phpOMS\Datatypes\SmartDateTime;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\File\Local\File;
use phpOMS\DataStorage\Database\Query\Builder;
use QuickDashboard\Application\Models\StructureDefinitions;
use phpOMS\System\MimeType;

class ReportsController extends DashboardController
{
	private function getDentistNewLostCustomersNew()
	{
		$date = new SmartDateTime();

		$query = new Builder($this->app->dbPool->get('sd'));
        $query->raw('
        	select case 
			when _Dataframe.KD_Verkaeufer = \'006\' or _Dataframe.KD_Verkaeufer = \'007\' or _Dataframe.KD_Verkaeufer = \'032\' or _Dataframe.KD_Verkaeufer = \'033\' 
				then \'62 - R. Mura\' 
			when _Dataframe.KD_Verkaeufer = \'004\' or _Dataframe.KD_Verkaeufer = \'008\' or _Dataframe.KD_Verkaeufer = \'013\' or _Dataframe.KD_Verkaeufer = \'019\' 
				then \'Unbesetzt\' 
			when _Dataframe.KD_Verkaeufer = \'011\' or _Dataframe.KD_Verkaeufer = \'012\' or _Dataframe.KD_Verkaeufer = \'024\' or _Dataframe.KD_Verkaeufer = \'044\' 
				then \'67 - S. Sohst\'
			when _Dataframe.KD_Verkaeufer = \'005\' or _Dataframe.KD_Verkaeufer = \'015\'
				then \'91 - T. Harscher\'
			when _Dataframe.KD_Verkaeufer = \'002\' or _Dataframe.KD_Verkaeufer = \'014\' or _Dataframe.KD_Verkaeufer = \'009\' or _Dataframe.KD_Verkaeufer = \'016\' 
				then \'92 - V. Ohloff\'
			when _Dataframe.KD_Verkaeufer = \'003\' or _Dataframe.KD_Verkaeufer = \'010\' or _Dataframe.KD_Verkaeufer = \'021\' or _Dataframe.KD_Verkaeufer = \'022\' 
				then \'94 - W. Albrecht\'
			else _Dataframe.KD_Verkaeufer end as ZA, 
			_Dataframe.KUNDENNUMMER, _Dataframe.KD_Verkaeufer, _Dataframe.KD_Name1, _Dataframe.KD_Name2, _Dataframe.KD_Telefon, _Dataframe.KD_PLZ, _Dataframe.KD_Ort, 
			_Dataframe.KD_Strasse, _Dataframe.BELEGNUMMER,_Dataframe.BELEGDATUM, _Dataframe.ARTIKELNUMMER, rtrim(_Dataframe.ArtikelBez1) as ArtikelBez1
			from _Dataframe 
			where _Dataframe.KUNDENNUMMER in (
				select tbl1.KUNDENNUMMER
				from (
					select _Dataframe.KUNDENNUMMER, MIN(CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 102)) AS first 
					from _Dataframe 
					where _Dataframe.Sparte in (16, 32, 33, 34, 61, 62, 63) and _Dataframe.Belegbuchung = \'R\' and _Dataframe.Belegstufe = 4
					group by _Dataframe.KUNDENNUMMER
				) tbl1
				inner join (
					select t.KUNDENNUMMER, MIN(t.first) MinPoint
					from (
						select _Dataframe.KUNDENNUMMER, MIN(CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 102)) AS first 
						from _Dataframe 
						where _Dataframe.Sparte in (16, 32, 33, 34, 61, 62, 63) and _Dataframe.Belegbuchung = \'R\' and _Dataframe.Belegstufe = 4
						group by _Dataframe.KUNDENNUMMER
					) t
					group by t.KUNDENNUMMER
				) tbl2
				on tbl1.KUNDENNUMMER = tbl2.KUNDENNUMMER
				where tbl2.MinPoint = tbl1.first and tbl1.first >= \'' . $date->createModify(0,0,-7)->format('Y.m.d') . '\' and tbl1.first <= \'' . $date->format('Y.m.d') . '\'
			) and _Dataframe.Belegbuchung = \'R\' and _Dataframe.Belegstufe = 4
			and _Dataframe.Sparte in (16, 32, 33, 34, 61, 62, 63) 
			and _Dataframe.Artikelnummer is not null 
			and _Dataframe.VERTRETER = 1001
			order by _Dataframe.KUNDENNUMMER asc'
		);
        $result = $query->execute()->fetchAll();
        $result = empty($result) ? [] : $result;

        $newCustomers = [];
        foreach($result as $customer) {
        	if(!isset($newCustomers[$customer['ZA']])) {
        		$newCustomers[$customer['ZA']] = [];
        	}

        	$newCustomers[$customer['ZA']][] = $customer;
        }

        return $newCustomers;
	}

	private function getDentistNewLostCustomersLost()
	{
		$dateLost = new SmartDateTime();

        $query = new Builder($this->app->dbPool->get('sd'));
        $query->raw('
        	select distinct
				_Dataframe.KUNDENNUMMER, 
				case 
				when _Dataframe.KD_Verkaeufer = \'006\' or _Dataframe.KD_Verkaeufer = \'007\' or _Dataframe.KD_Verkaeufer = \'032\' or _Dataframe.KD_Verkaeufer = \'033\' 
					then \'62 - R. Mura\' 
				when _Dataframe.KD_Verkaeufer = \'004\' or _Dataframe.KD_Verkaeufer = \'008\' or _Dataframe.KD_Verkaeufer = \'013\' or _Dataframe.KD_Verkaeufer = \'019\' 
					then \'Unbesetzt\' 
				when _Dataframe.KD_Verkaeufer = \'011\' or _Dataframe.KD_Verkaeufer = \'012\' or _Dataframe.KD_Verkaeufer = \'024\' or _Dataframe.KD_Verkaeufer = \'044\' 
					then \'67 - S. Sohst\'
				when _Dataframe.KD_Verkaeufer = \'005\' or _Dataframe.KD_Verkaeufer = \'015\'
					then \'91 - T. Harscher\'
				when _Dataframe.KD_Verkaeufer = \'002\' or _Dataframe.KD_Verkaeufer = \'014\' or _Dataframe.KD_Verkaeufer = \'009\' or _Dataframe.KD_Verkaeufer = \'016\' 
					then \'92 - V. Ohloff\'
				when _Dataframe.KD_Verkaeufer = \'003\' or _Dataframe.KD_Verkaeufer = \'010\' or _Dataframe.KD_Verkaeufer = \'021\' or _Dataframe.KD_Verkaeufer = \'022\' 
					then \'94 - W. Albrecht\'
				else _Dataframe.KD_Verkaeufer end as ZA, 
				_Dataframe.KD_Verkaeufer, _Dataframe.KD_Name1, _Dataframe.KD_Name2, _Dataframe.KD_Telefon, _Dataframe.KD_PLZ, _Dataframe.KD_Ort, _Dataframe.KD_Strasse
			from _Dataframe 
			where _Dataframe.KUNDENNUMMER in (
				select tbl1.KUNDENNUMMER
				from (
					select _Dataframe.KUNDENNUMMER, MAX(CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 102)) AS first 
					from _Dataframe 
					where _Dataframe.Sparte in (16, 61, 62, 63) and _Dataframe.Belegbuchung = \'R\' and _Dataframe.Belegstufe = 4
					group by _Dataframe.KUNDENNUMMER
				) tbl1
				inner join (
					select t.KUNDENNUMMER, MAX(t.first) MinPoint
					from (
						select _Dataframe.KUNDENNUMMER, MAX(CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 102)) AS first 
						from _Dataframe 
						where _Dataframe.Sparte in (16, 61, 62, 63) and _Dataframe.Belegbuchung = \'R\' and _Dataframe.Belegstufe = 4
						group by _Dataframe.KUNDENNUMMER
					) t
					group by t.KUNDENNUMMER
				) tbl2
				on tbl1.KUNDENNUMMER = tbl2.KUNDENNUMMER
				where tbl2.MinPoint = tbl1.first and tbl1.first >= \'' . $dateLost->createModify(0,-12)->format('Y.m.d') . '\' and tbl1.first < \'' . $dateLost->createModify(0,-6)->format('Y.m.d') . '\'
			) and _Dataframe.Belegbuchung = \'R\' and _Dataframe.Belegstufe = 4
			and CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 102) >= \'' . $dateLost->createModify(0,-12)->format('Y.m.d') . '\' and CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 102) < \'' . $dateLost->createModify(0,-6)->format('Y.m.d') . '\'
			and _Dataframe.Sparte in (16, 61, 62, 63) 
			and _Dataframe.Artikelnummer is not null 
			and _Dataframe.VERTRETER = 1001
			order by _Dataframe.KUNDENNUMMER asc'
		);
        $result = $query->execute()->fetchAll();
        $result = empty($result) ? [] : $result;

        $lostCustomers = [];
        foreach($result as $customer) {
        	if(!isset($lostCustomers[$customer['ZA']])) {
        		$lostCustomers[$customer['ZA']] = [];
        	}

        	$lostCustomers[$customer['ZA']][] = $customer;
        }

        return $lostCustomers;
	}

    public function DentistNewLostCustomers(RequestAbstract $request, ResponseAbstract $response)
    {
        $view = new View($this->app, $request, $response);
        $view->setTemplate('/Reports/Application/Templates/reports/Dentist_New_Lost_Customers');

        $newCustomers = $this->getDentistNewLostCustomersNew();
        $view->addData('newCustomers', $newCustomers);

        $lostCustomers = $this->getDentistNewLostCustomersLost();
        $view->addData('lostCustomers', $lostCustomers);

        return $view;
    }

    public function DentistNewLostCustomersExport(RequestAbstract $request, ResponseAbstract $response)
    {
    	$view = new View($this->app, $request, $response);
        $view->setTemplate('/Reports/Application/Templates/exports/DentistNewLostCustomers.xlsx');

        $newCustomers = $this->getDentistNewLostCustomersNew();
        $view->addData('newCustomers', $newCustomers);

        $lostCustomers = $this->getDentistNewLostCustomersLost();
        $view->addData('lostCustomers', $lostCustomers);

        $response->getHeader()->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', true);
        $response->getHeader()->set('Content-Disposition', 'attachment; filename="DentistNewLostCustomers.xlsx"', true);
        $response->getHeader()->set('Expires', gmdate('D, d M Y H:i:s').' GMT', true);
        $response->getHeader()->set('Last-Modified', 'Mon, 26 Jul 1997 05:00:00 GMT', true);
        $response->getHeader()->set('Cache-Control', 'cache, must-revalidate', true);

        $response->set('export', $view->render());
    }

    public function TotalActiveCustomers(RequestAbstract $request, ResponseAbstract $response)
    {
        $view = new View($this->app, $request, $response);
        $view->setTemplate('/Reports/Application/Templates/reports/Total_Active_Customers');

        $current      = new SmartDateTime($request->getData('t') ?? 'now');
        $currentYear  = $current->format('m') - $this->app->config['fiscal_year'] < 0 ? $current->format('Y') - 1 : $current->format('Y');
        $mod          = (int) $current->format('m') - $this->app->config['fiscal_year'];
        $currentMonth = (($mod < 0 ? 12 + $mod : $mod) % 12) + 1;
        $start        = $this->getFiscalYearStart($current);
        $start->modify('-10 year');

        $accounts = StructureDefinitions::PL_ACCOUNTS['Sales'];
        if ($request->getData('u') === 'sd' || $request->getData('u') === 'gdf') {
            $accounts[] = 8591;
        }

        $customerCount  = [];

        $query = new Builder($this->app->dbPool->get('sd'));
        $query->raw('
        	SELECT DISTINCT
                t.years, t.months, COUNT(t.customer) AS customers
            FROM (
                    SELECT
                        datepart(yyyy, CONVERT(VARCHAR(30), FiBuchungsArchiv.Buchungsdatum, 104)) AS years, 
                        datepart(m, CONVERT(VARCHAR(30), FiBuchungsArchiv.Buchungsdatum, 104)) AS months,
                        KUNDENADRESSE.KONTO AS customer
                    FROM FiBuchungsArchiv, KUNDENADRESSE
                    WHERE 
                        KUNDENADRESSE.KONTO = FiBuchungsArchiv.GegenKonto
                        AND FiBuchungsArchiv.Konto IN (' . implode(',', $accounts) . ')
                        AND CONVERT(VARCHAR(30), FiBuchungsArchiv.Buchungsdatum, 104) >= CONVERT(datetime, \'' . $start->format('Y.m.d') . '\', 102) 
                        AND CONVERT(VARCHAR(30), FiBuchungsArchiv.Buchungsdatum, 104) <= CONVERT(datetime, \'' . $current->format('Y.m.d') . '\', 102)
                    GROUP BY
                        datepart(yyyy, CONVERT(VARCHAR(30), FiBuchungsArchiv.Buchungsdatum, 104)), 
                        datepart(m, CONVERT(VARCHAR(30), FiBuchungsArchiv.Buchungsdatum, 104)),
                        KUNDENADRESSE.KONTO
                UNION ALL
                    SELECT 
                        datepart(yyyy, CONVERT(VARCHAR(30), FiBuchungen.Buchungsdatum, 104)) AS years, 
                        datepart(m, CONVERT(VARCHAR(30), FiBuchungen.Buchungsdatum, 104)) AS months,
                        KUNDENADRESSE.KONTO AS customer
                    FROM FiBuchungen, KUNDENADRESSE
                    WHERE 
                        KUNDENADRESSE.KONTO = FiBuchungen.GegenKonto
                        AND FiBuchungen.Konto IN (' . implode(',', $accounts) . ')
                        AND CONVERT(VARCHAR(30), FiBuchungen.Buchungsdatum, 104) >= CONVERT(datetime, \'' . $start->format('Y.m.d') . '\', 102) 
                        AND CONVERT(VARCHAR(30), FiBuchungen.Buchungsdatum, 104) <= CONVERT(datetime, \'' . $current->format('Y.m.d') . '\', 102)
                    GROUP BY
                        datepart(yyyy, CONVERT(VARCHAR(30), FiBuchungen.Buchungsdatum, 104)), 
                        datepart(m, CONVERT(VARCHAR(30), FiBuchungen.Buchungsdatum, 104)),
                        KUNDENADRESSE.KONTO
                ) t
            GROUP BY t.years, t.months;
        ');

        if ($request->getData('u') !== 'gdf') {
        	$result = $query->execute()->fetchAll();
        	$result = empty($result) ? [] : $result;

        	$this->loopCustomerCount($result, $customerCount);
    	}

        if ($request->getData('u') !== 'sd') {
        	$query->setConnection($this->app->dbPool->get('gdf'));
	        $result = $query->execute()->fetchAll();
	        $result = empty($result) ? [] : $result;

	        $this->loopCustomerCount($result, $customerCount);
        }

        $view->setData('currentFiscalYear', $currentYear);
        $view->setData('customerCount', $customerCount);

        return $view;
    }

    private function loopCustomerCount(array $resultset, array &$customerCount)
    {
        foreach ($resultset as $line) {
            $fiscalYear  = $line['months'] - $this->app->config['fiscal_year'] < 0 ? $line['years'] - 1 : $line['years'];
            $mod         = ($line['months'] - $this->app->config['fiscal_year']);
            $fiscalMonth = (($mod < 0 ? 12 + $mod : $mod) % 12) + 1;

            if (!isset($customerCount[$fiscalYear][$fiscalMonth])) {
                $customerCount[$fiscalYear][$fiscalMonth] = 0;
            }

            $customerCount[$fiscalYear][$fiscalMonth] += $line['customers'];
        }
    }

    public function FederalStatesSalesNormalized(RequestAbstract $request, ResponseAbstract $response) 
    {
        $view = new View($this->app, $request, $response);
        $view->setTemplate('/Reports/Application/Templates/reports/Federal_States_Sales_Normalized');

        $current      = new SmartDateTime($request->getData('t') ?? 'now');
        $currentYear  = $current->format('m') - $this->app->config['fiscal_year'] < 0 ? $current->format('Y') - 1 : $current->format('Y');
        $mod          = (int) $current->format('m') - $this->app->config['fiscal_year'];
        $currentMonth = (($mod < 0 ? 12 + $mod : $mod) % 12) + 1;
        $start        = $this->getFiscalYearStart($current);
        $start->modify('-10 year');

        return $view;
    }

    public function ServiceSalesEvaluation(RequestAbstract $request, ResponseAbstract $response) 
    {
        $view = new View($this->app, $request, $response);
        $view->setTemplate('/Reports/Application/Templates/reports/Service_Sales_Evaluation');

        $current      = new SmartDateTime($request->getData('t') ?? 'now');
        $currentYear  = $current->format('m') - $this->app->config['fiscal_year'] < 0 ? $current->format('Y') - 1 : $current->format('Y');
        $mod          = (int) $current->format('m') - $this->app->config['fiscal_year'];
        $currentMonth = (($mod < 0 ? 12 + $mod : $mod) % 12) + 1;
        $start        = $this->getFiscalYearStart($current);
        $start->smartModify(-2);

        $query = new Builder($this->app->dbPool->get('sd'));
        $query->raw('
            select t.years, t.months, sum(t.sales) as sales
            from (
                select 
                            sum(ArchivBelegkopf.WarenwertNetto) as sales,
                            datepart(yyyy, CONVERT(VARCHAR(30), ArchivBelegkopf.ROW_UPDATE_TIME, 104)) AS years, 
                            datepart(m, CONVERT(VARCHAR(30), ArchivBelegkopf.ROW_UPDATE_TIME, 104)) AS months
                        from ArchivBelegkopf 
                        where 
                            ArchivBelegkopf.BelegartId = 5
                            and ArchivBelegkopf.Status > 1
                            and CONVERT(VARCHAR(30), ArchivBelegkopf.ROW_UPDATE_TIME, 104) >= CONVERT(datetime, \'' . $start->format('Y.m.d') . '\', 102) 
                            and CONVERT(VARCHAR(30), ArchivBelegkopf.ROW_UPDATE_TIME, 104) <=  CONVERT(datetime, \'' . $current->format('Y.m.d') . '\', 102) 
                        group by
                            datepart(yyyy, CONVERT(VARCHAR(30), ArchivBelegkopf.ROW_UPDATE_TIME, 104)), 
                            datepart(m, CONVERT(VARCHAR(30), ArchivBelegkopf.ROW_UPDATE_TIME, 104))
            ) t
            GROUP BY t.years, t.months
        ');

        $result = $query->execute()->fetchAll();
        $result = empty($result) ? [] : $result;

        $service = [];
        $this->loopMonth($result, $service);

        $query = new Builder($this->app->dbPool->get('sd'));
        $query->raw('
            select 
                datepart(yyyy, CONVERT(VARCHAR(30), s.BELEGDATUM, 104)) AS years, 
                datepart(m, CONVERT(VARCHAR(30), s.BELEGDATUM, 104)) AS months,
                sum(s.STATUMSATZ) as sales
            from (
                select _Dataframe.ROW_ID as ROW_ID, _Dataframe.BELEGDATUM as BELEGDATUM, _Dataframe.STATUMSATZ as STATUMSATZ
                from _Dataframe
                left join _ArtikelLagerorte ON _Dataframe.ARTIKELNUMMER = _ArtikelLagerorte.ARTIKELNUMMER
                where 
                    _ArtikelLagerorte.LAGERORTXML in (\'62\', \'63\', \'64\', \'65\', \'66\', \'67\', \'68\', \'69\', \'70\', \'71\', \'72\', \'73\', \'74\', \'75\', \'76\', \'77\', \'78\', \'79\')
                    and (_Dataframe.Belegbuchung = \'R\' or _Dataframe.Belegbuchung = \'G\') and _Dataframe.Belegstufe = 4 and _Dataframe.KUNDENNUMMER IS NOT NULL and _Dataframe.Status > 1
                    and CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 104) >= CONVERT(datetime, \'' . $start->format('Y.m.d') . '\', 102)
                    and CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 104) <= CONVERT(datetime, \'' . $current->format('Y.m.d') . '\', 102) 
                    group by _Dataframe.ROW_ID, _Dataframe.BELEGDATUM, _Dataframe.STATUMSATZ
            ) s 
            group by
                datepart(yyyy, CONVERT(VARCHAR(30), s.BELEGDATUM, 104)), 
                datepart(m, CONVERT(VARCHAR(30), s.BELEGDATUM, 104))
        ');

        $result = $query->execute()->fetchAll();
        $result = empty($result) ? [] : $result;

        $warehouse = [];
        $this->loopMonth($result, $warehouse);

        $query = new Builder($this->app->dbPool->get('sd'));
        $query->raw('
            select 
                datepart(yyyy, CONVERT(VARCHAR(30), s.BELEGDATUM, 104)) AS years, 
                datepart(m, CONVERT(VARCHAR(30), s.BELEGDATUM, 104)) AS months,
                sum(s.STATUMSATZ) as sales
            from (
                select _Dataframe.ROW_ID as ROW_ID, _Dataframe.BELEGDATUM as BELEGDATUM, _Dataframe.STATUMSATZ as STATUMSATZ
                from _Dataframe
                where 
                    _Dataframe.ARTIKELNUMMER in (\'523327\', \'523029\')
                    and (_Dataframe.Belegbuchung = \'R\' or _Dataframe.Belegbuchung = \'G\') and _Dataframe.Belegstufe = 4 and _Dataframe.KUNDENNUMMER IS NOT NULL and _Dataframe.Status > 1
                    and CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 104) >= CONVERT(datetime, \'' . $start->format('Y.m.d') . '\', 102)
                    and CONVERT(VARCHAR(30), _Dataframe.BELEGDATUM, 104) <= CONVERT(datetime, \'' . $current->format('Y.m.d') . '\', 102) 
                    group by _Dataframe.ROW_ID, _Dataframe.BELEGDATUM, _Dataframe.STATUMSATZ
            ) s 
            group by
                datepart(yyyy, CONVERT(VARCHAR(30), s.BELEGDATUM, 104)), 
                datepart(m, CONVERT(VARCHAR(30), s.BELEGDATUM, 104))
        ');

        $result = $query->execute()->fetchAll();
        $result = empty($result) ? [] : $result;

        $article = [];
        $this->loopMonth($result, $article);

        $view->setData('current', $currentYear);
        $view->setData('currentMonth', $currentMonth);
        $view->setData('service', $service);
        $view->setData('warehouse', $warehouse);
        $view->setData('article', $article);

        return $view;
    }

    private function loopMonth(array $resultset, array &$sales) 
    {
        foreach($resultset as $line) {
            $fiscalYear  = $line['months'] - $this->app->config['fiscal_year'] < 0 ? $line['years'] - 1 : $line['years'];
            $mod         = ($line['months'] - $this->app->config['fiscal_year']);
            $fiscalMonth = (($mod < 0 ? 12 + $mod : $mod) % 12) + 1;

            if (!isset($sales[$fiscalYear][$fiscalMonth])) {
                $sales[$fiscalYear][$fiscalMonth] = 0.0;
            }

            $sales[$fiscalYear][$fiscalMonth] += $line['sales'];
        }
    }

}