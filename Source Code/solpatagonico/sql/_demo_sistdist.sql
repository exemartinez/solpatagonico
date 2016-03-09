-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jul 26, 2007 at 12:13 PM
-- Server version: 5.0.32
-- PHP Version: 5.2.0-8+etch4
-- 
-- Database: `_demo_sistdist`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `cliente`
-- 

CREATE TABLE `cliente` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `cuit` varchar(12) NOT NULL,
  `razonsocial` varchar(255) NOT NULL,
  `nroiibb` varchar(50) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cuit` (`cuit`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `cliente`
-- 

INSERT INTO `cliente` (`id`, `cuit`, `razonsocial`, `nroiibb`, `direccion`, `telefono`, `fecha_alta`, `fecha_baja`) VALUES 
(1, '20275937305', 'Manuel Gustavo Perez', '20275937305', 'Parana 26 5to N', '45021369', '2007-07-18 08:35:30', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `cuenta`
-- 

CREATE TABLE `cuenta` (
  `cbu` bigint(22) unsigned NOT NULL,
  `id_tipo` bigint(20) NOT NULL,
  `banco` varchar(255) default NULL,
  `sucursal` varchar(100) default NULL,
  `id_proveedor` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`cbu`),
  KEY `id_tipo` (`id_tipo`),
  KEY `id_proveedor` (`id_proveedor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `cuenta`
-- 

