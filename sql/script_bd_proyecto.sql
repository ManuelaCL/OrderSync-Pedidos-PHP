-- MySQL Workbench Forward Engineering
-- Evidencia GA6-220501096-AA2-EV03. Script bases de datos del proyecto.
-- Modelo físico: gestion_pedidos (Sistema de gestión de pedidos para emprendimientos)
-- Autor: Manuela Cañas López

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema gestion_pedidos
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `gestion_pedidos`;
CREATE SCHEMA IF NOT EXISTS `gestion_pedidos` DEFAULT CHARACTER SET utf8mb4 ;
USE `gestion_pedidos` ;

-- -----------------------------------------------------
-- Table `gestion_pedidos`.`clientes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_pedidos`.`clientes` (
  `id_cliente` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(15) NOT NULL,
  `correo` VARCHAR(100) NULL,
  `direccion` VARCHAR(150) NULL,
  `fecha_registro` DATE NOT NULL DEFAULT (CURRENT_DATE),
  PRIMARY KEY (`id_cliente`),
  UNIQUE INDEX `correo_UNIQUE` (`correo` ASC) VISIBLE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `gestion_pedidos`.`categorias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_pedidos`.`categorias` (
  `id_categoria` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_categoria`),
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) VISIBLE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `gestion_pedidos`.`productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_pedidos`.`productos` (
  `id_producto` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` VARCHAR(255) NULL,
  `precio` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `id_categoria` INT NULL,
  PRIMARY KEY (`id_producto`),
  INDEX `fk_productos_categoria_idx` (`id_categoria` ASC) VISIBLE,
  CONSTRAINT `fk_productos_categoria`
    FOREIGN KEY (`id_categoria`)
    REFERENCES `gestion_pedidos`.`categorias` (`id_categoria`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `chk_productos_precio` CHECK (`precio` >= 0))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `gestion_pedidos`.`usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_pedidos`.`usuarios` (
  `id_usuario` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `correo` VARCHAR(100) NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL,
  `rol` ENUM('administrador', 'vendedor') NOT NULL DEFAULT 'vendedor',
  PRIMARY KEY (`id_usuario`),
  UNIQUE INDEX `correo_UNIQUE` (`correo` ASC) VISIBLE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `gestion_pedidos`.`pedidos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_pedidos`.`pedidos` (
  `id_pedido` INT NOT NULL AUTO_INCREMENT,
  `id_cliente` INT NOT NULL,
  `id_usuario` INT NOT NULL,
  `fecha_pedido` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` ENUM('pendiente', 'en_preparacion', 'enviado', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente',
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pedido`),
  INDEX `fk_pedidos_cliente_idx` (`id_cliente` ASC) VISIBLE,
  INDEX `fk_pedidos_usuario_idx` (`id_usuario` ASC) VISIBLE,
  CONSTRAINT `fk_pedidos_cliente`
    FOREIGN KEY (`id_cliente`)
    REFERENCES `gestion_pedidos`.`clientes` (`id_cliente`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `gestion_pedidos`.`usuarios` (`id_usuario`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `gestion_pedidos`.`detalle_pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_pedidos`.`detalle_pedido` (
  `id_detalle` INT NOT NULL AUTO_INCREMENT,
  `id_pedido` INT NOT NULL,
  `id_producto` INT NOT NULL,
  `cantidad` INT NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  INDEX `fk_detalle_pedido_idx` (`id_pedido` ASC) VISIBLE,
  INDEX `fk_detalle_producto_idx` (`id_producto` ASC) VISIBLE,
  CONSTRAINT `fk_detalle_pedido`
    FOREIGN KEY (`id_pedido`)
    REFERENCES `gestion_pedidos`.`pedidos` (`id_pedido`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_producto`
    FOREIGN KEY (`id_producto`)
    REFERENCES `gestion_pedidos`.`productos` (`id_producto`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `chk_detalle_cantidad` CHECK (`cantidad` > 0))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `gestion_pedidos`.`pagos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gestion_pedidos`.`pagos` (
  `id_pago` INT NOT NULL AUTO_INCREMENT,
  `id_pedido` INT NOT NULL,
  `fecha_pago` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metodo_pago` ENUM('efectivo', 'transferencia', 'tarjeta') NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id_pago`),
  UNIQUE INDEX `id_pedido_UNIQUE` (`id_pedido` ASC) VISIBLE,
  CONSTRAINT `fk_pagos_pedido`
    FOREIGN KEY (`id_pedido`)
    REFERENCES `gestion_pedidos`.`pedidos` (`id_pedido`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
