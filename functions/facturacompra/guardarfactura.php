<?php

header('Content-type: application/json');

require_once("../../php/restrict.php");



include_once($CLASS . "data.php");

include_once($CLASS . "lista.php");

include_once($CLASS . "control.php");



$oControl=new Control();



date_default_timezone_set("America/Bogota"); 



$datos  = (isset($_REQUEST['datos'] ) ? $_REQUEST['datos'] : "" );

$item  = (isset($_REQUEST['item'] ) ? $_REQUEST['item'] : "" );

$impuesto  = (isset($_REQUEST['impuesto'] ) ? $_REQUEST['impuesto'] : "" );



if( isset($_FILES['file']) && $_FILES['file'] != 'undefined')

    {

               

        $sNombre = $_FILES['file']['name'];                

        $sExtension = substr(strrchr($sNombre, '.'), 1);

        $sTemporal = $_FILES['file']['tmp_name'];

        

        $nombreEncript = uniqid(); 

        

        $nombre_archivo = "{$nombreEncript}.{$sExtension}"; 

        

        $directorioTmp = 'FACTURACOMPRA/';

        $ubicacionTmp = "{$directorioTmp}{$nombre_archivo}";  



        if(move_uploaded_file($sTemporal, "../../".$directorioTmp.$nombre_archivo))

        {	                                              

            $sFoto = 'FACTURACOMPRA/'.$nombre_archivo;

        }

        else

        {

            $sFoto = "";

        }

    

} 



$oItem=new Data("tercero","idTercero",$datos["idTercero"]); 

$aProveedor=$oItem->getDatos(); 

unset($oItem);



if($aProveedor["periodoPago"]==0){

    $nuevafecha=$datos["fechaRecibido"];

}else{

    $nuevafecha = strtotime ( '+'.$aProveedor["periodoPago"].' day' , strtotime ( $datos["fechaRecibido"] ) ) ;

    $nuevafecha = date ( 'Y-m-d' , $nuevafecha );    

}





if(!isset($_SESSION)){ session_start(); }

$aDatos["fechaRegistro"]=date("Y-m-d H:i:s");

$aDatos["idUsuarioRegistra"]=$_SESSION["idUsuario"]; 

$aDatos["idEmpresa"]=$datos["idEmpresa"]; 

if ($datos["tipoCompraB"]!="" && $datos["tipoCompraS"]!="") {
    $aDatos["tipoFactura"]=3;
}
if ($datos["tipoCompraB"]=="" && $datos["tipoCompraS"]!="") {
    
    $aDatos["tipoFactura"]=2; 
}
if ($datos["tipoCompraB"]!="" && $datos["tipoCompraS"]=="") {
    
    $aDatos["tipoFactura"]=1; 
}
if ($datos["tipoCompraB"]=="" && $datos["tipoCompraS"]=="") {
    
    $aDatos["tipoFactura"]=1; 
}

$aDatos["fechaRecibido"]=$datos["fechaRecibido"]; 

$aDatos["fechaPago"]=$nuevafecha; 

$aDatos["idProveedor"]=$datos["idTercero"]; 

$aDatos["nroFactura"]=$datos["nroFactura"]; 

$aDatos["archivo"]=$sFoto; 

$aDatos["subtotal"]=str_replace("$", "", str_replace(".", "", $datos["subtotal"])); 

$aDatos["descuento"]=str_replace("$", "", str_replace(".", "", $datos["descuento"])); 

$aDatos["iva"]=str_replace("$", "", str_replace(".", "", $datos["iva"])); 

$aDatos["total"]=str_replace("$", "", str_replace(".", "", $datos["total"])); 

$aDatos["estado"]=1; 

$aDatos["saldo"]=str_replace("$", "", str_replace(".", "", $datos["totalPago"])); 



$oItem=new Data("factura_compra","idFacturaCompra"); 

foreach($aDatos  as $key => $value){

    $oItem->$key=$value; 

}

$oItem->guardar(); 

$idfactura=$oItem->ultimoId(); 

unset($oItem);