INSERT INTO `cuenta` (`cbu`, `id_tipo`, `banco`, `sucursal`, `id_proveedor`) VALUES 
(223121238, 3, 'Santender Rio', '119', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `estado_item`
-- 

CREATE TABLE `estado_item` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `estado_item`
-- 

INSERT INTO `estado_item` (`id`, `nombre`) VALUES 
(2, 'Habilitado'),
(3, 'No Habilitado');

-- --------------------------------------------------------

-- 
-- Table structure for table `estado_pedido`
-- 

CREATE TABLE `estado_pedido` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `estado_pedido`
-- 

INSERT INTO `estado_pedido` (`id`, `nombre`) VALUES 
(2, 'Habilitado'),
(3, 'No Habilitado'),
(4, 'Cancelado');

-- --------------------------------------------------------

-- 
-- Table structure for table `estado_producto`
-- 

CREATE TABLE `estado_producto` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `estado_producto`
-- 

INSERT INTO `estado_producto` (`id`, `nombre`) VALUES 
(2, 'Habilitado'),
(3, 'No Habilitado');

-- --------------------------------------------------------

-- 
-- Table structure for table `item_pedido`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `item_pedido`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `lista_precio_vta`
-- 

CREATE TABLE `lista_precio_vta` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `precio` decimal(10,2) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime default NULL,
  `id_producto` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_producto` (`id_producto`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `lista_precio_vta`
-- 

INSERT INTO `lista_precio_vta` (`id`, `precio`, `fecha_alta`, `fecha_baja`, `id_producto`) VALUES 
(1, 1.00, '2007-07-26 12:06:24', '2007-07-26 12:09:17', 1),
(2, 102.00, '2007-07-26 12:09:17', NULL, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `pedido`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `pedido`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `producto`
-- 

CREATE TABLE `producto` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `codigo` varchar(100) NOT NULL,
  `id_estado_producto` bigint(20) unsigned NOT NULL,
  `id_rubro` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `presentacion` varchar(100) default NULL,
  `punto_reposicion` int(11) NOT NULL,
  `cantidad_real` int(11) NOT NULL,
  `cantidad_reserva` int(11) NOT NULL,
  `comentario` text,
  `tamanio_pedido` varchar(100) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_baja` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `id_estado_producto` (`id_estado_producto`),
  KEY `id_rubro` (`id_rubro`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `producto`
-- 

INSERT INTO `producto` (`id`, `codigo`, `id_estado_producto`, `id_rubro`, `nombre`, `descripcion`, `presentacion`, `punto_reposicion`, `cantidad_real`, `cantidad_reserva`, `comentario`, `tamanio_pedido`, `fecha_alta`, `fecha_baja`) VALUES 
(1, '123', 2, 2, 'Rueda negra', '', '', 0, 0, 0, '', '', '2007-07-19 07:17:11', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `proveedor`
-- 

CREATE TABLE `proveedor` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `cuit` varchar(12) NOT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `proveedor`
-- 

INSERT INTO `proveedor` (`id`, `cuit`, `razonsocial`, `direccion`, `cp`, `telefono`, `fax`, `mail`, `contacto`, `fecha_alta`, `fecha_baja`) VALUES 
(1, '20268858470', 'Sebastian Gomez', 'Artigas 2964 7mo B', '', '4504-7047', '', '', 'Sebastian Gomez', '2007-07-18 09:22:54', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `proveedor_producto`
-- 

CREATE TABLE `proveedor_producto` (
  `id_producto` bigint(20) unsigned NOT NULL,
  `id_proveedor` bigint(20) unsigned NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY  (`id_producto`,`id_proveedor`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `proveedor_producto`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `remito_factura`
-- 

CREATE TABLE `remito_factura` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `nro_factura` varchar(40) NOT NULL,
  `fecha_factura` date NOT NULL,
  `patente` char(6) NOT NULL,
  `dni_transportista` varchar(10) NOT NULL,
  `fecha_entrega` date NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `patente` (`patente`,`dni_transportista`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `remito_factura`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `rubro`
-- 

CREATE TABLE `rubro` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `rubro`
-- 

INSERT INTO `rubro` (`id`, `nombre`) VALUES 
(2, 'Ruedas'),
(3, 'Cubiertas'),
(4, 'Puertas');

-- --------------------------------------------------------

-- 
-- Table structure for table `sucursal`
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `sucursal`
-- 

INSERT INTO `sucursal` (`id`, `id_zona`, `id_cliente`, `nombre`, `direccion`, `cp`, `telefono`, `fax`, `mail`, `contacto`, `fecha_alta`, `fecha_baja`) VALUES 
(1, 4, 1, 'Villa del Parque', 'Gral. Rivas 2375 PB A', '1417', '45021369', '', '', 'Manuel Dominguez', '2007-07-25 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `sys_actions`
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `sys_actions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `sys_config`
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
-- Dumping data for table `sys_config`
-- 

INSERT INTO `sys_config` (`id`, `variable`, `valor`, `descripcion`, `owner_id`, `owner_ip`, `owner_date`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(1, 'auto_logoff', '3600', 'tiempo maximo sin actividad para que expire la sesion. (en segundos)', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'maintenance', '0', '0 = sistema en funcionamiento normal\r\n1 = funcionamiento fuera de linea por mantenimiento\r\n(solo acceden los usuarios o grupos autorizados a saltear el mantenimiento)', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'pass_expires', '0', '1 = las contrase?as de todo el sistema expiran\r\n0 = las contrase?as expiraran segun configuracion individual de cada usuario', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'expiration_days', '0', 'cantidad de dias en que expirara la contrase?a, si es que pass_expires es seteado en 1', NULL, NULL, NULL, 2, '192.168.0.10', '2007-07-12 07:59:19'),
(5, 'inactive_account_expiration', '30', 'tiempo maximo de inactividad de una cuenta, una vez superado este limite, la cuenta se bloquea.\r\nexpresado en días.\r\n0 indica sin caducidad.', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'pass_black_list', '', 'lista de palabras prohibidas para la contraseña.\r\ndeben estar separadas por coma (,).\r\nsi se desea prohibir una coma debe estar escapada por el caracter "". ej: ,', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'pass_block_history', '6', 'cantidad de contraseñas a considerar en la lista negra', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'pass_block_user_data', '1', 'indica si se incorporan los datos personales del usuario al black list para su contraseña.\r\n1 = se bloquean los datos\r\n0 = se permiten datos filiatorios en la contraseña', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'pass_format', '-6,a1,#1', 'brinda el formato valido para la contraseña:\r\na1 - indica al menos una letra minuscula.\r\na1 - indica al menos una letra mayuscula.\r\n#1 - indica al menos un numero.\r\n-1 - indica que la contraseña debe tener al menos un caracter.\r\n+10 - indica que la contraseña debe tener como maximo 10 caracteres.\r\nsa - indica que no se permitiran secuencias alfabeticas.\r\ns# - indica que no se permitiran secuencias numericas.\r\n^ - indica inicio combinando con a1, a1, #1\r\n$ - indica fin combinando con a1, a1, #1\r\n*todos los indicadores deben estar separados por comas (,)', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'wrong_pass_limit', '3', 'cantidad maxima de intentos fallidos de logueo.', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'log_activity', '1', '1 = Activa el logueo de actividades.\r\n0 = No activa el logueo de actividades.', 2, '192.168.0.10', '2007-07-12 08:06:11', 2, '192.168.0.10', '2007-07-12 08:06:16');

-- --------------------------------------------------------

-- 
-- Table structure for table `sys_groups`
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `sys_groups`
-- 

INSERT INTO `sys_groups` (`id`, `skip_maintenance`, `nombre`, `nota`, `owner_id`, `owner_ip`, `owner_date`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(1, 1, 'Sistemas', 'Grupo exclusivo para personal de E4system.', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 0, 'E4/Systems', '', 2, '192.168.0.10', '2007-07-12 08:00:57', NULL, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `sys_groups_actions`
-- 

CREATE TABLE `sys_groups_actions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_grupo` bigint(20) unsigned NOT NULL default '0',
  `id_accion` bigint(20) unsigned NOT NULL default '0',
  `valor` varchar(255) default NULL,
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `sys_groups_actions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `sys_logs`
-- 

CREATE TABLE `sys_logs` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_usuario` bigint(20) unsigned default NULL,
  `usuario` varchar(255) default NULL,
  `tipo_entrada` varchar(255) NOT NULL default '',
  `fecha` bigint(20) unsigned NOT NULL default '0',
  `ip` varchar(20) default NULL,
  `user_agent` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- 
-- Dumping data for table `sys_logs`
-- 

INSERT INTO `sys_logs` (`id`, `id_usuario`, `usuario`, `tipo_entrada`, `fecha`, `ip`, `user_agent`) VALUES 
(1, 2, 'manuel', 'Acceso Satisfactorio', 1184238385, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(2, 4, 'solcattaneo', 'Acceso Satisfactorio', 1184238406, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(3, 2, 'manuel', 'Acceso Satisfactorio', 1184260698, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(4, 2, 'manuel', 'Acceso Satisfactorio', 1184260808, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(5, 0, 'admin', 'Acceso Fallido - Usuario Incorrecto', 1184756193, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(6, 2, 'manuel', 'Acceso Satisfactorio', 1184756197, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(7, 2, 'manuel', 'Acceso Satisfactorio', 1184767635, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(8, 2, 'manuel', 'Acceso Satisfactorio', 1184837449, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(9, 2, 'manuel', 'Acceso Satisfactorio', 1184841113, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(10, 3, 'seba', 'Acceso Satisfactorio', 1184960803, '200.114.128.74', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
(11, 4, 'solcattaneo', 'Cambio Contraseña', 1184960857, '200.114.128.74', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
(12, 4, 'solcattaneo', 'Acceso Satisfactorio', 1184960866, '200.114.128.74', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
(13, 3, 'seba', 'Acceso Satisfactorio', 1184960883, '200.114.128.74', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
(14, 4, 'solcattaneo', 'Acceso Satisfactorio', 1184960915, '200.114.128.74', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
(15, 2, 'manuel', 'Acceso Satisfactorio', 1185186858, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(16, 2, 'manuel', 'Acceso Satisfactorio', 1185206822, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)'),
(17, 2, 'manuel', 'Acceso Satisfactorio', 1185459541, '192.168.0.10', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)');

-- --------------------------------------------------------

-- 
-- Table structure for table `sys_permitions`
-- 

CREATE TABLE `sys_permitions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_grupo` bigint(20) unsigned NOT NULL default '0',
  `id_seccion` bigint(20) unsigned NOT NULL default '0',
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=57 ;

-- 
-- Dumping data for table `sys_permitions`
-- 

INSERT INTO `sys_permitions` (`id`, `id_grupo`, `id_seccion`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(21, 1, 17, NULL, NULL, NULL),
(20, 1, 16, NULL, NULL, NULL),
(19, 1, 15, NULL, NULL, NULL),
(18, 1, 14, NULL, NULL, NULL),
(17, 1, 13, NULL, NULL, NULL),
(16, 1, 12, NULL, NULL, NULL),
(15, 1, 11, NULL, NULL, NULL),
(14, 1, 10, NULL, NULL, NULL),
(41, 4, 14, NULL, NULL, NULL),
(40, 4, 13, NULL, NULL, NULL),
(39, 4, 12, NULL, NULL, NULL),
(38, 4, 11, NULL, NULL, NULL),
(37, 4, 10, NULL, NULL, NULL),
(22, 1, 18, NULL, NULL, NULL),
(23, 1, 19, NULL, NULL, NULL),
(24, 1, 20, NULL, NULL, NULL),
(25, 1, 21, NULL, NULL, NULL),
(26, 1, 22, NULL, NULL, NULL),
(27, 1, 23, NULL, NULL, NULL),
(28, 1, 24, NULL, NULL, NULL),
(29, 1, 8, NULL, NULL, NULL),
(30, 1, 1, NULL, NULL, NULL),
(31, 1, 2, NULL, NULL, NULL),
(32, 1, 3, NULL, NULL, NULL),
(33, 1, 4, NULL, NULL, NULL),
(34, 1, 5, NULL, NULL, NULL),
(35, 1, 6, NULL, NULL, NULL),
(36, 1, 7, NULL, NULL, NULL),
(42, 4, 15, NULL, NULL, NULL),
(43, 4, 16, NULL, NULL, NULL),
(44, 4, 17, NULL, NULL, NULL),
(45, 4, 18, NULL, NULL, NULL),
(46, 4, 19, NULL, NULL, NULL),
(47, 4, 20, NULL, NULL, NULL),
(48, 4, 21, NULL, NULL, NULL),
(49, 4, 22, NULL, NULL, NULL),
(50, 4, 23, NULL, NULL, NULL),
(51, 4, 24, NULL, NULL, NULL),
(52, 4, 8, NULL, NULL, NULL),
(53, 4, 1, NULL, NULL, NULL),
(54, 4, 4, NULL, NULL, NULL),
(55, 4, 5, NULL, NULL, NULL),
(56, 4, 7, NULL, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `sys_sections`
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- 
-- Dumping data for table `sys_sections`
-- 

INSERT INTO `sys_sections` (`id`, `id_padre`, `nombre`, `vinculo`, `posicion`, `descripcion`, `img`, `img_mime`, `img_name`, `owner_id`, `owner_ip`, `owner_date`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(1, 0, 'Sistema', '', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 'Configuraciones', './sys/configuraciones.php', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 'Acciones', 'sys/actions.php', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 'Grupos y Permisos', './sys/grupos.php', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 1, 'Historial', 'sys/logs.php', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 1, 'Secciones', './sys/secciones.php', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 1, 'Usuarios', './sys/usuarios.php', 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 0, 'Datos Personales', './sys/datos_personales.php', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 0, 'Admin.', '', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 07:58:34', NULL, NULL, NULL),
(11, 10, 'Clientes', './sections/clientes.php', 1, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 07:58:55', NULL, NULL, NULL),
(12, 10, 'Sucursales', './sections/sucursales.php', 2, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 07:59:09', NULL, NULL, NULL),
(13, 10, 'Zonas', './sections/zonas.php', 3, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:04:58', NULL, NULL, NULL),
(14, 10, 'Estados de pedido', './sections/estados_pedido.php', 4, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:05:27', NULL, NULL, NULL),
(15, 10, 'Pedidos', './sections/pedidos.php', 5, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:05:38', NULL, NULL, NULL),
(16, 10, 'Tipos de cuenta', './sections/tipos_cuenta.php', 6, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:05:51', 2, '192.168.0.10', '2007-07-18 08:18:45'),
(17, 10, 'Cuentas', './sections/cuentas.php', 7, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:06:02', NULL, NULL, NULL),
(18, 10, 'Estados de item', './sections/estados_item.php', 8, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:06:21', NULL, NULL, NULL),
(19, 10, 'Rubros', './sections/rubros.php', 9, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:06:53', NULL, NULL, NULL),
(20, 10, 'Proveedores', './sections/proveedores.php', 10, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:07:27', NULL, NULL, NULL),
(21, 10, 'Estados de producto', './sections/estados_producto.php', 11, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:07:52', NULL, NULL, NULL),
(22, 10, 'Productos', './sections/productos.php', 12, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:08:06', NULL, NULL, NULL),
(23, 10, 'Tipos de transporte', './sections/tipos_transporte.php', 13, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:09:25', NULL, NULL, NULL),
(24, 10, 'Transportes', './sections/transportes.php', 14, NULL, NULL, '', '', 2, '192.168.0.10', '2007-07-18 08:09:37', NULL, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `sys_user_passwords`
-- 

CREATE TABLE `sys_user_passwords` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_usuario` bigint(20) unsigned NOT NULL default '0',
  `clave` varchar(50) NOT NULL default '',
  `fecha` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `sys_user_passwords`
-- 

INSERT INTO `sys_user_passwords` (`id`, `id_usuario`, `clave`, `fecha`) VALUES 
(1, 4, 'benito1', 1184960857);

-- --------------------------------------------------------

-- 
-- Table structure for table `sys_users`
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
  PRIMARY KEY  (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `sys_users`
-- 

INSERT INTO `sys_users` (`id`, `id_grupo`, `id_sesion`, `timestamp_sesion`, `last_ip`, `last_login`, `bloqueado`, `fecha_bloqueo`, `skip_maintenance`, `pass_expires`, `pass_renew`, `usuario`, `clave`, `clave_fecha`, `nombre`, `direccion`, `localidad`, `cp`, `provincia`, `pais`, `telefono`, `celular`, `fax`, `mail`, `cumple`, `nota`, `owner_id`, `owner_ip`, `owner_date`, `last_user_id`, `last_user_ip`, `last_user_date`) VALUES 
(2, 1, '52fa9a23852672afefad98c893696523', 1185462227, '192.168.0.10', 1185459541, 0, 0, 0, 0, 0, 'manuel', 'nanu123', NULL, 'Manuel Dominguez', 'Mercedes 3865', 'Capital Federal', '1419', '', '', '15-4098-0896', '', '', 'manuel@e4system.com.ar', '1979-08-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, '090634967b2dabed1368889510fbb9e0', 1184960883, '200.114.128.74', 1184960883, 0, 0, 0, 0, 0, 'seba', 'seba123', '2007-07-11', 'Sebastian Gomez', '', '', '', '', '', '', '', '', 'seba@e4system.com.ar', '0000-00-00', '', 2, '192.168.0.10', '2007-07-12 08:01:22', NULL, NULL, NULL),
(4, 4, '49a719328046007fe5f3ceb315840d1f', 1184960915, '200.114.128.74', 1184960915, 0, 0, 0, 0, 0, 'solcattaneo', 'benito1', '2007-07-20', 'Maria Sol Cattaneo', '', '', '', '', '', '', '', '', 'solcattaneo@fibertel.com.ar', '0000-00-00', '', 2, '192.168.0.10', '2007-07-12 08:03:05', 3, '200.114.128.74', '2007-07-20 16:47:37');

-- --------------------------------------------------------

-- 
-- Table structure for table `sys_users_tipo_transporte`
-- 

CREATE TABLE `sys_users_tipo_transporte` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_usuario` bigint(20) unsigned NOT NULL,
  `id_tipo` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_tipo` (`id_tipo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

-- 
-- Dumping data for table `sys_users_tipo_transporte`
-- 

INSERT INTO `sys_users_tipo_transporte` (`id`, `id_usuario`, `id_tipo`) VALUES 
(32, 4, 2),
(31, 3, 2),
(30, 2, 2),
(7, 2, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `tipo_cuenta`
-- 

CREATE TABLE `tipo_cuenta` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `tipo_cuenta`
-- 

INSERT INTO `tipo_cuenta` (`id`, `nombre`) VALUES 
(2, 'Cuenta Corriente'),
(3, 'Caja de Ahorro');

-- --------------------------------------------------------

-- 
-- Table structure for table `tipo_transporte`
-- 

CREATE TABLE `tipo_transporte` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `tipo_transporte`
-- 

INSERT INTO `tipo_transporte` (`id`, `nombre`) VALUES 
(1, 'Camion hasta 14.000 kg.'),
(2, 'Camion hasta 5.000 kg.');

-- --------------------------------------------------------

-- 
-- Table structure for table `transporte`
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
-- Dumping data for table `transporte`
-- 

INSERT INTO `transporte` (`patente`, `id_tipo`, `nombre`, `capacidad`, `imagen`, `imagen_mime`, `imagen_name`) VALUES 
('ABC123', 1, 'Scania 14000', 10000, 0x89504e470d0a1a0a0000000d4948445200000060000000600806000000e29877380000000473424954080808087c086488000000097048597300000b1200000b1201d2dd7efc0000002574455874536f667477617265004d6163726f6d656469612046697265776f726b73204d5820323030348776accf00000015744558744372656174696f6e2054696d650031312f372f303589c986700000400049444154789c3cbc59b32c59729df7b9fbde3b2233cf7087ba55b7abbbaa84a141000d1a45134d94e94dff477f403f51341a25a3a0112240a21b5d5d5d7738f7e410117b70d7431ce1f9589e888cf0bd7cf95acb53fea7fff17f88a2470ef38cf64aeb0112d4a5518e99ad39db3568eb208fa0139828de3bc594d3c148778a9892ef0fa4f98e7c7f87e4c2b6de98f381341d8831f07ee3faf9f7d4cb8deb72255aa257787eda787eeaace72ff897cfb4eb461c0f482adcdac6ac8dd18ce576c3a7c270e194269c4497cad604af0d15e1feab77f8f933b42b7727435a9029cc0f47f27c8747a35d2fa82aa337fa36984b02518654b41ce9b592a699d966662b748520b1f6851c99b9cc6876649ea87db07e7ce2f8e691729868037cadfcf8f989169dbbe391e5d64859f0b640addc2e8d2e8d11821a139a136d347a28391b591365cad4bae2eb8284a096e9120c07018e931211d4d6e9b571b83f90cac4d0848822e1e46c8030c64a1f0ba0984ed4ba905428c7cc6dddd8b6860f10294499e8661c050eb9536f2b3194a0e3629807b304c31b36256a1b1485c3e98e928fac4f175aeda84cf4b5100e584231d665a1b64ee018a029830a214a303017d83a490ded1d11a8792686a096283ae36da38e0d28b43ed004438264c2940b114ef70d3f7f613b3ff3f9e347d40a323d701bc6cd85568c11423243458d708191b0c9d01906034b99799e30146fc1184e77216bd0bce332d8bce3a1e85ce8be32da42ce4a883206c4105c8231608ca08f4aeb8dc94e64cd6c6d65d93a974f677c2882507b63f5c692663649a4485c6edbfee044099416ce3206cbf51919413727b433da0251b9ffe63d968c244184e1de88e19880b7ca68832e46441040eb0dc77109ba74ba76082704c257221b9e12a2833156c658a9a31203a841ed1db5cca091a5334d477ef98b5f719794c99db7f733930e8e01f7a5f06ace1ca6132607b4cc47d48494413511ddb092d06208c690a046473d806084c35096d5e89e583d18cbfea5b0bc57aa074150d7ca725ee8dba0b931dc08e0f972e6d3f9c6f353c5541114898ed70d6a9048dcae1b9a0bd343425449144ec578f3fa3587f9442121c07c38d0cf2bea89d3ab5798c0567fc6a393a6092989f0a045a551f1beed15a982108437dad68810da50ea6d50b7fdfe6540b8e3387d5c8810220b5b5b90063284e64ece99a50da4c331078759d043e16e3a723a9cd0c381582b770f27b275cab81031c8494921414a9984709c324466ab1bebb6305a45444918ee01383e1c8b04f87e84f2c05b835ed8ba206ee041f8c025d3a331d646a0b876be5c562e972bb924966b67abe0197c59613ad2f54257a51c26ced7057fbe520e051548116cd7277a1bdcdd7fcdedf681ebed8909a1ae2bf331e15eb9fd71e554945632624242312d34ef882b2925248050922546af3b544a257c201e500e900bbd7584ceb65c2992e823f018840c440a8810d190fa85c3fd03472d6cd659dae0f8fe6bd6db826f9df978406470790edab55244f0d39164a69c0e0fd056aa2fb45e912e1c72e1d61a1686e86013c137415b400e2c0553e994143482ea03f3a00e90d669eb4a6078087dac48c0b25df8c33ffe9ea73f3cf3fad589651baccb60dc16f2a69026c426a6a3524e89cb79c31162ab18d0476001b3246abb90081e09aac1da6e7cf8696592ca29155483690c2240342336630c9a54908e8d440b276460aaf4e18418218d2409933bba267412faba90c4a8ed86a11cf21d2d36b42b5a84ad0be9d669af859e85da12c51aa646d6d7b494602ce8a6f40dd2e92dc7c391664a2ad3446b0bc743668c1344666d1746ab2809a2e3e11886a833e71917a8a3424bf40e654ab401d13ae37663a8b1de2a42a247d0b62bb56d9c3f3f312e95341df9f069dd7b8f282299cdcf8cdae8a668ec989de78cca81b656bc0e8ea7033665b6eb86f58dde063d60194a0b9825302bdc8663ddc9d63132913ade6f8818a6428b06c58815d485b01da61c48b9801888d3bdd1b76734608c0ea6d03ae538f165bd20a971975f31a78c8773b9eecf2ac2985226ba526d20aa900ef4a8bcf9ea2bd6db997912d275214d56e8b2d2db86a2c8e85808751d4cc93847c78792104c8c90ceda578a2614a1f6c1e174a0d7ce125764285b17dad668be51fbd8e99e7738dff0f3caf154f8c5dbcce7e74a8cc0ee133f8f99e5f9cab099e883290bd21bede982a9120e5fd6056b2b348100f2c4ba354604e2835582de0465fff3a5754c8c294069e414644d308275a97834124668307a2703e44c440711ea76210dc58a32b61bdbb2719aeea96d639e2652125485c3eb07eaf90b3606b51f511a82620a4aa744a7fb012b071ebeca944f83f3e79f391e8f24dc767caccf78939d829130cd745fb1702613d6bad2bb10aa981cd8bc53d70531f8b26df44dd07287d70b6b0dd6adf3d56c7cfbfa81b7efbfe3f52fdfd33fff8e3ffedd3fd2bdb2de2aafef85a9402990f3ccdffefbdff1e1f3333fb546eb4ebf2db81bd98cee15a9829a0083d0c46499a11bbd0f3abef79910b2420a4130d0c1eac11485c4a08d8651283ad1105aab8808ea463904b7ba10a910114814b67ae64e8f04860ef0d8b0e991ac472436ba0b45410f3396209913119cb2706b8e22680411636fe6387638923e4fdcae179244a3e482c6449890a7044da838d7db461608efc480eb6d613acc10426b1d43b1c360694eef42db6e78087ff2d52bbefdcd9f9352e2cbef7ee20fffcbdff1e37ff87f984f85be5ef0eee48789beadf4ae8cabf0e9f29909e59bfbd7fce2f5c2342dfcf643e1efae57babed494c3bba3ee27201ba9ec4d7f1975878136786a83a5efb341918cba5234d36b20628817262b6c716344e023300dd4048b991cc1ad39b7f385e96e26b2412ee83044137465db56462e88291a3b32252642843e2ab328cd41453191fde589d35a801b929cc3a9505d48a20b41605a90e8481f387b972f18ebe8b4de39bc36d64bc36b2634e863634388e7414f4e15e7d75fbde6fbbff917f4b5f3d3fff60f88086fffec17bcfbcdf798642c1a7efe23eb72e57ab9f12c9557af4e9c8e27aeebc2eddffd1fb09d90c8d850fef287c4c1077debc8e90408aa8a8a50ec0049e873e63927d432f5da78cc03516762b085e0625c7cb0b8b3ad83d912431d194a1f1b5994da1b59064b31f294487583b9b08593b2ee34d826646ed84814174a0eb6086aac881766533477204869a60da3fbc66c868bd23c30d79d1df6816523a54212316e1f3f7038dca3c5589f2fa020964873506e8deb0a1f7ff75ba0c02101853a94b537daa83c1c847ffbaffe84924efcf8efff5f0eef5ef1fd7fff2fc88703684687604988b1215342b72027e370c88c318086a5a0e4036c0b63323eaf0dfbc939cd6f9043a27b4781e1fbd4ac49893046db2869c243787c3c90b3d2c7a02463db36bc55de68663a1ed9b62b3f2d2b9f968d836664003a20020d6578e062a81a6642f544f2412f4ad282e62395856d340e295308bc0709c3a43189c08b62e0d2995246440817ba0c44764812cb78c91ccb1d893e4084f5fc99c3f19e9c0d51618c8d8b0eba0a7932527a40ca448de0d61a6d0cead6f8fa04bff957df73fee9ca73bff2edbff98ee9f16b540d1c34837b23c7844b50b78d7c7820e2c6f27ca3b686e4a0de1a4b4fd4d6a85e68ab329e7fde99904dd4e54668a0aaf4eaccc72339dfb3dece3c6f95e685cee0501212861194a49462dc9f0ee494c9f981c39ca10f9e6be70f17a70f4542f769baedcd5fd5e80ed3e98ed804c42973a1f79d56fb5868b71bf970e4940493a02448668c304241a431d9440b25c49924d1cc11764665d30ca6a4dbf54a4a19f71b6dbd928e47460cb675a3af8d2e832a8edc9d68eeb43ee85e596ae5bb93f127bffe964fffe90b0f3f7cc5abefbf46b32112843888e12f7456b2108b73b87bcdc79f7f4bbb2ca828d7e7c1452fd45ab1323342496310e61cdf7d455bafc40b04346f388226a76d1bb50d241bc513750bbe7bf3c8d372616d9055f852174e2393f2c6e2c2310b733a2059980ec16399b92e677ebb0cda703c1a920a1242f25db668b795e3ac4c778fd465d94f8b1e31291455dad8200d1a336338b336eeacb078a2f92029684a980ae7d8586a308601ca68571206a24ea4c47a3993478754707764800ed9594d071cd6b6b16e835fde17debdfb8a3ffedf3ff1ddbff915c76f1e760e9d0b2206228c7014271c9a27c466541b93659e3ffd085aa823b89e37722e3c3c24a239a30dda2db16d03a5a0c319e10c77241cd3429a26eaba222238a0167c5e375a174674aede30d5bdf24de8d269dc113e481624cd944910853f49576e6be50f5b4547504291de310c2d13cd3b753897e5095d564e8faf890c3e2ae6ca40190c8a40c2d070e6247414629fc4db08bc031148181e158f40274940a7af1b58665b17640c3414a7d3fb601bce188d5badac9bf3a628efbf7ecff5771fb8fffe15f9fe88d8ae3c1a09d10c1a880bea65af00879227aecf7fe476bd804e3c7dde90a19cee4ebc7dffc0fd9d713a18b843bd2263d9612767a2144a9a306624204f4774be23e7131ab2cf09974a84924d10cf584cb82a559c4ae216d0d3c475286bef8c0806f070ff9687e391dfbc7d45797979ab4e6c0664c374c21d7063b8d07adf8545829e953a0634c1c2c80622091541344020eb2062a03148a91132d0809c2652d0f13a482afbd83f0cdf2a31152415d00d1983b5775a1d1c24f8ee97bfe4fad3478ebfb8e3fed54cd104ee58114cf786639ac8e88b866468326ed79f583f9d2914e22ef0976bbdfee61de571227e3cd3c5c9aa580cdae8f880ae993e827438e06da3df3ade3b3a4d8c3e189648112c5e79eec2418d5983d53bb96770613e1d195668b1f3f2da57900edef8f874e1eef880e6c2fb3471abce6f2368ad730cc53468eaa432216a0c1fcc23980fc646425d1077825d52ef3e76981c7bc59f52670aa59a40d79dfa66c592a0a30d7a1d60073c82a8835a1df784480294cd83d683de3b7ff9c36bfaf90b87c799bbc719c2596e57249c60d0154476d3a62e675c065069eb67c6edc2743a717afb9a52120f0f07eeee27f2fd1d2d8444709ccb3ee8a689a0105620656204b7eb33b7b6b24950fb606bc1f3b6b08ec1128dfea27056068b7732194d90d2fea2427605354d33693e502b8c10b20a63fb828fc1713a72381df9f59b47dadab8b6c695a092613a50fb4616c145209c2c83790ee6924929b17adf9f63188411a1ac5d3055d40c2748e268d8decfae9795f5b281b71d7624b3e1dc6e0ba3ef5243eb9dad6ffcf2be102de17d30dd65a6027912d49470a30f600c942070d27cdc65ecbe22ad52d24c3e9c68fd8626613a1856a02d0b12099b05f7411dc1268a1f1e18e9c465addca2b1bd546525e85258dc79bede58c6e0529dd695ee0e0e1d25acefb382647a1fe8760136b608464a94e3032289261db4308b725ebe907d3069f017df7f4722b8b5ced6829bc36dbb311c1ca387a3c999d3c4712e4c5971832e8288522cb83f041ec2e6bb93653aa33993d54822244b65c7d994e85e2107e2d0da404459db2068303a5fbdfb05ebc733f9cd1d3e0c623f2dd30c324da86552cea804f4466842b40006a931d6a75dc64e279a546adf08497cfef891725fa87db05427dca855f1d919da09cb6c3da8ada1182ed0b71b5b7796d6108411baeb4a39d3c2c974d6214815e2681c74101e6c6d50686cacfb7d8d81486210dc24a892603827c0962ffcf0ed7b3efdf1f77cda2ee4ed8ab9d0485cdb46998f589aa8ec53f24994634a0841f3c66d1b6cbd601608bb31d5c377ff251ca490540bf9a8389590043250538a08b7bab2f6c1b6393f3cceb4b52165bf58079e576152b87b4ca494d024880e52245c78397203d30c23635310da599f7fa613b431e863a03141833a82cee0da566e6da37bd0ea40b3b1b6416b15c47636d4579a076304a13086e3e1f4be7fd92906c58c0448db68da39e54cef09978a45a7b54ad6848c8ccb20bc83dbde5b4cb134335d2fbc7ffb0e3e7ce0e3308cfd94c550dc325b18a6861248e83f375ee9704ce305e202420884d982948cad0d22434a194c13ad757212f04e6d953e1297b5713eaf9c97c19f7ef548ff7ce5f5af1ed906a838164e2a85943319c34588507a8c7d52b57dc8c1032d10f988d733d11cc2e8dd19dd91e4dc36e1e3f9c69765e352076bdf88700ed389a32a877bc5b5c07006039d5fedecac3696cb33c382d08ce880306edd79de1634adb00a46902627acd349e8e81494ad57923b968c312a86e0d3238b65728269cad8edc29bc73b94c1cf6d6263b03268121841d7bebf0869ccda3958c2d5382fc694064507dd0b252b8453dd1173229cd4d627863f301f32de1b6d146e970d18d4dee91efceabe70283337dbf5ee9c3b6586b9ccccf733d36982d411606dfb9071b2403c50339001add2ea42bd5dc8c7477afb427765d96ec4eaacee5cce9d4fe78d9c8d1f5e3df278103694edba1162b41769bbdc3d22796678a7fa8a89432e6c037c6b4c13dc99f1ea30f1cd37aff9b2069f96ca656d680e443b1963198d1403d1418fc04277610043dcd0985935c8f3c4bc6e7cfdfa354f971bb7088a3ab53977a9be182f0955216501dbf6136f8931824dc63e2d4bd0c4e8a32102734ee8766ec4d2980f2746170427e54460249d80e0ddeb421a9d873713875362ce82291c8f79b7da12480404e4a81c4c10df8710307065b49d9544573e7ffc99a5aef431b8adce874f679ece8d2965fee5b76ff9b3f76f29c9585d18a91004a1c27cbce7f8f08e944e984ed83c416b7402f7e0a033692a24942925cc8c8fd78f941cfcfaed1b7e7875cfe8ced69d4b5b18a3139e77d851a1c70035a82b42c71924d975a7713a406dfceaae90115a779a57561f104e52278b830f2401287376a6e228460fc1c5f1083263b7245154ccd004b7db85eed03b740fb6d6a9a3d387331f2696cbcac3d78569367c0bc6c559ce57061d2170ef840a4976e7cc653f0116609a098465b9b1d695edb6713baf3c5f362e4ba735e7af7ef18afff6bff98e6fdf9fb82bba434233fc5a1171d2083c3a5bec10b3c5eed3a65498f2910c446c64c98023a9914f13cbc5d9d60b39296f4f47feeaeb77bc3f9d58abd3802d2ab73ed81c723e32e744a80330a463a28c00b73d4131e7cc9fbfb9676b9d5bed2c4d681e4cb9914a258962ad9074e7fa92825b872d9c9c942c8d22ca3c2554029df24c3a1c5e285260a66412ad39a3ed0ed2fc6aa66d9dad43ac8ee4405fb86c0a0306be56a20d221912bb802509ba0cdc1decc8b22c3bf66be6bc2e7c783a13cbe0bffb9bef79ffed3d31063605f7774a9ecb2e33e840c8589e8848b4aded9194ba31d685da6f0c09241bc240c7def03246928d8729f3984fa40223836ae334cdfcfacd2b3482c59d21503b7415224f447702db45badef1b611db20444836910e333f3c1496dea9756378678b4e8de0dc95a52b5b13ae1d2e3573ccc24985490d34d135e801c5044dc9c89688ae9841a5d319a8056d38f7495117d26cac3767244344c8b3918f307c451c9257faf94cdbaef46d254bc123f03ea8db46dd36428db5ae2cb5f27cdbb0a1fceb7ff93da9bcd0550fc295b50629055983a23362897534fae89ce6c29c8c432acce5885a467a634447c4f60095244608db0af4bd0f492ab41644242206353aef4e13b326b60eb71e3c2f95ad8dbd118b318620646424c66d63b95d59769ec2ebc7b74cc0a535da706eabb075dd9371e27b4f4039291ccb8b9decc1086588d25d11323ac4e9bdedf4ce12d163a75811b456d97a6554c7b29255c9062967c4a02d1bb156a2ae9c9f9e58b733ed79a558466c9f3a359ce883ba2d8c1eacb7caf5b2d237f8b7fff50f4c474002514353c6e642ca405646ecd8df3df0313079899744303111c940f3fee08760c5b02c58826c82ea01ca4cc919d55d16b88d1d5ecb082c8477a799d98ce1c1da064bbd3144f7349f0dd6e85407082c29ad0715a3ae9d3ffdea0db7ee3cad70ad8e38cc79200c3c37928e5d8ed74045484918387d2b58049b075aeeef76d54e02c210b5bd8984606937c3eb3ac8d9988f50eb46ce0046ab8abb50972be70fcf7cf9ed0762045a665a5bf6ac4d08dd7625b3b6411d89f3972b7ff9f53dc99ce4b263fb7225cd77980ac99ca202025e2b4985294d1455b68025066e012ff94e7148499188dd1654a5a4c49c33b9249a647a0806ccd14902e6c2a499acf0f63801bb5bd507ac924067a282b495b6dd182184085676874c44485af8e5a9f05c3bd73af8720b6e9bd005dc9dde84ad2786bf42c590088e094ec7c0531006ea35500533c5dbc0474322b0648819af4f13de41b3d0c72046625b2aebb2518e13f930534eaf797cfb8eaf7ef11da7570f9027241288e011e0b0f5c6b26d5c962b2735befaf6119b3314c8e540b9bb271d329a8c5c32a779468beca64e086367f28cde185be5f97666f44e3223ace0a1b8c3681553b09470ad6c743ac1d60524912c536430a4b37aa7f6418c1bef4e99ea63d7e9095a122432a01c4b212583dec9924806918d2fcf67debe7a43eb95e7da70db3877f870cdb421384661f0f0fe359112b7a6aca104ecb20d90d62f9f110de2ee1e29195d1362816a6024cedbcafb3746bd0ed635384ece54327298984f87fd9fa9317ffd86e9ee1592664654642e4473a23b6d59a9b552eb605b2a7ff1cbaf48f309a2e1344c823419e1504e8594941e3b0369a174367e3c9ff9fd7963ca86492224e3da69b73381ed493e8c6feeee781b4a1d4eb63df39a52c266886a8cb8018e5a4787129a584741754ffd21e0bee17d41ecc0684a3205775aad903bc984ad0e4285eb32787f3cf04f9b73b80a5fdf750e2244734e33a80ab74fff48c404c9a009d96217000592e85e35f5cb17f4ee015121180441cac1100581180398d094480f1387290182e8b47b012fa1ddc2dea41141a2830cc214dce80da207effff46bea7641879334e3b78528eb1ed27285ac1090c2186cfcc73f3c8138f75321e5844921a9b17a9055c1324906a305e775e3c3f9cad78f277e79573057e664608a7b25aa814fb46824dfe385620df10ab167417508da159b039b0bbdf63d4b14420e61b8edf2466be43eb87ffb0efff10f3cadc15d715e9d3a960c0f25252705f4c9199bed908d5124284948769819e10441bf5ed034111e681fd076c3c2e6847f741eee94f96e62f44ac3c9870966657847259374420c5c94c98ca682d7976474ebf45e3926a5f68d765d5182610d03eada3113421c3161ca82cdcefffe9f3fa0a24c79624e4a4913482625c891d86aa18d866a66d8a08a602e3c2d0b5fb695bf797cc5d6378e52763d1e23ccd12e901c1f2bde1a62704a7977f03a981686268c20a703cbf209f5beabbd1294f98ee3b1d303e88d6f0f993fb6d88dfd1444da87aeeeb2d37b7154066d14a622cc3950171471d40ccd07c899b5567454543a1ace6d38a90c9673c509e49f3dce8c8421d131552c25d08e7b47f13dde1d091f1d1f83d1376a6f3c1c8d6d5df19159d78eb481da9e4ed803b37b76df1c7e7e5e708592f7f09389606a946ce46c9454d004930587a224716681c3347110c801ffe97cc56be0dd4931a811440420d4903dd3ca4004b631108c290bce9e98f6105c614a505448a2a8c0a08109799e18cb8dc7d7aff15169a3f1b40ed6dae83db0118c087ad3dd1fd0c6f006c40e8382a1a21c4a21158118480d5a5dd9d61b63a97cfadd197bc96c7a5b2853224f897c3c20d3893cdd21699731c4122a86fba04565c80e713e82d11a636bace7bd35cec7822643b49053a6dd2ec4d2296a38f0d3b962223b068b93c4c8a6c8cb67920a73ce4cf98e2c429932250b491b53124e2933a7c43f9daf4c0225efee6d8833449021587454213c934428014995e24226f6392720a503f3e10117851e880c860430f6cca914dee44065d0db6019ce18ffbf4e9088d4f01776b73478baed6ab06a9e49879916151f83644248dd3391aed0854f3fdf90a4fcf4bb1bb773c36fdbfe107226db04ee68db8fae5011091cc1baefb4d03b3d1c77e7cdeb23ea469e0aa246ef9d31567cdd68db6010bbe067b1f7a221b8b34bbe02d137466b48afbb5fbded74228952422892c8c01c85a28e99e0e1fcc31f3fe2cd100286a01ea4e8c8302ac68f97670e395172267cec9ace8b5b969360b391ef4e9083f04a6b836deb6c1ea43c31b71bdfbf290c1f9424c80033a78533aae3bdefeaae0525efd1c91c8a8eeee479424bc15df68584e1884196c072f0f3b6e27966fd0297a7c1a8413d5fb87df889e5e9f7443d23b6ebdd8c7de921c64034610811b2ebf5eedc6e2bd37dc1cc887042f6a45b20e894d905fc8e64e7edf1400de87d50ac13ba27105ac0d22a6dfb4288a36af84bad312a10cc9630534a38734ab808ffd7cf9f386f8d2d065bef2c9af8d43a1fce17e6b4cf1a9a322690149a2baa05b14cb64412c174a6f54e36a36024cfd871665d57ee0ef7ac6df7cf87076b047b5241f098c89a7019a401870c4d841463257cc2159214dc2a968d3c9c5c32ba34ce3d88b12038a753261f27dabab05c16e6c77b24260ec757c834eff8da0531613020c63e5388a0622c4bdf359f2cb829d13b44265221fbc05761e98ef7893ffde6153f9f179e6e8de7062a1b079dc1822ac6b25594a0d891be477676bc1d414dfb4e9a8a3165a5e482014fd78dda1aad55824e4298ad3029240b3c3968c19261695fce1b11a0099b264aac844e94692259a66bec85a7703c18de07231cb3695f6409213cc819467708614a815abc243e9252974abb6c7b341d010454c873e63417b20a3f7e3813a7cce79f6f78c0ddbbb75c6a70fd32b0e3092913aafb24ea3ef693e4bb94904b2227a1a4cc7c7fc24c684b2585009d210e118c0eddf74485b00b73fffa8747feeced034fcbe0b26e5cfac2badda823b07464d4c1756bb8d8def045c16c9f4643f6a0ba412e868992d428669492283a9155500952dad7964a0cee0c54762500758406eb86872373c1ee12ad5d0981432a14098ef3ccb274dece1363407367f195b53baabad3648784a269b7258f13244f8649478781ed2ca08833ac833861fb43fdfbe72baf5e3fa0cbe0f2f1429a95afdf3f7298ee2877f70cdf485d119d5eec3d454328627412659ad0f4cccf9faf7cf38b1336cff4f58ae98408c8e8fbb54d69ee604eed0162bcbfcb9ce415ab073fdd06d7daf0581157b6e1c8edcc6cbb8932865314dc0cd31dd34169f1228fa3981822035747dc48c9299649624c7ac0d2f4b210b8c7f1ddf742b1d11005d303528dd61ccbbb6a9a2de17de1cd43e6bf3c37eebb534aa6186473860a691a8ccd1005914e849270dd1ba50dc421ba233a68dbe0726b7c790ed6b6675cd6cb42b1c2f9cb461d1f3113f2fb09ad57d40e8c04e43da7afb25f68c4402cfe99c1fc7cbba07cc3344f78df9b9da244f47d1aee1348dd55ccde706f7bd22e3a8732f1d7f31b2439e7113cdf6efc3c167eba5c596a63da3183feb2231062d0e16e0c466d78c44b5a4f2924aa2f243afae2e99a0c98843045353321ac9176dd27274c5e5699bca3e2c4683b15970e29f1f4b9f1fedb03f573e569abdc4d9930615d85e3c9485979de026f30973d17954a16d64d60744048c9f8f2c5f9fcbc715e1acb9659dbb463ebe111bf5d785e836387fbd31e45399008514889f4126d0973a42d64949e32652adcddddf3fcf9897ffacf9ff88bbf9e48a723b813de40133e0251219d94bbc7c4f35558ae425441d38432833a03c74ae11427c6ed13c787d3aeffa02c5df8bc2e2ca333c2587ba59972c45ef8fb8ed142901584bceb4d40b61944d87c9093e004250217401203d9f7931ba839244575305bc734e86df0f878c47e7ce250f6deb1b4be6751c3f19e99357005c9fb6eb24a188292ccc08d6d087504ad196d5146efbbe32f42af421761d996bd9a2658963d5ad24603071f9de6cb3e6888e16188777236a6e381d3e9c47ff9c327eae2a083d08094a1947d8bc40722199b84c3c198f2842448b1bb6a1df62d9e10862a9266664b148208a730f8f690f9abd7aff8f3b7f77cfb70c254785a2ad7b573ad956bdf68bdd244d9e8384213e80879ec34b646305c48ea686c0c31c22726b9836c84652432e7b553fbcb4c72488405c33b2610e2ccb3314dfa625106a9389183d604f6945e605990e9c47c3832e54429137822b4200ad1076f8a90806e46937db9b9af9d566f6cdb4aca33d90ccb05e9896803b53dfc5a52269599523277af1e2873e66fffcfdfd2da3eda7b6f84efcb17a60913618c4a7ad1d2f79dadd8a1438ca00383e1c2dd0c6fee14b1bd90c48516460c2759e22e2bffd5ab7b7ef3ee15df3d1c39a8d2dace9486efe68ab8334620385d9da160bd91a4a3110ccf0882c9864bc32d70354673726f288d5bcdb8263e7d68bc4eca36061e10bea742487be56b52668324c1f5a6686485b10f252336bcae4c79e0dac11294c38bdd17b837948477c1c78ed1b3648cdd45eaee7b5cdb404208354a56743a50a6cc34258ef7470ef7339f9685bffd5f7f4bc42edce11d8b5d0c4ed32e758467ca7420dfef90868c9ddaea9eef2c2adc46625b97bd59861336b0e8b8ef5b9b628924859232799af8f6f191bf7ef3965399b8d44ac559a2d25eb2a87d74640811b15b95a2f84bae67231081ec8149e02a0c13beb4ce53edbb68d77597c687ef815e0d90412989344da8cafe2b02119c8e8e8acda43c6379c22c93a64c2a89fbe344ce1d7ac7fbcb36639a09f7fd065f7e7f2102c6d621d837cf5b237a0505b38ca613d98ce4158bc66cc2e934217df06504fff16f7fb7c34edb834dde1ad1d8373b887dcf17a108a8bce0a742b660b24c991ef8f9b64f9b4574cfe38bd0187403a7d2a94812268c62995c325f1d0adf3ddcd13cb1f9bed9b90d61ddda0b141a1e8910c8024ec7d4493a48baf723cd8614a335c33a9c8e993e9c6d0b943d8b040d2bc24088de767420939332a5818a0e342534cd5849508c65a9f42e0c971d229a43123cefe646ca4ab1be53b9d1f17503dda566c8fb80d282186004a24ab86338492af3e87cf3e69ebb63e11c83fff9dffd3d4f1faec894d169422cc8b3205976757508e690559912a83aaaa0d61082e374206210c9f7238f10237017dc0b42c2040e53e76e7af13a3428def9f3fb23dd61c3691e340f96f1b260c21ed30c2064267c02f60d221130330e52787d37319d266e51f8b2f67dc373ec3d0edf73b3d2642f306dbb8069beb3208b4c68c3bdd346b0ad8de72f1bcf17c73718beed95d803bdae4452963a3816c5a68ce5cce84e5f579c469e1e711c651faa52c4cbbe5941c70475e1f1fec8f1c1f8725b919bd399f90f7fff7b0efff04ffce5f7af7938ca1eee55a3e8c067fdff7a7ab32759b2e33ef3733f4b4464d672975ed0d84800142068a786360b5f66fe34fd53f33e0f321b4924050e454a5884a51b68a0d77beb56556646c439c77d1e3c016b6bb3b6b6ee5b5559b11c77ff7e9f73be80d0e278e9999194d907674de49241b620ba515c37b214c4a369a6430255d104ea74db9034b1d4c46e67beffe28e4f2e3bebe841794f133e1c8673c809f7c6a44a17a779b4bb258f406100d5e8a1992bb7c79977976bb02f5f1fdd6e483194ccc0fe9441dbccc8c346bce4dc2908932ac38dbedb95b70ce055244668da2d6699aa4855e43a68b63ed029b3f70b722d80541db8ce4fe723c38d793b915fbc608baa3c40605929b9b26dc64f7efd2576e97cb0146e4aa1a832a452f27a9d26150cc3cd18d7a3a530739041938ea933a8d1a033b8290ba3344ebd72d0066350f08891a60c96c01b3fb8bbe7e3c713798a531b3e50df695688c9a8930d922a969ce290355f2f08e3b628a96ed850f6e15411faf5bfc79d7dedf1ecb4f2a753652d838c0535ec321863c7cca86926a515ed83ea8566d1b3b711279bc39c7979bbc48b0f98971ab72405b9227ae6031982a5f8827468a7c7e0e415646d942971a7473effdd03cb5499ea444dce9a2f7cf978e1e7efdeb29d9d97d34295c68d0499010315508339671e93c06821ce9078970c8baad56c048b2f83d6954cb40310a30b242a6e3b99c4477707be5c2f4cc3e33d36a638f188925d500c934632c1e2dac26d50b253f3e071186d1432e15d4ad7b67a42499ea0404568c3701a954c861ef1c9a68c11fcccf1b093a667d2266899b17146acc5512f39b7738dde7a4a74195184a5cc508d8e2631c24c2e24776c80f513e283bd6db4b6d3cd41945c0bf7f7479e1f2fdcbf7f4ba985f2a42c96c896783b761eb60bdb65a38f13e9e189d7cb81bba5b05c138a6ac6eeca680da52243c9285d762e3271b09962714aea2ee46b8408db214b340ad57891277e7f3ee3e6e1a2e80b45054a50cf6686b8d0bb53b4328029477b6327f3787e8758c89bcc060631e037632ee0b2631d12319a7487bc5f4315b3285226c495653971331d687943b70bb3c0d7ddf8200bd9065316f294518f86d7de3ad9609aa698850e4135c2dea377466a58eb30126e4aca3731634e29a2a5f7b7ec231e83f550e25d118d54daa62451e6d41943180ee776e2614d5c5abc212711b257aa0a7bef088a66424d3314ab8a189006ba0bae86476dc4d8072aa1afe9c3f8c6f1c8bbbe072e634e1f46d2ce9c0abdc0d80d69833407d2d23d32615ffdfe73b6d3e0fb7f7ecb7ffdec2db79eb0e678d1609e92929232866083c83324c8c9943e845e523ca35c71558e47a15da222f406a554b254d4c268322c45d8e29affca69d09e9f90e516c1e2032e89a19d7ee90c1fd72be948b7c67a6eeca79da1203e58a60505daba913dac0a3753825be399ca7e4d549a65b218db70a6ecacbb71d9060fe76774ec748757d3c2cb12ad80214a91c86fd900bd0e5a321e95b40636931c3c17ee53e2cdc34af34ef12df0c46140c48cf66d47fb4e2d05f3c4695dd94e5f3338b0e48d9b5705fb1c3409c38c8493526204e6c4b8de49e64a72254f53a104c01c27a13546703519f526916da2b68d7d6ff4ed19cdca32413de42b055c7057dc099a6e5b4945f1146a805aa2721d6367df3a6ddbb8ac67da10d6d38931e2f6576bd43a9355d09a49b7133c9eb89912de32d91bd50d72e4ac7c38698ab461f64eb689a76de0cdf9e2b2f1c5e5c2dd32f3a3a5a33a2149a12959768cc450b06ea8548c0b4d9dbbacb82670c74d306f0c122b99ea2bb3171649f854b80c301aa7778f68bee1bd0f3f60fdf2533effe2897d771830b271d93a73ea68ca885a54eb0873d6c0d9b7be055ade8106a926644ee4e7cadd22b47ec2fa601b7124cd73646de35c50c9baa09ab8b49d9a0f680e63898e1054f4d66908975d18ee9c9e2e6c6be77974d6e70ba285b19e280265194c53cc20a65cc8aaec3e8018e315579044971e19e6348177c67ee25817d6d649b293d571cd5c76e3ef3f7bc35fffe016c331f53842ba5fc372a127108f83813148d5e11a3e110b4f5276899cb40ca652693d21a3e36fbf46e73b6e5fbce440a72561aa957537d6dd280ec50b5bddb8f100ddbc3b2447b291939255fe38ad1a38815a2731dc85de0d91e8752f1560500f99edbc311d2b659a99de3f20cb441f9de13b25cf6ccf4f6877ca32e3c0ba364e5bc7d63397b5f1f878e683db03effff09b1c5e1c184fcfd865f0ab4fbee0cdc3196e13560c1fd0c328c2486059e86ee42c5120b9326946a7035b6b1c0aecd7a2cd93a0ae6c38bf7bf7c8fb2f5f91d5c298223b8e9172465c485670126d74e651231a8b73f0b8ca2dc52966f31ce84c3bf3eef3cf519978fde14b0e7960798f968d08df7bb1f0f5dec9546e66c3bd44366c4fa87b1cdf44e922e4526f200fd6c707188ea1485a48654736c0530c3fdc79b376fec58db2be338ef7f794e31d43fcaa6ca9d09cbeed945ca27a54a1f5865827e13c9d1a8fa7333ffadefbbcfcf055b435dca0166a29fcf8df7c97cf7efb8efff9f95b7a9550160cc705cc0b43b99ecf1daec18d6508922718cefd5c786b4e1e8321827b8a5cc2be4748dae3b8ed96113106d1e3193ac0151bd1e37a759cb9f498650b4eb620b5dd3b831d4dca3e84fbd7afc28698a2409bee6ff8facb07beffad7b3effe5179805619e93e2e60803d462f8158106748c9d7ddd89438846a181a393120446f431dad619184d8ce1d05c4063ba04859e265c13be07a4550e0b2ed1e3efb68731c484fb3cf1e2e56d04b83521089833c605baf39defdcf1dd17373c9c369e37e379db59f78175a113059221a8090b9922314cd1a2ccea31c74d5124a41c75cad3b6338953e47a664f8ea610ee6540ac8045d13964f0703e037e4dde28c384641e2ff0017b73e69b7b8eb55024e1571d4392606c0f75e2552da402929475ef2194528ff74b8e8347f1846eefbe607f7aa4cac4f17847ce5390c5d301a98185fbd06bca2ff3e6b30b8763657bb7a2390c8288b31227a99ca3efe31e694541e9fbe0727e62f7c1ebd7c7b88a05c6bee26e188196880549f117fffc1bdca7c2c3d385a773e7796b9cfa60df9d8b396d24d6d16832e8c9d96c447b591692c5f07b8cc418513cdecf13dd9ca5387311b20e92358cc813648c241b5906bef7ab90a35370d2b5e2321f2cc919bbf1f4fc4cee04a8209d6e0ada994c391c2bef9e56be7157e9dd60c49137e1a87bb0aec32812325a7d7e78667b7c8a424305722297e8a7d702530d6c24a73838fff6e144c9cae3e78f8cd119ad41eb64eb8cd1b9c884a9a0167d7eb38e24e1f9dd03fbe939389bf926febd2bbd35ecb2a343989643980bb7c1bfff97df84060fcf1b7b774e7df0bc37f60b5cd6c1bac3bbbdf3784da9f8186c63d05b67df8db539cd0666cecb5caf670fc786b28e704b14a2d9a74858485db98c6813a4a1a4ebff8308cd335d883b6d5e4061dd06cf5d78386da401553befdd551edf343eb83fd287611282a6614a37508f0412aee4ace8be294f6f1ba7c7af393f7f056d637d78a66a228f2950c13c0294d1c4bb0df636b06ed8eeccf31192905a94e86a835c4a801529862cee895c267cefb4cbce7a7ea2b70dc348a9529699344d8c7dc7b71525bc13ffc78fbf4926f1d5f385c775e5d276b6b171e93b2706cf9b713a3776374eb6b3b7c1b0cc7063b7ceb90fbe71bbf06aca94041787ad6960f349712b0c8b6a7e5c736ead0f2ac292a0e8f5033443c45963a242ca0b9b1634572635ee8f9963cd6c187f38c165edccd3ccba77ac4776a113b544cd90b3e02ad1e5fdab1f7efb3f5c76e3b26e6c97c676de797e0e11ded3e3e0bc0fdade694da321a74eea8d0f3fbae3e9ab678e2f6ef1ebdc3427255da7579284be355c9c6defb4d159b70ddf8c0f3fbc4752258d4eca30b6c6183dc2de2ad7aa78d09fcfbcaa335f3d34bebeacf46101bfbab00f616b46ef83d68ce7f5c2d6775a1f6c579de5775edcf3fab030e784a4390653c4242b72611e8d441f300449c6dbf34a7798a64c2a194999a2952480678a183642f2518b32d598154f557975cc9cb7c4791d7cfae691afdf6d74578e07a520cc35139d0d2349c8707306ba149e2ec6dbe74ed59d0ef4ed99dee3e5327a0a943b0955139f9f377ea03ba7af3b9622829fae6336d18c9aa1166cbcf50a76a1a4cc9427be7cf7c88fae89c7beefcc652667c576c1fa4e4a19f3c6c3a76f387d76a1cac4fff2fdf7f8c3db858fbf7acb17a7959a3a35e7ab931a461b6cdbceb8cac4efa7c2b76e674a4928112a2fa993b2606af4d511cb0c0dcc3011e91773e5cbcbce8b9b1a9d5a2f2497ab4323b4336699210334d3466677611109bfaa66ee0e83cb9c787739b0befb8c87d305f1d7fcf8a385519c72cc984664b51ba4fffdc7dffe0f6bebec6ba36f4a1f8eef465f0546a599d02e3dc8b99c91ac241954ebbcfee607f45598dfbfbb763d630c97cb95e394c2688e8d8dde3b7d349ecf4fe865e7fef501514587d2d715619073b885dae9c2fac53b26e650e99ba3645e57e55baf6ef9e0f6c86dc9dcd68ab9f062cebc58663e3ccc7c3809ef5541074cc7fb20ae191cefc28ed236029f4ca14618d62387eccedb7dc3dc3996c2b1566acdf1a852212bd196bee6d6b0464e025a29a2a42b0e7372e5ddc999e78977cf6ba8cfeaccc37ae1b4c1612adc2d1afa7c13d4afce066f8adbced84e9cce17b6d6d946c77b43652789611232d25c2b9f3c756e3eb8e5cd275fe07b4485b44e941cfdeff06e6ee462f1e86a3b39394b5df8c5676fe94362c429462ad714cbb444b3f8fa4bccc73856a6ae80514aa6a8722899d7c703efdfdcf2ed97af38a0d0cfaca747faa5e35db10c7d6ce4a4d4ac140d27123ed8088e7f9128b04c0c13f8f2f94211a8d79fb3e1b8c7715c6974db400387945e2141cd9d86621a23cd8735233583c2373fb8e5f6fe9ebb9b894b13be7834fef17727be7a8ae8d55412baef1db1423e2e749c750d42603718fd4237c1eb826a66aef1c3cc45998e477ef58bdff3fe0f3fe2f39f7d8ab71db31554aec3f199de0c1f1b73cdcc8789639d787177cbedab57fce41f3e895aa01686edb0ee78eff8959bb191e3ea4cd790b4354c7ba46f30ba1bc3000b599e9f37c4078eb1b5a0b1f771e2b23fa3286a09650083e483ee8ddd36124a91ca2f1ede5252a08bd1b7d1eb551f4145b312fa4917867918503c6450c73ce88c080f1ac8b651d28e71e6c317839bdcf8cefd3d872973d906fff4c9337ff8ea1282c0e31c4b1676b1c8b022a1adb170ea334e315d2a953909f773e5bd17470eb7331f3faf0c1b8cadb33d9db17e65869a633d74302a21329a526599676a768e599092f8cf7ffb736c83697ac526c297bffb0c3f5f82ce93111fc4d0d0c613d49eca8e004925421d36486566bebd21e584a68166bf0e3b32a3ad249cb63baba548ab27c892d8c68e31f8c3d389d913c794b8a9b198a29b922db4c611dab280b8049c86e7464ae92a0671d656a1c3a144d53f2b216652879c787507dffdc6916fbf788fa9cefcf48b133ff9d967e8749ce8e3823faf580bc45b0dd4436cb7e489c923cf3455e776491cef67a649382c13ffe3979ff28dbffc333efdfb5fa192584a602c8944ae398cb43973b8b9a5d4c43c4d1c0f95dbb93270fed3dffd92a753e734e0173ffb145f57465fc162a03344d02ca45c804a10df23d08ea4a4e2d858f166144d240928202df1cf37e58e46e2ecc2904ca76252c806b3277ef1e62d9b39b5e66800a644c1f011105af6a8a0913d107827d8230a8d8d29af2cd92313ec8a7a80c9234dac43b8acc2cdb1301de0581bcbcd802c2c87f778bb2fe4744cd1786b12dcd9088d6f4d1392e43adfcdd462dca8b2cc4ace1377b5223a78ba747efbd3dff0d1bffa337ef7b73fe72ffefa5f43be8e2f25414ad11b2f83455f302421db99d432a8f2b877fed34f7fc5b70e333ff88b6fb135e3f9eb134b29e80cd90c7b8eaea556871633e1e411dc08196a8cfec63026514637da7967394ca8564c32bb28d51dd18114e571ddf9f8e18992137315ca2814890e2cd2496ee8eea459a25f65195204ee0651a4158cdd12c98c943abb65e8425b77c46e78d78c0f5e44ffaa3538edcad7eb4ea2f0dea2bc7efd82ac68d0c0de483ab3e5338989f9e56bda7e26f78dc321f3eaf6c8cdadc3542009922a266079e7370f67deffc8c887c2e7bff884f77ff8ddc050520dd3c8760e19d230920a2517d48d747f24ef3b73cd3cac1bbffbf80d2f87f3d15de1e5fb996d0cb64d38ef8d619d9c60a75dc1df818fc4e889a486684c5cb6ebe34afbf571910d54a34014783a373e7b3ed1dae05827344514a92aa876b24e5429088ddd15ef159b62f429d618840132a59d2a85e9da21a80ef793338ad3de29a7cb2968ef1c1dd466c6d3b97257124b4d2cd5286990a7c3442a0f57e95ea2e619c7b89cdf9154b9a9958f3eb8e5701f23bb5414299546a67ae740651ce19f7ef13bfed7bffa17fce1ef7fcdf1f50387d777f4bec6ccae6f0c73868537687f3e719817e6a5526aa5a6134ffb85a1ce9bd385cfde3df3e2cd990f6f27aa17ee96ccd60eac7b8fbd2e1ebb69f042ef1bfb3853342159b17645010f906a45a5b2b7c1d7e7335f5d366a4acc32384c2928e5924230352c0c669e308c498522c2937564444ec03a2477640479e19ec8a95135d146c2c5b1ad737f9b783e5dc8a2f4ee9c4ecefd34f18d9709b511d0569558616243e24586d1f68679a0d6de0cadcacdcb99e341b011fded795ea04c3046e0df1a2ddc6771fee66fff91bffc773fe493fff20bfefc2fff1937ef1dd9b6cb956e4b7fc231b2c2d82f2c53222d33e217debe7bc36d4adcbc3c3286d31c7ef77ce6fcbcf3f864dce4992ce180c8224cb9607ec1ac61ddc0335a6e781e1b083c9e3a637bcbe6c25ce6f859ea4452e1e09952c072475bb042e68d4a65eb67e652b01493baa4833e22d76b02324604f488e17a3105c9dcd480d51e1e365ebf5ef8c39b076e6accc0ef6f33f787c80444d6c042eb59843cc68608e40cd585bdc76832cd29027769e671dd384e9965094bad98b3cc0b65ebe43ac213ad8937bdf3ab9f7eccbffd3fff0d3ffb7ffe813ffbabef9316098d7bfe63abbbe323d31e37c6b633cf99342f7cfb1bef61bdf3f4ee842e57f19ec495380b3c9d06cffbca3a9cd11cf1b0a0fb1e1ae3e151d16e7d0b22cf9d520b77871ba6b9a0225119ab7254255984d1f7641cb53050dc07551df3848fc809243408b701c58ccd0d758d534e0a56aa7863ca8ae900135a1b7cbe0f3e384ebc7f2c2c13e42ab48b904878366a36b20a5945f0dd106f4c1247b42dee73a654f05df00a2c8a67476b4443a524f65aae139e50c9dcdfdcf0eeb2f28f3ff929ffeafffad7fccffff7bff3def73ee0f8f2969a831d151b5c4e2b258323e42488c3cdeb034f9f3d50cd49559935935d78364855c8629455989b6165449bc30aa33a6b52dac8a4b13189428a217b3a540e871ba61233eb52271283ea211f9126e4dc30f1d00bef314320f5d0f98b5093d152626cb1994318cc6e41f4100a1acdc6a9c1f3dbc6749cf9e5a70f64cdd4aae40277b747baed0cb3ebdd2b94eaec6390cb5cc813aca7085b4b17161ca5b3d4996586c3a2d42593eb442a15d7488b4ca5a04478a13747742315f8faed33ffed273fe55ffe6f3fe6e3bffb19ed7ce1c33f7fcdd6379ebff89a6c0a0bf4b6a2f292bebda58ec1beee1c0f47f231c509a77546a9a4c9c91afee8290dc4cb9ffaf0c3120561db62c053c660d098a78579b92125c27de12540dbf0f9063f6a4a96ccc5b650d9a7b8c38b282957bae66bb30ecc942e51cc8d6c681ee01db7c4d83273312eab916ae6b3f3c6072f2aa50866e1553d1e8e8ccb099138b199057d9dcb5c38dc64f6538a3f781bd0959c9ce3a24c4ba1d60cc42a919467cc43ce4d1be45ad159a0cda41ceb2446731e2f67fecbdffe947fffef7ec0d7bffe945ffecdc77ce72fbf157ed16698274acd6c9747acef60707fac949490a5e01e79b5be5944573d739c95de4e60e1f3374b3483922059e3e2cfd498d693734805b7beb38c895a85dd849a9c99908098767a5faf6e0b25eb8612cbeb5c3aa61515438621795c81b39966a11d330bfa1c11be7cbbb11c17be7af88a790ec3ca61aa1c0ff1683a5d821f1553bc253a3b644773aa24664abd9a4a8a90ab727c79cbfb1fdd717c7d241f27e6a9449270402a092b02932026249938dc2e2c75e6982bb74be6ee76c16ae63ffecd4fd143e5c54737fcc3fffddfc004494a5b375282ad9d19404e99a9c6e42a8b32d5999a2ac7b9703f276e2bdc97ccb1cccc19e6ac1c6b0a7b4a0e4bd63155268dd6ef448dbb54a1a648483206a38f18bf4807756a5e98a76334d882720a7faa0a139daa710738811c567692099bc5496cedcef36523e7384d7d72dab99d2bc725d8d8a914c48ced6cac4d49a9e17a215d11c9bc9e76e6a506926ee05a382e13ef7d708b4e4aad05d798c9ee7da72e8531f630a48b322d337d6b0c81e53085ce4c9cd3be7355dff15f7ffd39df9de0477ffd033efbf51708c6cb3fbb671814266a12322b4d61acce989de976a29482b707ecb23197815bb93e3c4ac4ff813981b54caa8657e3d4cf0c624f63220598951c75a34a04f176e911851dd05284a9abfa35a306ab43699912010a5c3aee7b18d1138c142680b55b740c86f1eafd03fffdd32fb95b0a53d5c0687261df07b773e2fddbc4e97c4d59124f184f835c8f37bcf71da154e1e94d27cd4796b9508a524a45b321aad7b84db09168ba5ec1952167128594026af2d6f12c2c9e80852ca194f9ece9c4e32f3ee52fbe790326fcf6fffb82fb0f0e7cebcfdf27df85959c27619e0bd33c2175c6fd827a0bdd181927cee442000261b71392142ed6a36dad33ae2bce0019e46171b5a70971277bf468448c1dc1c71e6b4b70441c618f0f951cfbc4d8c04283d0093c66f41e23db04ea83d7af163efde20bbac06d0ed38ba951aa72ac1af9e3a278c96cdec9e2982a6d1772490539dc32df6fc8d8b93916f611cdaae998b8ec2b539a99aba064ba0f6c14725df0d1f1d6184929445733614c53c12593ab53ea35f4acca3e36feeee3377c908c0fbf770fe7cc6ffef133ea4de2836f2d1ca7783448515211d6872dba98ea981a73725281b50d18d12c5384668336f60805aa903cbaa48252b5c60bb82bec6b688eb39329e09dc933dd15a7201a6693ec217715032ccc895262567e5e0d5d95e91088e3dddd913fbcf98acff7c1a11e10cd4c3941cfaca7c1314592727862ef57ef5749accdb19ec9d33cd1da99c354995fc6063c3545e7c1beef4c3aa3aa5813ac44e0595519ae91ef4d39f435b542578a24f2be3365e7b96f98645edc2ccc25713e3b7dcefceeab777cf2e6cc8f3e7ac59fffdb6f71fef2914ffee92d754a7cf0e1810fef63898394d8ee9a9b6318d60071724a64cd61ad02242b53555a1f08c610234b43c7a0394837a44496c1006b4a3ce69d5d62725cd6a01900000b5249444154276e94a4f4162ebad02b284384e28d6d33069dd91aa58695717971e0b3b75ff2f9a5733fcf1c6b62a913533296295eec5b6bccdb429e1d4d5143f8b59734cf4eceb990f2441f139711ab44d423cf5416659a2369beef0de90d3ce329f0f352e2c3cf4530b6806f7d904aa1bb5364079909ee31768d5515ee96898ef0c9f3853ffcfd6ff8a02adffefe919777377cfde9997ff8cfbfa32c95db7ba1688fed4443b16638999214f31ead871e83ff5c66e82bbb5b6cdecb53d068b222dcc270d200cf81260602135cac2618a3c4926adda925b857eb01556d0c2ee7674a3b72980a1d414ae6e3df7fc1e3157bb99b122f0e897996abb2c79953221525cf421b9da20924b1b5d09e4982ec5a718b7580ea4e9d33e69dc4c4b41c20c3b8348a8356651d8321eddae9ac7886312265afe98f81c7fddacb4f14157a5e492b1ceb022f3b321a3e12698ebced574f677efff3afb8495ff167f733dffbe7ef717cff255f7ff286b79faf3c7cb9b16d46ad4acd11fe086039aec4de0c5c49b532e94b44a2a7af9a105f50b7f8c6d260a284d5b7443a472ce83995106e0830ac507bc7a5f0fc7c66bf34ee970368448fd6def9e40f6f48cbcc8b65e6b60a7747e55013a542b7d07ea623148bcec2be3a5355a0c7fbd22380985d43b0943421a54614b4a7e8834868d8bb09f990e9a3e312c093a8a16a306281734a99acb0ee16f9ae0487e9189b2ffa39b4975326d78e0e43c8d4a5b0136ea0c7b7031fc66f9f3bbff9f997a45fbee5a51847e9bc7a3f7190232ecef9b2b1b710810c37b691c9d7f8686b4adbcfd8fa8c31713cdee2aa740d7fa94ac2242c91a285e142c311efe80070240776df77e75ddb5084ba1cc25fa489af9e9ff8f8f9c2712a1c72a268e56e169649d012618f798910a1e480ba7c28552d1686f6d88596452865227b2ed89eaf07c60df6d89592bc625b47a644be99a3dddb827aa8530d94e462cc3334dbf17ec0e672ddf518e76e590413a5a6234576548c9a84f27ea29685b55dd0ad535edc42db29c3a88789510afb504e979537cf2be7d353304b96b8cfe1a8eb36708bc0c6dea3aa351b7cf6ee31469cece869e73b87ca87c799e970cb306573c547101ccea0115b992cc5be1aed422736886794a109c4f9eabcf1d979c3538a0fbf4cdcd689e32ce49ca9ee14492432de3a4cc2243568093ad35ce82d16077513a8c2ee9d2c49d0049e1329877ade7d402db11b5e2bd77021cb349153c6cac41ee7d258ba301c72437a285ecc2222ba9f2f812926474a88bc5d120b472e970b09e7e66666db062fef8f2c7fb4331e279a2863c9ecb3f328835bc97483d6c187d1fb15b0b2c148b141c31c5ed4426bb1bda3e4c2e310debcdb98cf3b1fceca7d8923762af7604ed211691951b0ce76beb0b69552327b4a3c9e76beda630f4d9d32879aafa72ba5a4c0da734a74076d42cd4e9a334d8c3e3a99449630f34a024d8aca46b284582627c998646cc4eef844c2655c1f9072054a33e4a083c50d6550a68c77031fcc35c279248fc05f6f482ed830d48c1c33acf84588b014a8d391d61b753a301f3a9c2223906b262d3396152b1b2d2de46d63f7ce3660bdc04895920274151f6cc359bb41d3eb0ec78ca71aab1411860f3c095fb4c167970b66c671de5089b527f735f36eebf4b6a3de79b70f72c9e46921abf0eaa0982a532e2c45c132290d727644168c78f4d45c3069e0ced69da2469d4a405f629806f28b67d0728df1e2a856d23461a362e78e1b7833f2b1d2bd6123d69e0f15f6e70d1d500e95e142d28cf7906e28252ae4d4e9eb2362124b74ecda1e9684cf15eb3bb6ed88647c5be9eb996549a4ee1c6e0fe4c3c4b0c6ee89d90a7e5c58fb89b9473ca9b75863a26eb4a19021a905d59d3379545c12738e4cb1692265650c63949dde8de61a9694015f5f020f1e0e550baf968c240d8b570e25ff867228955c23b4c1757974d678fca91823b7b880c7ce9c32739e1131641e30126a4ad68c781482a892c547ac9d2af18d766d486b482a4498a390a6191b0ddb5a14325ba3e42882b66164adecdb4eedceb48439a20da7d68a63681672511461b41ef8e3c30937e1e69819fbc6a2ca7228a49910be6a652a9135cbc094a62bc8d57095ebd780d494bc27f66aacdb33aac648ce603049ec348b465b4552671fc2606322444c0e24a01e0aed026ece94202f05974a7709aec807b50e9654d9dc51af4ce93a1f2e12bf8801a50ceabca0b52018a7f385e5304571e6c6a88371515a0792937b92eb8c355fc54819c9397a2e5b272d4b2c4cde37b81a02b7d6393fad689d1051f490989663ec5a3cadb81a9a0aa852528d2c992a7ddb185b431c6e6f0eecd6787e3ab394c4542b53d578b9d2994bc5eb4497ce6129acbb309a9372452d9cd42e827aa3e94edf056917661dec39886e1167520f1362566041c78ae64697f8c05c1db544a24612531a39556a9ad0294e31c3467c26e25481e44253286967c9ca94152d4229917c39afb135ef3857a6798291e8c9639ee0094dc66eb0642527cf8ced84ed2ba54ee8bdb2ee83d49d52139e12661754052d13bd7934c952106c5a94ed1ca9915c32d6067ed575e552d8b70d9a516aa500eb18b82aa5384bcf5c7484d3610c7a0a9da437b0d4af854a34d66a6dac16c7c4d12ca029526c284d09f3b09e7bdf511aeac7106d1f6a84bbf335d3ec86b64cc97a55e144762c0b11d8eb504aa254a5d400d7cd07270f5d7296780f1a1656453ac5c336dcbad0aee649f1414d422a29749e1a0a4f3a78874351a69cc842bb66a58e58bb80c2649954639b863162b142ffe3faa533e4855484b60dd4226feb39de0764c75a0689cd7b6ad79e960d4ea71527e1bd713eaf88addcdf1dd82f23363091c853e6d25732769d7a3962818504b31461097721fba05f457c661b55a127218d4cb30d618a628c8290480386655272b2665cae249b7b7c2d899161a9896cf6a7d852d24c52a327610c612702877f4cd7741f88092525fc3afabc348f5dc93966c1c553ace56dfd8ac2474d93534e90a66bbbe1c2f6f448960465be56860d19cef3f3137ddd48c703db30e84e3d168628497238d71c54667adad8f70de94a491322315c998f075a6b24af6c3618fba078412661dd774a53b666e494026f1462b1a75b90689642e2e4837dc0a45c37623b6289522a8cce0e6422e1a208a21142dcc5912cd4a12415540bde1be60aa5a09d78dc74c16a62928417c70c6c18355798c19e373246b1983df49ea8d3ce5c842e4a2d9d5c85f5e2dc6a2197c1768194b72bc4eb51f449428bccd00797e7776cefde4511215729cf7567a2a9b11c0f94b9506b8a291030f64ef1c86b59b788335dd742252d2457d294e3ef928270033c39cbedcc723c201a2b548ecb444e8a26c11a681be453fc998851ae81899c2c523496835bb5684b8b1a4d1d17a7fb1a0e8e146d92f08342cd51ed0e6b18b04b00b84a2615094e9538b3333c26616eacb6c5e3c2210d23a7816867007bdb710d3b57e6da62c0c845588e2996084e132eb18c2ed04fa724e1fe00f9f4f8967e75bc2d370724dd633d7e203c109b7ab881da59cf6b6c9d9e96d0da8f58dcb3db0e409a67240d948cf88a66a58d95294d0cc2b0126b13051a74319a6d24338e87298eb4a2083b7edd44ead6a3b87367b46082484a4a9143b35e23cf9b3a3505e550540353b93a19440cc9ce9c84b1839a4657d515ae0b3f4d06968c9ce1723993ac6275463471436188d33ccc32221d8692481c9292f30ead62d3a0aac6b20917941480721dd402fd94d87ba396a005a5389ab37077f79ae3abf7e1788b5df762f5ad5fbfb9ca7e394592726bf475c55390007529a8686ccd938861faf52fa911e729146438d21bea89e9784f56658c467667ca95b676689d611b3d3e65cc84e6160e3acd24cf51ed3268bab3f986982069c7d9c362922bc21cba611570c37a43cc63bedc9d9aa2ed5c49e4744d5e69f828640f7d5a16476a4134de0522ceee71776f5db1bd92b8eeb821648731bc57e652495ae95d9124acd6797838e13dbe2e5e994b585ccc94ff1f054bb55cd594cf550000000049454e44ae426082, 'image/x-png', 'image/x-png');

-- --------------------------------------------------------

-- 
-- Table structure for table `zona`
-- 

CREATE TABLE `zona` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `zona`
-- 

INSERT INTO `zona` (`id`, `nombre`) VALUES 
(2, 'Norte'),
(3, 'GBA'),
(4, 'Capital');
