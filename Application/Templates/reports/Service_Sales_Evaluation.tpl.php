<?php 
$service = $this->getData('service');
$warehouse = $this->getData('warehouse');
$article = $this->getData('article');
$current = $this->getData('current');

$serviceAcc = [];

foreach($service as $year => $arr) {
	$temp = 0;
	ksort($service[$year]);
	ksort($arr);

	foreach($arr as $month => $value) {
		$temp += $value;
		$serviceAcc[$year][$month] = $temp;
	}
}

$warehouseAcc = [];

foreach($warehouse as $year => $arr) {
    $temp = 0;
    ksort($warehouse[$year]);
    ksort($arr);

    foreach($arr as $month => $value) {
        $temp += $value;
        $warehouseAcc[$year][$month] = $temp;
    }
}

$articleAcc = [];

foreach($article as $year => $arr) {
    $temp = 0;
    ksort($article[$year]);
    ksort($arr);

    foreach($arr as $month => $value) {
        $temp += $value;
        $articleAcc[$year][$month] = $temp;
    }
}

for($i = 1; $i <= 12; $i++) {
    $warehouse[$current][$i] = ($warehouse[$current][$i] ?? 0) + ($article[$current][$i] ?? 0) * 0.3;
}

?>

<h1>Service Department</h1>

<h2>Total Service Sales</h2>

<p>All service articles (except machines and software) incl. service minutes, incl. 1 year service contracts for machines and 30% of service contracts for software</p>

<table style="width: 100%; float: left;">
    <caption>Service Articles, Contracts & Hours</caption>
    <thead>
    <tr>
        <th>Type
        <th>Jul
        <th>Aug
        <th>Sep
        <th>Oct
        <th>Nov
        <th>Dec
        <th>Jan
        <th>Feb
        <th>Mar
        <th>Apr
        <th>May
        <th>Jun
    <tbody>
    <tr>
        <td>Iso.
        <?php for($i = 1; $i <= 12; $i++) : ?>
            <td><?= number_format($warehouse[$current][$i] ?? 0, 0, '.', ','); ?>
        <?php endfor; ?>
    <tr>
        <td>Acc.
        <?php $value = 0; for($i = 1; $i <= 12; $i++) : $value += $warehouse[$current][$i] ?? 0; ?>
            <td><?= number_format($value, 0, '.', ','); ?>
        <?php endfor; ?>
</table>

<div class="box" style="width: 50%; float: left;">
    <canvas id="warehouse"></canvas>
</div>

<div class="box" style="width: 50%; float: left;">
    <canvas id="warehouseAcc"></canvas>
</div>

<div class="clear"></div>

<h2>Service Invoices</h2>

<p>All service invoices on a monthly basis (based on invoice date not on service date). These sales are a subset of the total service sales.</p>

<table style="width: 100%; float: left;">
    <caption>Service</caption>
    <thead>
    <tr>
        <th>Type
        <th>Jul
        <th>Aug
        <th>Sep
        <th>Oct
        <th>Nov
        <th>Dec
        <th>Jan
        <th>Feb
        <th>Mar
        <th>Apr
        <th>May
        <th>Jun
    <tbody>
    <tr>
    	<td>Iso.
    	<?php for($i = 1; $i <= 12; $i++) : ?>
    		<td><?= number_format($service[$current][$i] ?? 0, 0, '.', ','); ?>
    	<?php endfor; ?>
    <tr>
    	<td>Acc.
    	<?php $value = 0; for($i = 1; $i <= 12; $i++) : $value += $service[$current][$i] ?? 0; ?>
    		<td><?= number_format($value, 0, '.', ','); ?>
    	<?php endfor; ?>
</table>

<div class="box" style="width: 50%; float: left;">
    <canvas id="service"></canvas>
</div>

<div class="box" style="width: 50%; float: left;">
    <canvas id="serviceAcc"></canvas>
</div>

<div class="clear"></div>

