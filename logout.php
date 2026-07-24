<?php
require_once 'config/functions.php';

logoutUser();
setMessage('success', 'You have been logged out successfully');
header('Location: index.php');
exit();
?>