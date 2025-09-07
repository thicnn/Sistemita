<?php
require_once '../models/Error.php';

class ErrorController
{
    private $errorModel;
    private $errorOptions = [
        'Blanco y negro de Bh227' => 0.9,
        'Blanco y negro de C454' => 3,
        'Color C454' => 10
    ];

    public function __construct($db_connection)
    {
        $this->errorModel = new ErrorModel($db_connection);
    }

    public function showCreateForm()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }

        $errorOptions = $this->errorOptions;

        require_once '../views/layouts/header.php';
        require_once '../views/pages/errors/create.php';
        require_once '../views/layouts/footer.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_error = $_POST['tipo_error'];
            $cantidad = (int)$_POST['cantidad'];
            $usuario_id = $_SESSION['user_id'];

            if (array_key_exists($tipo_error, $this->errorOptions) && $cantidad > 0) {
                $costo_unitario = $this->errorOptions[$tipo_error];
                $costo_total = $costo_unitario * $cantidad;

                $success = $this->errorModel->create($tipo_error, $cantidad, $costo_total, $usuario_id);

                if ($success) {
                    $_SESSION['toast'] = ['message' => 'Error registrado con éxito.', 'type' => 'success'];
                } else {
                    $_SESSION['toast'] = ['message' => 'Error al registrar el error.', 'type' => 'danger'];
                }
            } else {
                $_SESSION['toast'] = ['message' => 'Datos de error inválidos.', 'type' => 'danger'];
            }

            header('Location: /sistemagestion/errors/create');
            exit();
        }
    }
}
