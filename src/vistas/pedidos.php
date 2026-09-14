<?php
/** @var array $pedidos */
/** @var array $clientes */
/** @var array $usuarios */
/** @var array $estados */

$etiquetasEstado = [
    'pendiente' => 'Pendiente',
    'en_preparacion' => 'En preparación',
    'enviado' => 'Enviado',
    'entregado' => 'Entregado',
    'cancelado' => 'Cancelado',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OrderSync - Módulo de Pedidos</title>
    <style>
        body { font-family: Arial, sans-serif; background: #F4F1F8; margin: 0; padding: 24px; color: #2B2130; }
        h1 { color: #6A1B9A; margin-bottom: 4px; }
        .subtitulo { color: #6B6070; margin-top: 0; margin-bottom: 24px; }
        .contenedor { display: flex; gap: 24px; flex-wrap: wrap; }
        .tarjeta { background: #FFFFFF; border-radius: 10px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.12); }
        .tarjeta-form { flex: 1 1 280px; max-width: 340px; }
        .tarjeta-lista { flex: 2 1 560px; }
        label { display: block; margin-top: 12px; font-size: 14px; font-weight: bold; color: #4A3B57; }
        select, input[type=number] {
            width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #D8CEE2; border-radius: 6px; box-sizing: border-box;
        }
        button {
            margin-top: 18px; background: #6A1B9A; color: #FFFFFF; border: none; padding: 10px 18px;
            border-radius: 6px; font-size: 14px; cursor: pointer;
        }
        button:hover { background: #55157E; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #EDE6F2; font-size: 14px; vertical-align: middle; }
        th { color: #6A1B9A; }
        .mensaje-exito { background: #E8F5E9; color: #2E7D32; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; }
        .mensaje-error { background: #FDECEA; color: #B3261E; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; }
        .vacio { color: #8A7E96; font-style: italic; }
        .badge { padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-pendiente { background: #FFF3CD; color: #8A6D00; }
        .badge-en_preparacion { background: #E3E0FB; color: #4A3AA8; }
        .badge-enviado { background: #D6EAF8; color: #1B5E8C; }
        .badge-entregado { background: #D9F2DE; color: #1E7B34; }
        .badge-cancelado { background: #FBE0E0; color: #A63333; }
        .fila-accion { display: flex; gap: 6px; align-items: center; }
        .fila-accion select { width: auto; }
        .fila-accion button { margin-top: 0; padding: 6px 10px; font-size: 12px; }
    </style>
</head>
<body>

    <h1>OrderSync</h1>
    <p class="subtitulo">Módulo de Pedidos — PHP con arquitectura MVC (front controller + PDO)</p>

    <?php if (isset($_GET['creado'])): ?>
        <div class="mensaje-exito">Pedido creado correctamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['actualizado'])): ?>
        <div class="mensaje-exito">Estado del pedido actualizado correctamente.</div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="contenedor">

        <div class="tarjeta tarjeta-form">
            <h2>Nuevo pedido</h2>
            <form action="/pedidos" method="post">
                <label for="id_cliente">Cliente *</label>
                <select id="id_cliente" name="id_cliente" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= (int) $cliente['id_cliente'] ?>"><?= htmlspecialchars($cliente['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="id_usuario">Vendedor *</label>
                <select id="id_usuario" name="id_usuario" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= (int) $usuario['id_usuario'] ?>"><?= htmlspecialchars($usuario['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="total">Total *</label>
                <input type="number" id="total" name="total" step="0.01" min="0" required>

                <button type="submit">Registrar pedido</button>
            </form>
        </div>

        <div class="tarjeta tarjeta-lista">
            <h2>Pedidos registrados</h2>
            <?php if (empty($pedidos)): ?>
                <p class="vacio">Todavía no hay pedidos registrados.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Cambiar estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?= $pedido->idPedido ?></td>
                                <td><?= htmlspecialchars($pedido->nombreCliente) ?></td>
                                <td><?= htmlspecialchars($pedido->nombreUsuario) ?></td>
                                <td><?= htmlspecialchars($pedido->fechaPedido) ?></td>
                                <td>$<?= number_format($pedido->total, 2) ?></td>
                                <td><span class="badge badge-<?= $pedido->estado ?>"><?= $etiquetasEstado[$pedido->estado] ?></span></td>
                                <td>
                                    <form class="fila-accion" action="/pedidos/estado" method="post">
                                        <input type="hidden" name="id_pedido" value="<?= $pedido->idPedido ?>">
                                        <select name="estado">
                                            <?php foreach ($estados as $estado): ?>
                                                <option value="<?= $estado ?>" <?= $estado === $pedido->estado ? 'selected' : '' ?>>
                                                    <?= $etiquetasEstado[$estado] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit">Actualizar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
