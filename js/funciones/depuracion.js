$(document).ready(function() {
  $('.select').select2();

  // searchFilter();
  // $("#origen").change(function(){
  //   searchFilter();
  // });
  // $('#producto_buscar').on('keyup', function(event) {
  //    searchFilter();
  //  });
});
$(function() {
  //binding event click for button in modal form
  $(document).on("click", "#btnDelete", function(event) {
    deleted();
  });
  // Clean the modal form
  $(document).on('hidden.bs.modal', function(e) {
    var target = $(e.target);
    target.removeData('bs.modal').find(".modal-content").html('');
  });
});
// function searchFilter(page_num)
// {
//   page_num = page_num ? page_num : 0;
//   var producto_buscar = $('#producto_buscar').val();
//   var origen = $('#origen').val();
//   //var limite = $('#limite').val();
//   getData(producto_buscar,origen,page_num)
// }

//  function getData(producto_buscar,origen,page_num){
//   var sortBy = 'asc';//$('#sortBy').val();
//   var records = 50;//$('#records').val();
//   urlprocess = "depuracion.php";
//   $.ajax({
//     type: 'POST',
//     url: urlprocess,
//     data: {
//       process: 'traerdatos',
//       page: page_num,
//       producto_buscar: producto_buscar,
//       origen: origen,
//       sortBy: sortBy,
//       records: records
//     },
//     beforeSend: function()
//     {
//       $('.loading-overlay').show();
//     },
//     success: function(html)
//     {
//         $('#mostrardatos').find('input:checkbox:not(:checked)').closest('tr').remove();
//         $('#mostrardatos').append(html);
//         $(".sel").select2();
//         $(".cant").numeric({negative:false,decimal:false});
//     }
//   });
//   $.ajax({
//     type: 'POST',
//     url: "depuracion.php",
//     data: {
//       process: 'traerpaginador',
//       page: page_num,
//       producto_buscar: producto_buscar,
//       origen: origen,
//       sortBy: sortBy,
//       records: records
//     },
//     success: function(value)
//     {
//       $('#paginador').html(value);
//     }
//   });
// }

//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#inventable", function() {
  totales();
});
$(document).on("keyup", ".precio_compra, .precio_venta", function() {
  totales();
});
$(document).on("keyup", ".cant", function() {
  var tr  = $(this).parents("tr");
  var unidad = parseInt(tr.find(".unidad").val());
  var exis = parseInt(tr.find(".exis").text());
  var cant = parseInt($(this).val());
  var tot = cant * unidad;
  if(tot > exis)
  {
    $(this).val(0);
  }
  totales();
});
// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function()
{
  $(this).parents("tr").remove();
  totales();
});
$(document).on("click", ".cheke", function()
{
  var tr = $(this).parents("tr");
  if($(this).is(":checked"))
  {
    tr.find(".cant").attr("readOnly", false);
  }
  else
  {
    tr.find(".cant").attr("readOnly", true);
  }
});
//Calcular Totales del grid
function totales()
{
  var subtotal = 0;
  var total = 0;
  var totalcantidad = 0;
  var subcantidad = 0;
  var total_dinero = 0;
  var total_cantidad = 0;
  $("#inventable>tbody tr").each(function()
  {
    var compra = $(this).find(".precio_compra").val();
    var unidad = $(this).find(".unidad").val();
    var venta = $(this).find(".precio_venta").val();
    var cantidad = parseInt($(this).find(".cant").val());
    var vence = $(this).find(".vence").val();
    subtotal = compra * cantidad;
    if (isNaN(cantidad) == true)
    {
      cantidad = 0;
    }
    totalcantidad += cantidad;
    if (isNaN(subtotal) == true)
    {
      subtotal = 0;
    }
    total += subtotal;
  });
  if (isNaN(total) == true)
  {
    total = 0;
  }
  total_dinero = round(total,2);
  total_cantidad = round(totalcantidad,2);
  total_dinero = round(total,2);
  total_cantidad = round(totalcantidad,2);

  $('#total_dinero').html("<strong>" + total_dinero + "</strong>");
  $('#totcant').html(total_cantidad);

}
// actualize table
$(document).one("click", "#btnCambio", function()
{
  $('#btnCambio').attr('disabled', true);
  swal({
    title: " Desactivar esta lista de productos?",
    text: "",
    type: "warning",
    showCancelButton: true,
    confirmButtonClass: "btn-danger",
    confirmButtonText: "Si, desactivar esta lista de productos!",
    cancelButtonText: "No, cancelar!",
    closeOnConfirm: true,
    closeOnCancel: false
  },
  function(isConfirm) {
    if (isConfirm)
    {
      senddata();
    }
    else
    {
      swal("Cancelado", "Operación cancelada", "error");
    }
  });

});

function senddata()
{
  var myCheckboxes = new Array();
  var cuantos=0;
  $("input[name='myCheckboxes']:checked").each(function(index)
	{
    var obj = new Object();
    obj.id_producto = $(this).val();

    text = JSON.stringify(obj);
		myCheckboxes.push(text);
		cuantos=cuantos+1;
	});

  if (cuantos==0)
	{
		myCheckboxes='0';
	}
  var json_Check = '[' + myCheckboxes + ']';
	var dataString='process=depurar&myCheckboxes='+json_Check+'&cuantos='+cuantos;

  $.ajax({
  	type:'POST',
  	url:'depuracion.php',
  	data: dataString,
  	dataType: 'json',
  	success: function(datax)
  	{
  		process=datax.process;
  		display_notify(datax.typeinfo,datax.msg);
  		if(datax.typeinfo == "Success")
  		{
  			setInterval("reload1();", 1500);
  		}
  	}
  });
}
function reload1()
{
  location.href = "admin_producto.php";
}

$(document).on("click", "#generar", function (event) {
  var array_json = new Array();
  i=0;
  $('#loadtable>tbody tr' ).each(function() {
    var id_prod = $(this).find("td:eq(0)").text();
    var cant = $(this).find(".cant").val();
    var cheque = $(this).find(".cheke").prop('checked');
    var existencia =$(this).find(".exis").text();

    if (cant != "" && parseInt(cant)>0 && cheque==true)
    {
      var obj = new Object();
      obj.id_prod = id_prod;
      obj.cantidad = cant;
      obj.existencia= existencia;
      //convert object to json string
      text = JSON.stringify(obj);
      array_json.push(text);

      i = i + 1;
    }
    else
    {
      error = true;
    }
  });

  json_arr = '[' + array_json + ']';

  console.log(json_arr);
  console.log(i);

  if(i>0)
  {
    $('#params').val(json_arr);
    $('#con').val($('#concepto').val());
    $('#fecha').val($('#fecha1').val());
    $('#frm1').submit();
  }
  else
  {
    display_notify("Warning","Seleccione al menos un producto a asignar y la cantidad de ubicaciones a asignar")
  }

});

function round(value, decimals)
{
  return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}
