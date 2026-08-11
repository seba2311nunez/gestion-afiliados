-- 
-- Estructura de tabla para la tabla `tabs_tabla_1`
-- 

CREATE TABLE `tabs_tabla_1` (
  `id` int(2) NOT NULL auto_increment,
  `datos` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Volcar la base de datos para la tabla `tabs_tabla_1`
-- 

INSERT INTO `tabs_tabla_1` (`id`, `datos`) VALUES (1, 'Texto de la sección 1'),
(2, 'Texto de la sección 2');

-- 
-- Estructura de tabla para la tabla `tabs_tabla_2`
-- 

CREATE TABLE `tabs_tabla_2` (
  `id` int(2) NOT NULL auto_increment,
  `datos` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Volcar la base de datos para la tabla `tabs_tabla_2`
-- 

INSERT INTO `tabs_tabla_2` (`id`, `datos`) VALUES (1, 'Texto de la sección 3');


