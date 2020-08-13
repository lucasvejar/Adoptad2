<div class="container my-5">
	<div class="row">
		<div class="col-sm-12 col-md-3 ">
			<div class="card">
				<div class="card-body">
					<center><h1 class="my-2"><i class="fas fa-book"></i></h1></center>

					<center><h4 class="card-title">Períodos de Seguimiento</h4></center>
					<p class="card-text">En esta sección encontrarás los períodos seguimientos activos de los Centros de Adopción, en caso de ser personal del Centro de Adopción, aquí tambien podrás iniciar nuevos períodos</p>
					<div class="alert alert-warning" role="alert">
						<p class="font-italic"> <i class="fas fa-info"></i> Cuando se inicia un Periodo de Seguimiento nuestro sistema envía automáticamente un email a aquellos adoptantes que que cumplan con las condiciones relacionadas a este.</p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-12 col-md-9">
			<div class="card">
				<div class="card-body">
					<center><h1 class="my-2"><i class="fas fa-check"></i></center>						
						<center><h4 class="card-title">Períodos de Seguimiento Activos:</h4></center>
						<button type="button" class="btn btn-primary my-2" data-toggle="modal" data-target="#modalPeriodo" data-placement="right" title="Presione para agregar un nuevo periodo de seguimiento"><i class="far fa-calendar-plus"></i> Agregar Nuevo</button>
                        <div class="scroll" style="height: auto;">
                            
                            <table class="table table-striped table-dark display" id="table_id">
                                <thead>
                                    <tr>
                                        <th>Tipo de Periodo</th>
                                        <th>Fecha Inicio Periodo</th>
                                        <th>Fecha Fin Periodo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($periodos != false): ?>
                                        <?php foreach($periodos as $periodo): ?>
                                            <tr>
                                             <?php switch($periodo->tipo_periodo):
                                                case 'Vacunacion': 
                                                echo '<td><i class="fas fa-pills"></i> '.$periodo->tipo_periodo.'</td>';break;
                                                case 'Castracion': 
                                                echo '<td><i class="fas fa-cut"></i> '.$periodo->tipo_periodo.'</td>';break;
                                                case 'Seguimiento': 
                                                echo '<td><i class="fas fa-calendar-check"></i> '.$periodo->tipo_periodo.'</td>';break;
                                            endswitch
                                            ?>          
                                            <td><?= $periodo->fecha_inicio_periodo ?></td>  
                                            <td><?= $periodo->fecha_fin_periodo ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php  endif ?>
                            </tbody>
                        </table>                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="modalPeriodo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo Periodo de Seguimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario" class="formulario" method="post"> 
                    <fieldset class="form-group">
                        <label for="inputGroupSelect01">Tipo de Periodo</label>
                        <select class="custom-select" id="tipoPeriodo">
                            <option selected >Seleccione un tipo de periodo de seguimiento</option>
                            <option value="Vacunacion">Vacunación</option>
                            <option value="Castracion">Castración</option>
                            <option value="Seguimiento">Seguimiento</option>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="fechaDesde">Fecha Inicio Periodo</label>
                        <input type="date" class="form-control" name="" id="fechaDesde">
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="fechaHasta">Fecha Fin Periodo</label>
                        <input type="date" name="" class="form-control" id="fechaHasta">
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <div class="loader"></div><input type="submit" class="btn btn-primary btn-periodo" id="btn_periodo" value="Iniciar Periodo">
            </div>
        </div>
    </div>
</div>



<!-- Modal que se muestra cuando se registro correctamente el periodo de seguimiento -->
<div class="modal fade" id="modalMensaje" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="cartelModal"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
         </button>
        </div>
        <div class="modal-body">
            <form>
                <fieldset>
                    <label id="mensaje"></label>
                </fieldset>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
        </div>
    </div>
   </div>
</div>



