// Chart Script //
(function($) {
    $(document).ready(function() {
      var ctx = document.getElementById("user-points-chart").getContext("2d");
      var scurfChartData = window.scurfChartData; // Access the localized data
  
      var chart = new Chart(ctx, {
        type: "line",
        data: {
          labels: scurfChartData.labels,
          datasets: [{
            label: "User Points",
            data: scurfChartData.data,
            backgroundColor: "rgba(75, 192, 192, 0.2)",
            borderColor: "rgba(75, 192, 192, 1)",
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    });
})(jQuery);
  