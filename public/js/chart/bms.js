<<<<<<< HEAD
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

var coloring_template = {
  lineTension: 0.3,
  pointRadius: 3,
  pointHoverRadius: 3,
  pointHitRadius: 10,
  pointBorderWidth: 2,
}

var coloring_sd = {
  ...coloring_template,
  label: "Total didapatkan",
  borderColor: "#40b34a",
  pointBackgroundColor: "#40b34a",
  pointBorderColor: "#40b34a",
  pointHoverBackgroundColor: "#40b34a",
  pointHoverBorderColor: "#40b34a",
}
var coloring_sma = {
  ...coloring_template,
  label: "SMA",
  borderColor: "#bfbfbf",
  pointBackgroundColor: "#808080",
  pointBorderColor: "#808080",
  pointHoverBackgroundColor: "#808080",
  pointHoverBorderColor: "#808080",
}
var coloring_smp = {
  ...coloring_template,
  label: "Total Plan",
  borderColor: "#96bfff",
  pointBackgroundColor: "#4287f5",
  pointBorderColor: "#4287f5",
  pointHoverBackgroundColor: "#4287f5",
  pointHoverBorderColor: "#4287f5",
}

var option_chart = {
  maintainAspectRatio: false,
  layout: {
    padding: {
      left: 10,
      right: 25,
      top: 25,
      bottom: 0
    }
  },
  scales: {
    xAxes: [{
      time: {
        unit: 'date'
      },
      gridLines: {
        display: false,
        drawBorder: false
      },
      ticks: {
        maxTicksLimit: 7
      }
    }],
    yAxes: [{
      ticks: {
        maxTicksLimit: 5,
        padding: 10,
        // Include a dollar sign in the ticks
        callback: function(value, index, values) {
          return 'Rp ' + number_format(value);
        }
      },
      gridLines: {
        color: "rgb(234, 236, 244)",
        zeroLineColor: "rgb(234, 236, 244)",
        drawBorder: false,
        borderDash: [2],
        zeroLineBorderDash: [2]
      }
    }],
  },
  legend: {
    display: false
  },
  tooltips: {
    backgroundColor: "rgb(255,255,255)",
    bodyFontColor: "#858796",
    titleMarginBottom: 10,
    titleFontColor: '#6e707e',
    titleFontSize: 14,
    borderColor: '#dddfeb',
    borderWidth: 1,
    xPadding: 15,
    yPadding: 15,
    displayColors: false,
    intersect: false,
    mode: 'index',
    caretPadding: 10,
    callbacks: {
      label: function(tooltipItem, chart) {
        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
        return datasetLabel + ': Rp' + number_format(tooltipItem.yLabel);
      }
    }
  }
};

var option_chart1 = {
  maintainAspectRatio: false,
  layout: {
    padding: {
      left: 10,
      right: 25,
      top: 25,
      bottom: 0
    }
  },
  scales: {
    xAxes: [{
      time: {
        unit: 'date'
      },
      gridLines: {
        display: false,
        drawBorder: false
      },
      ticks: {
        maxTicksLimit: 7
      }
    }],
    yAxes: [{
      ticks: {
        maxTicksLimit: 5,
        padding: 10,
        // Include a dollar sign in the ticks
        callback: function(value, index, values) {
          return ' ' + number_format(value);
        }
      },
      gridLines: {
        color: "rgb(234, 236, 244)",
        zeroLineColor: "rgb(234, 236, 244)",
        drawBorder: false,
        borderDash: [2],
        zeroLineBorderDash: [2]
      }
    }],
  },
  legend: {
    display: false
  },
  tooltips: {
    backgroundColor: "rgb(255,255,255)",
    bodyFontColor: "#858796",
    titleMarginBottom: 10,
    titleFontColor: '#6e707e',
    titleFontSize: 14,
    borderColor: '#dddfeb',
    borderWidth: 1,
    xPadding: 15,
    yPadding: 15,
    displayColors: false,
    intersect: false,
    mode: 'index',
    caretPadding: 10,
    callbacks: {
      label: function(tooltipItem, chart) {
        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
        return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
      }
    }
  }
};

