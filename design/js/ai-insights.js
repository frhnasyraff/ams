(function () {
  var root = document.querySelector('.ai-insights');
  if (!root) return;

  var queryUrl = root.getAttribute('data-query-url');
  var statusUrl = root.getAttribute('data-status-url');
  var messages = root.querySelector('[data-ai-messages]');
  var form = root.querySelector('[data-ai-form]');
  var input = root.querySelector('[data-ai-input]');
  var status = root.querySelector('[data-ai-status]');
  var chartCanvas = root.querySelector('[data-ai-chart]');
  var chartTitle = root.querySelector('[data-ai-chart-title]');
  var emptyChart = root.querySelector('[data-ai-empty-chart]');
  var tableOutput = root.querySelector('[data-ai-table]');
  var chartInstance = null;
  var chartPalette = ['#38bdf8', '#22c55e', '#f59e0b', '#a78bfa', '#fb7185', '#14b8a6', '#f97316', '#60a5fa', '#e879f9', '#84cc16'];

  function setStatus(state, text) {
    status.classList.remove('is-ready', 'is-error');
    if (state) status.classList.add(state);
    status.querySelector('strong').textContent = text;
  }

  function addMessage(text, type) {
    var item = document.createElement('div');
    item.className = 'ai-message ai-message--' + (type || 'assistant');
    var avatar = type === 'user' ? '' : '<div class="ai-message__avatar"><i class="fas fa-chart-line"></i></div>';
    item.innerHTML = avatar + '<div class="ai-message__bubble"></div>';
    item.querySelector('.ai-message__bubble').textContent = text;
    messages.appendChild(item);
    messages.scrollTop = messages.scrollHeight;
    return item;
  }

  function makeGradient(color) {
    var ctx = chartCanvas.getContext('2d');
    var area = chartCanvas.getBoundingClientRect();
    var gradient = ctx.createLinearGradient(0, 0, 0, Math.max(area.height, 260));
    gradient.addColorStop(0, hexToRgba(color, 0.48));
    gradient.addColorStop(0.65, hexToRgba(color, 0.14));
    gradient.addColorStop(1, hexToRgba(color, 0.02));
    return gradient;
  }

  function hexToRgba(hex, alpha) {
    var value = hex.replace('#', '');
    var r = parseInt(value.substring(0, 2), 16);
    var g = parseInt(value.substring(2, 4), 16);
    var b = parseInt(value.substring(4, 6), 16);
    return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + alpha + ')';
  }

  function styleDatasets(chart) {
    var type = chart.type || 'bar';
    return (chart.datasets || []).map(function (dataset, index) {
      var color = chartPalette[index % chartPalette.length];
      var styled = Object.assign({}, dataset);

      if (type === 'line') {
        styled.borderColor = color;
        styled.backgroundColor = makeGradient(color);
        styled.pointBackgroundColor = '#071426';
        styled.pointBorderColor = color;
        styled.pointHoverBackgroundColor = color;
        styled.pointHoverBorderColor = '#ffffff';
        styled.pointRadius = 4;
        styled.pointHoverRadius = 6;
        styled.pointBorderWidth = 2;
        styled.borderWidth = 3;
        styled.tension = 0.42;
        styled.fill = true;
        return styled;
      }

      if (type === 'doughnut' || type === 'pie') {
        styled.backgroundColor = (chart.labels || []).map(function (_, labelIndex) {
          return chartPalette[labelIndex % chartPalette.length];
        });
        styled.borderColor = '#071426';
        styled.borderWidth = 3;
        styled.hoverOffset = 8;
        return styled;
      }

      styled.backgroundColor = (dataset.data || []).map(function (_, itemIndex) {
        return hexToRgba(chartPalette[itemIndex % chartPalette.length], 0.78);
      });
      styled.borderColor = (dataset.data || []).map(function (_, itemIndex) {
        return chartPalette[itemIndex % chartPalette.length];
      });
      styled.borderWidth = 1;
      styled.borderRadius = 7;
      styled.hoverBackgroundColor = (dataset.data || []).map(function (_, itemIndex) {
        return chartPalette[itemIndex % chartPalette.length];
      });
      return styled;
    });
  }

  function renderChart(chart) {
    if (chartInstance) {
      chartInstance.destroy();
      chartInstance = null;
    }
    tableOutput.style.display = 'none';
    tableOutput.innerHTML = '';
    if (!chart || !window.Chart) {
      chartCanvas.style.display = 'none';
      emptyChart.style.display = 'flex';
      chartTitle.textContent = 'No graph for this answer';
      return;
    }
    if (chart.type === 'table') {
      chartCanvas.style.display = 'none';
      emptyChart.style.display = 'none';
      chartTitle.textContent = chart.title || 'AI Table';
      tableOutput.style.display = 'block';
      tableOutput.innerHTML = buildTable(chart.columns || [], chart.rows || []);
      return;
    }
    chartCanvas.style.display = 'block';
    emptyChart.style.display = 'none';
    chartTitle.textContent = chart.title || 'AI Graph';
    chartInstance = new Chart(chartCanvas, {
      type: chart.type || 'bar',
      data: {
        labels: chart.labels || [],
        datasets: styleDatasets(chart)
      },
      options: buildChartOptions(chart.type || 'bar')
    });
  }

  function buildChartOptions(type) {
    var isRoundChart = type === 'doughnut' || type === 'pie';
    return {
      responsive: true,
      maintainAspectRatio: false,
      animation: { duration: 650, easing: 'easeOutQuart' },
      interaction: { intersect: false, mode: 'index' },
      plugins: {
        legend: {
          position: isRoundChart ? 'bottom' : 'top',
          labels: {
            color: '#dbeafe',
            usePointStyle: true,
            pointStyle: 'circle',
            boxWidth: 8,
            boxHeight: 8,
            padding: 14,
            font: { family: 'Montserrat', size: 12, weight: '600' }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(7, 20, 38, 0.94)',
          borderColor: 'rgba(56, 189, 248, 0.38)',
          borderWidth: 1,
          titleColor: '#ffffff',
          bodyColor: '#dbeafe',
          padding: 11,
          cornerRadius: 9,
          displayColors: true
        }
      },
      scales: isRoundChart ? {} : {
        x: {
          ticks: {
            color: '#b7c8dd',
            maxRotation: 28,
            minRotation: 18,
            font: { family: 'Montserrat', size: 11, weight: '500' }
          },
          grid: { color: 'rgba(148, 163, 184, 0.08)', drawBorder: false }
        },
        y: {
          beginAtZero: true,
          ticks: {
            color: '#c7d8ea',
            precision: 0,
            font: { family: 'Montserrat', size: 11, weight: '500' }
          },
          grid: { color: 'rgba(148, 163, 184, 0.14)', drawBorder: false }
        }
      }
    };
  }

  function buildTable(columns, rows) {
    if (!columns.length || !rows.length) {
      return '<div class="p-3 text-muted">No table data.</div>';
    }
    var head = columns.map(function (column) {
      return '<th>' + escapeHtml(column.replace(/_/g, ' ')) + '</th>';
    }).join('');
    var body = rows.map(function (row) {
      return '<tr>' + columns.map(function (column) {
        return '<td>' + escapeHtml(String(row[column] == null ? '' : row[column])) + '</td>';
      }).join('') + '</tr>';
    }).join('');
    return '<table><thead><tr>' + head + '</tr></thead><tbody>' + body + '</tbody></table>';
  }

  function escapeHtml(text) {
    return String(text).replace(/[&<>'"]/g, function (char) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' })[char];
    });
  }

  async function checkStatus() {
    try {
      var response = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
      var data = await response.json();
      if (data.error) throw new Error(data.message || 'AI offline');
      setStatus(data.status === 'ready' ? 'is-ready' : '', data.status === 'ready' ? 'AI Active' : 'AI Local Mode');
    } catch (error) {
      setStatus('is-error', 'AI Offline');
    }
  }

  async function sendMessage(text) {
    addMessage(text, 'user');
    var loading = addMessage('Analyzing system data...', 'assistant');
    try {
      var response = await fetch(queryUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ message: text })
      });
      var data = await response.json();
      loading.remove();
      if (!response.ok || data.error) {
        addMessage(data.message || 'AI request failed.', 'assistant');
        renderChart(null);
        return;
      }
      addMessage(data.response || 'No response received.', 'assistant');
      renderChart(data.chart || null);
    } catch (error) {
      loading.remove();
      addMessage('Connection error. Make sure Assets AI API is running on port 8002.', 'assistant');
      renderChart(null);
    }
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    var text = input.value.trim();
    if (!text) return;
    input.value = '';
    sendMessage(text);
  });

  root.querySelectorAll('[data-ai-suggestion]').forEach(function (button) {
    button.addEventListener('click', function () {
      var text = button.getAttribute('data-ai-suggestion');
      input.value = text;
      sendMessage(text);
      input.value = '';
    });
  });

  checkStatus();
})();
