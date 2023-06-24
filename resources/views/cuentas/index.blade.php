@extends('layouts.app')
@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-easyui@1.5.21/css/easyui.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery-easyui@1.5.21/js/jquery.easyui.min.js"></script>
<script>
    function zpopupWindow(url, title, win, w, h) {
        const y = win.top.outerHeight / 2 + win.top.screenY - ( h / 2);
        const x = win.top.outerWidth / 2 + win.top.screenX - ( w / 2);
        return win.open(url, title, `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=${w}, height=${h}, top=${y}, left=${x}`);
    }
    
    $(document).on("popupafterclose","popupWindow",function() {
        console.log('cerró');
    })

    $(document).ready(function() {

        var popupWindow;
        var parentNode; // Variable para almacenar el nodo padre del nodo actualmente seleccionado

        var timer = setInterval(function() {
            if(popupWindow.closed) {
                clearInterval(timer);
                var selectedNode = $('#tree').tree('getSelected');
                if (selectedNode) {
                    $('#tree').tree('reload', selectedNode.target); // Recargar los hijos del nodo seleccionado
                } else {
                    console.log('No se ha seleccionado ningún nodo para actualizar los hijos.');
                }
            }
        }, 1000);

        $('#edit-node').click(function() {
            var selectedNode = $('#tree').tree('getSelected');
            if (selectedNode) {
                var parentNodeId = selectedNode.id;
                var url = 'http://head-office.test/cuentas/create/' + parentNodeId;
        
                popupWindow = window.open(url, 'popupWindow', 'width=500,height=500');
                parentNode = selectedNode; // Almacenar el nodo padre antes de abrir la ventana emergente
            } else {
                console.log('No se ha seleccionado ningún nodo para agregar un hijo.');
            }
        });

        $('#refresh').click(function() {
            console.log('refresh');
            var selectedNode = $('#tree').tree('getSelected');
            if (selectedNode) {
                $('#tree').tree('loadData');
            } else {
                console.log('No se ha seleccionado ningún nodo para actualizar los hijos.');
            }
        });
        
      $('#refresh-children').click(function() {
        var selectedNode = $('#tree').tree('getSelected');
        if (selectedNode) {
          $('#tree').tree('reload', selectedNode.target); // Recargar los hijos del nodo seleccionado
        } else {
          console.log('No se ha seleccionado ningún nodo para actualizar los hijos.');
        }
      });

      $(window).on('beforeunload', function() {
        if (popupWindow && !popupWindow.closed) {
          popupWindow.addEventListener('unload', function() {
            if (parentNode) {
              var isExpanded = $('#tree').tree('isExpanded', parentNode.target);
              $('#tree').tree('reload', parentNode.target); // Recargar el nodo padre cuando se cierra la ventana emergente
              if (isExpanded) {
                $('#tree').tree('expand', parentNode.target); // Expandir el nodo padre si estaba expandido previamente
              } else {
                $('#tree').tree('collapse', parentNode.target); // Colapsar el nodo padre si estaba colapsado previamente
              }
            }
            console.log('La ventana emergente se ha cerrado.');
          });
        }
      });

      $('#tree').tree( {
            onClick: function(node) {
                $.get('/uui/cuenta?id='+node.id,function(data){
                    var w = 800;
                    var h = 700;
                    var left = (screen.width/2)-(w/2);
                    var top = (screen.height/2)-(h/2);
                    var winData = 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left ;
                    var urlCreate = '{{ route('cuentas.create') }}/'+node.id;
                    var urlEdit = '{{ route('cuentas.edit') }}/'+node.id;
                    document.getElementById('hrefcreate').setAttribute("href","javascript:zpopupWindow('"+urlCreate+"','popup',window,"+w+","+h+")");
                    document.getElementById('hrefedit').setAttribute("href","javascript:zpopupWindow('"+urlEdit+"','popup',window,"+w+","+h+")");
                    var urlDelete = 'http://head-office.test/uui/cuentas/delete/'+node.id;
                    document.getElementById('hrefdelete').setAttribute("href","javascript:zpopupWindow('"+urlDelete+"','popup',window,"+w+","+h+")");
                    document.getElementById('cuenta-codigo').innerHTML = data.codigo;
                    document.getElementById('cuenta-nombre').innerHTML = data.name;
                    document.getElementById('cuenta-tipo').innerHTML = data.tipo;
                    document.getElementById('cuenta-habilitado').innerHTML = data.habilitado;
                    if( data.sistema ) {
                        $('#cuenta-sistema').show();
                    } else {
                        $('#cuenta-sistema').hide();
                    }
                });
                $('#btncreate').prop('disabled', false);
                $('#btnedit').prop('disabled', false);
                $('#btndelete').prop('disabled', false);
            }
        }            
      );
    });
  </script>    
@endsection
@section('body')
    <h1>Cuentas</h1>
    <div class="row">
        <div class="col-6">
            <div style="border:1px solid #ccc;">
                <ul id="tree" class="easyui-tree" url="/ui/cuentashijas" data-options="animate:true"></ul>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-accent-primary shadow mb-5 bg-white rounded">
                <div class="card-body">
                <h4>Cuenta seleccionada</h4>
                <p>Código: <b><span id='cuenta-codigo'></span></b></p>
                <p>Nombre: <b><span id='cuenta-nombre'></span></b>
                <p>Tipo: <b><span id='cuenta-tipo'></span></b></p>
                <p>Habilitada: <b><span id='cuenta-habilitado'></span></b></p>
                <p>Empresa: <b><span id='cuenta-empresa'></span></b></p>
                <p id='cuenta-sistema' class="badge badge-danger" style="display: none;">(*) Cuenta utilizada por el sistema para asientos automáticos en las sucursales</p>
                    <button id="edit-node">Agregar hijo al nodo seleccionado</button>
                    <button id="refresh">Actualizar</button>
                    <button id="refresh-children">Actualizar hijos</button>
            
                    <a id="hrefcreate" href="">
                        <button id="btncreate" class="btn btn-primary">Nuevo hijo</button>
                    </a>
                    <a id="hrefedit" href="">
                        <button id="btnedit" class="btn btn-primary">Editar</button>
                    </a>
                    <a id="hrefdelete" href="">
                        <button id="btndelete" class="btn btn-danger">Borrar</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('scripts')

@endsection