<!-- Script para hacer el ajax y las validaciones y cargar la tabla --> 	
<script>
    
    $(document).ready( function () {
        
    //-----> acac oculto del spinner asi no se ve en modal cuando arranca
    $('.loader').hide();
        
    //------------> seteo las configuraciones de la tabla
    $('#table_id').DataTable({
        select: true,  //-----> hace que las filas sean seleccionables
        paging: true,  //--> habilita el paginado
        "language": {    //-------> en este array se puede perzonalizar el texto que se muestra en cada uno de los botones y labels de la tabla y como se muestran los datos
            "lengthMenu": "Muestra _MENU_ períodos por página",
            "zeroRecords": "No se encontro resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_ páginas",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(Filtrando los _MAX_ registros)",
            select: {
                rows: "%d fila seleccionada"
            },
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "previous": "Anterior",
                "next": "Siguiente"
            }
        },
        pagingType: 'full_numbers',   //---> es el tipo de botonsitos del paginado, ej: next,previous,first,last
        lengthChange: true,           //----> le habilita el combo box para que el usuario cambie el numero de paginas que quiere ver
        lengthMenu: [5,10,20],       //--> longitud del menu del paginado
        searching: false,             //---> habilita la busqueda de registros
        "columnDefs": [              //-----> se le cambia propiedades a las columnas, cuales son buscables por filtros, visibles, ordenables
        { "orderable": true, "visible": true,"targets": [0,1,2]}
        ],
        "ordering": true                     //-->  habilita el ordenamiento de columnas
    });
});
    
    
    
    //------> validacion de fechas para mandar por el formulario
    function validoSeleccionFechas() {
        
        var fechaHasta = $('#fechaHasta').val();
        var fechaDesde = $('#fechaDesde').val();
        
        if(fechaDesde == fechaHasta){  
            console.log('son iguales');
            return false;
        } 
        fechaHasta = fechaHasta.split('-',3);   
        console.log(fechaHasta); 
        fechaDesde = fechaDesde.split('-',3); 
        console.log(fechaDesde);
        
        //---> creo dos objetos Date con los datos de los array fechas
        fechaHasta = new Date(fechaHasta[0],fechaHasta[1],fechaHasta[2]);
        fechaDesde = new Date(fechaDesde[0],fechaDesde[1],fechaDesde[2]);
        
        var hoy = new Date();
        if(fechaDesde < hoy){ 
            console.log('la fecha es menor al dia de hoy y no es valida '+hoy);
            return false;
        }
        return (fechaDesde<fechaHasta ? true : false );
    }
    
    //----> Cuando se cierra el modal donde ingresa los datos de periodo seguimiento limpio los campos
    $('#modalPeriodo').on('hide.bs.modal', function (e) {
        $('#fechaDesde').val('');
        $('#fechaHasta').val('');
        $('#tipoPeriodo').val('');
    });
    
    
    //-------> Comportamiento cuando clickea el boton de iniciar periodo
    $('#btn_periodo').click(function(event){
        
        event.preventDefault();
        var tipo = $('#tipoPeriodo').val();
        var fechaHasta = $('#fechaHasta').val();
        var fechaDesde = $('#fechaDesde').val();
        console.log("algo loco "+tipo+" fecha hasta: "+fechaHasta+" fecha desde: "+fechaHasta);
        
        if(tipo != "Seleccione un tipo de periodo de seguimiento"){
            if (validoSeleccionFechas()){
                // creo un nuevo Ajax
                $.ajax({
                    url: "<?= base_url('/C_Seguimiento/validaFechas') ?>",     // The URL for the request
                    data: {              // The data to send (will be converted to a query string)
                        tipoPeriodo: tipo,
                        fechaDesde: fechaDesde,
                        fechaHasta: fechaHasta
                    },
                    type: "POST",         // Whether this is a POST or GET request
                    'beforeSend': function (data)
                    {
                        console.log('... cargando...');
                        $('.loader').show();  //---> hago visible el spinner
                    },
                    'error': function (data) {
                        //si hay un error mostramos un mensaje
                        console.log('Tenemos problemas Houston ' + data);
                    },
                    'success': function (data) {
                        datos = JSON.parse(data);
                        console.log(datos);
                        if (datos['periodo_valido']){
                            $('#cartelModal').html('<i class="fas fa-check-circle"> Periodo registrado');
                            $('#mensaje').html('El periodo creado se registro exitosamente!');
                        } else {
                            $('#cartelModal').html('<i class="fas fa-exclamation-triangle"> Periodo no registrado');
                            $('#mensaje').html('El periodo ingresado no es valido porque se superpone con otros periodos de seguimiento.');
                        }
                    }
                })
                // Code to run if the request succeeds (is done);
                // The response is passed to the function
                .done(function( json ) {         
                    console.log('se hizo bien man 👏👏');
                    $('.loader').hide(); //---> oculta el spinner
                    $('#modalPeriodo').modal('hide'); 
                    $('#modalMensaje').modal('show');
                })
                // Code to run if the request fails; the raw request and
                // status codes are passed to the function
                .fail(function( xhr, status, errorThrown ) {
                    alert( "Sorry, there was a problem!" );
                    console.log( "Error: " + errorThrown );
                    console.log( "Status: " + status );
                    console.dir( xhr );
                })
                // Code to run regardless of success or failure;
                .always(function( xhr, status ) {
                    console.log( "The request is complete!" );
                });
            } else {
                alert('Seleccione las fechas correctamente!');
            }
        } else {
            alert('Seleccion un tipo de periodo de seguimiento!');
        }
        
    });  
    
</script>







