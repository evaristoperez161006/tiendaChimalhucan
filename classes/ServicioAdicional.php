<?php
class ServicioAdicional {
    public $idServicio;
    public $nombreServicio;
    public $precioFijo;
    public $descripcion;

    public function __construct($idServicio, $nombreServicio, $precioFijo, $descripcion) {
        $this->idServicio = $idServicio;
        $this->nombreServicio = $nombreServicio;
        $this->precioFijo = (float)$precioFijo;
        $this->descripcion = $descripcion;
    }

    public function obtenerPrecio() {
        return $this->precioFijo;
    }
}