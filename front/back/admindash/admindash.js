document.addEventListener('DOMContentLoaded', function() {
            // Sales Chart
            const salesChart = new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Sales 2023',
                        data: [15000, 18000, 21000, 19000, 23000, 25000, 27000, 29000, 26000, 28000, 30000, 32000],
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Sales 2022',
                        data: [12000, 14000, 16000, 15000, 17000, 19000, 21000, 23000, 22000, 24000, 26000, 28000],
                        borderColor: '#f8961e',
                        backgroundColor: 'rgba(248, 150, 30, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Monthly Sales Comparison'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Update stats cards with animation
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
                    stat.textContent = finalValue >= 1000 ? 
                        '$' + Math.round(currentValue).toLocaleString() : 
                        Math.round(currentValue).toLocaleString();
                }, 20);
            });
        });