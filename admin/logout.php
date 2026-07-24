<?php
require_once '../config/functions.php';
logoutUser();
setMessage('success', 'Logged out successfully');
header('Location: login.php');
exit();
?>