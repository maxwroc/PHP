google.load("visualization", "1", {packages:["gauge"]});
google.setOnLoadCallback(drawChart);
function drawChart() {

  var checkInterval = 2000;
  
  var options = {
    width: 400, height: 120,
    redFrom: 80, redTo: 100,
    yellowFrom:60, yellowTo: 80,
    minorTicks: 5
  };

  var dataProc = google.visualization.arrayToDataTable([
    ['Label', 'Value'],
    ['CPU', 0],
  ]);
  
  var dataMem = google.visualization.arrayToDataTable([
    ['Label', 'Value'],
    ['Memory', 0]
  ]);
  
  var dataDrive = google.visualization.arrayToDataTable([
    ['Label', 'Value'],
    ['Hdd', 0]
  ]);
  



  var chartProc = new google.visualization.Gauge(document.getElementById('perf_proc'));
  var chartDrive = new google.visualization.Gauge(document.getElementById('perf_drive'));
  var chartMem = new google.visualization.Gauge(document.getElementById('perf_mem'));

  chartProc.draw(dataProc, options);
  chartDrive.draw(dataDrive, options);
  chartMem.draw(dataMem, options);

  setInterval(function() {
    dataProc.setValue(0, 1, 40 + Math.round(24 * Math.random()));
    chartProc.draw(dataProc, options);
    
    dataDrive.setValue(0, 1, 10 + Math.round(24 * Math.random()));
    chartDrive.draw(dataDrive, options);
    
    dataMem.setValue(0, 1, 10 + Math.round(24 * Math.random()));
    chartMem.draw(dataMem, options);
  }, checkInterval);
}