
<div class="container">
	<div class="row">
		<div class="col-3">
			<div class="card my-5">
				<center><h1 class="my-2"><i class="fas fa-bullhorn"></i></h1></center>
				<div class="card-body">
					<h5 class="card-title">Sección de Denuncias</h5>
					<p class="card-text">En esta sección podrás registrar denuncias a aquellos adoptantes registrados en el sistema que hayan incumplido las obligaciones legales del cuidado animal.</p>

					<div class="alert alert-warning" role="alert">
						<p class="font-italic"> <i class="fas fa-info"></i> Nuestro sistema usa este registro de denuncias para llevar el control de aquellas personas que ya no son aptas para adoptar</p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-9">
			<div class="card my-5">
				<center><h1 class="my-2"><i class="fas fa-question-circle"></i> Formulario de denuncia</center>
                    <div class="card-body scroll" style="height: auto;">
                      <table class="table table-striped table-dark display" id="table_id">
                          <thead>
                             <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nombre y Apellido</th>
                                <th scope="col">Dirección</th>
                                <th scope="col">Email</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php if ($adoptantes != false): ?>
                              <?php foreach($adoptantes as $adoptante): ?>
                                 <tr>
                                    <input id="id_adoptante" class="oculto" type="hidden" value="<?= $adoptante -> id_adoptante ?>">
                                    <th scope="row"><?= $adoptante -> id_adoptante ?></th>
                                    <th scope="row"><?= $adoptante ->nombre_adoptante." ".$adoptante->apellido_adoptante ?></th>
                                    <td><?= $adoptante->direccion_adoptante ?></td>
                                    <td><?= $adoptante->email_adoptante ?></td>
                                    <td><?= $adoptante->telefono_adoptante ?></td>
                                    <td><button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Registrar Denuncia</button></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>




<!-- Modal para completar con los datos de la denuncia -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Datos de la Denuncia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
      </button> 
  </div>
  <div class="modal-body">
    <form class="formDenuncia" id="formDenuncia" method="post">
        <fieldset class="form-group">
            <label for="formGroupExampleInput">Motivo de la denuncia</label>
            <select class="custom-select selectDenuncia" id="selectMotivoDenuncia" >
                <option selected>Seleccione motivo de la denuncia</option>
                <option value="1">Maltrato</option>
                <option value="2">Abandono</option>
                <option value="3">Tenencia Irresponsable</option>
                <option value="4">Otros</option>
            </select>
        </fieldset>
        <fieldset>
            <label for="formGroupExampleInput" >Detalle de denuncia:</label>
            <textarea  class="form-control" id="descripcionDenuncia" ></textarea>
        </fieldset>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <div class="loader"></div><button type="submit" class="btn btn-primary btn-registrar-denuncia">Regsitrar Denuncia</button>
        </div> 
    </form>
</div>
</div>
</div>
</div>







<!-- Modal para mostrar que la denuncia fue registrada --> 
<div class="modal registro_denuncia" id="registro_denuncia" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Denuncia registrada</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
      </button>
  </div>
  <div class="modal-body">
     <h5><p>La denuncia fue registrada exitosamente!</p></h5>
     <form>
        <fieldset >
            <div class="form-group  mb-4">
                <label class="col-form-label" id="nomApe"></label>
            </div>
            <div class="form-group  mb-4">
                <label class=" col-form-label" id="motivoD"></label>
            </div>
            <div class="form-group  mb-4">
                <label class=" col-form-label" id="descr"></label>
            </div>
            <div class="form-group  mb-4">
                <label class=" col-form-label" id="cant"></label>
            </div>
        </fieldset>
    </form>
</div>
<div id="divMuestra"></div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
</div>
</div>
</div>
</div>




