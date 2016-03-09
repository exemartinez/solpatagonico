DROP TABLE IF EXISTS `sys_actions`;
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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `sys_config`;
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
) TYPE=MyISAM AUTO_INCREMENT=11 ;

-- 
-- Dumping data for table `sys_config`
-- 

INSERT INTO `sys_config` VALUES (1, 'auto_logoff', '3600', 'tiempo maximo sin actividad para que expire la sesion. (en segundos)', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_config` VALUES (2, 'maintenance', '0', '0 = sistema en funcionamiento normal\r\n1 = funcionamiento fuera de linea por mantenimiento\r\n(solo acceden los usuarios o grupos autorizados a saltear el mantenimiento)', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_config` VALUES (3, 'pass_expires', '0', '1 = las contrase?as de todo el sistema expiran\r\n0 = las contrase?as expiraran segun configuracion individual de cada usuario', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_config` VALUES (4, 'expiration_days', '60', 'cantidad de dias en que expirara la contrase?a, si es que pass_expires es seteado en 1', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_config` VALUES (5, 'inactive_account_expiration', '30', 'tiempo maximo de inactividad de una cuenta, una vez superado este limite, la cuenta se bloquea.\r\nexpresado en días.\r\n0 indica sin caducidad.', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_config` VALUES (6, 'pass_black_list', '', 'lista de palabras prohibidas para la contraseña.\r\ndeben estar separadas por coma (,).\r\nsi se desea prohibir una coma debe estar escapada por el caracter "". ej: ,', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_config` VALUES (7, 'pass_block_history', '6', 'cantidad de contraseñas a considerar en la lista negra', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_config` VALUES (8, 'pass_block_user_data', '1', 'indica si se incorporan los datos personales del usuario al black list para su contraseña.\r\n1 = se bloquean los datos\r\n0 = se permiten datos filiatorios en la contraseña', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_config` VALUES (9, 'pass_format', '-6,a1,#1', 'brinda el formato valido para la contraseña:\r\na1 - indica al menos una letra minuscula.\r\na1 - indica al menos una letra mayuscula.\r\n#1 - indica al menos un numero.\r\n-1 - indica que la contraseña debe tener al menos un caracter.\r\n+10 - indica que la contraseña debe tener como maximo 10 caracteres.\r\nsa - indica que no se permitiran secuencias alfabeticas.\r\ns# - indica que no se permitiran secuencias numericas.\r\n^ - indica inicio combinando con a1, a1, #1\r\n$ - indica fin combinando con a1, a1, #1\r\n*todos los indicadores deben estar separados por comas (,)', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_config` VALUES (10, 'wrong_pass_limit', '3', 'cantidad maxima de intentos fallidos de logueo.', NULL, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `sys_groups`;
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
) TYPE=MyISAM AUTO_INCREMENT=4 ;

INSERT INTO `sys_groups` VALUES (1, 1, 'Sistemas', 'Grupo exclusivo para personal de E4system.', NULL, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `sys_groups_actions`;
CREATE TABLE `sys_groups_actions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_grupo` bigint(20) unsigned NOT NULL default '0',
  `id_accion` bigint(20) unsigned NOT NULL default '0',
  `valor` varchar(255) default NULL,
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `sys_logs`;
CREATE TABLE `sys_logs` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_usuario` bigint(20) unsigned default NULL,
  `usuario` varchar(255) default NULL,
  `tipo_entrada` varchar(255) NOT NULL default '',
  `fecha` bigint(20) unsigned NOT NULL default '0',
  `ip` varchar(20) default NULL,
  `user_agent` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `sys_permitions`;
CREATE TABLE `sys_permitions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_grupo` bigint(20) unsigned NOT NULL default '0',
  `id_seccion` bigint(20) unsigned NOT NULL default '0',
  `last_user_id` bigint(20) unsigned default NULL,
  `last_user_ip` varchar(20) default NULL,
  `last_user_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;

INSERT INTO `sys_permitions` VALUES (1, 1, 1, NULL, NULL, NULL);
INSERT INTO `sys_permitions` VALUES (2, 1, 2, NULL, NULL, NULL);
INSERT INTO `sys_permitions` VALUES (3, 1, 3, NULL, NULL, NULL);
INSERT INTO `sys_permitions` VALUES (4, 1, 4, NULL, NULL, NULL);
INSERT INTO `sys_permitions` VALUES (5, 1, 5, NULL, NULL, NULL);
INSERT INTO `sys_permitions` VALUES (6, 1, 6, NULL, NULL, NULL);
INSERT INTO `sys_permitions` VALUES (7, 1, 7, NULL, NULL, NULL);
INSERT INTO `sys_permitions` VALUES (8, 1, 8, NULL, NULL, NULL);

DROP TABLE IF EXISTS `sys_sections`;
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
) TYPE=MyISAM AUTO_INCREMENT=10 ;

INSERT INTO `sys_sections` VALUES (1, 0, 'Sistema', '', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_sections` VALUES (2, 1, 'Configuraciones', './sys/configuraciones.php', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_sections` VALUES (3, 1, 'Acciones', 'sys/actions.php', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_sections` VALUES (4, 1, 'Grupos y Permisos', './sys/grupos.php', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_sections` VALUES (5, 1, 'Historial', 'sys/logs.php', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_sections` VALUES (6, 1, 'Secciones', './sys/secciones.php', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_sections` VALUES (7, 1, 'Usuarios', './sys/usuarios.php', 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `sys_sections` VALUES (8, 0, 'Datos Personales', './sys/datos_personales.php', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `sys_user_passwords`;
CREATE TABLE `sys_user_passwords` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `id_usuario` bigint(20) unsigned NOT NULL default '0',
  `clave` varchar(50) NOT NULL default '',
  `fecha` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `sys_users`;
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
) TYPE=MyISAM AUTO_INCREMENT=2 ;

INSERT INTO `sys_users` VALUES (2, 1, NULL, 0, NULL, 0, 0, 0, 0, 0, 0, 'manuel', 'nanu123', NULL, 'Manuel Gomez', 'Mercedes 3865', 'Capital Federal', '1419', '', '', '15-4098-0896', '', '', 'manuel@e4system.com.ar', '1979-08-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL);