const CHART_CONFIG = {
    colors: {
        primary: 'rgb(75, 192, 192)',
        primaryAlpha: 'rgba(75, 192, 192, 0.1)',
        secondary: 'rgb(255, 99, 132)',
        secondaryAlpha: 'rgba(255, 99, 132, 0.1)'
    },
    
    labels: {
        days: ['7 days ago', '6 days ago', '5 days ago', '4 days ago', '3 days ago', '2 days ago', 'Yesterday'],
        titles: {
            chart: 'Articles Published in Last 7 Days',
            yAxis: 'Number of Articles',
            xAxis: 'Date'
        }
    },
    
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 1000,
            easing: 'easeInOutQuart'
        },
        plugins: {
            title: {
                display: true,
                font: {
                    size: 16,
                    weight: 'bold'
                }
            },
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            },
            x: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            }
        }
    }
};

window.CHART_CONFIG = CHART_CONFIG;