<!-- Script para activar el datatable en la tabla -->
<script>
    $(document).ready( function () {

    //-----> oculto el spinner que se carga en el modal
    $('.loader').hide();    
        
    //------------> seteo las configuraciones de la tabla
    $('#table_id').DataTable({
        select: true,  //-----> hace que las filas sean seleccionables
        paging: true,  //--> habilita el paginado
        "language": {    //-------> en este array se puede perzonalizar el texto que se muestra en cada uno de los botones y labels de la tabla y como se muestran los datos
            "lengthMenu": "Muestra _MENU_ denuncias por página",
            "zeroRecords": "No se encontro resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_ páginas",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(Filtrando los _MAX_ registros)",
            "search": "<i class='fas fa-filter'></i> Buscar por dirección: ",
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
        searching: true,             //---> habilita la busqueda de registros
        "columnDefs": [              //-----> se le cambia propiedades a las columnas, cuales son buscables por filtros, visibles, ordenables
            { "searchable": false, "targets": 0, "orderable": false, "visible": false},   //---> columna del id
            { "searchable": false, "targets": [1,3,4], "orderable": true, "visible": true},      
            { "searchable": true, "targets": 2, "orderable": true, "visible": true},
            { "searchable": false, "targets": 5, "orderable": false, "visible": true}   
            ],
        "ordering": true,                     //-->  habilita el ordenamiento de columnas
        "search": {                           // -----> opciones para la busqueda de datos 
            "caseInsensitive": true,        //----> habilita el caseSensitive
            "search": " ",               //---> se le puede asignar un filtro por defecto a la busqueda asi los encuentra y ordena por ese filtro
            "smart": true                    //----->  activa la busqueda smart, no busca el String identico, busca los similares y las ocurrencias
        }
    });

    
    $('#registro_denuncia').on('hide.bs.modal', function (e) {
        window.location.reload();
    });
        
} );
</script>


<script>

    //------> Si manda el formulario entonces pasa esto
    $('#formDenuncia').on('submit',function(event){

        event.preventDefault();
        var tipoDenuncia = $('#selectMotivoDenuncia').val();
        var id_adoptante = $('.selected').find('.oculto').val();   
        console.log("elegido: "+id_adoptante+", tipoDenuncia:"+tipoDenuncia);
        
        //----> si el motivo de la denuncia es correcto pasa al otro if
        if (tipoDenuncia != "Seleccione motivo de la denuncia"){
            $.ajax({
                url: "<?= base_url('/C_Denuncia/registraDenuncia') ?>",     
                data: {              
                    tipoDenuncia: tipoDenuncia,
                    descripcionDenuncia: $('#descripcionDenuncia').val(),
                    id_adoptante: id_adoptante
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
                    console.log(data);
                    var arr = JSON.parse(data);
                    
                    console.log(arr['usuario']);

                    $('#nomApe').html("<h5>Nombre persona que registro la denuncia: </h5>"+ arr['usuario']['nombre_usuario']+" "+arr['usuario']['apellido_usuario']);
                    $('#motivoD').html("<h5>Motivo de la denuncia: </h5>"+ arr['motivo']);
                    $('#descr').html("<h5>Detalle de denuncia:</h5><p>"+ arr['descripcionDenuncia']+"</p>");
                    var cantidad = parseInt(arr.cantidad_denuncias)+1;
                    $('#cant').html("<h5>Cantidad de denuncias: </h5>"+ cantidad);
                }
            })
            .done(function( json ) {    
                // si todo anda bien
                console.log( "The request is good!" );
                $('#exampleModal').modal('hide');
                $('.loader').hide(); //---> oculta ek spinner 
                $('#registro_denuncia').modal('show');
                //---> seteo los valores por defecto
                $('#selectMotivoDenuncia').val('');
                $('#descripcionDenuncia').val('');
            })
            .fail(function( xhr, status, errorThrown ) {
                alert( "Sorry, there was a problem!" );
                console.log( "Error: " + errorThrown );
                console.log( "Status: " + status );
                console.dir( xhr );
            })
            .always(function( xhr, status ) {
                console.log( "The request is complete!" );
            });
            
        }else{
            alert('Seleccione un tipo de denuncia!');
        }
    });
    
</script>

