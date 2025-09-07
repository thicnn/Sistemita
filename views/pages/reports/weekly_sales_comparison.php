<h2 class="mb-4">Comparativa de Ventas Semanales</h2>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="/sistemagestion/reports/weekly_sales_comparison" method="GET" class="row g-3 align-items-center justify-content-center">
            <div class="col-auto">
                <label for="date" class="form-label">Seleccionar una fecha para ver la semana y compararla con la anterior:</label>
            </div>
            <div class="col-auto">
                <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($selectedDate); ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Comparar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">
            Comparativa: Semana del <?php echo date('d/m/Y', strtotime($startOfPrevWeek->format('Y-m-d'))); ?> al <?php echo date('d/m/Y', strtotime($endOfPrevWeek->format('Y-m-d'))); ?>
            vs.
            Semana del <?php echo date('d/m/Y', strtotime($startOfWeek->format('Y-m-d'))); ?> al <?php echo date('d/m/Y', strtotime($endOfWeek->format('Y-m-d'))); ?>
        </h5>
    </div>
    <div class="card-body">
        <canvas id="comparisonChart" style="min-height: 400px;"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('comparisonChart');
    if (ctx) {
        const labels = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        const dataCurrentWeek = <?php echo json_encode($dataCurrentWeek ?? []); ?>;
        const dataPrevWeek = <?php echo json_encode($dataPrevWeek ?? []); ?>;

        const salesCurrentWeek = dataCurrentWeek.map(d => d.total_ventas);
        const salesPrevWeek = dataPrevWeek.map(d => d.total_ventas);

        const ordersCurrentWeek = dataCurrentWeek.map(d => d.total_pedidos);
        const ordersPrevWeek = dataPrevWeek.map(d => d.total_pedidos);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ventas Semana Anterior',
                        data: salesPrevWeek,
                        backgroundColor: 'rgba(108, 117, 125, 0.5)',
                        borderColor: 'rgba(108, 117, 125, 1)',
                        borderWidth: 1,
                        yAxisID: 'ySales',
                    },
                    {
                        label: 'Ventas Semana Actual',
                        data: salesCurrentWeek,
                        backgroundColor: 'rgba(13, 110, 253, 0.6)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        yAxisID: 'ySales',
                    },
                    {
                        label: 'Pedidos Semana Anterior',
                        data: ordersPrevWeek,
                        backgroundColor: 'rgba(255, 193, 7, 0.5)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'yOrders',
                        tension: 0.3
                    },
                    {
                        label: 'Pedidos Semana Actual',
                        data: ordersCurrentWeek,
                        backgroundColor: 'rgba(25, 135, 84, 0.6)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'yOrders',
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    ySales: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Ventas ($)'
                        }
                    },
                    yOrders: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nº de Pedidos'
                        },
                        grid: {
                            drawOnChartArea: false, // only draw grid lines for the first Y axis
                        },
                    }
                }
            }
        });
    }
});
</script>
