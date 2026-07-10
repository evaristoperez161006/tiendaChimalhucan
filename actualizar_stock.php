<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: inventario.html");
    exit();
}

// 1. Capturar parámetros enviados desde el formulario de la fila
$idBicicleta = $_POST['idBicicleta'] ?? '';
$nuevoStock = isset($_POST['nuevoStock']) ? (int)$_POST['nuevoStock'] : 0;

$archivo = 'data/inventario.json';

if (file_exists($archivo)) {
    // 2. Extraer el contenido del archivo y convertirlo en un arreglo de PHP
    $inventarioArreglo = json_decode(file_get_contents($archivo), true);
    $encontrado = false;

    // 3. Recorrer el arreglo por referencia (&) para modificar el valor directamente
    foreach ($inventarioArreglo as &$bici) {
        if ($bici['id'] === $idBicicleta) {
            $bici['stock'] = $nuevoStock;
            $encontrado = true;
            break;
        }
    }

    // 4. Si se actualizó el elemento, se vuelve a guardar el arreglo serializado en el JSON
    if ($encontrado) {
        file_put_contents($archivo, json_encode($inventarioArreglo, JSON_PRETTY_PRINT));
        
        // Redireccionar de vuelta al inventario de forma limpia
        header("Location: inventario.html?status=success");
        exit();
    } else {
        die("Error: El ID de la unidad no existe en el registro.");
    }
} else {
    die("Error: El archivo de persistencia de datos no fue encontrado.");
}