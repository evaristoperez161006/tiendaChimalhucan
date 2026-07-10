<?php
class Cliente {
    public $idCliente;
    public $nombre;
    public $telefono;
    public $email;
    public $tieneMembresia; 
    public $fechaRegistro;

    public function __construct($idCliente, $nombre, $telefono, $email, $tieneMembresia) {
        $this->idCliente = $idCliente;
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->tieneMembresia = (bool)$tieneMembresia;
        $this->fechaRegistro = date("Y-m-d");
    }

    public function obtenerDescuentoMembresia() {
        return $this->tieneMembresia ? 0.15 : 0.0;
    }
}