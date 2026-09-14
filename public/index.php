<?php

/**
 * Front controller del módulo de Pedidos de OrderSync.
 *
 * Todas las peticiones pasan por este único punto de entrada, que:
 *  1) Carga (autoload) las clases del proyecto (sin Composer: un autoloader
 *     PSR-4 simple, mapeando el namespace OrderSync\ a la carpeta src/).
 *  2) Enruta según el método HTTP y la ruta solicitada.
 *  3) Delega en el controlador correspondiente.
 */

spl_autoload_register(function (string $clase): void {
    $prefijo = 'OrderSync\\';
    if (!str_starts_with($clase, $prefijo)) {
        return;
    }
    $rutaRelativa = str_replace('\\', '/', substr($clase, strlen($prefijo)));
    $rutaArchivo = __DIR__ . '/../src/' . $rutaRelativa . '.php';
    if (file_exists($rutaArchivo)) {
        require $rutaArchivo;
    }
});

use OrderSync\Controladores\PedidoControlador;

$metodo = $_SERVER['REQUEST_METHOD'];
$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ruta = rtrim($ruta, '/');
if ($ruta === '') {
    $ruta = '/';
}

$controlador = new PedidoControlador();

if ($ruta === '/' || $ruta === '/pedidos') {
    if ($metodo === 'GET') {
        $controlador->index();
    } elseif ($metodo === 'POST') {
        $controlador->crear();
    } else {
        http_response_code(405);
        echo 'Método no permitido';
    }
} elseif ($ruta === '/pedidos/estado' && $metodo === 'POST') {
    $controlador->actualizarEstado();
} else {
    http_response_code(404);
    echo 'Ruta no encontrada: ' . htmlspecialchars($ruta);
}
