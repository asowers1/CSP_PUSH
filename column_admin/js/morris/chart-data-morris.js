// First Chart Example - Area Line Chart




window.a = Morris.Area({
  // ID of the element in which to draw the chart.
  element: 'morris-chart-area',
  // Chart data records -- each entry in this array corresponds to a point on
  // the chart.
  data: favoritesByDay,
  // The name of the data record attribute that contains x-visitss.
  xkey: 'date',
  // A list of names of data record attributes that contain y-visitss.
  ykeys: ['count'],
  // Labels for the ykeys -- will be displayed when you hover over the
  // chart.
  labels: ['Favorites per day'],
  // Disables line smoothing
  smooth: true,
  resize: true,
});


window.b = Morris.Area({
  // ID of the element in which to draw the chart.
  element: 'morris-chart-area-beacons',
  // Chart data records -- each entry in this array corresponds to a point on
  // the chart.
  data: beaconTriggers,
  // The name of the data record attribute that contains x-visitss.
  xkey: 'date',
  // A list of names of data record attributes that contain y-visitss.
  ykeys: ['count'],
  // Labels for the ykeys -- will be displayed when you hover over the
  // chart.
  labels: ['Beacon Triggers Per Day'],
  // Disables line smoothing
  smooth: true,
  resize: true,
});


Morris.Bar ({
  element: 'morris-chart-bar',
  data: todaysFavorites,
  xkey: 'label',
  ykeys: ['value'],
  labels: ['label'],
  barRatio: 0.4,
  xLabelAngle: 35,
  hideHover: 'auto',
  resize: true
});
