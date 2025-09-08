<h2 class="mb-4">Reporte de Producción Semanal</h2>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="/sistemagestion/reports/weekly_production" method="GET" class="row g-3 align-items-center justify-content-center">
            <div class="col-auto">
                <label for="date" class="form-label">Seleccionar una fecha para ver la semana:</label>
            </div>
            <div class="col-auto">
                <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($selectedDate); ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Ver Semana</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Semana del <?php echo date('d/m/Y', strtotime($startDate)); ?> al <?php echo date('d/m/Y', strtotime($endDate)); ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>Día</th>
                        <th>Producción (Máquina)</th>
                        <th>Ingresos (Método de Pago)</th>
                        <th>Ventas</th>
                        <th>Costos</th>
                        <th>Ganancias</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                    $totals = [
                        'produccion' => ['bh227_bw' => 0, 'c454e_bw' => 0, 'c454e_color' => 0, 'servicios' => 0],
                        'pagos' => ['Efectivo' => 0, 'Débito' => 0, 'Crédito' => 0],
                        'ventas' => 0, 'costos' => 0, 'ganancias' => 0
                    ];
                    for ($i = 0; $i < 7; $i++):
                        $currentDay = (clone $startOfWeek)->modify("+$i days")->format('Y-m-d');
                        $dayData = $weeklyData[$currentDay] ?? null;

                        if ($dayData) {
                            $totals['ventas'] += $dayData['ventas'];
                            $totals['costos'] += $dayData['costos'];
                            $totals['ganancias'] += $dayData['ganancias'];
                            foreach ($dayData['produccion'] as $tipo => $cantidad) {
                                $totals['produccion'][$tipo] += $cantidad;
                            }
                            foreach ($dayData['pagos'] as $metodo => $monto) {
                                $totals['pagos'][$metodo] += $monto;
                            }
                        }
                    ?>
                        <tr>
                            <td class="fw-bold text-center align-middle"><?php echo $dias[$i]; ?><br><small class="text-muted"><?php echo date('d/m', strtotime($currentDay)); ?></small></td>
                            <td>
                                <?php if ($dayData && array_sum($dayData['produccion']) > 0): ?>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><strong>BH-227 (B&N):</strong> <?php echo $dayData['produccion']['bh227_bw']; ?></li>
                                        <li><strong>C454e (B&N):</strong> <?php echo $dayData['produccion']['c454e_bw']; ?></li>
                                        <li><strong>C454e (Color):</strong> <?php echo $dayData['produccion']['c454e_color']; ?></li>
                                        <?php if($dayData['produccion']['servicios'] > 0): ?>
                                            <li class="mt-1 border-top pt-1"><strong>Servicios:</strong> <?php echo $dayData['produccion']['servicios']; ?></li>
                                        <?php endif; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted text-center mb-0">-</p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($dayData && array_sum($dayData['pagos']) > 0): ?>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><strong>Efectivo:</strong> $<?php echo number_format($dayData['pagos']['Efectivo'], 2); ?></li>
                                        <li><strong>Débito:</strong> $<?php echo number_format($dayData['pagos']['Débito'], 2); ?></li>
                                        <li><strong>Crédito:</strong> $<?php echo number_format($dayData['pagos']['Crédito'], 2); ?></li>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted text-center mb-0">-</p>
                                <?php endif; ?>
                            </td>
                            <td class="text-end align-middle"><?php echo $dayData ? '$' . number_format($dayData['ventas'], 2) : '-'; ?></td>
                            <td class="text-end text-danger align-middle"><?php echo $dayData ? '$' . number_format($dayData['costos'], 2) : '-'; ?></td>
                            <td class="text-end text-success fw-bold align-middle"><?php echo $dayData ? '$' . number_format($dayData['ganancias'], 2) : '-'; ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr class="text-end">
                        <th colspan="3" class="text-center">TOTALES DE LA SEMANA</th>
                        <th>$<?php echo number_format($totals['ventas'], 2); ?></th>
                        <th class="text-danger">$<?php echo number_format($totals['costos'], 2); ?></th>
                        <th class="text-success">$<?php echo number_format($totals['ganancias'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
