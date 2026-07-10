<?php
class Renta {
    public $idRenta;
    public $fecha;
    public $cliente;           // Instancia de Cliente
    public $bicicleta;         // Instancia de Bicicleta
    public $empleado;          // NUEVO ATRIBUTO: Instancia de Empleado
    public $cantidadHoras;
    public $serviciosIncluidos = []; 
    public $estadoRenta;

    // Se actualiza el constructor para inyectar la dependencia del empleado
    public function __construct($idRenta, $cliente, $bicicleta, $empleado, $cantidadHoras) {
        $this->idRenta = $idRenta;
        $this->fecha = date("d/m/Y H:i:s");
        $this->cliente = $cliente;
        $this->bicicleta = $bicicleta;
        $this->empleado = $empleado; // Vinculación del objeto Empleado
        $this->cantidadHoras = (int)$cantidadHoras;
        $this->estadoRenta = "Activa";
    }

    public function agregarServicio($servicio) {
        $this->serviciosIncluidos[] = $servicio;
    }

    public function calcularSubtotal() {
        $costoBicicleta = $this->bicicleta->obtenerTarifa() * $this->cantidadHoras;
        $costoServicios = 0;
        foreach ($this->serviciosIncluidos as $servicio) {
            $costoServicios += $servicio->obtenerPrecio();
        }
        return $costoBicicleta + $costoServicios;
    }

    public function aplicarDescuento() {
        return $this->calcularSubtotal() * $this->cliente->obtenerDescuentoMembresia();
    }

    public function calcularTotalFinal() {
        return $this->calcularSubtotal() - $this->aplicarDescuento();
    }
}