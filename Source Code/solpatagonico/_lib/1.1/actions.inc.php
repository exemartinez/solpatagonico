<?
	/**
	* actions
	* Genera un objecto con las acciones autorizadas para el grupo dado.
	* @param string $id_grupo usuario ingreado a validar.
	* @return booleano.	
	*/	
	class actions{
		function actions( $id_grupo = '' ){
			if( $id_grupo == '' ){
				global $USER;
				$id_grupo = $USER->id_grupo;
			}
			$db = NewADOConnection( CFG_DB_dsn );
			$rs = $db->Execute( "
				SELECT a.accion, p.valor 
				FROM ".CFG_groupsPrivilegesTable." p
					LEFT JOIN ".CFG_actionsTable." a ON p.id_accion = a.id
				WHERE p.id_grupo = '".$id_grupo."'
				ORDER BY a.accion
			" );
			while( $r = $rs->fetchNextObject(false) ){
				$this->{$r->accion} = $r->valor;
			}
			return true;
		}
	}
?>