foreach ($item as $key => $value) {

    $aItem["idFacturaCompra"]=$idfactura; 

    $aItem["detalleProducto"]=$value["producto"]; 

    $aItem["idProductoServicio"]=$value["idProducto"]==""?0:$value["idProducto"]; 

    $aItem["descripcion"]=$value["descripcion"]; 

    $aItem["idUnidad"]=$value["idUnidad"]; 

    $aItem["cantidad"]=$value["cantidad"]; 

    $aItem["iva"]=$value["iva"]; 

    $aItem["valorUnitario"]=str_replace("$", "", str_replace(".", "", $value["valorUnitario"])); 

    $aItem["subtotal"]=str_replace("$", "", str_replace(".", "", $value["subtotal"])); 

    $aItem["total"]=str_replace("$", "", str_replace(".", "", $value["total"]));



    $oItem=new Data("factura_compra_item","idFacturaCompraItem"); 

    foreach($aItem  as $key => $value){

        $oItem->$key=$value; 

    }

    $oItem->guardar(); 

    unset($oItem);

}






$aGestion["idFacturaCompra"]=$idfactura; 

$aGestion["fechaRegistro"]=date("Y-m-d H:i:s"); 

$aGestion["idUsuarioRegistra"]=$_SESSION["idUsuario"]; 

$aGestion["totalDeduccion"]=str_replace("$", "", str_replace(".", "", $datos["totalDeduccion"]));

$aGestion["valorAmortizacion"]=0; 

$aGestion["totalPagar"]=str_replace("$", "", str_replace(".", "", $datos["totalPago"])); 

$aGestion["comprobanteEgreso"]=0; 



$oItem=new Data("factura_compra_gestion","idFacturaCompraGestion"); 

foreach($aGestion  as $keyfcg => $valuefcg){

    $oItem->$keyfcg=$valuefcg; 

}

$oItem->guardar(); 

unset($oItem);




foreach ($impuesto as $keyimp => $valueimp) {

    $aImpuesto["idFacturaCompra"]=$idfactura; 

    $aImpuesto["tipoDeduccion"]=$valueimp["tipoDeduccion"]; 

    if($value["idConcepto"]!=""){

        $aImpuesto["idConcepto"]=$valueimp["idConcepto"]; 

    }

    if($value["baseImpuestos"]!=""){

        $aImpuesto["baseImpuestos"]=$valueimp["baseImpuestos"]; 

    }

    $aImpuesto["concepto"]=$valueimp["concepto"]; 

    $aImpuesto["valor"]=$valueimp["valor"]; 





    $oItem=new Data("factura_compra_deduccion","idFacturaCompraDeduccion"); 

    foreach($aImpuesto  as $key => $valor){

        $oItem->$key=$valor; 

    }

    $oItem->guardar(); 

    unset($oItem);

}








$oLista = new Lista('usuario_empresa');

$oLista->setFiltro("idEmpresa","=",$datos["idEmpresa"]);

$lista=$oLista->getLista();



$oItem=new Data("empresa","idEmpresa",$datos["idEmpresa"]); 

$aEmpresa=$oItem->getDatos();

unset($oItem);



$oItem=new Data("tercero","idTercero",$datos["idTercero"]); 

$aProveedor=$oItem->getDatos();

unset($oItem);

$aEmail["asunto"]="Factura de compra enviada";

foreach ($lista as $key => $value) {

    $oItem=new Data("usuario","idUsuario",$value["idUsuario"]); 

    $aUser=$oItem->getDatos();

    unset($oItem); 

    $mensaje="<p>Estimado ".$aUser["nomberUsuario"]." ".$aUser["apellidoUsuario"]." <br>

    El usuario ".$_SESSION["nombreUsuario"]." ".$_SESSION["apellidoUsuario"]." ha enviado una factura a traves de la empresa ".$aEmpresa["razonSocial"]." correspondiente al proveedor ".$aProveedor["razonSocial"]." <br>

    Por favor dirigase a realizar la gestión de los impuestos correspondientes

    <br>

    </p>"; 

    $aEmail["correo"]=$aUser["correo"]; 

    $aEmail["mensaje"]=$mensaje; 
    

    // $oControl->enviarCorreo($aEmail); 

    unset($oControl);
}


$cLista = new Lista('usuario');

$cLista->setFiltro("idRol","=",'2');

$colista=$cLista->getLista();

