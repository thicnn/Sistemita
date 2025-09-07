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
        $this->db_connection = $db_connection;
        $this->orderModel = new Order($this->db_connection);
    }

    private function generatePdf($html, $filename)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, ["Attachment" => 1]); // 1 = Download, 0 = Show in browser
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

        $this->generatePdf($html, "Presupuesto-{$orderId}.pdf");
    }

    public function generateReceipt($orderId)
    {
        $order = $this->orderModel->findByIdWithDetails($orderId);
        if (!$order) {
            echo "Pedido no encontrado.";
            return;
        }

        ob_start();
        include __DIR__ . '/../views/pages/pdf/receipt_template.php';
        $html = ob_get_clean();

        $this->generatePdf($html, "Recibo-{$orderId}.pdf");
    }
}
