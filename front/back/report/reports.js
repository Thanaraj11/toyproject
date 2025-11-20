// Date range toggle
        document.getElementById('date-range').addEventListener('change', function() {
            const customRange = document.getElementById('custom-range');
            customRange.style.display = this.value === 'custom' ? 'flex' : 'none';
        });

        // Chart period buttons
        document.querySelectorAll('.chart-actions button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.chart-actions button').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                // In a real application, this would update the chart data
                updateCharts(this.dataset.period || 'week');
            });
        });

        // Initialize charts
        function initCharts() {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            window.salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Sales',
                        data: [3200, 4500, 3800, 5100, 4200, 6300, 4800],
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Orders',
                        data: [12, 18, 14, 20, 16, 25, 19],
                        borderColor: '#f8961e',
                        backgroundColor: 'rgba(248, 150, 30, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Sales ($)'
                            }
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Orders'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            window.categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Electronics', 'Clothing', 'Home & Kitchen', 'Books', 'Beauty'],
                    datasets: [{
                        data: [45, 22, 18, 10, 5],
                        backgroundColor: [
                            '#4361ee',
                            '#4cc9f0',
                            '#f8961e',
                            '#f72585',
                            '#4895ef'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Update charts based on selected period
        function updateCharts(period) {
            // This would fetch new data based on the period in a real application
            // For this example, we'll just update with sample data
            let salesData, ordersData, categoryData;
            
            switch(period) {
                case 'week':
                    salesData = [3200, 4500, 3800, 5100, 4200, 6300, 4800];
                    ordersData = [12, 18, 14, 20, 16, 25, 19];
                    categoryData = [45, 22, 18, 10, 5];
                    break;
                case 'month':
                    salesData = [12500, 13200, 14100, 15800, 13300, 14200, 15600, 16400, 13800, 14700, 15300, 16200];
                    ordersData = [42, 45, 48, 52, 44, 47, 51, 54, 46, 49, 50, 53];
                    categoryData = [42, 25, 16, 12, 5];
                    break;
                case 'quarter':
                    salesData = [45800, 47200, 49600];
                    ordersData = [145, 152, 162];
                    categoryData = [48, 20, 15, 11, 6];
                    break;
                case 'year':
                    salesData = [125000, 132000, 118000, 145000, 152000, 148000, 162000, 158000, 142000, 156000, 168000, 185000];
                    ordersData = [420, 445, 395, 480, 505, 490, 535, 520, 475, 515, 555, 610];
                    categoryData = [50, 18, 14, 12, 6];
                    break;
            }
            
            // Update sales chart
            window.salesChart.data.datasets[0].data = salesData;
            window.salesChart.data.datasets[1].data = ordersData;
            window.salesChart.update();
            
            // Update category chart
            window.categoryChart.data.datasets[0].data = categoryData;
            window.categoryChart.update();
        }

        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            
            // Animate stats counters
            const stats = document.querySelectorAll('.stat-content h3');
            stats.forEach(stat => {
                const finalValue = parseInt(stat.textContent.replace('$', '').replace(',', ''));
                let currentValue = 0;
                const increment = finalValue / 50;
                
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        clearInterval(timer);
                        currentValue = finalValue;
                    }
                    stat.textContent = stat.textContent.includes('$') ? 
                        '$' + Math.round(currentValue).toLocaleString() : 
                        Math.round(currentValue).toLocaleString();
                }, 20);
            });
        });

        // Export report functionality
        document.querySelector('.export-section .btn-primary').addEventListener('click', function() {
            const reportType = document.getElementById('export-report').value;
            const format = document.getElementById('export-format').value;
            alert(`Generating ${reportType} report in ${format.toUpperCase()} format...`);
        });