<div class="card">
    <div class="card-header">
        <h5 class="card-title">{{translate('Earning Statistics')}}</h5>
    </div>
    <div class="card-body">
        <canvas id="earningStatisticsChart" width="400" height="200"></canvas>
    </div>
</div>

<script>
    var ctx = document.getElementById('earningStatisticsChart').getContext('2d');
    var earningStatisticsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($data)),
            datasets: [{
                label: 'Earnings',
                data: @json(array_values($data)),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
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
