
<?php

header('Content-type: application/json');
require_once("../../php/restrict.php");

include_once($CLASS . "data.php");
include_once($CLASS . "lista.php");
include_once($CLASS . "control.php");

date_default_timezone_set("America/Bogota");
if(!isset($_SESSION)){ session_start(); 

    
}
 
$oControl=new Control();


$datos  = (isset($_REQUEST['datos'] ) ? $_REQUEST['datos'] : "" );
 

$retencion = str_replace(",", ".",$datos['valorRetencion']);
$retencionAdicional = str_replace(",", ".",$datos['retencion']);

if (!empty($retencionAdicional)) {

	$totalRetencion= $retencionAdicional + $retencion;
}if (empty($retencionAdicional)){
    // str_replace(".", "", $datos["subtotal"])
	$totalRetencion = $retencion;
}



str_replace("$", "", ); 

    $dDatos["tipoImpuesto"]='RETENCION';
    $dDatos["valor"]=$retencion;
    $dDatos["valorAdicional"]=$retencionAdicional;
    $dDatos["sanciones"] =  $datos["sancionesRetencion"];
    $dDatos["intereses"] =  $datos["interesesRetencion"];
    $dDatos["cuentaBancaria"] = $datos["cuentaBancaria"];
    $dDatos["idUsuarioRegistra"] = $_SESSION["idUsuario"];
    $dDatos["fechaRegistro"]=date("Y-m-d H:m:s");
    $dDatos["idEmpresa"] = $datos["idEmpresa"];
    

    $oItem=new Data("impuesto_pagado","idImpuestoPagado"); 
    foreach($dDatos  as $key => $value){
    $oItem->$key=$value; 
    }
    $oItem->guardar(); 
    unset($oItem);


if (!empty($datos['sancionesRetencion'])) {
    $totalRetencion = $totalRetencion +  $datos['sancionesRetencion'];
}
if (!empty($datos['interesesRetencion'])) {
    $totalRetencion = $totalRetencion +  $datos['interesesRetencion'];
}

$oItem=new Data("cuenta_bancaria","idCuentaBancaria",$datos["cuentaBancaria"]); 

$aDatos=$oItem->getDatos(); 

$saldoActual=$aDatos["saldoActual"];

$nuevoSaldo=$saldoActual - $totalRetencion;
$cuatropormil=$aDatos['aplicaCuatroMil'];
unset($oItem);



$bDatos["idCuentaBancaria"]=$datos["cuentaBancaria"];
$bDatos["idTipoMovimiento"]=1;
$bDatos["fechaRegistro"]=date("Y-m-d H:i:s");
$bDatos["valorIngreso"]=0;
$bDatos["valorEgreso"]=$totalRetencion;  
$bDatos["saldoAnterior"]=$saldoActual;
$bDatos["saldoActual"]=$nuevoSaldo;
$bDatos["descripcionMovimiento"]='pago de la retencion'; 

$oItem=new Data("cuenta_bancaria_movimientos","idCuentaBancariaMovimientos"); 
    foreach($bDatos  as $key => $value){
        $oItem->$key=$value; 
    }
    $oItem->guardar();

    unset($oItem);



$oItem=new Data("cuenta_bancaria","idCuentaBancaria",$datos["cuentaBancaria"]); 

$oItem->saldoActual=$nuevoSaldo; 

$oItem->guardar(); 

unset($oItem);





// $oItem=new Data("cuenta_bancaria_movimientos","idCuentaBancariaMovimientos"); 
//     foreach($cDatos  as $key => $value){
//         $oItem->$key=$value; 
//     }
//     $oItem->guardar();

//     unset($oItem);


$oItem=new Data("empresa","idEmpresa",$datos["idEmpresa"]); 

$aEmpresa=$oItem->getDatos();

unset($oItem);

$cLista = new Lista('usuario');

$cLista->setFiltro("idRol","=",'2');

$colista=$cLista->getLista();

foreach ($colista as $key => $contador) {

    $dDatos["fechaNotificacion"]=date("Y-m-d H:m:s");
    $dDatos["idUsuarioRegistra"] = $_SESSION["idUsuario"];
    $dDatos["idUsuarioNotificado"] =$contador["idUsuario"];
    $dDatos["notificacion"] =  "El usuario ".$_SESSION["nombreUsuario"]." ".$_SESSION["apellidoUsuario"]." ha marcado como pago la retencion de la empresa ".$aEmpresa["razonSocial"];   

    $oItem=new Data("notificacion","idNotificacion"); 
    foreach($dDatos  as $key => $value){
    $oItem->$key=$value; 
    }
    $oItem->guardar(); 
    unset($oItem);
}
$sLista = new Lista('usuario');

$sLista->setFiltro("idRol","=",'1');

$solista=$sLista->getLista();

foreach ($solista as $key => $super) {

    $sDatos["fechaNotificacion"]=date("Y-m-d H:m:s");
    $sDatos["idUsuarioRegistra"] = $_SESSION["idUsuario"];
    $sDatos["idUsuarioNotificado"] =$super["idUsuario"];
    $sDatos["notificacion"] =  "El usuario ".$_SESSION["nombreUsuario"]." ".$_SESSION["apellidoUsuario"]." ha marcado como pago la retencion de la empresa ".$aEmpresa["razonSocial"];
    

    $oItem=new Data("notificacion","idNotificacion"); 
    foreach($sDatos  as $key => $svalue){
    $oItem->$key=$svalue; 
    }
    $oItem->guardar(); 
    unset($oItem);
}


$msg=true; 



echo json_encode(array("msg"=>$msg));

 ?>

