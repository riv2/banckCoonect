import Chart from 'chart.js';

var ctx = document.getElementById("reportChart");
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: JSON.parse(ctx.dataset.labels),
        datasets: [
            {
                label: JSON.parse(ctx.dataset.newtitle),
                backgroundColor: '#3a8dca',
                borderColor: '#3a8dca',
                borderWidth: 1,
                barPercentage: 0.5,
                order: 3,
                stack: 'status',
                data: JSON.parse(ctx.dataset.new),
            },
            {
                label: JSON.parse(ctx.dataset.saletitle),
                backgroundColor: '#aae0d3',
                borderColor: '#aae0d3',
                borderWidth: 1,
                barPercentage: 0.5,
                order: 2,
                stack: 'status',
                data: JSON.parse(ctx.dataset.sale),
            },
            {
                label: JSON.parse(ctx.dataset.rejecttitle),
                backgroundColor: '#BBBBBB',
                borderColor: '#BBBBBB',
                borderWidth: 1,
                barPercentage: 0.5,
                order: 1,
                stack: 'status',
                data: JSON.parse(ctx.dataset.reject),
            },
        ]
    },
    options: {
        scales: {
            xAxes: [{
                offset: true,
                bounds: 'data',
                distribution: 'linear',
            }],
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    precision: 0,
                },
            }]
        },
        legend: {
            display: false,
        },
        tooltips: {
            intersect: false,
            mode: 'index',
            callbacks: {
                label: function (tooltipItem, myData) {
                    var label = myData.datasets[tooltipItem.datasetIndex].label || '';
                    if (label) {
                        label += ': ';
                    }
                    label += parseFloat(tooltipItem.value).toFixed(0);
                    return label;
                }
            }
        }
    }
});