function reloadChart (){

  var unit_id = $('#unit_id').val();
  var year_start = $('#year_start').val();
  var year_end = $('#year_end').val();

  $('#myAreaChart').remove();
  $('#myAreaChart1').remove();

  $.ajax({
    url         : window.location.href,
    type        : 'POST',
    dataType    : 'JSON',
    headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data        : {
        unit_id : unit_id,
        year_start : year_start,
        year_end : year_end,
    },
    beforeSend  : function() {
    },
    complete    : function() {
    }, 
    success: function async(response){
      
      console.log(response);

      $('#show_total').text('Rp '+response.show_total);
      $('#show_get').text('Rp '+response.show_get);
      $('#show_selisih').text('Rp '+response.show_selisih);
      $('#show_total_student').text(response.show_total_student);
      $('#show_student_remain').text(response.show_student_remain);
      
      $('.chart-area').append('<canvas id="myAreaChart"></canvas>');
      $('.chart-area1').append('<canvas id="myAreaChart1"></canvas>');
      
      // Area Chart Example
      var ctx = $("#myAreaChart");
      var ctx1 = $("#myAreaChart1");

      var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: response.label,
          datasets: [
            {
              ...coloring_smp,
              data: response.total,
            },
            {
            ...coloring_sd,
            data: response.get,
            },
            {
            ...coloring_sma,
            label: "Selisih",
            data: response.selisih,
            },
          ],
        },
        options: option_chart,
      });
      var myLineChart1 = new Chart(ctx1, {
        type: 'line',
        data: {
          labels: response.label,
          datasets: [
            {
              ...coloring_smp,
              label: "Total Siswa",
              data: response.total_student,
            },
            {
            ...coloring_sd,
              label: "Total Siswa kurang",
            data: response.student_remain,
            },
          ],
        },
        options: option_chart1,
      });
    },
    error: function(xhr, textStatus, errorThrown){
        alert(xhr.responseText);
    },
  });

}

=======
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

var coloring_template = {
  lineTension: 0.3,
  pointRadius: 3,
  pointHoverRadius: 3,
  pointHitRadius: 10,
  pointBorderWidth: 2,
}

var coloring_sd = {
  ...coloring_template,
  label: "Total didapatkan",
  borderColor: "#40b34a",
  pointBackgroundColor: "#40b34a",
  pointBorderColor: "#40b34a",
  pointHoverBackgroundColor: "#40b34a",
  pointHoverBorderColor: "#40b34a",
}
var coloring_sma = {
  ...coloring_template,
  label: "SMA",
  borderColor: "#bfbfbf",
  pointBackgroundColor: "#808080",
  pointBorderColor: "#808080",
  pointHoverBackgroundColor: "#808080",
  pointHoverBorderColor: "#808080",
}
var coloring_smp = {
  ...coloring_template,
  label: "Total Plan",
  borderColor: "#96bfff",
  pointBackgroundColor: "#4287f5",
  pointBorderColor: "#4287f5",
  pointHoverBackgroundColor: "#4287f5",
  pointHoverBorderColor: "#4287f5",
}

var option_chart = {
  maintainAspectRatio: false,
  layout: {
    padding: {
      left: 10,
      right: 25,
      top: 25,
      bottom: 0
    }
  },
  scales: {
    xAxes: [{
      time: {
        unit: 'date'
      },
      gridLines: {
        display: false,
        drawBorder: false
      },
      ticks: {
        maxTicksLimit: 7
      }
    }],
    yAxes: [{
      ticks: {
        maxTicksLimit: 5,
        padding: 10,
        // Include a dollar sign in the ticks
        callback: function(value, index, values) {
          return 'Rp ' + number_format(value);
        }
      },
      gridLines: {
        color: "rgb(234, 236, 244)",
        zeroLineColor: "rgb(234, 236, 244)",
        drawBorder: false,
        borderDash: [2],
        zeroLineBorderDash: [2]
      }
    }],
  },
  legend: {
    display: false
  },
  tooltips: {
    backgroundColor: "rgb(255,255,255)",
    bodyFontColor: "#858796",
    titleMarginBottom: 10,
    titleFontColor: '#6e707e',
    titleFontSize: 14,
    borderColor: '#dddfeb',
    borderWidth: 1,
    xPadding: 15,
    yPadding: 15,
    displayColors: false,
    intersect: false,
    mode: 'index',
    caretPadding: 10,
    callbacks: {
      label: function(tooltipItem, chart) {
        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
        return datasetLabel + ': Rp' + number_format(tooltipItem.yLabel);
      }
    }
  }
};

