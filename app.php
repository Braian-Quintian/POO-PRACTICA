<?php
declare(strict_types=1);

$_DATA = json_decode(file_get_contents("php://input"), true);

abstract class Vehiculo
{
    public function __construct(protected string $placa)
    {
    }

    public function getPlaca(): string
    {
        return $this->placa;
    }

    abstract public function getType(): string;
}

class Coche extends Vehiculo
{
    public function getType(): string
    {
        return 'Coche';
    }
}

class Motocicleta extends Vehiculo
{
    public function getType(): string
    {
        return 'Motocicleta';
    }
}

interface ParqueaderoInterface
{
    public function estacionar(Vehiculo $vehiculo): void;
    public function retirar(Vehiculo $vehiculo): void;
}

class Parqueadero implements ParqueaderoInterface
{
    private array $vehiculos;

    public function __construct()
    {
        $this->vehiculos = [];
    }

    public function estacionar(Vehiculo $vehiculo): void
    {
        $this->vehiculos[$vehiculo->getPlaca()] = $vehiculo;
        echo "Vehículo con placa " . $vehiculo->getPlaca() . " ha sido estacionado.\n";
    }

    public function retirar(Vehiculo $vehiculo): void
    {
        if (isset($this->vehiculos[$vehiculo->getPlaca()])) {
            unset($this->vehiculos[$vehiculo->getPlaca()]);
            echo "Vehículo con placa " . $vehiculo->getPlaca() . " ha sido retirado.\n";
        } else {
            echo "El vehículo con placa " . $vehiculo->getPlaca() . " no se encuentra en el parqueadero.\n";
        }
    }
}

// Crear un objeto Parqueadero
$parqueadero = new Parqueadero();

// Estacionar los vehículos del JSON
if (isset($_DATA['Vehiculos'])) {
    $vehiculos = $_DATA['Vehiculos'];

    foreach ($vehiculos as $vehiculoData) {
        $tipo = $vehiculoData['tipo'];
        $placa = $vehiculoData['placa'];
        $estado = $vehiculoData['estado'];

        if ($tipo === 'Coches') {
            $vehiculo = new Coche($placa);
        } elseif ($tipo === 'Motocicleta') {
            $vehiculo = new Motocicleta($placa);
        } else {
            continue; // Saltar al siguiente vehículo si el tipo no es válido
        }

        if ($estado === 'estacionado') {
            $parqueadero->estacionar($vehiculo);
        } elseif ($estado === 'retirado') {
            $parqueadero->retirar($vehiculo);
        }
    }
} else {
    echo "No se encontró el arreglo 'Vehiculos' en el JSON.";
}