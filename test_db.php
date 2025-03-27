<?php
require 'db.php';

if ($conn) {
    echo "Database connected successfully!";
} else {
    echo "Failed to connect to database.";
}
?>
