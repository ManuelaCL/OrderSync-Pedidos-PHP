<?php

namespace OrderSync\Controladores;

use OrderSync\Modelos\PedidoRepository;

/**
 * Controlador del módulo de Pedidos: recibe la petición ya enrutada por
 * public/index.php, ejecuta la operación correspondiente sobre el
 * repositorio y renderiza la vista.
 */
class PedidoControlador
{
    private PedidoRepository $repositorio;

    public function __construct()
    {
        $this->repositorio = new PedidoRepository();
    }

    /** GET /pedidos — lista los pedidos existentes y muestra el formulario de creación. */
    public function index(array $datosVista = []): void
    {
        $datosVista['pedidos'] = $this->repositorio->listarTodos();
        $datosVista['clientes'] = $this->repositorio->listarClientes();
        $datosVista['usuarios'] = $this->repositorio->listarUsuarios();
        $datosVista['estados'] = PedidoRepository::estadosValidos();

        $this->renderizar($datosVista);
    }

    /** POST /pedidos — crea un nuevo pedido a partir de los datos del formulario. */
    public function crear(): void
    {
        $idCliente = filter_input(INPUT_POST, 'id_cliente', FILTER_VALIDATE_INT);
        $idUsuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $total = filter_input(INPUT_POST, 'total', FILTER_VALIDATE_FLOAT);

        if (!$idCliente || !$idUsuario || $total === false || $total === null || $total < 0) {
            $this->index(['error' => 'Cliente, usuario y total son obligatorios, y el total no puede ser negativo.']);
            return;
        }

        $this->repositorio->crear($idCliente, $idUsuario, $total);

        header('Location: /pedidos?creado=1');
        exit;
    }

    /** POST /pedidos/estado — actualiza el estado de un pedido existente. */
    public function actualizarEstado(): void
    {
        $idPedido = filter_input(INPUT_POST, 'id_pedido', FILTER_VALIDATE_INT);
        $nuevoEstado = $_POST['estado'] ?? '';

        if (!$idPedido || !in_array($nuevoEstado, PedidoRepository::estadosValidos(), true)) {
            $this->index(['error' => 'No se pudo actualizar el estado: datos inválidos.']);
            return;
        }

        $this->repositorio->actualizarEstado($idPedido, $nuevoEstado);

        header('Location: /pedidos?actualizado=1');
        exit;
    }

    private function renderizar(array $datosVista): void
    {
        extract($datosVista);
        require __DIR__ . '/../vistas/pedidos.php';
    }
}
