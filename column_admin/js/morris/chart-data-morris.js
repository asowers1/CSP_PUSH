// First Chart Example - Area Line Chart




Morris.Area({
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

Morris.Donut({
  element: 'morris-chart-donut',
  data: topFavorites,
  formatter: function (y) { return y + " favorites";}
});


Morris.Bar ({
  element: 'morris-chart-bar',
  data: todaysFavorites,
  xkey: 'label',
  ykeys: ['value'],
  labels: ['label'],
  barRatio: 0.4,
  xLabelAngle: 35,
  hideHover: 'auto'
});
