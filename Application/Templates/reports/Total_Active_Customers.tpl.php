<?php
$customerCount = $this->getData('customerCount');
$currentFiscalYear = $this->getData('currentFiscalYear');
?>

<div class="box" style="width: 100%; float: left">
    <canvas id="customers-count" height="100"></canvas>
</div>

<div class="clear"></div>

<script>
    let configCustomerCount = {
        type: 'bar',
        data: {
            labels: ["July", "August", "September", "October", "November", "December", "January","February", "March", "April", "May", "June"],
            datasets: [
            {
                label: 'FY-8',
                backgroundColor: "rgba(92,107,192, 1)",
                yAxisID: "y-axis-1",
                data: [<?php echo implode(',', $customerCount[$currentFiscalYear-8] ?? []); ?>]
            }, {
                label: 'FY-7',
                backgroundColor: "rgba(255,152,0, 1)",
                yAxisID: "y-axis-1",
                data: [<?php echo implode(',', $customerCount[$currentFiscalYear-7] ?? []); ?>]
            }, {
                label: 'FY-6',
                backgroundColor: "rgba(255,235,59, 1)",
                yAxisID: "y-axis-1",
                data: [<?php echo implode(',', $customerCount[$currentFiscalYear-6] ?? []); ?>]
            }, {
                label: 'FY-5',
                backgroundColor: "rgba(39,195,74, 1)",
                yAxisID: "y-axis-1",
                data: [<?php echo implode(',', $customerCount[$currentFiscalYear-5] ?? []); ?>]
            }, {
                label: 'FY-4',
                backgroundColor: "rgba(141,110,99, 1)",
                yAxisID: "y-axis-1",
                data: [<?php echo implode(',', $customerCount[$currentFiscalYear-4] ?? []); ?>]
            }, {
                label: 'FY-3',
                backgroundColor: "rgba(103,58,183, 1)",
                yAxisID: "y-axis-1",
                data: [<?php echo implode(',', $customerCount[$currentFiscalYear-3] ?? []); ?>]
            }, {
                label: 'FY-2',
                backgroundColor: "rgba(33,150,243, 1)",
                yAxisID: "y-axis-1",
                data: [<?php echo implode(',', $customerCount[$currentFiscalYear-2] ?? []); ?>]
            }, {
                label: 'FY-1',
                backgroundColor: "rgba(156,39,176, 1)",
                yAxisID: "y-axis-1",
                data: [<?php echo implode(',', $customerCount[$currentFiscalYear-1] ?? []); ?>]
            }, {
                label: 'FY',
                backgroundColor: "rgba(229,57,53,1)",
                yAxisID: "y-axis-1",
                data: [<?php echo implode(',', $customerCount[$currentFiscalYear] ?? []); ?>]
            }]
        },
        options: {
            responsive: true,
            hoverMode: 'label',
            hoverAnimationDuration: 400,
            stacked: false,
            title:{
                display:true,
                text:"Customers per Month"
            },
            tooltips: {
                mode: 'label',
                callbacks: {
                    label: function(tooltipItem, data) {
                        let datasetLabel = data.datasets[tooltipItem.datasetIndex].label || 'Other';

                        return ' ' + datasetLabel + ': ' + Math.round(tooltipItem.yLabel).toString();
                    }
                }
            },
            scales: {
                xAxes: [{
                    ticks: {
                        autoSkip: false
                    }
                }],
                yAxes: [{
                    type: "linear",
                    display: true,
                    position: "left",
                    id: "y-axis-1",
                    ticks: {
                        userCallback: function(value, index, values) { return value.toString(); },
                        beginAtZero: true,
                        min: 0
                    }
                }],
            }
        }
    };

    window.onload = function() {
        let ctxSalesCustomersCount = document.getElementById("customers-count");
        window.salesCustomersCount = new Chart(ctxSalesCustomersCount, configCustomerCount);
    };
</script>