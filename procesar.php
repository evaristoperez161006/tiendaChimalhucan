<?php
require_once 'classes/Cliente.php';
require_once 'classes/Bicicleta.php';
require_once 'classes/ServicioAdicional.php';
require_once 'classes/Empleado.php'; // NUEVA INCLUSIÓN
require_once 'classes/Renta.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit();
}

// 1. NUEVO: Procesamiento e Instanciación del Objeto: EMPLEADO
$empleadoDataRaw = $_POST['datosEmpleado'] ?? '';
$empPartes = explode('|', $empleadoDataRaw);
if (count($empPartes) === 4) {
    $idEmp = $empPartes[0];
    $nombreEmp = $empPartes[1];
    $puestoEmp = $empPartes[2];
    $turnoEmp = $empPartes[3];
    $empleadoObj = new Empleado($idEmp, $nombreEmp, $puestoEmp, $turnoEmp);
} else {
    // Fallback preventivo por si no viajan datos válidos
    $empleadoObj = new Empleado("EMP-000", "Genérico", "Operador");
}

// 2. Instanciar Cliente
$idClienteSimulado = "CLI-" . rand(1000, 9999);
$nombre = $_POST['nombreCliente'] ?? '';
$telefono = $_POST['telefonoCliente'] ?? '';
$email = $_POST['emailCliente'] ?? '';
$tieneMembresia = isset($_POST['tieneMembresia']);
$clienteObj = new Cliente($idClienteSimulado, $nombre, $telefono, $email, $tieneMembresia);

// 3. Buscar e Instanciar Bicicleta desde el JSON
$idBiciSeleccionada = $_POST['tipoBicicleta'] ?? '';
$bicicletaObj = Bicicleta::buscarPorId($idBiciSeleccionada);

if (!$bicicletaObj || $bicicletaObj->stock <= 0) {
    die("<h2>Error: La unidad seleccionada no cuenta con stock. <a href='index.html'>Volver</a></h2>");
}

$bicicletaObj->descontarStockDeArchivo();

// 4. Instanciar Renta Operacional (Ahora se incluye la instancia $empleadoObj)
$idRentaSimulado = "TRX-" . rand(100000, 999999);
$horas = (int)($_POST['cantidadHoras'] ?? 1);
$rentaObj = new Renta($idRentaSimulado, $clienteObj, $bicicletaObj, $empleadoObj, $horas);

// 5. Procesar e Instanciar Servicios Adicionales
if (isset($_POST['servicios']) && is_array($_POST['servicios'])) {
    foreach ($_POST['servicios'] as $idServicio => $valoresServicio) {
        $servicioPartes = explode('|', $valoresServicio);
        if (count($servicioPartes) === 3) {
            $servicioObj = new ServicioAdicional($idServicio, $servicioPartes[0], (float)$servicioPartes[1], $servicioPartes[2]);
            $rentaObj->agregarServicio($servicioObj);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>BiciRent Pro - Comprobante</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container" style="max-width: 600px; margin: 20px auto;">
        <main class="main-content" style="grid-template-columns: 1fr;">
            <section class="receipt-section">
                <div class="card receipt-card">
                    <div class="receipt-content">
                        <div class="ticket-header">
                            <div class="ticket-company">BICIRENT PRO S.A. DE C.V.</div>
                            <div class="ticket-title">COMPROBANTE DE RENTA DIGITAL</div>
                            <div class="ticket-divider"></div>
                        </div>
                        
                        <div class="ticket-meta">
                            <p><strong>ID Transacción:</strong> <?php echo $rentaObj->idRenta; ?></p>
                            <p><strong>Fecha/Hora Emisión:</strong> <?php echo $rentaObj->fecha; ?></p>
                            <p><strong>Atendido por:</strong> [<?php echo $rentaObj->empleado->idEmpleado; ?>] <?php echo $rentaObj->empleado->obtenerNombreFormateado(); ?> en turno <?php echo $rentaObj->empleado->turno; ?></p>
                            <p><strong>Estado Operación:</strong> <span style="color: #059669; font-weight: 600;"><?php echo $rentaObj->estadoRenta; ?></span></p>
                        </div>
                        
                        <div class="ticket-divider"></div>
                        
                        <div class="ticket-section">
                            <h4>Ficha Técnica del Cliente [<?php echo $rentaObj->cliente->idCliente; ?>]</h4>
                            <p><strong>Nombre completo:</strong> <?php echo htmlspecialchars($rentaObj->cliente->nombre); ?></p>
                            <p><strong>Número telefónico:</strong> <?php echo htmlspecialchars($rentaObj->cliente->telefono); ?></p>
                            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($rentaObj->cliente->email); ?></p>
                        </div>

                        <div class="ticket-divider"></div>

                        <div class="ticket-section">
                            <h4>Especificación del Inventario Rentado</h4>
                            <p><strong>Código Unidad:</strong> <?php echo $rentaObj->bicicleta->idBicicleta; ?></p>
                            <p><strong>Marca / Modelo:</strong> <?php echo $rentaObj->bicicleta->marca; ?> (<?php echo $rentaObj->bicicleta->color; ?>)</p>
                            <div class="ticket-row" style="margin-top: 10px;">
                                <span>Línea <?php echo $rentaObj->bicicleta->tipo; ?> (<?php echo $rentaObj->cantidadHoras; ?>h x $<?php echo $rentaObj->bicicleta->obtenerTarifa(); ?>/h)</span>
                                <span>$<?php echo number_format($rentaObj->bicicleta->obtenerTarifa() * $rentaObj->cantidadHoras, 2); ?></span>
                            </div>
                            
                            <?php if (count($rentaObj->serviciosIncluidos) > 0): ?>
                                <?php foreach ($rentaObj->serviciosIncluidos as $servicio): ?>
                                    <div class="ticket-row service-item">
                                        <span>+ <?php echo $servicio->nombreServicio; ?></span>
                                        <span>$<?php echo number_format($servicio->obtenerPrecio(), 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="ticket-divider"></div>

                        <div class="ticket-totals">
                            <div class="ticket-row">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($rentaObj->calcularSubtotal(), 2); ?></span>
                            </div>
                            <div class="ticket-row discount">
                                <span>Descuento Membresía:</span>
                                <span>-$<?php echo number_format($rentaObj->aplicarDescuento(), 2); ?></span>
                            </div>
                            <div class="ticket-row total">
                                <span>TOTAL NETO A PAGAR:</span>
                                <span>$<?php echo number_format($rentaObj->calcularTotalFinal(), 2); ?></span>
                            </div>
                        </div>

                        <div class="ticket-footer">
                            <p>Los datos han sido correctamente procesados bajo arquitectura OOP.</p>
                            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
                                <a href="index.html" class="btn-secondary" style="text-decoration: none; line-height: 2.5; text-align: center;">Registrar Nueva</a>
                                <button class="btn-primary" onclick="window.print()">Imprimir Ticket</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>