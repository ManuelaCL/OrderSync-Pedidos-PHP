<?php

namespace OrderSync\Modelos;

use OrderSync\Database;
use PDO;

/**
 * Repositorio (capa de acceso a datos) del módulo de Pedidos: encapsula las
 * consultas PDO sobre las tablas pedidos, clientes y usuarios.
 */
class PedidoRepository
{
    private const ESTADOS_VALIDOS = ['pendiente', 'en_preparacion', 'enviado', 'entregado', 'cancelado'];

    public function listarTodos(): array
    {
        $sql = "SELECT p.id_pedido, p.id_cliente, p.id_usuario, p.fecha_pedido, p.estado, p.total,
                       c.nombre AS nombre_cliente, u.nombre AS nombre_usuario
                FROM pedidos p
                INNER JOIN clientes c ON c.id_cliente = p.id_cliente
                INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
                ORDER BY p.fecha_pedido DESC";

        $conexion = Database::obtenerConexion();
        $filas = $conexion->query($sql)->fetchAll();

        return array_map(fn(array $fila) => Pedido::desdeFila($fila), $filas);
    }

    public function crear(int $idCliente, int $idUsuario, float $total): int
    {
        $sql = "INSERT INTO pedidos (id_cliente, id_usuario, estado, total) VALUES (:id_cliente, :id_usuario, 'pendiente', :total)";

        $conexion = Database::obtenerConexion();
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute([
            ':id_cliente' => $idCliente,
            ':id_usuario' => $idUsuario,
            ':total' => $total,
        ]);

        return (int) $conexion->lastInsertId();
    }

    public function actualizarEstado(int $idPedido, string $nuevoEstado): bool
    {
        if (!in_array($nuevoEstado, self::ESTADOS_VALIDOS, true)) {
            throw new \InvalidArgumentException("Estado no válido: {$nuevoEstado}");
        }

        $sql = "UPDATE pedidos SET estado = :estado WHERE id_pedido = :id_pedido";

        $conexion = Database::obtenerConexion();
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute([
            ':estado' => $nuevoEstado,
            ':id_pedido' => $idPedido,
        ]);

        return $sentencia->rowCount() > 0;
    }

    /** Lista simple de clientes (id, nombre), para el combo del formulario. */
    public function listarClientes(): array
    {
        $conexion = Database::obtenerConexion();
        return $conexion->query("SELECT id_cliente, nombre FROM clientes ORDER BY nombre")->fetchAll();
    }

    /** Lista simple de usuarios (id, nombre), para el combo del formulario. */
    public function listarUsuarios(): array
    {
        $conexion = Database::obtenerConexion();
        return $conexion->query("SELECT id_usuario, nombre FROM usuarios ORDER BY nombre")->fetchAll();
    }

    public static function estadosValidos(): array
    {
        return self::ESTADOS_VALIDOS;
    }
}
