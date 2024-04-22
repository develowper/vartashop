import Chart from 'chart.js/auto';

window.addEventListener("DOMContentLoaded", function () {


    createChart('chart-logs-costumers', ' ثبت نام');
    createChart('chart-logs-transactions', ' تراکنش');
    createChart('chart-logs-emotes', ' دسته بندی مشتریان بر اساس زمان خرید');
    createChart('chart-logs-levels', 'دسته بندی مشتریان بر اساس مجموع خرید');


});

const createChart = (id, title) => {

    const ctx = document.getElementById(id);
    if (!ctx) return;
    const dataset = ctx.dataset;
    const labels = JSON.parse(dataset.labels);
    const backgroundColors = JSON.parse(dataset.background);
    let data = JSON.parse(dataset.data);

    let datasets = [];
    for (let i in data) {
        datasets.push({
            label: labels[i],
            backgroundColor: backgroundColors[i],
            data: [data[i]],

        })
    }

    const myChart = new Chart(ctx, {
        type: 'bar',
        // type: 'doughnut',
        data: {
            labels: [' '],
            datasets: datasets
        },
        options: {
            responsive: false,
            plugins: {
                legend: {
                    position: 'top',
                    font: {
                        family: 'Tanha'
                    }
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        family: 'Tanha'
                    }
                },
                labels: {
                    // This more specific font property overrides the global property
                    font: {
                        family: 'Tanha'
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }
                    }]
                }
            }
        },
    });
};