foreach ($colista as $key => $contador) {


   
    $correoContador = $contador["correo"];

    $cmensaje="<p>Estimado ".$contador["nombreUsuario"]." ".$contador["apellidoUsuario"]." <br>

    El usuario ".$_SESSION["nombreUsuario"]." ".$_SESSION["apellidoUsuario"]." ha enviado una factura a traves de la empresa ".$aEmpresa["razonSocial"]." correspondiente al proveedor ".$aProveedor["razonSocial"]." <br><br> </p>"; 




    $cEmail["correo"]=$contador["correo"]; 
    

    $cEmail["asunto"]="Factura de compra enviada"; 

    $cEmail["mensaje"]=$cmensaje; 

    $cControl=new Control();

    // $cControl->enviarCorreo($cEmail);
    unset($cControl);


    $dDatos["fechaNotificacion"]=date("Y-m-d H:m:s");
    $dDatos["idUsuarioRegistra"] = $_SESSION["idUsuario"];
    $dDatos["idUsuarioNotificado"] =$contador["idUsuario"];
    $dDatos["notificacion"] =  "El usuario ".$_SESSION["nombreUsuario"]." ".$_SESSION["apellidoUsuario"]." ha enviado una factura de compra";
    

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



    $smensaje="<p>Estimado ".$super["nombreUsuario"]." ".$super["apellidoUsuario"]." <br>

    El usuario ".$_SESSION["nombreUsuario"]." ".$_SESSION["apellidoUsuario"]." ha enviado una factura a traves de la empresa ".$aEmpresa["razonSocial"]." correspondiente al proveedor ".$aProveedor["razonSocial"]." <br><br> </p>"; 




    $sEmail["correo"]=$super["correo"]; 
    

    $sEmail["asunto"]="Factura de compra enviada"; 

    $sEmail["mensaje"]=$smensaje; 

    $sControl=new Control();

    // $sControl->enviarCorreo($sEmail);
    unset($sControl);


    $sDatos["fechaNotificacion"]=date("Y-m-d H:m:s");
    $sDatos["idUsuarioRegistra"] = $_SESSION["idUsuario"];
    $sDatos["idUsuarioNotificado"] =$super["idUsuario"];
    $sDatos["notificacion"] =  "El usuario ".$_SESSION["nombreUsuario"]." ".$_SESSION["apellidoUsuario"]." ha enviado una factura de compra";
    

    $oItem=new Data("notificacion","idNotificacion"); 
    foreach($sDatos  as $key => $svalue){
    $oItem->$key=$svalue; 
    }
    $oItem->guardar(); 
    unset($oItem);
}




    $comp=true;

    $oLista=new Lista("compra_cuenta_contable");
    $oLista->setFiltro("concepto","=",'1');
    $oLista->setFiltro("idEmpresa","=",$datos["idEmpresa"]);
    $oLista->setFiltro("tipoFactura","like",'compra');
    $aCC=$oLista->getLista();
    unset($oLista);
// print_r($aCC);

