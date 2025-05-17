<?php
session_start();

// Clean up order-related session variables
unset($_SESSION['order_created']);
unset($_SESSION['current_order_id']);
unset($_SESSION['payment_success']);
unset($_SESSION['total_amount']);

// Optional: Destroy the entire session
// session_destroy();
?> 