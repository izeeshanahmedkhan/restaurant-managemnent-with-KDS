<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{translate('Order Statistics')}}</h5>
    </div>
    <div class="card-body">
        <canvas id="orderStatisticsChart" width="400" height="200"></canvas>
    </div>
</div>

<script>
    var ctx = document.getElementById('orderStatisticsChart').getContext('2d');
    var orderStatisticsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(array_keys($data)),
            datasets: [{
                label: 'Orders',
                data: @json(array_values($data)),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
