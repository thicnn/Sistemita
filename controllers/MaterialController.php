<?php
require_once '../models/Material.php';
require_once '../config/database.php'; // Necesario para la conexión

class MaterialController
{
    private $materialModel;
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->materialModel = new Material($this->db);
    }

    private function checkAdmin()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Acceso denegado. Se requieren permisos de administrador.'];
            header('Location: /sistemagestion/dashboard');
            exit();
        }
    }

    public function index()
    {
        $this->checkAdmin();
        $materials = $this->materialModel->findAll();
        $data = ['materials' => $materials];

        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/materials/index.php';
        require_once '../views/layouts/footer.php';
    }

    public function showCreateForm()
    {
        $this->checkAdmin();
        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/materials/create.php';
        require_once '../views/layouts/footer.php';
    }

    public function store()
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'] ?? '';
            $stock_actual = (float)($_POST['stock_actual'] ?? 0);
            $stock_minimo = (float)($_POST['stock_minimo'] ?? 0);
            $unidad_medida = $_POST['unidad_medida'];

            if ($this->materialModel->create($nombre, $descripcion, $stock_actual, $stock_minimo, $unidad_medida)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Material creado con éxito.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error al crear el material.'];
            }
        }
        header('Location: /sistemagestion/admin/materials');
        exit();
    }

    public function showEditForm($id)
    {
        $this->checkAdmin();
        $material = $this->materialModel->findById($id);

        if (!$material) {
            http_response_code(404);
            echo "Material no encontrado";
            exit();
        }

        $data = ['material' => $material];
        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/materials/edit.php';
        require_once '../views/layouts/footer.php';
    }

    public function update($id)
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'] ?? '';
            $stock_actual = (float)($_POST['stock_actual'] ?? 0);
            $stock_minimo = (float)($_POST['stock_minimo'] ?? 0);
            $unidad_medida = $_POST['unidad_medida'];

            if ($this->materialModel->update($id, $nombre, $descripcion, $stock_actual, $stock_minimo, $unidad_medida)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Material actualizado con éxito.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error al actualizar el material.'];
            }
        }
        header('Location: /sistemagestion/admin/materials');
        exit();
    }

    public function delete($id)
    {
        $this->checkAdmin();
        if ($this->materialModel->delete($id)) {
            $_SESSION['flash_message'] = ['type' => 'info', 'message' => 'Material eliminado correctamente.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Error al eliminar el material.'];
        }
        header('Location: /sistemagestion/admin/materials');
        exit();
    }
}
