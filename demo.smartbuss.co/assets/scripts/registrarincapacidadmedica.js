$("body").on("change","[name='datos[idEmpleado]']",function(e){
	$("#numeroDocumento").val('')
	if($(this).val()!=""){
		var cc=$(this).find("option:selected").attr("cc")
		$("#numeroDocumento").val(cc)
	}
})

$("body").on("change","[name='datos[fechaInicio]'], [name='datos[fechaFinal]']",function(e){

	var fechaInicio=$("[name='datos[fechaInicio]']").val(); 
	var fechaFin=$("[name='datos[fechaFinal]']").val(); 
	
	var ONE_DAY = 1000 * 60 * 60 * 24;

	if(fechaInicio!=""){
		firstDate = new Date(fechaInicio);
	    var date1_ms = firstDate.getTime();
	    var date2_ms = new Date().getTime();
	    
	    var difference_ms = Math.abs(date1_ms - date2_ms);
	    
	    if(date1_ms>=date2_ms){
	    	min=(Math.round(difference_ms/ONE_DAY)+1);
	    }else{
	    	min=(Math.round(difference_ms/ONE_DAY)-1)*-1;
	    }
	    
	    $("[name='datos[fechaFinal]']").datepicker("destroy").removeClass("hasDatepicker");
		$("[name='datos[fechaFinal]']").datepicker({ minDate:min, dateFormat:'yy-mm-dd' });
	}
    if(fechaFin!=""){
    	firstDate = new Date(fechaFin);
	    var date1_ms = firstDate.getTime();
	    var date2_ms = new Date().getTime();
	    
	    var difference_ms = Math.abs(date2_ms - date1_ms);
	    if(date2_ms>=date1_ms){
	    	max=(Math.round(difference_ms/ONE_DAY)-1)*-1;
	    }else{
	    	max=Math.round(difference_ms/ONE_DAY)+1;
	    }
	    
	    
	    $("[name='datos[fechaInicio]']").datepicker("destroy").removeClass("hasDatepicker");
		$("[name='datos[fechaInicio]']").datepicker({ maxDate:max, dateFormat:'yy-mm-dd' });
    }
	 
	
})

$("body").on("click","#btnGuardar",function(e){
e.preventDefault();
  if(true === $("#frmGuardar").parsley().validate()){
      Swal.fire({
          title: 'Está seguro?',
          text: 'Está a punto de registrar incapacidad medica a '+$("[name='datos[idEmpleado]']").find("option:selected").html()+'!',
          icon: 'warning', 
          showCancelButton: true,
          showLoaderOnConfirm: true,
          confirmButtonText: `Si, Guardar!`,
          cancelButtonText:'Cancelar',
          preConfirm: function(result) {
          return new Promise(function(resolve) {
            var formu = document.getElementById("frmGuardar");
        
            var data = new FormData(formu);
            $.ajax({
            url:URL+"functions/nomina/guardarincapacidadmedica.php", 
            type:"POST", 
            data: data,
            contentType:false, 
            processData:false, 
            dataType: "json",
            cache:false 
            }).done(function(msg){  
              if(msg.msg){
                Swal.fire(
                  {
                  icon: 'success',
                  title: "Incapacidad registrada!",
                  text: 'con exito',
                  closeOnConfirm: true,
                }
                ).then((result) => {
                 location.reload(); 
                })
              }else{
                 Swal.fire(
                  'Algo ha salido mal!',
                  'Verifique su conexión a internet',
                  'error'
                ).then((result) => {
                  
                })
              }
            
          });
          });
        }
        }).then((result) => {
          if (result.isConfirmed) {
          } 

         })

        
      // swal({
      //   title: 'Está seguro?',
      //   text: 'Está a punto de registrar incapacidad medica a '+$("[name='datos[idEmpleado]']").find("option:selected").html()+'!',
      //   icon: 'warning',
      //   buttons: {
      //           confirm : {text:'Si, Guardar!',className:'sweet-warning',closeModal:false},
      //           cancel : 'Cancelar' 
      //       },
      //   dangerMode: true,
      // })
      //   .then((willDelete) => {
      //     if (willDelete) {
      //       var formu = document.getElementById("frmGuardar");
        
      //       var data = new FormData(formu);
      //       $.ajax({
      //       url:URL+"functions/nomina/guardarincapacidadmedica.php", 
      //       type:"POST", 
      //       data: data,
      //       contentType:false, 
      //       processData:false, 
      //       dataType: "json",
      //       cache:false 
      //       }).done(function(msg){  
      //         if(msg.msg){

      //           swal({   
      //             title: "Incapacidad registrada!",   
      //             text: "con exito",
      //             type: "success",        
      //             closeOnConfirm: true 
      //             }).then((element)=>{
      //               location.reload(); 
      //             })
      //         }else{
      //           swal({   
      //             title: "Algo ha salido mal!",   
      //             text: "El NIT ya se encuentra registrado",
      //             type: "error",        
      //             closeOnConfirm: true 
      //             }).then((element)=>{
                    
      //             });
      //         }
            
      //     });
      //     } else {
            
      //     }
      //   });
        
   
  }
})