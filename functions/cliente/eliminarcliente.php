<?php

header('Content-type: application/json');

require_once("../../php/restrict.php");



include_once($CLASS . "data.php");

include_once($CLASS . "lista.php");



date_default_timezone_set("America/Bogota"); 


$idEliminar=(isset($_REQUEST['idEliminar'] ) ? $_REQUEST['idEliminar'] : "" );




$oItem=new Data("cliente","idCliente",$idEliminar); 
$oItem->eliminar(); 
unset($oItem);


$oLista=new Lista("cliente_empresa");
$oLista->setFiltro("idCliente","=",$idEliminar);
$clientesEmpresa=$oLista->getLista();
unset($oLista);


if (!empty($clientesEmpresa)) {
	foreach ($clientesEmpresa as $keym => $valuem) {
	$oItem=new Data("cliente_empresa","idClienteEmpresa",$valuem["idClienteEmpresa"]);
	$oItem->eliminar();
	unset($oItem);
	}
}


    


$msg=true; 


echo json_encode(array("msg"=>$msg));

?>