var option_chart1 = {
  maintainAspectRatio: false,
  layout: {
    padding: {
      left: 10,
      right: 25,
      top: 25,
      bottom: 0
    }
  },
  scales: {
    xAxes: [{
      time: {
        unit: 'date'
      },
      gridLines: {
        display: false,
        drawBorder: false
      },
      ticks: {
        maxTicksLimit: 7
      }
    }],
    yAxes: [{
      ticks: {
        maxTicksLimit: 5,
        padding: 10,
        // Include a dollar sign in the ticks
        callback: function(value, index, values) {
          return ' ' + number_format(value);
        }
      },
      gridLines: {
        color: "rgb(234, 236, 244)",
        zeroLineColor: "rgb(234, 236, 244)",
        drawBorder: false,
        borderDash: [2],
        zeroLineBorderDash: [2]
      }
    }],
  },
  legend: {
    display: false
  },
  tooltips: {
    backgroundColor: "rgb(255,255,255)",
    bodyFontColor: "#858796",
    titleMarginBottom: 10,
    titleFontColor: '#6e707e',
    titleFontSize: 14,
    borderColor: '#dddfeb',
    borderWidth: 1,
    xPadding: 15,
    yPadding: 15,
    displayColors: false,
    intersect: false,
    mode: 'index',
    caretPadding: 10,
    callbacks: {
      label: function(tooltipItem, chart) {
        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
        return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
      }
    }
  }
};

function reloadChart (){

  var unit_id = $('#unit_id').val();
  var year_start = $('#year_start').val();
  var year_end = $('#year_end').val();

  $('#myAreaChart').remove();
  $('#myAreaChart1').remove();

  $.ajax({
    url         : window.location.href,
    type        : 'POST',
    dataType    : 'JSON',
    headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data        : {
        unit_id : unit_id,
        year_start : year_start,
        year_end : year_end,
    },
    beforeSend  : function() {
    },
    complete    : function() {
    }, 
    success: function async(response){
      
      console.log(response);

      $('#show_total').text('Rp '+response.show_total);
      $('#show_get').text('Rp '+response.show_get);
      $('#show_selisih').text('Rp '+response.show_selisih);
      $('#show_total_student').text(response.show_total_student);
      $('#show_student_remain').text(response.show_student_remain);
      
      $('.chart-area').append('<canvas id="myAreaChart"></canvas>');
      $('.chart-area1').append('<canvas id="myAreaChart1"></canvas>');
      
      // Area Chart Example
      var ctx = $("#myAreaChart");
      var ctx1 = $("#myAreaChart1");

      var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: response.label,
          datasets: [
            {
              ...coloring_smp,
              data: response.total,
            },
            {
            ...coloring_sd,
            data: response.get,
            },
            {
            ...coloring_sma,
            label: "Selisih",
            data: response.selisih,
            },
          ],
        },
        options: option_chart,
      });
      var myLineChart1 = new Chart(ctx1, {
        type: 'line',
        data: {
          labels: response.label,
          datasets: [
            {
              ...coloring_smp,
              label: "Total Siswa",
              data: response.total_student,
            },
            {
            ...coloring_sd,
              label: "Total Siswa kurang",
            data: response.student_remain,
            },
          ],
        },
        options: option_chart1,
      });
    },
    error: function(xhr, textStatus, errorThrown){
        alert(xhr.responseText);
    },
  });

}

>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
