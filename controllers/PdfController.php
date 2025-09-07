<?php
use Dompdf\Dompdf;
use Dompdf\Options;

require_once __DIR__ . '/../models/Order.php';

class PdfController
{
    private $orderModel;
    private $db_connection;

    public function __construct($db_connection)
    {
        if (!class_exists('Dompdf\Dompdf')) {
            die("Error: La librería DomPDF no está instalada. Por favor, ejecute 'composer require dompdf/dompdf' en la raíz de su proyecto e incluya el autoloader.");
        }
        $this->db_connection = $db_connection;
        $this->orderModel = new Order($this->db_connection);
    }

    public function generateQuote($orderId)
    {
        $order = $this->orderModel->findByIdWithDetails($orderId);
        if (!$order) {
            echo "Pedido no encontrado.";
            return;
        }

        ob_start();
        include __DIR__ . '/../views/pages/pdf/quote_template.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Presupuesto-{$orderId}.pdf", ["Attachment" => 1]);
    }
}
