
/* global Chart, filterLatest, calculateRecStat,
         top3CategoriesCount, maxCategoryData, minCategoryData */

// store chart instances by canvas id so we can maintain more than one
// chart on the page simultaneously. destroying only the affected chart
// prevents later renders from wiping out earlier ones.
const charts = Object.create(null);
const riasecDef = { 'R': 'Realistic', 'I': 'Investigative', 'A': 'Artistic', 'S': 'Social', 'E': 'Enterprising', 'C': 'Conventional' };

// make sure the helper functions we rely on exist somewhere in the bundle.
// they may be defined in another script, but a warning is helpful during
// development if they are accidentally removed or renamed.
function verifyHelpers() {
  const missing = [];
  ['filterLatest', 'calculateRecStat', 'top3CategoriesCount',
    'maxCategoryData', 'minCategoryData'].forEach(fn => {
      if (typeof window[fn] !== 'function' && typeof eval(fn) !== 'function') {
        missing.push(fn);
      }
    });
  if (missing.length) {
    console.warn('Missing helper functions:', missing.join(', '));
  }
}

async function loadData() {
  verifyHelpers();
  try {
    const res = await fetch('actions/records-json.php');
    if (!res.ok) throw new Error(`Network response was not ok: ${res.status}`);
    const rawData = await res.json();
    
    // Map database keys to the expected format used by statmod.js and charts
    const data = rawData.map(r => ({
      ...r,
      'id': r.rec_id,
      'name': r.name,
      'nic-no': r.nic,
      'attempt-no': r.log_no, // using log_no as attempt number
      'created_at': new Date(r.created_at),
      r: parseInt(r.score_r, 10) || 0,
      i: parseInt(r.score_i, 10) || 0,
      a: parseInt(r.score_a, 10) || 0,
      s: parseInt(r.score_s, 10) || 0,
      e: parseInt(r.score_e, 10) || 0,
      c: parseInt(r.score_c, 10) || 0
    }));

    const filterType = getDashFilterType();
    const filterDates = getDashFilterDates();

    let fildata;
    const dataArray = Object.values(data);

    if(filterType == 'custom'){
        if(filterDates.start_date || filterDates.end_date){
            fildata = await filterRecordsByDateRange(dataArray, filterDates.start_date, filterDates.end_date);
        } else {
            fildata = dataArray;
        }
    }
    else{
        fildata = await filterRecords(dataArray, filterType);
    }

    //Update Total-Records and Total-People Display from Filtered Data
    const countTR = document.getElementById('total-records');
    const countTP = document.getElementById('total-people');
    if(countTR) countTR.innerHTML = fildata.length;
    

    // helpers defined in statmod.js are async functions, so make sure to await
    const uldata = await filterLatest(fildata);
    if(countTP) countTP.innerHTML = Object.keys(uldata).length;
    await calculateRecListStat(uldata);

    const top3 = await top3CategoriesCount(uldata);
    const mostPrefEl = document.getElementById('most-preferred-personality');
    if (mostPrefEl) {
      if(fildata.length == 0) {
        mostPrefEl.innerHTML = `---`;
      } else {
        const topCategory = Object.keys(top3).at(0);
        mostPrefEl.innerHTML = `${topCategory} <span style="font-size: small; color: #6c757d">${top3[topCategory]}</span>`;
      }
    }
    createTop3StatChart(Object.keys(top3), Object.values(top3));

    const riasec = Object.keys(riasecDef);
    const maxCounts = await maxCategoryData(uldata);
    const minCounts = await minCategoryData(uldata);
    const maxEl = document.getElementById('most-preferred-category');
    if (maxEl) {
      if(fildata.length == 0) {
        maxEl.innerHTML = `---`;
      } else {
        const topCategory = Object.keys(maxCounts).reduce((a, b) => maxCounts[a] > maxCounts[b] ? a : b, null);
        maxEl.innerHTML = `${riasecDef[topCategory]} <span style="font-size: small; color: #6c757d">${(maxCounts[topCategory]*100/Object.keys(maxCounts).length).toFixed(0)}%</span>`;
      }
    }
    const minEl = document.getElementById('least-preferred-category');
    if (minEl) {
      if(fildata.length == 0) {
        minEl.innerHTML = `---`;
      } else {
        const bottomCategory = Object.keys(minCounts).reduce((a, b) => minCounts[a] > minCounts[b] ? a : b, null);
        minEl.innerHTML = `${riasecDef[bottomCategory]} <span style="font-size: small; color: #6c757d">${(minCounts[bottomCategory]*100/Object.keys(minCounts).length).toFixed(0)}%</span>`;
      }
    }
    createMaxMinStatChart(riasec, riasec.map(c => maxCounts[c] || 0), riasec.map(c => minCounts[c] || 0));

    // Add resize listener to make charts responsive on window resize
    window.addEventListener('resize', chartsResponsive);
  } catch (e) {
    console.error('Error fetching records:', e);
    displayError('Failed to load records. Check console for details.');
  }
}

// kick off processing once the document (and all scripts) are loaded.
// we always listen for DOMContentLoaded instead of calling immediately,
// since helper modules (statmod.js) may not yet be available.
document.addEventListener('DOMContentLoaded', loadData);

async function chartsResponsive() {
  // if we have charts rendered, trigger a resize to make them responsive
  Object.values(charts).forEach(chart => {
    if (chart) {
      if (typeof chart.resize === 'function') {
        chart.resize();
      }
      chart.update();
    }
  });
}

