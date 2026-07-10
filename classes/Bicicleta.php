<?php
class Bicicleta {
    public $idBicicleta;
    public $tipo;
    public $tarifaPorHora;
    public $estado;
    public $marca;
    public $color;
    public $stock; // NUEVO ATRIBUTO

    public function __construct($idBicicleta, $tipo, $tarifaPorHora, $marca, $color, $stock) {
        $this->idBicicleta = $idBicicleta;
        $this->tipo = $tipo;
        $this->tarifaPorHora = (float)$tarifaPorHora;
        $this->marca = $marca;
        $this->color = $color;
        $this->stock = (int)$stock;
        $this->estado = $this->stock > 0 ? "Disponible" : "Agotado";
    }

    public function obtenerTarifa() {
        return $this->tarifaPorHora;
    }

    public function actualizarEstado($nuevoEstado) {
        $this->estado = $nuevoEstado;
    }

    /**
     * Busca una bicicleta en el archivo JSON por su ID e instancia el objeto.
     */
    public static function buscarPorId($id) {
        $archivo = 'data/inventario.json';
        if (!file_exists($archivo)) return null;

        $inventario = json_decode(file_get_contents($archivo), true);
        foreach ($inventario as $bici) {
            if ($bici['id'] === $id) {
                return new Bicicleta($bici['id'], $bici['tipo'], $bici['tarifa'], $bici['marca'], $bici['color'], $bici['stock']);
            }
        }
        return null;
    }

    /**
     * Reduce en una unidad el stock del elemento en el JSON si cuenta con existencias.
     */
    public function descontarStockDeArchivo() {
        $archivo = 'data/inventario.json';
        if (!file_exists($archivo)) return false;

        $inventario = json_decode(file_get_contents($archivo), true);
        foreach ($inventario as &$bici) {
            if ($bici['id'] === $this->idBicicleta) {
                if ($bici['stock'] > 0) {
                    $bici['stock']--;
                    $this->stock = $bici['stock'];
                    if($this->stock === 0) {
                        $this->actualizarEstado("Agotado");
                    }
                    file_put_contents($archivo, json_encode($inventario, JSON_PRETTY_PRINT));
                    return true;
                }
                return false;
            }
        }
        return false;
    }
}