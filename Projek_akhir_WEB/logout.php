<?php
require_once 'database.php';

session_destroy();
redirect('index.php');
?>