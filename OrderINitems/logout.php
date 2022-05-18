<?php
/* I certify that this is my own work Danny Gee R01810967 */
session_start();
if (isset($_SESSION['account_loggedin'])) {
    unset($_SESSION['account_loggedin']);
    unset($_SESSION['account_id']);
    unset($_SESSION['account_admin']);
    unset($_SESSION['cart']);
}
header('Location: index.php');
?>
