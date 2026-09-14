# OrderSync — Módulo de Pedidos (PHP, MVC)

Evidencia GA7-220501096-AA3-EV01. Codificación de módulos del software
stand-alone, web y móvil.

## Descripción

Módulo web del proyecto **OrderSync**, correspondiente a la gestión de
pedidos (tabla `pedidos` de la base de datos `gestion_pedidos`, definida en
la evidencia GA6-220501096-AA2-EV03). Implementado en **PHP**, siguiendo el
patrón MVC (front controller + controlador + repositorio/modelo + vista)
visto en el componente formativo "Servicios web con PHP", sin depender de
un framework de terceros: el enrutamiento, la inyección de dependencias y
el autoload (PSR-4 simplificado) están hechos a mano, para mantener el
proyecto autocontenido.

Funcionalidades:

- Listar los pedidos existentes, con el nombre del cliente y del vendedor
  (JOIN con `clientes` y `usuarios`).
- Registrar un nuevo pedido desde un formulario web (cliente, vendedor,
  total), con estado inicial `pendiente`.
- Actualizar el estado de un pedido (`pendiente` → `en_preparacion` →
  `enviado` → `entregado`, o `cancelado`) desde la misma vista.

## Estructura del proyecto

```
public/
└── index.php                        # Front controller: autoload + enrutador

src/
├── Database.php                     # Conexión PDO a gestion_pedidos (singleton)
├── Controladores/
│   └── PedidoControlador.php        # index(), crear(), actualizarEstado()
├── Modelos/
│   ├── Pedido.php                   # Entidad
│   └── PedidoRepository.php         # Acceso a datos (PDO): listarTodos, crear, actualizarEstado
└── vistas/
    └── pedidos.php                  # Formulario + tabla de pedidos
```

## Rutas

| Método | Ruta             | Acción                                   |
|--------|------------------|-------------------------------------------|
| GET    | `/pedidos`       | Lista los pedidos y muestra el formulario |
| POST   | `/pedidos`       | Crea un nuevo pedido                      |
| POST   | `/pedidos/estado`| Actualiza el estado de un pedido          |

## Requisitos

- PHP 8.1+ con las extensiones `pdo` y `pdo_mysql`.
- La base de datos `gestion_pedidos` ya creada (ver
  `sql/script_bd_proyecto.sql`) con el usuario `ordersync` (ver evidencia
  GA7-220501096-AA2-EV01 para su creación), y al menos un registro en
  `clientes` y en `usuarios` para poder crear un pedido desde el formulario.

## Ejecutar en local

```bash
php -S localhost:8001 -t public public/index.php
```

Y abrir `http://localhost:8001/pedidos` en el navegador.

## Prueba funcional realizada

El módulo fue ejecutado con el servidor embebido de PHP y probado por línea
de comandos (`curl`) y desde un navegador real (Playwright), creando
pedidos y cambiando su estado, con verificación directa en la base de
datos. El detalle completo, incluyendo un bug real de codificación de
caracteres que se encontró y corrigió durante la prueba, está en
`prueba_funcional.log`. Las capturas de pantalla están en `capturas/`.
