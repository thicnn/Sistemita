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
<<<<<<< HEAD
{
    if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
    
    // Recoge los filtros de la URL (aunque no se usen, la función los espera)
    $filters = [
        'search' => $_GET['search'] ?? '',
        'estado' => $_GET['estado'] ?? '',
        'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
        'fecha_fin' => $_GET['fecha_fin'] ?? ''
    ];

    // ¡LÍNEA CORREGIDA! Ahora usa el método correcto con filtros.
    $orders = $this->orderModel->findAllWithFilters($filters);
    
    require_once '../views/layouts/header.php';
    require_once '../views/pages/orders/index.php';
    require_once '../views/layouts/footer.php';
}

    public function showCreateForm()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
=======
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        $orders = $this->orderModel->findAll();
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
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        $clients = $this->clientModel->findAll();
        $products = $this->productModel->findAllAvailable();
        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/create.php';
        require_once '../views/layouts/footer.php';
    }

    public function store()
    {
<<<<<<< HEAD
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $es_interno = isset($_POST['es_interno']) ? 1 : 0;
=======
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
            $success = $this->orderModel->create(
                $_POST['cliente_id'],
                $_SESSION['user_id'],
                $_POST['estado'],
                $_POST['notas'],
<<<<<<< HEAD
                $_POST['items'] ?? [],
                $es_interno
=======
                $_POST['items'] ?? []
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
            );
            if ($success) {
                header('Location: /sistemagestion/orders');
            } else {
<<<<<<< HEAD
                echo "Hubo un error al guardar el pedido. Revisa el log de errores.";
=======
                echo "Hubo un error al guardar el pedido.";
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
            }
            exit();
        }
    }

    public function show($id)
    {
<<<<<<< HEAD
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        $order = $this->orderModel->findByIdWithDetails($id);
        if (!$order) { echo "Pedido no encontrado."; exit(); }
=======
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        $order = $this->orderModel->findByIdWithDetails($id);
        if (!$order) {
            echo "Pedido no encontrado.";
            exit();
        }
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/show.php';
        require_once '../views/layouts/footer.php';
    }

    public function addPayment($id)
    {
<<<<<<< HEAD
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
=======
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order = $this->orderModel->findByIdWithDetails($id);
            if ($order) {
                $totalPagado = array_sum(array_column($order['pagos'], 'monto'));
                $saldoPendiente = $order['costo_total'] - $totalPagado;
<<<<<<< HEAD
                $monto = (float)$_POST['monto'];
                if (!empty($monto) && is_numeric($monto) && $monto > 0 && $monto <= $saldoPendiente) {
                    $this->orderModel->addPayment($id, $monto, $_POST['metodo_pago']);
=======

                $monto = (float)$_POST['monto'];
                $metodo_pago = $_POST['metodo_pago'];

                // Validación de pago en el servidor
                if (!empty($monto) && is_numeric($monto) && $monto > 0 && $monto <= $saldoPendiente) {
                    $this->orderModel->addPayment($id, $monto, $metodo_pago);
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
                }
            }
            header('Location: /sistemagestion/orders/show/' . $id);
            exit();
        }
    }
<<<<<<< HEAD

    public function showEditForm($id)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        $order = $this->orderModel->findByIdWithDetails($id);
        if (!$order) { echo "Pedido no encontrado."; exit(); }
=======
    /**
     * ¡NUEVO! Muestra el formulario para editar un pedido.
     */
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
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/edit.php';
        require_once '../views/layouts/footer.php';
    }

<<<<<<< HEAD
    public function update($id)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motivo_cancelacion = ($_POST['estado'] === 'Cancelado' && !empty($_POST['motivo_cancelacion'])) ? $_POST['motivo_cancelacion'] : null;
            $es_interno = isset($_POST['es_interno']) ? 1 : 0;
            
            $this->orderModel->update($id, $_POST['estado'], $_POST['notas'], $motivo_cancelacion, $es_interno);
=======
    /**
     * ¡NUEVO! Procesa la actualización de un pedido.
     */
    public function update($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $estado = $_POST['estado'];
            $notas = $_POST['notas'];
            $motivo_cancelacion = null;

            if (isset($_POST['cancelar_pedido']) && !empty($_POST['motivo_cancelacion'])) {
                $motivo_cancelacion = $_POST['motivo_cancelacion'];
            }

            $this->orderModel->update($id, $estado, $notas, $motivo_cancelacion);
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
            header('Location: /sistemagestion/orders/show/' . $id);
            exit();
        }
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
