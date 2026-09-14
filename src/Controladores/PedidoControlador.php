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

        // Validaciones diferenciadas por campo: cada una devuelve un mensaje
        // específico, para que la causa del rechazo sea clara tanto para el
        // usuario como en las pruebas de validación de la evidencia
        // GA7-220501096-AA3-EV02.
        if (!$idCliente) {
            $this->index(['error' => 'Debe seleccionar un cliente.']);
            return;
        }
        if (!$idUsuario) {
            $this->index(['error' => 'Debe seleccionar un vendedor.']);
            return;
        }
        if ($total === false || $total === null) {
            $this->index(['error' => 'El total debe ser un valor numérico válido.']);
            return;
        }
        if ($total < 0) {
            $this->index(['error' => 'El total no puede ser negativo.']);
            return;
        }
        if ($total > 99999999.99) {
            $this->index(['error' => 'El total supera la longitud máxima permitida (8 dígitos enteros, 2 decimales).']);
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

        if (!$idPedido) {
            $this->index(['error' => 'El identificador del pedido no es válido.']);
            return;
        }
        if (!in_array($nuevoEstado, PedidoRepository::estadosValidos(), true)) {
            $this->index(['error' => 'El estado enviado no es uno de los estados permitidos.']);
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
}<?php

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

        // Validaciones diferenciadas por campo: cada una devuelve un mensaje
        // específico, para que la causa del rechazo sea clara tanto para el
        // usuario como en las pruebas de validación de la evidencia
        // GA7-220501096-AA3-EV02.
        if (!$idCliente) {
            $this->index(['error' => 'Debe seleccionar un cliente.']);
            return;
        }
        if (!$idUsuario) {
            $this->index(['error' => 'Debe seleccionar un vendedor.']);
            return;
        }
        if ($total === false || $total === null) {
            $this->index(['error' => 'El total debe ser un valor numérico válido.']);
            return;
        }
        if ($total < 0) {
            $this->index(['error' => 'El total no puede ser negativo.']);
            return;
        }
        if ($total > 99999999.99) {
            $this->index(['error' => 'El total supera la longitud máxima permitida (8 dígitos enteros, 2 decimales).']);
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

        if (!$idPedido) {
            $this->index(['error' => 'El identificador del pedido no es válido.']);
            return;
        }
        if (!in_array($nuevoEstado, PedidoRepository::estadosValidos(), true)) {
            $this->index(['error' => 'El estado enviado no es uno de los estados permitidos.']);
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