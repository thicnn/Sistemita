<?php
function getStatusBadgeClass($status)
{
    $map = [
        'Entregado' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        'Listo para Retirar' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        'En Curso' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        'Confirmado' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'Cancelado' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        'Solicitud' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
        'Cotización' => 'bg-light text-dark border',
        'Error' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        'Uso Interno' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
    ];
    return $map[$status] ?? 'bg-dark';
}
?>
