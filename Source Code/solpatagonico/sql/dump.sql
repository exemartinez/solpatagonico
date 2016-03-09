-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Servidor: localhost
-- Tiempo de generación: 18-12-2008 a las 11:40:35
-- Versión del servidor: 5.0.27
-- Versión de PHP: 5.2.0
-- 
-- Base de datos: `solpatagonico`
-- 

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `cliente`
-- 

CREATE TABLE `cliente` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `cuit` varchar(13) NOT NULL,
  `razonsocial` varchar(255) NOT NULL,
  `nroiibb` int(11) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cuit` (`cuit`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- 
-- Volcar la base de datos para la tabla `cliente`
-- 

INSERT INTO `cliente` (`id`, `cuit`, `razonsocial`, `nroiibb`, `direccion`, `telefono`, `fecha_alta`, `fecha_baja`) VALUES 
(1, '27224032655', 'Perez Juan', 224032651, 'Rosario 294', '46129954', '2008-12-10 22:57:11', NULL),
(2, '27234053545', 'Garcia Federico', 2147483647, 'Viel 123', '46314132', '2008-12-10 22:59:15', NULL),
(3, '27204032659', 'Gimenez Anibal', 2147483647, 'Doblas 234', '46918329', '2008-12-10 23:01:57', NULL),
(4, '27194032655', 'Martinez Leonardo', 2147483647, 'Guayaquil 234', '49023456', '2008-12-10 23:03:43', NULL),
(5, '26204032601', 'Guevara Federica', 2147483647, 'Jose Maria Moreno 234', '49023405', '2008-12-10 23:05:01', NULL),
(6, '24304545638', 'Pereyra Isidoro', 2147483647, 'Lafuente 2341', '46323219', '2008-12-10 23:07:23', NULL),
(7, '20405983215', 'Bloise Patricia', 2147483647, 'Av.La  Plata 10', '45612345', '2008-12-10 23:10:16', NULL),
(8, '20345243004', 'Gutierrez Federico', 2147483647, 'Curapaligue 234', '49023496', '2008-12-10 23:17:36', NULL),
(9, '27282322655', 'Cattaneo', 11111, '3 de febrero 4517', '43431355', '2008-12-11 21:30:43', '2008-12-15 23:41:00');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `cuenta`
-- 

CREATE TABLE `cuenta` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `cbu` varchar(22) NOT NULL,
  `id_tipo` bigint(20) NOT NULL,
  `banco` varchar(255) default NULL,
  `sucursal` varchar(100) default NULL,
  `id_proveedor` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_tipo` (`id_tipo`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `cbu` (`cbu`),
  KEY `id_tipo_cuenta` (`id_tipo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Volcar la base de datos para la tabla `cuenta`
-- 

INSERT INTO `cuenta` (`id`, `cbu`, `id_tipo`, `banco`, `sucursal`, `id_proveedor`) VALUES 
(1, '1844674407370955161520', 2, 'Santander Rio', 'Palermo', 2),
(2, '1844674407370955161500', 1, 'Boston', 'Casa Central', 3),
(3, '1844674407370955161510', 2, 'Citibank', 'Devoto', 1);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `estado_item`
-- 

CREATE TABLE `estado_item` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- 
-- Volcar la base de datos para la tabla `estado_item`
-- 

INSERT INTO `estado_item` (`id`, `nombre`) VALUES 
(1, 'Pendiente inicial'),
(2, 'Pendiente stock'),
(3, 'Reservado'),
(4, 'En curso'),
(5, 'Entregado'),
(6, 'Facturado'),
(7, 'Baja');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `estado_pedido`
-- 

CREATE TABLE `estado_pedido` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- 
-- Volcar la base de datos para la tabla `estado_pedido`
-- 

INSERT INTO `estado_pedido` (`id`, `nombre`) VALUES 
(2, 'Pendiente stock'),
(3, 'Reservado'),
(4, 'En curso'),
(5, 'Entregado'),
(6, 'Facturado'),
(7, 'Baja');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `estado_producto`
-- 

CREATE TABLE `estado_producto` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- 
-- Volcar la base de datos para la tabla `estado_producto`
-- 

INSERT INTO `estado_producto` (`id`, `nombre`) VALUES 
(6, 'Habilitado'),
(7, 'No Habilitado');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `item_pedido`
-- 

CREATE TABLE `item_pedido` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_remito` bigint(20) unsigned NOT NULL,
  `id_estado_item` bigint(20) unsigned NOT NULL,
  `id_pedido` bigint(20) unsigned NOT NULL,
  `cantidad` int(11) NOT NULL,
  `id_producto` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_remito` (`id_remito`),
  KEY `id_estado_item` (`id_estado_item`),
  KEY `id_pedido` (`id_pedido`),
  KEY `id_producto` (`id_producto`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- 
-- Volcar la base de datos para la tabla `item_pedido`
-- 

INSERT INTO `item_pedido` (`id`, `id_remito`, `id_estado_item`, `id_pedido`, `cantidad`, `id_producto`) VALUES 
(1, 1, 4, 1, 10, 6),
(2, 0, 2, 1, 400, 5),
(3, 0, 4, 2, 12, 10),
(4, 3, 6, 3, 20, 4),
(5, 2, 5, 4, 2, 7),
(6, 2, 5, 4, 4, 6),
(7, 0, 3, 5, 30, 1),
(8, 4, 4, 6, 2, 6);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `lista_precio_vta`
-- 

CREATE TABLE `lista_precio_vta` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `precio` decimal(10,2) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime default NULL,
  `id_producto` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_producto` (`id_producto`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- 
-- Volcar la base de datos para la tabla `lista_precio_vta`
-- 

INSERT INTO `lista_precio_vta` (`id`, `precio`, `fecha_alta`, `fecha_baja`, `id_producto`) VALUES 
(1, 20.00, '2008-12-15 23:36:43', NULL, 6),
(2, 10.00, '2008-12-15 23:37:16', NULL, 3),
(3, 5.00, '2008-12-15 23:37:40', NULL, 1),
(4, 10.00, '2008-12-15 23:37:50', '2008-12-15 23:38:12', 5),
(5, 50.00, '2008-12-15 23:38:12', NULL, 5),
(6, 10.00, '2008-12-15 23:38:49', NULL, 4),
(7, 20.00, '2008-12-15 23:39:30', NULL, 10),
(8, 20.00, '2008-12-15 23:39:41', NULL, 8),
(9, 10.00, '2008-12-15 23:39:55', NULL, 9),
(10, 20.00, '2008-12-15 23:40:10', NULL, 2),
(11, 10.00, '2008-12-15 23:40:23', NULL, 7),
(12, 2.00, '2008-12-17 00:11:05', NULL, 11);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `pedido`
-- 

CREATE TABLE `pedido` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_cliente` bigint(20) unsigned NOT NULL,
  `id_sucursal` bigint(20) unsigned NOT NULL,
  `id_estado_pedido` bigint(20) unsigned NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_sucursal` (`id_sucursal`),
  KEY `id_estado_pedido` (`id_estado_pedido`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- 
-- Volcar la base de datos para la tabla `pedido`
-- 

INSERT INTO `pedido` (`id`, `id_cliente`, `id_sucursal`, `id_estado_pedido`, `fecha_alta`, `fecha_baja`) VALUES 
(1, 8, 2, 2, '2008-12-11 00:46:49', NULL),
(2, 7, 3, 4, '2008-12-11 01:33:49', NULL),
(3, 6, 4, 6, '2008-12-11 01:35:15', NULL),
(4, 5, 5, 5, '2008-12-11 01:36:56', NULL),
(5, 8, 1, 3, '2008-12-11 01:37:51', NULL),
(6, 8, 2, 4, '2008-12-18 11:36:26', NULL);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `producto`
-- 

CREATE TABLE `producto` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `codigo` varchar(100) NOT NULL,
  `id_estado_producto` bigint(20) unsigned NOT NULL,
  `id_rubro` bigint(20) unsigned NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `presentacion` varchar(100) default NULL,
  `punto_reposicion` int(11) NOT NULL,
  `cantidad_real` int(11) NOT NULL,
  `cantidad_reserva` int(11) NOT NULL,
  `ppp` decimal(10,2) default NULL,
  `comentario` text,
  `tamanio_pedido` int(11) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `id_estado_producto` (`id_estado_producto`),
  KEY `id_rubro` (`id_rubro`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- 
-- Volcar la base de datos para la tabla `producto`
-- 

INSERT INTO `producto` (`id`, `codigo`, `id_estado_producto`, `id_rubro`, `iva`, `nombre`, `descripcion`, `presentacion`, `punto_reposicion`, `cantidad_real`, `cantidad_reserva`, `ppp`, `comentario`, `tamanio_pedido`, `fecha_alta`, `fecha_baja`) VALUES 
(1, '0001', 6, 1, 21.00, 'Jabon LUX', 'pack 6 unidades', '6 unidades ', 20, 200, 30, 2.00, '', 40, '2008-12-10 23:42:24', NULL),
(2, '0002', 6, 1, 21.00, 'Shampu Sedal', '75ml', '20 unidades', 5, 700, 0, 2.86, '', 40, '2008-12-10 23:43:56', NULL),
(3, '1001', 6, 4, 21.00, 'Coca Cola Light', 'pack 6', 'pack 6', 10, 200, 0, 2.00, '', 30, '2008-12-10 23:44:58', NULL),
(4, '1002', 6, 4, 21.00, 'Latitas V', 'Energizante', 'pack 8', 10, 200, 0, 2.00, '', 30, '2008-12-10 23:45:40', NULL),
(5, '2001', 6, 3, 21.00, 'Juego Sabanas ', '1 plaza', 'paquete 10 unidades', 20, 300, 0, 2.67, '', 40, '2008-12-10 23:48:00', NULL),
(6, '2002', 6, 3, 21.00, 'Acolchado', '1 plaza', '10 unidades', 20, 498, 0, 2.60, '', 40, '2008-12-10 23:49:18', NULL),
(7, '2003', 6, 3, 21.00, 'Toallas-Toallones', '5 toalllas-5 toallones', 'juego de 5', 20, 200, 0, 2.00, '', 40, '2008-12-10 23:50:21', NULL),
(8, '3001', 6, 2, 21.00, 'Limpia Muebles', 'Botellon 10 litros', '5x10', 10, 200, 0, 2.00, '', 30, '2008-12-10 23:51:25', NULL),
(9, '3002', 6, 2, 21.00, 'Limpia Vidrios', '', 'Botellon 5 listros', 20, 0, 0, NULL, '', 40, '2008-12-11 00:04:21', NULL),
(10, '3003', 6, 2, 21.00, 'Limpia Piso', 'Botellon 5 listros', '5x10', 20, 200, 0, 2.00, '', 40, '2008-12-11 00:05:58', NULL),
(11, '0003', 6, 1, 21.00, 'Perfume Mujercitas', 'Frasco 75 mm', 'frasco 75 mm', 5, 400, 0, 1.50, '', 1, '2008-12-17 01:10:42', NULL);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `proveedor`
-- 

CREATE TABLE `proveedor` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `cuit` varchar(13) NOT NULL,
  `razonsocial` varchar(255) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fax` varchar(20) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `contacto` varchar(255) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cuit` (`cuit`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Volcar la base de datos para la tabla `proveedor`
-- 

INSERT INTO `proveedor` (`id`, `cuit`, `razonsocial`, `direccion`, `cp`, `telefono`, `fax`, `mail`, `contacto`, `fecha_alta`, `fecha_baja`) VALUES 
(1, '20345672819', 'Jumbo', 'Av.Roca 2333', '', '48024329', '48024329', 'jumbo@gmail.com', 'Ricardi Federico', '2008-12-11 00:11:50', NULL),
(2, '29323457822', 'Carrefour', 'Av.La Plata 1230', '', '49023456', '49023456', 'carrefour@gmail.com', 'Brenda Colarte', '2008-12-11 00:14:18', NULL),
(3, '34564453546', 'Coto', 'Acoyte 24', '', '49023456', '49023456', 'coto@gmail.com', 'Oscar Cumpe', '2008-12-11 00:16:14', NULL);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `proveedor_producto`
-- 

CREATE TABLE `proveedor_producto` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_producto` bigint(20) unsigned NOT NULL,
  `id_proveedor` bigint(20) unsigned NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_producto` (`id_producto`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- 
-- Volcar la base de datos para la tabla `proveedor_producto`
-- 

INSERT INTO `proveedor_producto` (`id`, `id_producto`, `id_proveedor`, `precio`) VALUES 
(1, 6, 2, 2.00),
(2, 5, 2, 2.00),
(3, 7, 2, 2.00),
(4, 3, 3, 2.00),
(5, 4, 3, 2.00),
(6, 1, 1, 2.00),
(7, 8, 1, 2.00),
(8, 10, 1, 2.00),
(9, 9, 1, 2.00),
(10, 9, 3, 2.00);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `remito_factura`
-- 

CREATE TABLE `remito_factura` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `fecha_remito` date NOT NULL,
  `nro_factura` varchar(40) NOT NULL,
  `fecha_factura` date NOT NULL,
  `patente` char(6) NOT NULL,
  `dni_transportista` varchar(10) NOT NULL,
  `fecha_entrega` date NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `patente` (`patente`,`dni_transportista`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Volcar la base de datos para la tabla `remito_factura`
-- 

INSERT INTO `remito_factura` (`id`, `fecha_remito`, `nro_factura`, `fecha_factura`, `patente`, `dni_transportista`, `fecha_entrega`) VALUES 
(1, '2008-12-11', '', '0000-00-00', 'AHH012', '25096468', '0000-00-00'),
(2, '2008-12-11', '', '0000-00-00', 'AIJ817', '27659593', '0000-00-00'),
(3, '2008-12-11', '00012', '2008-12-11', 'AHH012', '25096468', '0000-00-00'),
(4, '2008-12-18', '', '0000-00-00', 'AHH012', '25096468', '0000-00-00');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `rubro`
-- 

CREATE TABLE `rubro` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Volcar la base de datos para la tabla `rubro`
-- 

INSERT INTO `rubro` (`id`, `nombre`) VALUES 
(1, 'Tocador'),
(2, 'Limpieza'),
(3, 'Blanqueria'),
(4, 'Alimentos');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sucursal`
-- 

CREATE TABLE `sucursal` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_zona` bigint(20) unsigned NOT NULL,
  `id_cliente` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fax` varchar(20) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `contacto` varchar(255) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_zona` (`id_zona`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- 
-- Volcar la base de datos para la tabla `sucursal`
-- 

INSERT INTO `sucursal` (`id`, `id_zona`, `id_cliente`, `nombre`, `direccion`, `cp`, `telefono`, `fax`, `mail`, `contacto`, `fecha_alta`, `fecha_baja`) VALUES 
(1, 1, 8, 'Chacarita', 'Giribone 2430', '2345', '45515320', '45515320', '', 'Fabiana Infante', '2008-12-10 23:21:56', NULL),
(2, 4, 8, 'Balbaneda', 'Solis 23', '1234', '43012324', '43012324', '', 'Sergio Casagrande', '2008-12-10 23:25:15', NULL),
(3, 4, 7, 'Caballito', 'Pumacahua 33', '1426', '49016278', '49016278', '', 'Luis Alegre', '2008-12-10 23:32:49', NULL),
(4, 4, 6, 'Floresta', 'Quirno 1016', '1405', '46128874', '46128874', '', 'Ismael Ronconi', '2008-12-10 23:34:04', NULL),
(5, 3, 5, 'Haedo', 'Av.de Mayo 342', '1546', '48323420', '48323420', '', 'Agustin Champagna', '2008-12-10 23:35:42', NULL),
(6, 4, 4, 'Devoto', 'Bahia Blanca 2345', '1672', '45664793', '45664793', '', 'Viviana Quiroga', '2008-12-10 23:36:48', NULL),
(7, 4, 9, 'Caballito', 'San Juan 23', '1426', '46125543', '', '', '', '2008-12-11 21:48:51', NULL);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_actions`
-- 

CREATE TABLE `sys_actions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `accion` varchar(255) NOT NULL default '',
  `valores` varchar(255) NOT NULL default '',
  `descripcion` text NOT NULL,
  `owner_id` bigint(20) unsigned default NULL,
  `owner_ip` varchar(20) default NULL,
  `owner_date` datetime default NULL,
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `action` (`accion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Volcar la base de datos para la tabla `sys_actions`
-- 

INSERT INTO `sys_actions` (`id`, `accion`, `valores`, `descripcion`, `owner_id`, `owner_ip`, `owner_date`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(1, 'AdministrarStock', '1,0', 'Habilita a usuarios a realizar cambios sobre el stock actual e ingreso de nuevo stock. <br>\r\nSi <b>AdministrarStock</b>=1 Habilita a realizar cambios.<br>\r\nSi <b>AdministrarStock</b>=0 el usuario solo podrá visualizar datos.', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_config`
-- 

CREATE TABLE `sys_config` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `variable` varchar(255) NOT NULL default '',
  `valor` varchar(255) NOT NULL default '',
  `descripcion` text NOT NULL,
  `owner_id` bigint(20) unsigned default NULL,
  `owner_ip` varchar(20) default NULL,
  `owner_date` datetime default NULL,
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `variable` (`variable`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- 
-- Volcar la base de datos para la tabla `sys_config`
-- 

INSERT INTO `sys_config` (`id`, `variable`, `valor`, `descripcion`, `owner_id`, `owner_ip`, `owner_date`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(1, 'auto_logoff', '3600', 'tiempo maximo sin actividad para que expire la sesion. (en segundos)', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'maintenance', '0', '0 = sistema en funcionamiento normal\r\n1 = funcionamiento fuera de linea por mantenimiento\r\n(solo acceden los usuarios o grupos autorizados a saltear el mantenimiento)', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'pass_expires', '0', '1 = las contrase?as de todo el sistema expiran\r\n0 = las contrase?as expiraran segun configuracion individual de cada usuario', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'expiration_days', '0', 'cantidad de dias en que expirara la contrase?a, si es que pass_expires es seteado en 1', NULL, NULL, NULL, 2, '192.168.0.10', '2007-07-12 07:59:19'),
(5, 'inactive_account_expiration', '0', 'tiempo maximo de inactividad de una cuenta, una vez superado este limite, la cuenta se bloquea.\r\nexpresado en días.\r\n0 indica sin caducidad.', NULL, NULL, NULL, 2, '190.17.80.15', '2008-01-31 16:22:18'),
(6, 'pass_black_list', '', 'lista de palabras prohibidas para la contraseña.\r\ndeben estar separadas por coma (,).\r\nsi se desea prohibir una coma debe estar escapada por el caracter "". ej: ,', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'pass_block_history', '6', 'cantidad de contraseñas a considerar en la lista negra', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'pass_block_user_data', '1', 'indica si se incorporan los datos personales del usuario al black list para su contraseña.\r\n1 = se bloquean los datos\r\n0 = se permiten datos filiatorios en la contraseña', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'pass_format', '-6,a1,#1', 'brinda el formato valido para la contraseña:\r\na1 - indica al menos una letra minuscula.\r\na1 - indica al menos una letra mayuscula.\r\n#1 - indica al menos un numero.\r\n-1 - indica que la contraseña debe tener al menos un caracter.\r\n+10 - indica que la contraseña debe tener como maximo 10 caracteres.\r\nsa - indica que no se permitiran secuencias alfabeticas.\r\ns# - indica que no se permitiran secuencias numericas.\r\n^ - indica inicio combinando con a1, a1, #1\r\n$ - indica fin combinando con a1, a1, #1\r\n*todos los indicadores deben estar separados por comas (,)', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'wrong_pass_limit', '3', 'cantidad maxima de intentos fallidos de logueo.', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'log_activity', '1', '1 = Activa el logueo de actividades.\r\n0 = No activa el logueo de actividades.', 2, '192.168.0.10', '2007-07-12 08:06:11', 2, '192.168.0.10', '2007-07-12 08:06:16');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_groups`
-- 

CREATE TABLE `sys_groups` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `skip_maintenance` tinyint(4) unsigned NOT NULL default '0',
  `nombre` varchar(100) NOT NULL default '',
  `nota` text,
  `owner_id` bigint(20) unsigned default NULL,
  `owner_ip` varchar(20) default NULL,
  `owner_date` datetime default NULL,
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Volcar la base de datos para la tabla `sys_groups`
-- 

INSERT INTO `sys_groups` (`id`, `skip_maintenance`, `nombre`, `nota`, `owner_id`, `owner_ip`, `owner_date`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(1, 1, 'Sistemas', 'Grupo exclusivo para personal de E4System.', NULL, NULL, NULL, 2, '127.0.0.1', '2008-12-15 23:04:28'),
(4, 0, 'E4/Systems', '', 2, '192.168.0.10', '2007-07-12 08:00:57', NULL, NULL, NULL),
(5, 0, 'E3', '', 3, '192.168.0.22', '2007-08-01 08:56:36', 23, '190.139.165.49', '2008-06-07 17:00:24');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_groups_actions`
-- 

CREATE TABLE `sys_groups_actions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_grupo` bigint(20) unsigned NOT NULL default '0',
  `id_accion` bigint(20) unsigned NOT NULL default '0',
  `valor` varchar(255) default NULL,
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_sys_group` (`id_grupo`),
  KEY `id_sys_accion` (`id_accion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- 
-- Volcar la base de datos para la tabla `sys_groups_actions`
-- 

INSERT INTO `sys_groups_actions` (`id`, `id_grupo`, `id_accion`, `valor`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(1, 1, 1, '1', NULL, NULL, NULL),
(2, 4, 1, '1', NULL, NULL, NULL),
(8, 5, 1, '1', NULL, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_logs`
-- 

CREATE TABLE `sys_logs` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_usuario` bigint(20) unsigned default NULL,
  `usuario` varchar(255) default NULL,
  `tipo_entrada` varchar(255) NOT NULL default '',
  `fecha` bigint(20) unsigned NOT NULL default '0',
  `ip` varchar(20) default NULL,
  `user_agent` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

-- 
-- Volcar la base de datos para la tabla `sys_logs`
-- 

INSERT INTO `sys_logs` (`id`, `id_usuario`, `usuario`, `tipo_entrada`, `fecha`, `ip`, `user_agent`) VALUES 
(1, 2, 'manuel', '', 1228910937, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(2, 2, 'manuel', '', 1228910940, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(3, 2, 'manuel', '', 1228911504, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(4, 7, 'admin', '', 1228955510, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(5, 7, 'admin', '', 1228955899, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(6, 7, 'admin', '', 1228956944, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(7, 7, 'admin', '', 1228956957, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(8, 7, 'admin', '', 1228958612, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(9, 7, 'admin', '', 1228958629, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(10, 7, 'admin', '', 1228958648, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(11, 7, 'admin', '', 1228960996, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(12, 7, 'admin', '', 1228962307, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(13, 7, 'admin', '', 1228962324, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(14, 7, 'admin', '', 1228965023, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(15, 7, 'admin', '', 1228965307, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(16, 7, 'admin', '', 1228968136, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(17, 7, 'admin', '', 1229032519, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(18, 7, 'admin', '', 1229033021, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(19, 7, 'admin', '', 1229033079, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(20, 7, 'admin', '', 1229033648, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(21, 7, 'admin', '', 1229038033, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(22, 7, 'admin', '', 1229039102, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(23, 7, 'admin', '', 1229095918, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(24, 7, 'admin', '', 1229135514, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(25, 7, 'admin', '', 1229146493, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(26, 2, 'manuel', '', 1229369841, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(27, 7, 'admin', '', 1229372852, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(28, 2, 'manuel', '', 1229386993, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(29, 7, 'admin', '', 1229387557, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(30, 2, 'manuel', '', 1229387617, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(31, 2, 'manuel', '', 1229387633, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(32, 7, 'admin', '', 1229387690, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(33, 2, 'manuel', '', 1229387717, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(34, 7, 'admin', '', 1229388252, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(35, 2, 'manuel', '', 1229388269, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(36, 7, 'admin', '', 1229388352, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(37, 2, 'manuel', '', 1229388378, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(38, 2, 'manuel', '', 1229388491, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(39, 7, 'admin', '', 1229388508, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(40, 2, 'manuel', '', 1229389245, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(41, 2, 'manuel', '', 1229389809, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(42, 7, 'admin', '', 1229394837, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(43, 7, 'admin', '', 1229446662, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(44, 7, 'admin', '', 1229446717, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(45, 2, 'manuel', '', 1229446848, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(46, 7, 'admin', '', 1229447170, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(47, 7, 'admin', '', 1229477201, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(48, 7, 'admin', '', 1229480418, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(49, 7, 'admin', '', 1229483352, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(50, 7, 'admin', '', 1229483354, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(51, 7, 'admin', '', 1229484198, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(52, 7, 'admin', '', 1229550843, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),
(53, 7, 'admin', '', 1229607314, '127.0.0.1', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_permitions`
-- 

CREATE TABLE `sys_permitions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_grupo` bigint(20) unsigned NOT NULL default '0',
  `id_seccion` bigint(20) unsigned NOT NULL default '0',
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_sys_group` (`id_grupo`),
  KEY `id_sesion` (`id_seccion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1581 ;

-- 
-- Volcar la base de datos para la tabla `sys_permitions`
-- 

INSERT INTO `sys_permitions` (`id`, `id_grupo`, `id_seccion`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(1488, 5, 5, NULL, NULL, NULL),
(1487, 5, 4, NULL, NULL, NULL),
(1578, 1, 5, NULL, NULL, NULL),
(1577, 1, 4, NULL, NULL, NULL),
(1576, 1, 3, NULL, NULL, NULL),
(1575, 1, 2, NULL, NULL, NULL),
(1574, 1, 1, NULL, NULL, NULL),
(1573, 1, 8, NULL, NULL, NULL),
(1489, 5, 7, NULL, NULL, NULL),
(1531, 4, 5, NULL, NULL, NULL),
(1530, 4, 4, NULL, NULL, NULL),
(1572, 1, 48, NULL, NULL, NULL),
(1571, 1, 37, NULL, NULL, NULL),
(1570, 1, 33, NULL, NULL, NULL),
(1569, 1, 32, NULL, NULL, NULL),
(1568, 1, 45, NULL, NULL, NULL),
(1567, 1, 44, NULL, NULL, NULL),
(1566, 1, 43, NULL, NULL, NULL),
(1565, 1, 42, NULL, NULL, NULL),
(1564, 1, 41, NULL, NULL, NULL),
(1563, 1, 40, NULL, NULL, NULL),
(1562, 1, 39, NULL, NULL, NULL),
(1561, 1, 38, NULL, NULL, NULL),
(1560, 1, 28, NULL, NULL, NULL),
(1559, 1, 30, NULL, NULL, NULL),
(1558, 1, 50, NULL, NULL, NULL),
(1529, 4, 1, NULL, NULL, NULL),
(1528, 4, 8, NULL, NULL, NULL),
(1527, 4, 37, NULL, NULL, NULL),
(1526, 4, 33, NULL, NULL, NULL),
(1525, 4, 32, NULL, NULL, NULL),
(1524, 4, 45, NULL, NULL, NULL),
(1523, 4, 44, NULL, NULL, NULL),
(1522, 4, 43, NULL, NULL, NULL),
(1521, 4, 42, NULL, NULL, NULL),
(1520, 4, 41, NULL, NULL, NULL),
(1519, 4, 40, NULL, NULL, NULL),
(1518, 4, 39, NULL, NULL, NULL),
(1517, 4, 38, NULL, NULL, NULL),
(1516, 4, 28, NULL, NULL, NULL),
(1515, 4, 30, NULL, NULL, NULL),
(1557, 1, 49, NULL, NULL, NULL),
(1556, 1, 36, NULL, NULL, NULL),
(1555, 1, 35, NULL, NULL, NULL),
(1514, 4, 50, NULL, NULL, NULL),
(1513, 4, 49, NULL, NULL, NULL),
(1511, 4, 35, NULL, NULL, NULL),
(1512, 4, 36, NULL, NULL, NULL),
(1486, 5, 1, NULL, NULL, NULL),
(1485, 5, 8, NULL, NULL, NULL),
(1484, 5, 37, NULL, NULL, NULL),
(1483, 5, 33, NULL, NULL, NULL),
(1482, 5, 32, NULL, NULL, NULL),
(1481, 5, 45, NULL, NULL, NULL),
(1480, 5, 44, NULL, NULL, NULL),
(1479, 5, 43, NULL, NULL, NULL),
(1478, 5, 42, NULL, NULL, NULL),
(1477, 5, 41, NULL, NULL, NULL),
(1476, 5, 40, NULL, NULL, NULL),
(1475, 5, 39, NULL, NULL, NULL),
(1474, 5, 38, NULL, NULL, NULL),
(1554, 1, 34, NULL, NULL, NULL),
(1553, 1, 29, NULL, NULL, NULL),
(1552, 1, 18, NULL, NULL, NULL),
(1551, 1, 14, NULL, NULL, NULL),
(1550, 1, 15, NULL, NULL, NULL),
(1549, 1, 26, NULL, NULL, NULL),
(1548, 1, 52, NULL, NULL, NULL),
(1547, 1, 51, NULL, NULL, NULL),
(1510, 4, 34, NULL, NULL, NULL),
(1509, 4, 29, NULL, NULL, NULL),
(1508, 4, 18, NULL, NULL, NULL),
(1507, 4, 14, NULL, NULL, NULL),
(1506, 4, 15, NULL, NULL, NULL),
(1505, 4, 26, NULL, NULL, NULL),
(1504, 4, 52, NULL, NULL, NULL),
(1503, 4, 51, NULL, NULL, NULL),
(1502, 4, 47, NULL, NULL, NULL),
(1546, 1, 47, NULL, NULL, NULL),
(1545, 1, 21, NULL, NULL, NULL),
(1544, 1, 19, NULL, NULL, NULL),
(1543, 1, 22, NULL, NULL, NULL),
(1542, 1, 27, NULL, NULL, NULL),
(1541, 1, 16, NULL, NULL, NULL),
(1540, 1, 17, NULL, NULL, NULL),
(1539, 1, 46, NULL, NULL, NULL),
(1538, 1, 20, NULL, NULL, NULL),
(1473, 5, 28, NULL, NULL, NULL),
(1472, 5, 30, NULL, NULL, NULL),
(1471, 5, 50, NULL, NULL, NULL),
(1470, 5, 49, NULL, NULL, NULL),
(1469, 5, 36, NULL, NULL, NULL),
(1468, 5, 35, NULL, NULL, NULL),
(1467, 5, 34, NULL, NULL, NULL),
(1466, 5, 29, NULL, NULL, NULL),
(1465, 5, 18, NULL, NULL, NULL),
(1464, 5, 14, NULL, NULL, NULL),
(1463, 5, 15, NULL, NULL, NULL),
(1462, 5, 26, NULL, NULL, NULL),
(1461, 5, 52, NULL, NULL, NULL),
(1501, 4, 19, NULL, NULL, NULL),
(1500, 4, 22, NULL, NULL, NULL),
(1499, 4, 27, NULL, NULL, NULL),
(1498, 4, 16, NULL, NULL, NULL),
(1497, 4, 17, NULL, NULL, NULL),
(1496, 4, 46, NULL, NULL, NULL),
(1495, 4, 20, NULL, NULL, NULL),
(1494, 4, 25, NULL, NULL, NULL),
(1460, 5, 51, NULL, NULL, NULL),
(1493, 4, 13, NULL, NULL, NULL),
(1537, 1, 25, NULL, NULL, NULL),
(1459, 5, 47, NULL, NULL, NULL),
(1458, 5, 19, NULL, NULL, NULL),
(1457, 5, 22, NULL, NULL, NULL),
(1456, 5, 27, NULL, NULL, NULL),
(1455, 5, 16, NULL, NULL, NULL),
(1454, 5, 17, NULL, NULL, NULL),
(1453, 5, 46, NULL, NULL, NULL),
(1452, 5, 25, NULL, NULL, NULL),
(1450, 5, 12, NULL, NULL, NULL),
(1536, 1, 13, NULL, NULL, NULL),
(1535, 1, 12, NULL, NULL, NULL),
(1492, 4, 12, NULL, NULL, NULL),
(1451, 5, 13, NULL, NULL, NULL),
(1534, 1, 11, NULL, NULL, NULL),
(1533, 1, 10, NULL, NULL, NULL),
(1491, 4, 11, NULL, NULL, NULL),
(1490, 4, 10, NULL, NULL, NULL),
(1449, 5, 11, NULL, NULL, NULL),
(1448, 5, 10, NULL, NULL, NULL),
(1532, 4, 7, NULL, NULL, NULL),
(1579, 1, 6, NULL, NULL, NULL),
(1580, 1, 7, NULL, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_sections`
-- 

CREATE TABLE `sys_sections` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_padre` bigint(20) unsigned default NULL,
  `nombre` varchar(100) NOT NULL default '',
  `vinculo` varchar(255) default '',
  `posicion` bigint(20) unsigned NOT NULL default '0',
  `descripcion` text,
  `img` longblob,
  `img_mime` varchar(255) default '',
  `img_name` varchar(255) default '',
  `owner_id` bigint(20) unsigned default NULL,
  `owner_ip` varchar(20) default NULL,
  `owner_date` datetime default NULL,
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53 ;

-- 
-- Volcar la base de datos para la tabla `sys_sections`
-- 

INSERT INTO `sys_sections` (`id`, `id_padre`, `nombre`, `vinculo`, `posicion`, `descripcion`, `img`, `img_mime`, `img_name`, `owner_id`, `owner_ip`, `owner_date`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(1, 0, 'Sistema', '', 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '127.0.0.1', '2008-12-15 23:01:41'),
(2, 1, 'Configuraciones', './sys/configuraciones.php', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 'Acciones', 'sys/actions.php', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 'Grupos y Permisos', './sys/grupos.php', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 1, 'Historial', 'sys/logs.php', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 1, 'Secciones', './sys/secciones.php', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 1, 'Usuarios', './sys/usuarios.php', 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 0, 'Datos Personales', './sys/datos_personales.php', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 0, 'Clientes', '', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 07:58:34', 3, '192.168.0.22', '2007-07-30 16:14:35'),
(11, 10, 'Clientes', './sections/clientes.php', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 07:58:55', NULL, NULL, NULL),
(12, 10, 'Sucursales', './sections/sucursales.php', 2, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 07:59:09', NULL, NULL, NULL),
(13, 10, 'Zonas', './sections/zonas.php', 3, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:04:58', NULL, NULL, NULL),
(14, 26, 'Estados de pedido', './sections/estados_pedido.php', 2, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:05:27', 3, '192.168.0.22', '2007-07-30 16:15:50'),
(15, 26, 'Pedidos', './sections/pedidos.php', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:05:38', 3, '192.168.0.22', '2007-07-30 16:16:16'),
(16, 25, 'Tipos de cuenta', './sections/tipos_cuenta.php', 4, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:05:51', 3, '192.168.0.22', '2007-07-30 16:17:22'),
(17, 25, 'Cuentas', './sections/cuentas.php', 3, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:06:02', 3, '192.168.0.22', '2007-07-30 16:17:46'),
(18, 26, 'Estados de item', './sections/estados_item.php', 3, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:06:21', 3, '192.168.0.22', '2007-07-30 16:19:20'),
(19, 27, 'Rubros', './sections/rubros.php', 2, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:06:53', 3, '192.168.0.22', '2007-07-30 16:19:38'),
(20, 25, 'Proveedores', './sections/proveedores.php', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:07:27', 3, '192.168.0.22', '2007-07-30 16:20:16'),
(21, 27, 'Estados de producto', './sections/estados_producto.php', 3, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:07:52', 3, '192.168.0.22', '2007-07-30 16:20:27'),
(22, 27, 'Productos', './sections/productos.php', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:08:06', 3, '192.168.0.22', '2007-07-30 16:19:04'),
(51, 47, 'Stock', './sections/stock.php', 1, NULL, NULL, '', '', 2, '127.0.0.1', '2008-12-16 15:03:15', NULL, NULL, NULL),
(52, 47, 'Valor Stock', './sections/valor_stock.php', 2, NULL, NULL, '', '', 2, '127.0.0.1', '2008-12-16 15:04:12', NULL, NULL, NULL),
(25, 0, 'Proveedores', '', 2, NULL, NULL, '', '', 3, '192.168.0.22', '2007-07-30 16:15:00', NULL, NULL, NULL),
(26, 0, 'Pedidos', '', 5, NULL, NULL, '', '', 3, '192.168.0.22', '2007-07-30 16:15:06', NULL, NULL, NULL),
(27, 0, 'Productos', '', 3, NULL, NULL, '', '', 3, '192.168.0.22', '2007-07-30 16:18:26', NULL, NULL, NULL),
(28, 0, 'Reportes', '', 8, NULL, NULL, '', '', 3, '192.168.0.22', '2007-08-17 17:24:04', NULL, NULL, NULL),
(29, 0, 'Envíos', '', 6, NULL, NULL, '', '', 3, '192.168.0.22', '2007-08-17 17:25:57', 2, '127.0.0.1', '2008-12-15 22:23:48'),
(30, 0, 'Facturación', './sections/facturacion.php', 7, NULL, NULL, '', '', 3, '192.168.0.22', '2007-08-17 17:28:03', 2, '127.0.0.1', '2008-12-15 22:24:50'),
(32, 0, 'Tareas', '', 9, NULL, NULL, '', '', 2, '192.168.0.10', '2007-09-13 14:00:59', NULL, NULL, NULL),
(33, 32, 'Reserva de pedido', './sections/tarea_reservar.php', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-09-13 14:01:40', NULL, NULL, NULL),
(34, 29, 'Embalaje', './sections/embalaje.php', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-09-13 15:43:10', NULL, NULL, NULL),
(35, 29, 'Remito', './sections/remito.php', 2, NULL, NULL, '', '', 2, '192.168.0.10', '2007-09-13 15:44:12', NULL, NULL, NULL),
(36, 29, 'Recepción de entregas', './sections/entrega.php', 3, NULL, NULL, '', '', 2, '192.168.0.10', '2007-09-13 15:45:15', 2, '127.0.0.1', '2008-12-15 23:10:41'),
(37, 32, 'Actualizar Estado de Pedido', './sections/tarea_actualizar_pedido.php', 2, NULL, NULL, '', '', 2, '192.168.0.10', '2007-09-28 17:18:20', NULL, NULL, NULL),
(47, 0, 'Stock', '', 4, NULL, NULL, '', '', 2, '190.17.32.236', '2008-03-05 11:33:18', 2, '127.0.0.1', '2008-12-16 15:02:55'),
(38, 28, 'Clientes según estado del pedido', './sections/reportes_81.php', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-11-14 15:52:14', NULL, NULL, NULL),
(39, 28, 'Pedidos de un Cliente', './sections/reportes_82.php', 2, NULL, NULL, '', '', 2, '192.168.0.10', '2007-11-14 15:52:47', NULL, NULL, NULL),
(40, 28, 'Ranking de Clientes', './sections/reportes_83.php', 3, NULL, NULL, '', '', 2, '192.168.0.10', '2007-11-14 15:53:04', NULL, NULL, NULL),
(41, 28, 'Productos de un Proveedor', './sections/reportes_84.php', 4, NULL, NULL, '', '', 2, '192.168.0.10', '2007-11-14 15:53:21', NULL, NULL, NULL),
(42, 28, 'Proveedores de un Producto', './sections/reportes_85.php', 5, NULL, NULL, '', '', 2, '192.168.0.10', '2007-11-14 15:53:43', NULL, NULL, NULL),
(43, 28, 'Productos pendientes de Stock', './sections/reportes_86.php', 6, NULL, NULL, '', '', 2, '192.168.0.10', '2007-11-14 15:54:03', NULL, NULL, NULL),
(44, 28, 'Ranking de Productos', './sections/reportes_87.php', 7, NULL, NULL, '', '', 2, '192.168.0.10', '2007-11-14 15:54:16', NULL, NULL, NULL),
(45, 28, 'Remitos Facturables', './sections/reportes_88.php', 8, NULL, NULL, '', '', 2, '192.168.0.10', '2007-11-14 15:54:29', NULL, NULL, NULL),
(46, 25, 'Precios', './sections/proveedores_precios.php', 2, NULL, NULL, '', '', 2, '192.168.0.10', '2007-11-23 11:14:47', NULL, NULL, NULL),
(48, 32, 'Vaciar sistema', './sections/tarea_vaciar.php', 3, NULL, NULL, '', '', 2, '190.17.35.117', '2008-03-26 11:06:01', NULL, NULL, NULL),
(49, 29, 'Transportes', './sections/transportes.php', 4, NULL, NULL, '', '', 2, '127.0.0.1', '2008-12-15 22:29:35', 2, '127.0.0.1', '2008-12-15 22:39:10'),
(50, 29, 'Tipos de Transportes', './sections/tipos_transporte.php', 5, NULL, NULL, '', '', 2, '127.0.0.1', '2008-12-15 22:32:21', 2, '127.0.0.1', '2008-12-15 22:39:32');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_user_passwords`
-- 

CREATE TABLE `sys_user_passwords` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_usuario` bigint(20) unsigned NOT NULL default '0',
  `clave` varchar(50) NOT NULL default '',
  `fecha` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `sys_user_passwords`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_users`
-- 

CREATE TABLE `sys_users` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_grupo` bigint(20) unsigned NOT NULL default '0',
  `id_sesion` varchar(32) default '',
  `timestamp_sesion` bigint(20) unsigned default '0',
  `last_ip` varchar(20) default '',
  `last_login` bigint(20) unsigned default '0',
  `bloqueado` bigint(20) unsigned NOT NULL default '0',
  `fecha_bloqueo` bigint(20) unsigned default '0',
  `skip_maintenance` bigint(20) unsigned NOT NULL default '0',
  `pass_expires` bigint(20) unsigned NOT NULL default '0',
  `pass_renew` bigint(20) unsigned default '0',
  `usuario` varchar(20) NOT NULL default '',
  `clave` varchar(50) NOT NULL default '',
  `clave_fecha` date default NULL,
  `nombre` varchar(50) NOT NULL default '',
  `direccion` varchar(100) default '',
  `localidad` varchar(50) default '',
  `cp` varchar(20) default '',
  `provincia` varchar(40) default '',
  `pais` varchar(40) default '',
  `telefono` varchar(20) default '',
  `celular` varchar(30) default '',
  `fax` varchar(30) default '',
  `mail` varchar(50) NOT NULL default '',
  `cumple` date default NULL,
  `nota` text,
  `owner_id` bigint(20) unsigned default NULL,
  `owner_ip` varchar(20) default NULL,
  `owner_date` datetime default NULL,
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  `dni` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `dni` (`dni`),
  KEY `id_grupo` (`id_grupo`),
  KEY `id_sesion` (`id_sesion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

-- 
-- Volcar la base de datos para la tabla `sys_users`
-- 

INSERT INTO `sys_users` (`id`, `id_grupo`, `id_sesion`, `timestamp_sesion`, `last_ip`, `last_login`, `bloqueado`, `fecha_bloqueo`, `skip_maintenance`, `pass_expires`, `pass_renew`, `usuario`, `clave`, `clave_fecha`, `nombre`, `direccion`, `localidad`, `cp`, `provincia`, `pais`, `telefono`, `celular`, `fax`, `mail`, `cumple`, `nota`, `owner_id`, `owner_ip`, `owner_date`, `last_user_id`, `last_user_ip`, `last_user_date`, `dni`) VALUES 
(2, 1, '4bda425d009b0b56f360a90bc80798b7', 1229446848, '127.0.0.1', 1229446848, 0, 0, 0, 0, 0, 'manuel', 'c63ea20cd18d02e92c911c29582cc317', '2008-03-05', 'Manuel Perez', 'Mercedes 3865', 'Capital Federal', '1419', '', '', '15-4098-0896', '', '', 'manuel@gmail.com', '0000-00-00', 'nanu123', NULL, NULL, NULL, 2, '192.168.0.10', '2008-03-05 12:53:56', '19453203'),
(3, 1, '879351ed4d0a2a761d2fc2d2386289c5', 1213020804, '192.168.0.20', 1213020804, 0, 0, 0, 0, 0, 'seba', 'd494f3650319d882495067a92b217ff9', '2007-07-11', 'Sebastian Carranza', '', '', '', '', '', '', '', '', 'seba@gmail.com', '0000-00-00', 'seba123', 2, '192.168.0.10', '2007-07-12 08:01:22', 2, '201.255.252.25', '2007-10-04 12:59:20', '21324654'),
(4, 4, '91e006c30c44aff94f7e7b5b0933355f', 1210708634, '192.168.0.10', 1210708634, 0, 0, 0, 0, 0, 'solcattaneo', '6902ce59ec31fe95e7771edfba10af7b', '2007-07-20', 'Maria Sol Cattaneo', '3 de febrero 4517', 'Capital Federal', '1429', '', '', '', '', '', 'solcattaneo@gmail.com', '0000-00-00', 'benito1', 2, '192.168.0.10', '2007-07-12 08:03:05', 4, '201.255.241.16', '2008-05-13 12:21:02', '12345632'),
(6, 6, 'e54da149905647a9a08c0be7d19c9ed6', 1202250193, '201.231.237.143', 1202250193, 0, 1202250147, 0, 0, 0, '12345678', '95021724c3acfab92feecc6952189414', '2008-02-05', 'Maria Laura Manzur', '', '', '', '', '', '', '', '', 'marialauramanzur@hotmail.com', '0000-00-00', 'maria1', 3, '192.168.0.22', '2007-08-01 08:59:53', 7, '190.139.165.49', '2008-06-07 21:17:51', '23432030'),
(7, 4, '95b0b1cfbb122d76b86d16120c4a0f7c', 1229607314, '127.0.0.1', 1229607314, 0, 0, 0, 0, 0, 'admin', '0192023a7bbd73250516f069df18b500', '2007-11-11', 'Administrador', '', '', '', '', '', '', '', '', 'solcattaneo@gmail.com', '0000-00-00', 'admin123', 2, '192.168.0.10', '2007-11-12 14:10:14', 2, '192.168.0.10', '2007-11-14 11:23:51', '99999999'),
(19, 5, '113335e88df1820dea171def7b1775ba', 1222667956, '190.244.149.197', 1222667956, 0, 0, 0, 0, 0, '20433405', '017c33fdb8619ae006e5e78ff646f7dc', '2008-03-15', 'Maria Laura Manzur', 'Olazabal', 'Capital', '1426', '', '', '45556720', '', '', 'marialauramanzur@yahoo.com.ar', '0000-00-00', 'karma1', 7, '200.3.94.234', '2008-03-14 13:58:30', 7, '201.231.237.143', '2008-03-15 10:00:54', '20433405'),
(14, 8, '', 0, '', 0, 0, 0, 0, 0, 1, '12340678', '9cbf8a4dcb8e30682b927f352d6559a0', '2008-02-10', 'Carla Gimenez', '', '', '', '', '', '', '', '', 'cmg1510@gmail.com', '0000-00-00', '123456a', 7, '190.139.165.33', '2008-02-11 20:33:11', 7, '190.139.165.70', '2008-03-06 22:14:50', '20456738'),
(17, 4, '82921b6c1eb5fb7206d375928d408af6', 1226713268, '190.3.113.13', 1226710696, 0, 0, 0, 0, 0, '27659593', '54bb3b854a5b410b8d10e71849b867e7', '2008-03-13', 'Ezequiel Martinez', 'Cuba 2628', 'Cap Fed', '1425', '', '', '46129938', '', '', 'hernanezequielmartinez@gmail.com', '0000-00-00', 'ezequiel1', 7, '200.3.94.234', '2008-03-12 10:50:38', 7, '127.0.0.1', '2008-12-11 00:57:07', '27659593'),
(18, 4, '967219d003af9bf9b518a767909d8344', 1224701634, '200.51.46.53', 1224701634, 0, 0, 0, 0, 0, '25096468', '79440502d6700b4a9758b77174f40f7b', '2008-05-18', 'Laura Pallegrino', 'Jose Maria Moreno', 'Capital', '1601', '', '', '49016567', '', '', 'laly.pellegrino@gmail.com', '0000-00-00', 'violet02', 7, '200.3.94.234', '2008-03-14 12:02:38', 7, '127.0.0.1', '2008-12-11 00:57:39', '25096468'),
(23, 4, '90267e48f06ab87c9bb85119aa6a7dc7', 1213021230, '192.168.0.20', 1213021230, 0, 0, 0, 0, 0, '22403265', '6aeac8e159806f67b6c95a78cdb44ef8', '2008-06-06', 'Carla Gonzalez', 'Rosario 294 11 A', 'Capital', '1426', '', '', '1149016278', '', '', 'cmg1510@gmail.com', '0000-00-00', 'maga1510', 7, '190.139.165.49', '2008-06-07 16:35:26', 23, '190.139.165.49', '2008-06-07 17:20:27', '22403265');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sys_users_tipo_transporte`
-- 

CREATE TABLE `sys_users_tipo_transporte` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_usuario` bigint(20) unsigned NOT NULL,
  `id_tipo` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_tipo` (`id_tipo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Volcar la base de datos para la tabla `sys_users_tipo_transporte`
-- 

INSERT INTO `sys_users_tipo_transporte` (`id`, `id_usuario`, `id_tipo`) VALUES 
(1, 17, 1),
(2, 18, 2);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tipo_cuenta`
-- 

CREATE TABLE `tipo_cuenta` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Volcar la base de datos para la tabla `tipo_cuenta`
-- 

INSERT INTO `tipo_cuenta` (`id`, `nombre`) VALUES 
(1, 'Cuenta Corriente'),
(2, 'Caja de Ahorro');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tipo_transporte`
-- 

CREATE TABLE `tipo_transporte` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Volcar la base de datos para la tabla `tipo_transporte`
-- 

INSERT INTO `tipo_transporte` (`id`, `nombre`) VALUES 
(1, 'Agrale'),
(2, 'Isuzu');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `transporte`
-- 

CREATE TABLE `transporte` (
  `patente` char(6) NOT NULL,
  `id_tipo` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `imagen` longblob NOT NULL,
  `imagen_mime` varchar(255) NOT NULL,
  `imagen_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`patente`),
  KEY `id_tipo` (`id_tipo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Volcar la base de datos para la tabla `transporte`
-- 

INSERT INTO `transporte` (`patente`, `id_tipo`, `nombre`, `capacidad`, `imagen`, `imagen_mime`, `imagen_name`) VALUES 
('AIJ817', 1, 'Camion Agrale', 500, '', '', ''),
('AHH012', 2, 'Isuzu 4x4', 300, '', '', ''),
('jjk002', 1, '4x4', 200, 0xffd8ffe000104a46494600010100000100010000ffdb004300090607080706090807080a0a090b0d160f0d0c0c0d1b14151016201d2222201d1f1f2428342c242631271f1f2d3d2d3135373a3a3a232b3f443f384334393a37ffdb0043010a0a0a0d0c0d1a0f0f1a37251f253737373737373737373737373737373737373737373737373737373737373737373737373737373737373737373737373737ffc00011080055007f03012200021101031101ffc4001c0000010403010000000000000000000000000405060701020803ffc4004010000201030203040606070803000000000102030004110521061231134151610722718191a114153242b1c12324526372a2b2163443628292a3f1d2e1f0ffc40017010101010100000000000000000000000000010203ffc400191101010101010100000000000000000000000111022112ffda000c03010002110311003f00bc28a2b3418a6ad7f578b4ab132b2992573c90c43ac8fdc3f327b866975f5d4367692dc5cb848a352598f8557d3dccdaa6a1f4db90576e58623fe12787f11ea4fb07415621a6cf5bd7ccd792bea4eb23c8c8d130e748c823ec03b0a70b7e2bd661004f2c33f998803f2c536b47c973783f7ee693caca80b31000ea4d454ae0e337e9716e9eecaffe54b63e2fb0c66542807521c6dfeee5aaa356d74dba30b255671f79c6c3dd51fbce2abb9e628d6d19ecf29ea37286c13eb63c4d369e3a062e29d22403f582bed427fa734e116b1a74a2364bd83d77e44e670a59bc003be6b9ead78aa61108e4d3918018c86dfe3cc29d74ee288e1789ce973131b2b2f78041c8db7efa6d3c742a90c011d0d66abdb0f4a5a53281776d756e71be632cb9f69c54834fe35d06fc8583508798fdd73ca7e7b7ce86a454579c13c73af346eaca7a153906bd280a28a280ad59957a9ada9838b1e516e9146dca929e5931d4ae3a7b0f7d2064d73503ab5d058cfea713653f78ff00b5ec1ddf1f0a4f146030dab658b15ec8b822b488fde605fde2f84a7e601fcea1bc5ba935b144520b3b95552d8c6dd4d4d6fd39755bdf3914ff0022d545c7098e2195980cf2a9fe51f99acd1ada4b34f3dc09a4e6021c8503006fdc29baee026f6623a768d8f8d2bd27fbc5c9ef301fea5ac5db32de4a3933b83b03de33511e36f0b730daa4ba5c0e00393f1a63b39519c126303c79aa63a62a3a85428cd8e8ac09ab0a5d6af2c6a30d9f10452cfa2b5fa90ba6a4bfe711e71efad62b49c152609394e37e5cd4e6de549a35e5ce401952a41191e06a90dbe8e2e8d95fbe9efda2c73c5db468ec480cbd797c8ab03fe9ab2460e08aa8239beabb9d36fc2b7e8272247c6c50315619fe16618f21562689afdbea5abea9a642b28934d9163919b1cac5813b77f7114587ca28a2a28a63e2542e20f227f0a7ca68d740223f1e63f80ab12983936ad82ee2bd8af5ac30c62b488feb0bcbab5c0fda119fe41507e24d061d66ef3190976a870fd91e4601b6e66ffbf654df5e995f5ac000054881f3f541fce9038f58e063d63595577a568d7167ab3dbdc412977b761eb0181be798104865f57c8e4f4acea7a2de4a629b4d930ee0769131001dba8ceddd5623722a3336c3970485c9ff00edea31db2f61148ac0a850410720815111d8745e235e968929cf4cc269ea0d338a228bf43a5c6ad8d8a451e41f1054e41f653fdacd861934fd637814004d59042e3b2e269806bce1e576f116e727cfd5359365abdbab4abc3321703239609b7f9d58c97d19f0addef5715705693dfebd35abdbdce8777c9924318e41b63bf6df6eff00fbab1fd1a693716179aadddc5cc770f7a6291ca29ca300dcca4918272ddd9eef1a457d7d9825dfeeb7e152be099bb6d197273c8eebf3cfe752c5890514515145336bb22a72739032c7727ca9e698b8ae0b096c1cea534302004472cd398955cf4cb020f77caac0d6d796c8096727a7d952734df77af5944ec33b0c729621727df547eb8b77db5c482ea49611732448c2467438e9824ee3076f7521d3a668a49278dbb2755e78cae4b6479fc2a5eaa78b2f51d7ad5b56bd762b34690248cd0b86c1e4518c03e3b7b4d33daeb305ccb6baad8192381e616f77039fb24eeadd71efff00dd43ac76b2d5638d795a5b745c118dbb54a74e1fb747b5d4208b3ebc01ca9fda46041f816a42ac7b87eced9d8ec06013d3bc0aace1b9fa32931ca3041ca736436dde3b89a92f05442eb5336ba8b3dd42e09ece772ebb79138f0a71d1ac6d2eae6fa49eda796287998adb8031b9c1638d879fc8d73ef8debef71d38ef39f9cd3441a8dc1489a336051947ad25e84236ef1cb907e34ba1d5ae15cab5ce888a3ef36a2707e099a6cd652c6c64b9616e93dba382aaf1264e7a13b75eef6d249756d1ece730be9aa5c2863cb6d1e30403f9d74d7248e3d6ae5a456facb42842820a9bf2fcc0f7fd8a24e217e563f58681cc0ecbf5839cff00c751bfed56951b7a9a73291e1122fe06b238c6cb21534f6009ea7929a1f25d74b864fa769120287d586e5d9d8e3a01c8326acff45eee74495e4e6224b97656618e61851b796411eeaa821d4cde2a3c507246646476ceeb803076f1271565fa3c9a09758c428a1d6d58c8eae496cbafda1dc47754b36cad4be58b228a28aa0af09d434801233e06bde8db34146fa55874fd0d869504241b8b87bf3248d9259c61b07c32bd298bd1e7052f19adcc9f4d92c92d238d331c41839653b1dc74e553efae899ad2da791259ade29244fb0ee8095f613d2b78a28e152b146883ae1540144c523c63e8bcf0f69b3ea7a5ea12cd0471224f0cb086720bae5815e80639b71b0077a876a3a9b69ab0c91244fcb1347c9cbca58138ea0f4c018cefb127ad74cea36897f61736926cb3c4d193e01811f9d72eea5c2bc45f5acb64fa3dfc92c0dd9974b7760f8e854e3718dc502e8a46d3675d4e1e6678a1661d94bda00cc3057ec8208073bedb75db1492c7579565957e906d432e5d99c80475c1c677e9b75e9b55ebe8db85ce8bc2905aea96e86ea6769a54750c6327185f7003df9a934fa469b7058dc69f69296003192056240e99c8f33f1a0e614e21b58c87781e6209000b844c9e9e67c7bbbe9e74fbc86fde19a1e0696fda6255657791fb4c0c90a550741e676ae868b49d36020c1a7da464742902ae3e02952a2a801460018028639d6ff5786d66b98070569b6f3c2543c4e1fb44246464139df6eea4fa46a1f5bf12d8e9d0d8da470cb7690bb42a4165c8e620e7f8b07c31570f197a39d238a6e45ecad35adef2f2b4f063d718db9811838f1d8f9d69c1fe8d749e17be17d1cf3dddc22958da6e50133d4803bf1b515eb07a3ed0763f469dfbf2f70ff9114f3a270ee9ba1bc874db4484cb80ec1998b6338dd89f1a78d851430514514051451405145141827009ad15f99738eef1a28a0cac9960315bd14501477514501451450158a28a0cd145141fffd9, 'image/pjpeg', 'image/pjpeg');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `zona`
-- 

CREATE TABLE `zona` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Volcar la base de datos para la tabla `zona`
-- 

INSERT INTO `zona` (`id`, `nombre`) VALUES 
(1, 'Norte'),
(2, 'Sur'),
(3, 'Oeste'),
(4, 'Centro'),
(5, 'Este');
