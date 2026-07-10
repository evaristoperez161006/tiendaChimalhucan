<?php
header('Content-Type: application/json');
$archivo = 'data/inventario.json';

if (file_exists($archivo)) {
    echo file_get_contents($archivo);
} else {
    echo json_encode([]);
}