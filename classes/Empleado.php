<?php
class Empleado {
    public $idEmpleado;
    public $nombre;
    public $puesto;
    public $turno; // Atributo complementario

    public function __construct($idEmpleado, $nombre, $puesto, $turno = "Matutino") {
        $this->idEmpleado = $idEmpleado;
        $this->nombre = $nombre;
        $this->puesto = $puesto;
        $this->turno = $turno;
    }

    /**
     * Retorna una cadena formateada para la firma del ticket.
     */
    public function obtenerNombreFormateado() {
        return "{$this->nombre} ({$this->puesto})";
    }
}