if (!empty($aCC)) {

    if (!empty($datos["cuentaContableTotal"])) {
        $oLista=new Lista("compra_cuenta_contable");
        $oLista->setFiltro("concepto","=",'1');
        $oLista->setFiltro("idEmpresa","=",$datos["idEmpresa"]);
        $oLista->setFiltro("tipoFactura","like",'compra');
        $oLista->setFiltro("idEmpresaCuenta","=",$datos["cuentaContableTotal"]);
        $aCCT=$oLista->getLista();
        unset($oLista);
        $tipoDocumentoTotal=$aCCT[0]['tipoDocumento'];
    }
    if (empty($datos["cuentaContableTotal"])) {
        
        // $idCuentaContableTotal=$aCC[0]["idEmpresaCuenta"];
        $tipoDocumentoTotal=$aCC[0]['tipoDocumento'];
    }



    $oLista=new Lista("parametros_documentos");
    $oLista->setFiltro("idParametrosDocumentos","=",$tipoDocumentoTotal);
    $oLista->setFiltro("idEmpresa","=",$datos["idEmpresa"]);
    $aNumero=$oLista->getLista();
    unset($oLista);

    $numeroComprobante=intval($aNumero[0]["numeracionActual"]);

    $aDatos["idTipo"]=$aNumero[0]["tipo"]; 
    $aDatos["comprobante"]=$aNumero[0]["comprobante"]; 
    $aDatos["fecha"]=$datos["fechaRecibido"]; 
    $aDatos["fechaRegistro"]=date('Y-m-d'); 
    $aDatos["usuarioRegistra"]=$_SESSION["idUsuario"]; 
    $aDatos["archivo"]=$sFoto; 
    $aDatos["observaciones"]='Factura de compra No. '.$datos["nroFactura"]; 
    $aDatos["idEmpresa"]=$datos["idEmpresa"]; 
    $aDatos["numero"]=$numeroComprobante; 
    $oItem=new Data("comprobante","idComprobante"); 
    foreach($aDatos  as $key => $value){
        $oItem->$key=$value; 
    }
    $oItem->guardar(); 
    $idComprobante=$oItem->ultimoId(); 
    unset($oItem);
    $nCom["numeracionActual"]=$numeroComprobante+1;

    $oItem=new Data("parametros_documentos","idParametrosDocumentos",$aNumero[0]["idParametrosDocumentos"]); 
    foreach($nCom  as $keyC => $valueC){
        $oItem->$keyC=$valueC; 
    }
    $oItem->guardar(); 
    unset($oItem);

    foreach ($item as $key => $value) {
        $oLista=new Lista("producto_cuenta_contable");
        $oLista->setFiltro("idProducto","=",$value["idProducto"]);
        $oLista->setFiltro("idEmpresa","=",$datos["idEmpresa"]);
        $oLista->setFiltro("tipoFactura","like",'compra');
        $aProductoCuenta=$oLista->getLista();
        
        if (empty($aProductoCuenta)) {
            $comp=false;
        }

        if ($comp==true) {
            $oItem=new Data("cuenta_contable","idCuentaContable",$aProductoCuenta[0]["idEmpresaCuenta"]);
            $aCuentaContable=$oItem->getDatos();
            unset($oItem);

            $aItem["idComprobante"]=$idComprobante; 
            $aItem["idCuentaContable"]=$aProductoCuenta[0]["idEmpresaCuenta"];
            $aItem["idCentroCosto"]=" ";
            $aItem["idTercero"]=$datos["idTercero"];
            $aItem["descripcion"]=$value["descripcion"];
            $aItem["naturaleza"]='debito';
            $aItem["tipoTercero"]='p';
            $aItem["idUsuarioRegistra"]=$_SESSION["idUsuario"]; 
            $aItem["fecha"]=$datos["fechaRecibido"]; 
            $aItem["saldoDebito"]=str_replace(",", ".",str_replace("$", "", str_replace(".", "",$value["subtotal"])));
            $aItem["saldoCredito"]=0;
            $aItem["base"]=0;

             $oItem=new Data("comprobante_items","idComprobanteItem"); 
                foreach($aItem  as $keycc => $valuecc){
                    $oItem->$keycc=$valuecc; 
                }
                $oItem->guardar(); 
                unset($oItem);
        }
    }
        // if ($comp==true) {
            
        // }
        $oLista=new Lista("impuesto_cuenta_contable");
        $oLista->setFiltro("idEmpresa","=",$datos["idEmpresa"]);
        $oLista->setFiltro("tipoImpuesto","=",'3');
        $oLista->setFiltro("tipoFactura","like",'compra');
        $aCuentaContableN=$oLista->getLista();  

        if(!empty($aCuentaContableN)){

        $aIVA["idComprobante"]=$idComprobante; 
        $aIVA["idCuentaContable"]=$aCuentaContableN[0]["idEmpresaCuenta"];
        $aIVA["idCentroCosto"]=" ";
        $aIVA["idTercero"]=$datos["idTercero"];
        $aIVA["descripcion"]=$value["descripcion"];
        $aIVA["naturaleza"]='debito';
        $aIVA["tipoTercero"]='p';
        $aIVA["idUsuarioRegistra"]=$_SESSION["idUsuario"]; 
        $aIVA["fecha"]=$datos["fechaRecibido"]; 
        $aIVA["saldoDebito"]=str_replace(",", ".",str_replace("$", "", str_replace(".", "",$datos["iva"])));
        $aIVA["saldoCredito"]=0;
        $aIVA["base"]=0;

            $oItem=new Data("comprobante_items","idComprobanteItem"); 
            foreach($aIVA  as $keyIVA => $valueIVA){
                $oItem->$keyIVA=$valueIVA; 
            }
            $oItem->guardar(); 
            unset($oItem);
        }
            if(empty($aCuentaContableN)){
                $comp=false;
            }
            if (!empty($datos["cuentaContableTotal"])) {
                
                $idCuentaContableTotal=$datos["cuentaContableTotal"];
            }
            if (empty($datos["cuentaContableTotal"])) {
                
                $idCuentaContableTotal=$aCC[0]["idEmpresaCuenta"];
            }
                $oItem=new Data("cuenta_contable","idCuentaContable",$idCuentaContableTotal);
                $aCompraCuenta=$oItem->getDatos();
                unset($oItem);  
            
            if(empty($aCompraCuenta)){
                $comp=false;
            }
            
            if ($comp==true) {
            
            
                $aCompra["idComprobante"]=$idComprobante; 
                $aCompra["idCuentaContable"]=$idCuentaContableTotal;
                $aCompra["idCentroCosto"]=" ";
                $aCompra["idTercero"]=$datos["idTercero"];
                $aCompra["descripcion"]=$value["descripcion"];
                $aCompra["naturaleza"]='credito';
                $aCompra["tipoTercero"]='p';
                $aCompra["idUsuarioRegistra"]=$_SESSION["idUsuario"]; 
                $aCompra["fecha"]=$datos["fechaRecibido"]; 
                $aCompra["saldoDebito"]=0;
                $aCompra["saldoCredito"]=str_replace(",", ".",str_replace("$", "", str_replace(".", "",$datos["totalPago"])));
                $aCompra["base"]=0;

                $oItem=new Data("comprobante_items","idComprobanteItem"); 
                    foreach($aCompra  as $keyImpuesto => $valueImpuesto){
                        $oItem->$keyImpuesto=$valueImpuesto; 
                    }
                    $oItem->guardar(); 
                    unset($oItem);

            }

            // }



            foreach ($impuesto as $keyI => $valueI) {

                if ($comp==true) {


                    $oLista=new Lista("impuesto_cuenta_contable");
                    $oLista->setFiltro("idImpuesto","=",$valueI["idConcepto"]);
                    $oLista->setFiltro("idEmpresa","=",$datos["idEmpresa"]);
                    $oLista->setFiltro("tipoFactura","like",'compra');
                    $aImpuestoCuenta=$oLista->getLista();    

                    if(empty($aImpuestoCuenta)){
                        $comp=false;
                    }

                    // if (empty($aImpuestoCuenta[0]["idEmpresaCuenta"])) {
                    //     $idEmpresaCuenta='2917';
                    // }
                    // if (!empty($aImpuestoCuenta[0]["idEmpresaCuenta"])) {
                        $idEmpresaCuenta=$aImpuestoCuenta[0]["idEmpresaCuenta"];
                    // }

                    
                    $aImpuesto["idComprobante"]=$idComprobante; 
                    $aImpuesto["idCuentaContable"]=$idEmpresaCuenta;
                    $aImpuesto["idCentroCosto"]=" ";
                    $aImpuesto["idTercero"]=$datos["idTercero"];
                    $aImpuesto["descripcion"]=$value["descripcion"];
                    $aImpuesto["naturaleza"]='credito';
                    $aImpuesto["tipoTercero"]='p';
                    $aImpuesto["idUsuarioRegistra"]=$_SESSION["idUsuario"]; 
                    $aImpuesto["fecha"]=$datos["fechaRecibido"]; 
                    $aImpuesto["saldoDebito"]=0;
                    $aImpuesto["saldoCredito"]=str_replace(",", ".",str_replace("$", "", str_replace(".", "",$valueI["valor"])));
                    $aImpuesto["base"]=$valueI["baseImpuestos"];

                    $oItem=new Data("comprobante_items","idComprobanteItem"); 
                        foreach($aImpuesto  as $keyImpuesto => $valueImpuesto){
                            $oItem->$keyImpuesto=$valueImpuesto; 
                        }
                        $oItem->guardar(); 
                        unset($oItem);
                    }

                }




                if ($comp==true) {
                    $estado=1;

                    $aFacturaComprobante["idFacturaCompra"]=$idfactura;
                    $aFacturaComprobante["idComprobante"]=$idComprobante;
                    $aFacturaComprobante["estado"]=$estado;

                    $oItem=new Data("factura_compra_comprobante","idFacturaCompraComprobante"); 
                    foreach($aFacturaComprobante  as $keyFC => $valueFC){
                        $oItem->$keyFC=$valueFC; 
                    }
                    $oItem->guardar(); 
                    unset($oItem);
                }

                if ($comp==false) {



                    $oLista=new Lista("comprobante");
                    $oLista->setFiltro("idComprobante","=",$idComprobante);
                    $comEliminar=$oLista->getlista();
                    unset($oLista);
                    foreach ($comEliminar as $keym => $valuem) {
                        $oItem=new Data("comprobante","idComprobante",$valuem["idComprobante"]);
                        $oItem->eliminar();
                        unset($oItem);
                    }

                    $oLista=new Lista("comprobante_items");
                    $oLista->setFiltro("idComprobante","=",$idComprobante);
                    $comprobanteItemsEliminar=$oLista->getlista();
                    unset($oLista);
                    foreach ($comprobanteItemsEliminar as $keym => $valuem) {
                        $oItem=new Data("comprobante_items","idComprobanteItem",$valuem["idComprobanteItem"]);
                        $oItem->eliminar();
                        unset($oItem);
                    }



                }



    }


$msg=true; 

echo json_encode(array("msg"=>$msg));

?>