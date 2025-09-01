class ChartManager {
    constructor() {
        this.chart = null;
        this.chartConfig = {
            type: 'line',
            data: {
                labels: ['7 days ago', '6 days ago', '5 days ago', '4 days ago', '3 days ago', '2 days ago', 'Yesterday'],
                datasets: [{
                    label: 'Articles per Day',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Articles Published in Last 7 Days'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Articles'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        };
    }

    initialize(canvasId) {
        const ctx = document.getElementById(canvasId);
        if (ctx) {
            this.chart = new Chart(ctx, this.chartConfig);
            this.loadData();
        }
    }

    loadData() {
        fetch('/chart-data')
            .then(response => response.json())
            .then(data => {
                if (data.success && this.chart) {
                    this.updateData(data.labels, data.data);
                }
            })
            .catch(error => {
                console.error('Failed to load chart data:', error);
            });
    }

    updateData(labels, data) {
        if (this.chart) {
            this.chart.data.labels = labels;
            this.chart.data.datasets[0].data = data;
            this.chart.update();
        }
    }

    updateWithMockData(articlesCount) {
        if (this.chart) {
            const mockData = [5, 8, 12, 15, 10, 7, articlesCount];
            this.chart.data.datasets[0].data = mockData;
            this.chart.update();
        }
    }

    refresh() {
        this.loadData();
    }

    destroy() {
        if (this.chart) {
            this.chart.destroy();
            this.chart = null;
        }
    }
}
window.ChartManager = ChartManager;