<script>
    let configWarehouse = {
        type: 'line',
        data: {
            labels: ["July", "August", "September", "October", "November", "December", "January","February", "March", "April", "May", "June"],
            datasets: [{
                label: "Current Year",
                data: [<?php echo implode(',', $warehouse[$current]); ?>],
                fill: false,
                borderColor: 'rgba(255,99,132,1)',
                backgroundColor: 'rgba(255,99,132,1)',
                pointBorderColor: 'rgba(255,99,132,1)',
                pointBackgroundColor: 'rgba(255,99,132,1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }, {
                label: "Last Year",
                data: [<?php echo implode(',', $warehouse[$current-1]); ?>],
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }, {
                label: "Two Years Ago",
                data: [<?php echo implode(',', $warehouse[$current-2]); ?>],
                fill: false,
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderColor: 'rgba(255, 206, 86, 1)',
                pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }]
        },
        options: {
            responsive: true,
            title:{
                display:true,
                text:'Service Articles, Contracts & Hours Isolated Sales'
            },
            tooltips: {
                mode: 'label',
                callbacks: {
                    label: function(tooltipItem, data) {
                            let datasetLabel = data.datasets[tooltipItem.datasetIndex].label || 'Other';

                            return ' ' + datasetLabel + ': ' + '€ ' + Math.round(tooltipItem.yLabel).toString().split(/(?=(?:...)*$)/).join('.').replace('-.', '-');
                          }
                }
            },
            hover: {
                mode: 'dataset'
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        show: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        show: true,
                        labelString: 'Sales'
                    },
                    ticks: {
                        userCallback: function(value, index, values) { return '€ ' + value.toString().split(/(?=(?:...)*$)/).join('.').replace('-.', '-'); }
                    }
                }]
            }
        }
    };

    let configWarehouseAcc = {
        type: 'line',
        data: {
            labels: ["July", "August", "September", "October", "November", "December", "January","February", "March", "April", "May", "June"],
            datasets: [{
                label: "Current Year",
                data: [<?php echo implode(',', $warehouseAcc[$current]); ?>],
                fill: false,
                borderColor: 'rgba(255,99,132,1)',
                backgroundColor: 'rgba(255,99,132,1)',
                pointBorderColor: 'rgba(255,99,132,1)',
                pointBackgroundColor: 'rgba(255,99,132,1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }, {
                label: "Last Year",
                data: [<?php echo implode(',', $warehouseAcc[$current-1]); ?>],
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }, {
                label: "Two Years Ago",
                data: [<?php echo implode(',', $warehouseAcc[$current-2]); ?>],
                fill: false,
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderColor: 'rgba(255, 206, 86, 1)',
                pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }]
        },
        options: {
            responsive: true,
            title:{
                display:true,
                text:'Service Articles, Contracts & Hours Accumulated Sales'
            },
            tooltips: {
                mode: 'label',
                callbacks: {
                    label: function(tooltipItem, data) {
                            let datasetLabel = data.datasets[tooltipItem.datasetIndex].label || 'Other';

                            return ' ' + datasetLabel + ': ' + '€ ' + Math.round(tooltipItem.yLabel).toString().split(/(?=(?:...)*$)/).join('.').replace('-.', '-');
                          }
                }
            },
            hover: {
                mode: 'dataset'
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        show: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        show: true,
                        labelString: 'Sales'
                    },
                    ticks: {
                        userCallback: function(value, index, values) { return '€ ' + value.toString().split(/(?=(?:...)*$)/).join('.').replace('-.', '-'); }
                    }
                }]
            }
        }
    };

    let configService = {
        type: 'line',
        data: {
            labels: ["July", "August", "September", "October", "November", "December", "January","February", "March", "April", "May", "June"],
            datasets: [{
                label: "Current Year",
                data: [<?php echo implode(',', $service[$current]); ?>],
                fill: false,
                borderColor: 'rgba(255,99,132,1)',
                backgroundColor: 'rgba(255,99,132,1)',
                pointBorderColor: 'rgba(255,99,132,1)',
                pointBackgroundColor: 'rgba(255,99,132,1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }, {
                label: "Last Year",
                data: [<?php echo implode(',', $service[$current-1]); ?>],
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }, {
                label: "Two Years Ago",
                data: [<?php echo implode(',', $service[$current-2]); ?>],
                fill: false,
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderColor: 'rgba(255, 206, 86, 1)',
                pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }]
        },
        options: {
            responsive: true,
            title:{
                display:true,
                text:'Service Isolated Sales'
            },
            tooltips: {
                mode: 'label',
                callbacks: {
                    label: function(tooltipItem, data) {
                            let datasetLabel = data.datasets[tooltipItem.datasetIndex].label || 'Other';

                            return ' ' + datasetLabel + ': ' + '€ ' + Math.round(tooltipItem.yLabel).toString().split(/(?=(?:...)*$)/).join('.').replace('-.', '-');
                          }
                }
            },
            hover: {
                mode: 'dataset'
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        show: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        show: true,
                        labelString: 'Sales'
                    },
                    ticks: {
                        userCallback: function(value, index, values) { return '€ ' + value.toString().split(/(?=(?:...)*$)/).join('.').replace('-.', '-'); }
                    }
                }]
            }
        }
    };

    let configServiceAcc = {
        type: 'line',
        data: {
            labels: ["July", "August", "September", "October", "November", "December", "January","February", "March", "April", "May", "June"],
            datasets: [{
                label: "Current Year",
                data: [<?php echo implode(',', $serviceAcc[$current]); ?>],
                fill: false,
                borderColor: 'rgba(255,99,132,1)',
                backgroundColor: 'rgba(255,99,132,1)',
                pointBorderColor: 'rgba(255,99,132,1)',
                pointBackgroundColor: 'rgba(255,99,132,1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }, {
                label: "Last Year",
                data: [<?php echo implode(',', $serviceAcc[$current-1]); ?>],
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }, {
                label: "Two Years Ago",
                data: [<?php echo implode(',', $serviceAcc[$current-2]); ?>],
                fill: false,
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderColor: 'rgba(255, 206, 86, 1)',
                pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderWidth: 0,
                cubicInterpolationMode: 'monotone'
            }]
        },
        options: {
            responsive: true,
            title:{
                display:true,
                text:'Service Accumulated Sales'
            },
            tooltips: {
                mode: 'label',
                callbacks: {
                    label: function(tooltipItem, data) {
                            let datasetLabel = data.datasets[tooltipItem.datasetIndex].label || 'Other';

                            return ' ' + datasetLabel + ': ' + '€ ' + Math.round(tooltipItem.yLabel).toString().split(/(?=(?:...)*$)/).join('.').replace('-.', '-');
                          }
                }
            },
            hover: {
                mode: 'dataset'
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        show: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        show: true,
                        labelString: 'Sales'
                    },
                    ticks: {
                        userCallback: function(value, index, values) { return '€ ' + value.toString().split(/(?=(?:...)*$)/).join('.').replace('-.', '-'); }
                    }
                }]
            }
        }
    };
</script>

<script>
    window.onload = function() {
        let ctxWarehouse = document.getElementById("warehouse").getContext("2d");
        window.WarehouseLine = new Chart(ctxWarehouse, configWarehouse);

        let ctxWarehouseAcc = document.getElementById("warehouseAcc").getContext("2d");
        window.WarehouseAccLine = new Chart(ctxWarehouseAcc, configWarehouseAcc);

        let ctxService = document.getElementById("service").getContext("2d");
        window.ServiceLine = new Chart(ctxService, configService);

        let ctxServiceAcc = document.getElementById("serviceAcc").getContext("2d");
        window.ServiceAccLine = new Chart(ctxServiceAcc, configServiceAcc);
    };
</script>