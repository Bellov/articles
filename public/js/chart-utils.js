const ChartUtils = {
    formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    },

    generateDateLabels(days = 7) {
        const labels = [];
        for (let i = days - 1; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            labels.push(this.formatDate(date));
        }
        return labels;
    },

    validateChartData(data) {
        return Array.isArray(data) && data.length > 0 && data.every(item => typeof item === 'number');
    },

    createDataset(label, data, color, alphaColor) {
        return {
            label: label,
            data: data,
            borderColor: color,
            backgroundColor: alphaColor,
            tension: 0.1,
            fill: true,
            pointRadius: 6,
            pointHoverRadius: 8
        };
    },

    getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
};

window.ChartUtils = ChartUtils;