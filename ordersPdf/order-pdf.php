<?php

require __DIR__.'/../vendor/autoload.php';
include_once __DIR__.'/../model/dbh.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function generatePDF($orderId) {
    $options = new Options();
    $options->set('defaultFont', 'Courier');
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf($options);

    // Get order details from database
    $query = "SELECT c.*, p.name, p.price, p.discount 
              FROM cart c 
              INNER JOIN products p ON c.product_id = p.product_id
              WHERE c.order_id = ?";

    global $conn;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die('No items found for order ID: ' . $orderId);
    }

    $fontsize = 14;
    $total = 0;

    $html = '
    <html>
    <head>
        <style>
            body { 
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
            }
            .company-info {
                margin-bottom: 40px;
            }
            .order-details {
                margin-bottom: 30px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }
            th {
                background-color: #28353F;
                color: white;
            }
            .total-row {
                font-weight: bold;
                background-color: #f8f9fa;
            }
            .footer {
                margin-top: 50px;
                text-align: center;
                font-size: 12px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <img class="logo" src="https://firebasestorage.googleapis.com/v0/b/seks-f1000.appspot.com/o/ignorethis%2Ftest123.png?alt=media&token=bcb94f62-f29d-43c3-bf83-111886ea4787" alt="logo" style="width: 100%; height: auto;">
            <h1>Order Invoice</h1>
        </div>

        <div class="company-info">
            <table>
                <tr>
                    <td width="15%"><strong>Company:</strong></td>
                    <td width="35%">Digital Sea</td>
                    <td width="20%; "><strong>Order Date:</strong></td>
                    <td width="30%">'.date('Y-m-d').'</td>
                </tr>
                <tr>
                    <td><strong>Address:</strong></td>
                    <td>FSHM, Prishtine</td>
                    <td><strong>Order ID:</strong></td>
                    <td>#'.$orderId.'</td>
                </tr>
                <tr>
                    <td><strong>Phone:</strong></td>
                    <td>(383) 44 123 321</td>
                    <td><strong>Status:</strong></td>
                    <td>Completed</td>
                </tr>
            </table>
        </div>

        <div class="order-details">
            <h2>Order Items</h2>
            <table>
                <thead>
                    <tr>
                        <th width="50%" style="font-size:14px;">Product</th>
                        <th width="10%" style="font-size:14px;">Quantity</th>
                        <th width="10%" style="font-size:14px;">Discount</th>
                        <th width="15%" style="font-size:14px;">Unit Price</th>
                        <th width="15%" style="font-size:14px;">Total</th>
                    </tr>
                </thead>
                <tbody>';

    while ($row = $result->fetch_assoc()) {
        $discount = $row['price'] - ($row['price'] * $row['discount'] / 100);
        $subtotal = $row['quantity'] * $discount;
        $total += $subtotal;
        $html .= '<tr>
            <td>'.htmlspecialchars($row['name']).'</td>
            <td>'.htmlspecialchars($row['quantity']).'</td>
            <td>'.number_format($row['discount'], 2).'%</td>
            <td>$'.number_format($discount, 2).'</td>
            <td>$'.number_format($subtotal, 2).'</td>
        </tr>';
    }

    $html .= '</tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;"><strong>Total Amount:</strong></td>
                        <td colspan="2" style="text-align: right;"><strong>$'.number_format($total, 2).'</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This is a computer-generated invoice, no signature required.</p>
        </div>
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $dompdf->output();
}

// If this file is called directly (not included), handle the download
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    $orderId = $_POST['order_id'] ?? null;
    if (!$orderId) {
        die('No order ID provided');
    }

    $pdfContent = generatePDF($orderId);
    
    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="invoice_'.$orderId.'.pdf"');
    echo $pdfContent;
    exit();
}