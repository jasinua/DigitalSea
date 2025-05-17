<?php
session_start();

require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_POST['pdf'])) {
    $options = new Options();
    $options->set('defaultFont', 'Courier');
    $dompdf = new Dompdf($options);

    // Simple HTML content for testing
    $html = '
    <html>
    <head>
        <style>
            body { font-family: Courier, sans-serif; }
            h2 { color: #333; }
        </style>
    </head>
    <body>
        <h2>Order Details</h2>
        <p>This is a test PDF document.</p>
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Download the PDF
    $dompdf->stream("test.pdf", ["Attachment" => 1]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Download PDF</title>
</head>
<body>
    <form action="" method="post">
        <input type="submit" name="pdf" value="Download PDF">
    </form>
</body>
</html>
