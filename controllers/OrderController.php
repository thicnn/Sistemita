<?php
require_once '../models/Order.php';
require_once '../models/Client.php';
require_once '../models/Product.php';

class OrderController
{
    private $orderModel;
    private $clientModel;
    private $productModel;

    public function __construct($db_connection)
    {
        $this->orderModel = new Order($db_connection);
        $this->clientModel = new Client($db_connection);
        $this->productModel = new Product($db_connection);
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }

        $filters = [
            'search' => $_GET['search'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? '',
            'sort' => $_GET['sort'] ?? 'fecha_creacion',
            'dir' => $_GET['dir'] ?? 'desc'
        ];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $perPage = 15;

        $totalOrders = $this->orderModel->countAllWithFilters($filters);
        $totalPages = ceil($totalOrders / $perPage);
        $offset = ($page - 1) * $perPage;

        $orders = $this->orderModel->findAllWithFilters($filters, $perPage, $offset);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/index.php';
        require_once '../views/layouts/footer.php';
    }

    public function showCreateForm()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }

        $clients = $this->clientModel->findAll();

        $products = $this->productModel->findAllAvailable();
        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/create.php';
        require_once '../views/layouts/footer.php';
    }

    public function showQuickCreateForm()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }

        $products = $this->productModel->findAllAvailable();
        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/quick_create.php';
        require_once '../views/layouts/footer.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->orderModel->create(
                $_POST['cliente_id'],
                $_SESSION['user_id'],
                $_POST['estado'],
                $_POST['notas'],
                $_POST['items'] ?? [],
                (float)($_POST['descuento_total'] ?? 0),
                0, // es_interno
                0 // es_error
            );

            $_SESSION['toast'] = $success
                ? ['message' => '¡Pedido creado con éxito!', 'type' => 'success']
                : ['message' => 'Error al crear el pedido.', 'type' => 'danger'];

            header('Location: /sistemagestion/orders');
            exit();
        }
    }

    public function storeQuick()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newOrderId = $this->orderModel->create(
                null, // No client ID for quick orders
                $_SESSION['user_id'],
                $_POST['estado'],
                $_POST['notas'],
                $_POST['items'] ?? [],
                (float)($_POST['descuento_total'] ?? 0),
                0, // es_interno
                0 // es_error
            );

            if ($newOrderId) {
                if ($_POST['estado'] === 'Entregado') {
                    $metodo_pago_final = $_POST['metodo_pago_final'] ?? 'Efectivo';
                    $this->orderModel->settlePayment($newOrderId, $metodo_pago_final);
                    $this->orderModel->addHistory($newOrderId, $_SESSION['user_id'], "Saldó la cuenta al crear como 'Entregado' con " . htmlspecialchars($metodo_pago_final) . ".");
                }
                $_SESSION['toast'] = ['message' => '¡Pedido rápido creado con éxito!', 'type' => 'success'];
            } else {
                $_SESSION['toast'] = ['message' => 'Error al crear el pedido rápido.', 'type' => 'danger'];
            }

            header('Location: /sistemagestion/orders');
            exit();
        }
    }

    public function show($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        $order = $this->orderModel->findByIdWithDetails($id);
        if (!$order) {
            echo "Pedido no encontrado.";
            exit();
        }
        // Para poder recalcular descuentos en la vista, necesitamos los precios originales
        $products = $this->productModel->findAllAvailable();

        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/show.php';
        require_once '../views/layouts/footer.php';
    }

    public function addPayment($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order = $this->orderModel->findByIdWithDetails($id);
            if ($order) {
                $totalPagado = array_sum(array_column($order['pagos'], 'monto'));
                $saldoPendiente = $order['costo_total'] - $totalPagado;
                $monto = (float)$_POST['monto'];
                if (!empty($monto) && is_numeric($monto) && $monto > 0 && $monto <= $saldoPendiente) {
                    $this->orderModel->addPayment($id, $monto, $_POST['metodo_pago']);
                    $_SESSION['toast'] = ['message' => '¡Pago registrado correctamente!', 'type' => 'success'];

                    $descripcion = "Registró un pago de $" . number_format($monto, 2) . " (" . htmlspecialchars($_POST['metodo_pago']) . ").";
                    $this->orderModel->addHistory($id, $_SESSION['user_id'], $descripcion);
                } else {
                    $_SESSION['toast'] = ['message' => 'Monto de pago inválido.', 'type' => 'danger'];
                }
            }
            header('Location: /sistemagestion/orders/show/' . $id);
            exit();
        }
    }

    public function showEditForm($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        $order = $this->orderModel->findByIdWithDetails($id);
        if (!$order) {
            echo "Pedido no encontrado.";
            exit();
        }

        $history = $this->orderModel->getHistoryByOrderId($id);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/edit.php';
        require_once '../views/layouts/footer.php';
    }

    public function update($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $ordenActual = $this->orderModel->findByIdWithDetails($id);
            if (!$ordenActual) {
                header('Location: /sistemagestion/orders');
                exit();
            }

            $estadoAntiguo = $ordenActual['estado'];
            $nuevoEstado = $_POST['estado'] ?? $estadoAntiguo;
            $motivo_cancelacion = (!empty($_POST['motivo_cancelacion'])) ? trim($_POST['motivo_cancelacion']) : null;

            if ($motivo_cancelacion) {
                $nuevoEstado = 'Cancelado';
            }

            // Server-side validation for status progression
            $statusOrder = ["Solicitud" => 1, "Cotización" => 2, "Confirmado" => 3, "En curso" => 4, "Listo para entregar" => 5, "Entregado" => 6];
            $valorEstadoAntiguo = $statusOrder[$estadoAntiguo] ?? 0;
            $valorNuevoEstado = $statusOrder[$nuevoEstado] ?? 0;

            if ($nuevoEstado !== 'Cancelado' && $valorNuevoEstado < $valorEstadoAntiguo) {
                $_SESSION['toast'] = ['message' => 'Error: No se puede volver a un estado anterior.', 'type' => 'danger'];
                header('Location: /sistemagestion/orders/show/' . $id);
                exit();
            }

            if ($nuevoEstado !== $estadoAntiguo) {
                $descripcion = "Cambió el estado de '{$estadoAntiguo}' a '{$nuevoEstado}'.";
                if ($motivo_cancelacion) {
                    $descripcion .= " Motivo: " . htmlspecialchars($motivo_cancelacion);
                }
                $this->orderModel->addHistory($id, $_SESSION['user_id'], $descripcion);
            }

            if ($nuevoEstado === 'Entregado' && $estadoAntiguo !== 'Entregado') {
                $metodo_pago_final = $_POST['metodo_pago_final'] ?? 'Efectivo';
                $this->orderModel->settlePayment($id, $metodo_pago_final);
                $this->orderModel->addHistory($id, $_SESSION['user_id'], "Saldó la cuenta al marcar como 'Entregado' con " . htmlspecialchars($metodo_pago_final) . ".");
            }

            $this->orderModel->update($id, $nuevoEstado, $_POST['notas'], $motivo_cancelacion);

            $_SESSION['toast'] = ['message' => '¡Pedido actualizado!', 'type' => 'info'];

            header('Location: /sistemagestion/orders/show/' . $id);
            exit();
        }
    }
}