function createTop3StatChart(label, dataset) {
  // if we already rendered a chart on this canvas, remove it before
  // creating a fresh one. this preserves any other charts on the page.
  if (charts['top3statChart']) {
    charts['top3statChart'].destroy();
    delete charts['top3statChart'];
  }

  if (typeof Chart === 'undefined') {
    console.error('Chart.js is not loaded.');
    return;
  }
  const canvas = document.getElementById('top3statChart');
  if (!canvas) {
    console.error(`Canvas element with id "top3statChart" not found`);
    return;
  }
  const ctx = canvas.getContext('2d');
  const config = {
    type: 'bar',
    data: {
      labels: label,
      datasets: [{
        data: dataset,
        backgroundColor: '#125051d6',
        categoryPercentage: 1.0
      }]
    },
    options: {
      responsive: true,
      indexAxis: 'y',
      scales: {
        x: { beginAtZero: true, grid: { display: false, length: 10 }, ticks: { stepSize: 1 } },
        y: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
      },
      plugins: { legend: { display: false } }
    }
  };
  charts['top3statChart'] = new Chart(ctx, config);
}

function createMaxMinStatChart(label, maxDataset, minDataset) {
  if (charts['maxMinStatChart']) {
    charts['maxMinStatChart'].destroy();
    delete charts['maxMinStatChart'];
  }

  if (typeof Chart === 'undefined') {
    console.error('Chart.js is not loaded.');
    return;
  }

  const canvas = document.getElementById('maxMinStatChart');
  if (!canvas) {
    console.error(`Canvas element with id "maxMinStatChart" not found`);
    return;
  }

  const ctx = canvas.getContext('2d');
  const config = {
    type: 'bar',
    data: {
      labels: label,
      datasets: [
        {
          label: 'Max',
          data: maxDataset,
          backgroundColor: '#125051d6',
          categoryPercentage: 0.8
        },
        {
          label: 'Min',
          data: minDataset,
          backgroundColor: '#f44336d6',
          categoryPercentage: 0.8
        }
      ]
    },
    options: {
      responsive: true,
      indexAxis: 'y',
      scales: {
        x: { beginAtZero: true, grid: { display: false, length: 10 }, ticks: { stepSize: 1 } },
        y: { ticks: { font: { weight: 'bold' } } }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: (context) => {
              const category = context[0].label;
              return `${riasecDef[category]}`;
            }
          }
        }
      }
    }
  };
  charts['maxMinStatChart'] = new Chart(ctx, config);
}

const recordBarChartConfig = {
  data: {
    labels: ['R', 'I', 'A', 'S', 'E', 'C'],
    datasets: [{
        type: 'bar',
        data: [0, 0, 0, 0, 0, 0],
        backgroundColor: '#125051d6',
        categoryPercentage: 0.75,
        order: 2
      },
      {
        type: 'line',
        data: [0, 0, 0, 0, 0, 0],
        borderWidth: 2,
        borderColor: '#d92536',
        pointStyle: 'crossRot',
        pointRadius: 8,
        order: 1
      }
    ]
  },
  options: {
    responsive: true,
    scales: {
      x: { beginAtZero: true, grid: { display: false, length: 10 }, ticks: { stepSize: 1, font: { weight: 'bold' } } },
      y: { grid: { display: true }, ticks: { stepSize: 2 } }
    },
    plugins: { legend: { display: false } }
  }
};

const recordRadarChartConfig = {
  type: 'radar',
  data: {
    labels: ['R', 'I', 'A', 'S', 'E', 'C'],
    datasets: [{
      data: [0, 0, 0, 0, 0, 0],
      backgroundColor: 'rgba(18, 80, 81, 0.4)',
      borderColor: '#125051',
      borderWidth: 2,
      pointBackgroundColor: '#125051',
      pointBorderColor: '#fff',
      pointHoverBackgroundColor: '#fff',
      pointHoverBorderColor: '#125051',
    }]
  },
  options: {
    responsive: true,
    scales: {
      r: {
        beginAtZero: true,
        grid: { display: true, circular: true },
        ticks: { stepSize: 2 }
      }
    },
    plugins: {
      legend: { display: false }
    }
  }
};



function createRecordBarChart(record) {
  if (charts['recordBarChart']) {
    charts['recordBarChart'].destroy();
    delete charts['recordBarChart'];
  }
  if (typeof Chart === 'undefined') {
    console.error('Chart.js is not loaded.');
    return;
  }

  const canvas = document.getElementById('record-bar-chart');
  if (!canvas) {
    console.error(`Canvas element with id "record-bar-chart" not found`);
    return;
  }
  const ctx = canvas.getContext('2d');
  const data = [record.r ?? 0, record.i ?? 0, record.a ?? 0, record.s ?? 0, record.e ?? 0, record.c ?? 0];
  const config = JSON.parse(JSON.stringify(recordBarChartConfig)); // deep copy to avoid mutating original
  config.data.datasets[0].data = data;
  config.data.datasets[1].data = data;

  charts['recordBarChart'] = new Chart(ctx, config);
}

function createRecordRadarChart(record) {
  if (charts['recordRadarChart']) {
    charts['recordRadarChart'].destroy();
    delete charts['recordRadarChart'];
  }
  if (typeof Chart === 'undefined') {
    console.error('Chart.js is not loaded.');
    return;
  }

  const canvas = document.getElementById('record-radar-chart');
  if (!canvas) {
    console.error(`Canvas element with id "record-radar-chart" not found`);
    return;
  }
  const ctx = canvas.getContext('2d');
  const data = [record.r ?? 0, record.i ?? 0, record.a ?? 0, record.s ?? 0, record.e ?? 0, record.c ?? 0];
  const config = JSON.parse(JSON.stringify(recordRadarChartConfig));
  config.data.datasets[0].data = data;

  charts['recordRadarChart'] = new Chart(ctx, config);
}