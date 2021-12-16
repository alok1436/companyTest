
<?php if(isset($chart)){ ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<canvas id="line-chart" width="800" height="450"></canvas>
  <script>

   new Chart(document.getElementById("line-chart"), {

      type: 'line',

      data: {

        labels: <?php echo json_encode($chart['labels']); ?>,

        datasets: [{ 

            data:  <?php echo json_encode($chart['data']); ?>,

            label: "{{ $chart['label'] }}",

            borderColor: "#3e95cd",

            fill: false

          }

        ]

      },

      options: {

        title: {

          display: true,

          text: "{{$chart['text']}}"

        }

      }

    });

    </script>
<?php }elseif (isset($chartCount)){ ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<div id="view-chart-count" width="800" height="300"></div>
  <script>
  Highcharts.chart('view-chart-count', {
      chart: {
          type: 'line'
      },
      title: {
          text: 'Order quantity sold chart'
      },
      subtitle: {
          text: ''
      },
      xAxis: {
          categories: <?php echo json_encode($chartCount['labels']); ?>
      },
      yAxis: {
          title: {
              text: 'Quantity'
          }
      },
      plotOptions: {
          line: {
              dataLabels: {
                  enabled: true
              },
              enableMouseTracking: false
          }
      },
      series: [{
          name: '',
          data: <?php echo json_encode($chartCount['data']); ?>
      }]
  });
  /*
   Highcharts.chart('view-chart-count', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Orders'
    },
    
    xAxis: {
        categories: <?php echo json_encode($chartCount['labels']); ?>
    },
    yAxis: {
        title: {
            text: 'Orders'
        }
    },
    plotOptions: {
        line: {
          dataLabels: {
            enabled: true
          },
          enableMouseTracking: false
        }
    },
    series: [ {
        name: 'Orders',
        data: <?php echo json_encode($chartCount['data']); ?>
    }]
});*/

    </script>
<?php } ?>