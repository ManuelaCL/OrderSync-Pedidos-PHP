<?php

namespace OrderSync\Modelos;

/**
 * Entidad que representa un registro de la tabla pedidos, enriquecido con
 * el nombre del cliente y del usuario (vendedor) para mostrarlo en la vista.
 */
class Pedido
{
    public int $idPedido;
    public int $idCliente;
    public int $idUsuario;
    public string $fechaPedido;
    public string $estado;
    public float $total;
    public ?string $nombreCliente = null;
    public ?string $nombreUsuario = null;

    public static function desdeFila(array $fila): self
    {
        $pedido = new self();
        $pedido->idPedido = (int) $fila['id_pedido'];
        $pedido->idCliente = (int) $fila['id_cliente'];
        $pedido->idUsuario = (int) $fila['id_usuario'];
        $pedido->fechaPedido = $fila['fecha_pedido'];
        $pedido->estado = $fila['estado'];
        $pedido->total = (float) $fila['total'];
        $pedido->nombreCliente = $fila['nombre_cliente'] ?? null;
        $pedido->nombreUsuario = $fila['nombre_usuario'] ?? null;
        return $pedido;
    }
}
