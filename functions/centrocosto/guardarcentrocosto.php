<?php

header('Content-type: application/json');

require_once("../../php/restrict.php");

include_once($CLASS . "data.php");

include_once($CLASS . "lista.php");

date_default_timezone_set("America/Bogota"); 


$datos  = (isset($_REQUEST['datos'] ) ? $_REQUEST['datos'] : "" );

$item  = (isset($_REQUEST['subcentros'] ) ? $_REQUEST['subcentros'] : "" );


if(!isset($_SESSION)){ session_start(); }


$aDatos["centroCosto"]=$datos["centroCosto"]; 

$aDatos["responsable"]=$datos["responsable"]; 

$aDatos["direccion"]=$datos["direccion"]; 

$aDatos["telefono1"]=$datos["primerTelefono"]; 

$aDatos["telefono2"]=$datos["segundoTelefono"]; 

$aDatos["email"]=$datos["email"]; 

$aDatos["fax"]=$datos["fax"]; 

$aDatos["fechaRegistro"]=date("Y-m-d H:i:s");

$aDatos["usuarioRegistra"]=$_SESSION["idUsuario"];

$aDatos["idEmpresa"]=$datos["idEmpresa"]; 

$oItem=new Data("centro_costo","idCentroCosto"); 

foreach($aDatos  as $key => $value){

    $oItem->$key=$value; 

}

$oItem->guardar(); 

$idCentroCosto=$oItem->ultimoId(); 

unset($oItem);



foreach ($item as $keyc => $valuec) {

    $aItemSubcentroCosto["subcentroCosto"]=$valuec["nombre"];
    $aItemSubcentroCosto["idCentroCosto"]=$idCentroCosto;


    $oItem=new Data("subcentro_costo","idSubcentroCosto"); 

        foreach($aItemSubcentroCosto  as $keys => $values){

            $oItem->$keys=$values; 

        }

        $oItem->guardar(); 

        unset($oItem);
}



    $msg=true; 



    echo json_encode(array("msg"=>$msg));

?>