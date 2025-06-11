<?php
require_once 'includes/api_functions.php';
if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}
logoutUser();
header('Location: index.php');
exit;
?>