function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}


$(document).on('change', '#id_cliente', function(event) {
  id_cliente = $(this).val();
       $.ajax({
         type: 'POST',
         url: 'pedido.php',
         data: "process=datos_cliente&id_cliente="+id_cliente,
         dataType: 'json',
         success: function(datax)
         {
           //process = datax.process;
           var select_depa = datax.select_depa;
           var select_muni = datax.select_muni;
           var direccion = datax.direccion;

           $(".depa").html(select_depa);
           $(".muni").html(select_muni);
           $("#direccion").val(direccion);

           $(".select_depa").select2();
           $(".select_muni").select2();
         }
      });
});


$(document).on('change', '#origen', function(event) {
  $("#mostrardatos").html("");
});
$(document).ready(function() {
  totales();

  $("#codigo").keyup(function(evt)
	{
		var code = $(this).val();
    if (evt.keyCode == 13)
		{
			if($(this).val()!="")
			{
      	addProductList(code, "C");
			}
			$(this).val("");
    }
  });
  $("#scrollable-dropdown-menu #producto_buscar").typeahead({
    highlight: true,
  }, {
    limit: 100,
    name: 'productos',
    display: 'producto',
    source: function show(q, cb, cba) {
      console.log(q);
      var url = 'autocomplete_producto2_pedido.php'+"?query="+q+"&id_origen="+$("#origen").val();
      $.ajax({
          url: url
        })
        .done(function(res) {
          cba(JSON.parse(res));
        })
        .fail(function(err) {
          alert(err);
        });
    }
  }).on('typeahead:selected', onAutocompleted);

  function onAutocompleted($e, datum) {
    $('.typeahead').typeahead('val', '');
    var prod0 = datum.producto;
    var prod = prod0.split("|");
    var id_prod = prod[0];
    var descrip = prod[1];
    addProductList(id_prod, "D");
  }

  $(".decimal2").numeric({
    negative: false,
    decimal: false
  });
  $(".pp").numeric({
   negative: false,
   decimalPlaces: 4
 });

  $(".sel").select2();
    $(".sel_r").select2();
  $('#formulario').validate({
    rules: {
      descripcion: {
        required: true,
      },
      precio1: {
        required: true,
        number: true,
      },
    },
    submitHandler: function(form) {
      senddata();
    }
  });

  //select2 select autocomplete
  $(".select_depa").select2();
  $(".selc").select2();
  $(".select_muni").select2();
  $('#categoria').select2();
  $('#categoria').select2();
  $('#tipo_entrada').select2();
  $('#vendedor').select2();
  $('#origen').select2();

  $("#fecha1").datepicker({
    format: 'dd-mm-yyyy',
  })
  $("#fecha_entrega").datepicker({
    format: 'dd-mm-yyyy',
  })

});

$(document).keydown(function(e) {

  if (e.which == 114) { //F3 salir
    e.stopPropagation();
    e.preventDefault();

    if ($('#a').attr('hidden')) {
      $('#a').removeAttr('hidden');
      $('#b').attr('hidden', 'hidden');
      $('#codigo').focus();
    } else {
      $('#b').removeAttr('hidden');
      $('#a').attr('hidden', 'hidden');
      $('#producto_buscar').focus();
    }
  }
})
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

$(document).on("click", "#text_cliente", function()
{
  $("#cliente").attr("type", "text");
  $("#text_cliente").attr("type","hidden");
  $("#text_cliente").val("");
  $("#id_cliente").val("");

})

function addProductList(id_proda, tip)
{
  $(".select2-dropdown").remove();
  $('#inventable').find('tr#filainicial').remove();
  id_proda = $.trim(id_proda);
  id_factura = parseInt($('#id_pedido').val());
  if (isNaN(id_factura))
	{
    id_factura = 0;
  }

  id_origen = $("#origen").val();

  urlprocess = "pedido.php";
  var dataString = 'process=consultar_stock'+'&id_producto='+id_proda+'&id_factura='+id_factura+'&tipo='+tip+"&id_origen="+id_origen;
  $.ajax({
    type: "POST",
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(data)
		{
			if(data.typeinfo == "Success")
			{
	      var id_prod = data.id_producto;
	      var precio_venta = data.precio_venta;
	      var unidades = data.unidades;
	      var existencias = data.stock;
	      var perecedero = data.perecedero;
	      var descrip_only = data.descripcion;
	      var fecha_fin_oferta = data.fecha_fin_oferta;
	      var exento = data.exento;
	      var categoria = data.categoria;
	      var select_rank = data.select_rank;

	      var preciop_s_iva = parseFloat(data.preciop_s_iva);

	      var tipo_impresion = $('#tipo_impresion').val();

	      var filas = parseInt($("#filas").val());
        filas++;
	      var exento = "<input type='hidden' id='exento' name='exento' value='"+exento+"'>";

	      var cantidades = "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='txt_box decimal2 "+categoria+" cant' id='cant' name='cant' value='' style='width:60px;'></div></td>";
	      tr_add = '';
	      tr_add += "<tr  class='row100 head' id='"+filas+"'>";
	      tr_add += "<td hidden class='cell100 column10 text-success id_pps'><input type='hidden' id='unidades' name='unidades' value='"+data.unidadp+"'>"+id_prod+"</td>";
	      tr_add += "<td class='cell100 column30 text-success'>"+descrip_only+exento+'</td>';
	      tr_add += "<td class='cell100 column10 text-success' id='cant_stock'>"+existencias+"</td>";
	      tr_add += cantidades;
	      tr_add += "<td class='cell100 column10 text-success preccs'>"+data.select+"</td>";
	      tr_add += "<td class='cell100 column10 text-success rank_s'>"+data.select_rank+"</td>";
        tr_add += "<td class='cell100 column10 text-success'><input type'text' id='precio_venta' class='form-control pp' value='"+data.preciop+"' class='txt_box'></td>";
	      tr_add += "<td class='cell100 column10'>"+"<input type='hidden'  id='subtotal_fin' name='subtotal_fin' value='"+"0.00"+"'>"+"<input type='text'  class='decimal txt_box form-control subt' id='subtotal_mostrar' name='subtotal_mostrar'  value='"+"0.00"+"'readOnly></td>";
        tr_add += '<td class="cell100 column10 Delete text-center"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
	      tr_add += '</tr>';
	      //numero de filas
	      $("#mostrardatos").prepend(tr_add);
	      $(".decimal2").numeric({
	        negative: false,
	        decimal: false
	      });
	      $(".86").numeric({
	        negative: false,
	        decimalPlaces: 4
	      });
        $(".pp").numeric({
         negative: false,
         decimalPlaces: 4
       });
	      $('#filas').val(filas);
	      $('#items').val(filas);
	      $(".sel").select2();
	      $(".sel_r").select2();
	      $('#mostrardatos #' +filas).find("#cant").focus();
	      totales();
    	}
			else
			{
				display_notify("Error", data.msg);
			}
		}
  });
  totales();
}

$(document).on('change', '.select_depa', function(event)
{
  $("#select_muni *").remove();
  $("#select2-select_muni-container").text("");
  var ajaxdata = { "process" : "municipio", "id_departamento": $("#select_depa").val() };
    $.ajax({
        url:"pedido.php",
        type: "POST",
        data: ajaxdata,
        success: function(opciones)
        {
      $("#select2-select_muni-container").text("Seleccione");
          $("#select_muni").html(opciones);
          $("#select_muni").val("");
      }
    })
});

$(document).on('keyup', '.cant', function(evt) {
  var tr = $(this).parents("tr");
  if (evt.keyCode == 13) {
    num = parseFloat($(this).val());
    if (isNaN(num)) {
      num = 0;
    }
    if ($(this).val() != "" && num > 0) {
      tr.find('.sel').select2("open");
    }
  }
});


$(document).on('select2:close', '.sel_r', function() {

  if ($('#b').attr('hidden')) {
    $('#codigo').focus();
  } else {
    $('#producto_buscar').focus();
  }
});
$(document).on('select2:close', '.sel', function(event) {
  var tr = $(this).parents("tr");
  var cantid = tr.find("#cant").val();
  var id_presentacion = $(this).val();
  var a = $(this);
  //console.log(id_presentacion);
  $.ajax({
    url: 'venta.php',
    type: 'POST',
    dataType: 'json',
    data: 'process=getpresentacion'+"&id_presentacion="+id_presentacion+"&cant="+cantid,
    success: function(data) {
      a.closest('tr').find('.descp').html(data.descripcion);
      a.closest('tr').find('#precio_venta').val(data.precio);
      a.closest('tr').find('#unidades').val(data.unidad);
      a.closest('tr').find('#precio_sin_iva').val(data.preciop_s_iva);
      a.closest('tr').find(".rank_s").html(data.select_rank);
      fila = a.closest('tr');
      id_producto = fila.find('.id_pps').text();
      existencia = parseFloat(fila.find('#cant_stock').text());
      existencia = round(existencia, 4);
      a_cant = parseFloat(fila.find('#cant').val());
      unidad = parseInt(fila.find('#unidades').val());
      a_cant = parseFloat(a_cant * data.unidad);
      a_cant = round(a_cant, 4);
      $(".sel_r").select2();
      a.closest('tr').find('.sel_r').select2("open");

      a_asignar = 0;

      $('#mostrardatos tr').each(function(index) {

        if ($(this).find('.id_pps').text() == id_producto) {
          t_cant = parseFloat($(this).find('#cant').val());
          t_cant = round(t_cant, 4);
          if (isNaN(t_cant)) {
            t_cant = 0;
          }
          t_unidad = parseInt($(this).find('#unidades').val());
          if (isNaN(t_unidad)) {
            t_unidad = 0;
          }
          t_cant = parseFloat((t_cant * t_unidad));
          a_asignar = a_asignar + t_cant;
          a_asignar = round(a_asignar, 4);
        }
      });
      //console.log(existencia);
      //console.log(a_asignar);

      if (a_asignar > existencia) {
        val = existencia - (a_asignar - a_cant);
        val = val / unidad;
        val = Math.trunc(val);
        val = parseInt(val);
        fila.find('#cant').val(val);
      }

      totales();
    }
  });
  setTimeout(function() {
    totales();
  }, 200);


});

$(document).on('change', '.sel_r', function(event) {
  var a = $(this).closest('tr');
  precio = parseFloat($(this).val());
  a.find('#precio_venta').val(precio);
  a.find("#precio_sin_iva").val(precio / 1.13);
  totales();
});

// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function() {
  $(this).parents("tr").remove();
  totales();
});
$(document).on("click", ".Delete_bd", function() {
  var tr = $(this).parents("tr");
  id_detalle = tr.attr("id_detalle");
  $.ajax({
    type:'POST',
    url:'editar_cotizacion.php',
    data:'process=del&id_detalle='+id_detalle,
    dataType:'JSON',
    success: function(datax)
    {
      if(datax.typeinfo == "Success")
      {
        tr.remove();
      }
    }
  });
  totales();
});
$(document).on("keyup", "#cant", function() {
  fila = $(this).closest('tr');
  id_producto = fila.find('.id_pps').text();
  existencia = parseFloat(fila.find('#cant_stock').text());
  existencia = round(existencia, 4);
  var tr = $(this).parents("tr");
	id_presentacion_p = tr.find('.sel').val();
  a_cant=$(this).val();
  unidad= parseInt(fila.find('#unidades').val());
  a_cant=parseFloat(a_cant*unidad);
	a_cant=round(a_cant, 4);

  a_asignar =0;
  $('#mostrardatos tr').each(function(index) {

    if ($(this).find('.id_pps').text() == id_producto) {
      t_cant = parseFloat($(this).find('#cant').val());
      t_cant = round(t_cant, 4);
      if (isNaN(t_cant)) {
        t_cant = 0;
      }
      t_unidad = parseInt($(this).find('#unidades').val());
      if (isNaN(t_unidad)) {
        t_unidad = 0;
      }
      t_cant = parseFloat((t_cant * t_unidad));
      a_asignar = a_asignar + t_cant;
      a_asignar = round(a_asignar, 4);
    }
  });
  //console.log(existencia);
  //console.log(a_asignar);

  if (a_asignar > existencia) {
    val = existencia - (a_asignar - a_cant);
    val = val / unidad;
    val = Math.trunc(val);
    val = parseInt(val);
    $(this).val(val);
    setTimeout(function() {
      totales();
    }, 200);
  } else {
    totales();
  }
  var tr = $(this).parents("tr");

	setTimeout(function(){ totales(); }, 300);
});

$(document).on("keyup", "#precio_venta", function() {
  totales();
})

$(document).on("blur", "#precio_venta", function() {
  tr = $(this).closest('tr');
  precio = parseFloat($(this).val());

  if (isNaN(precio)) {
    precio=0;
  }

  precio_rank = parseFloat(tr.find('.sel_r').val());
  precio_rank_f = truncateDecimals(precio_rank,2);
  if (precio!=0 && precio<precio_rank_f) {
    tr.find("#precio_venta").val(precio_rank);
    precio = precio_rank;
  }

  totales();
})


$(document).on("keyup", "#precio_venta", function() {
  tr = $(this).closest('tr');
  precio = parseFloat(des =  String($(this).val()).replace(/[^0-9/.]/g, ""));
  precio_rank = parseFloat(tr.find('.sel_r').val());
  if (isNaN(precio)) {
    precio=precio_rank;
  }
  $(this).val(precio);
  precio_rank = parseFloat(tr.find('.sel_r').val());
  precio_rank_f = truncateDecimals(precio_rank,2);
  if (precio<precio_rank_f) {
    tr.find("#precio_venta").val(precio_rank);
    precio = precio_rank;
  }
  totales();
});


function truncateDecimals (num, digits) {
    var numS = num.toString(),
        decPos = numS.indexOf('.'),
        substrLength = decPos == -1 ? numS.length : 1 + decPos + digits,
        trimmedResult = numS.substr(0, substrLength),
        finalResult = isNaN(trimmedResult) ? 0 : trimmedResult;

    return parseFloat(finalResult);
}


function totales()
{
  var subtotal = 0;
  var total = 0;
  var totalcantidad = 0;
  var subcantidad = 0;
  var total_dinero = 0;
  var total_cantidad = 0;
  $("#mostrardatos tr").each(function()
  {
    var tr = $(this);
    var unidad = $(this).find(".unidad").val();
    var venta = parseFloat($(this).find(".pp").val());
    var cantidad = parseFloat($(this).find(".cant").val());
    var cantidad =round(cantidad,4);
    subtotal = venta * cantidad;
    if (isNaN(cantidad) == true)
    {
      cantidad = 0;
    }
    totalcantidad += cantidad;
    if (isNaN(subtotal) == true)
    {
      subtotal = 0;
    }
    tr.find(".subt").val(round(subtotal,4).toFixed(4));
    total += subtotal;
  });
  if (isNaN(total) == true)
  {
    total = 0;
  }
  total_dinero = round(total,4);
  total_cantidad = round(totalcantidad,4);

  $('#total_gravado').html("<strong>"+total_dinero.toFixed(4)+"</strong>");
  $('#totcant').html(total_cantidad);

}
// actualize table
$(document).on("click", "#submit1", function()
{
  $('#submit1').attr('disabled', true);
  if($("#mostrardatos tr").length>0)
  {
    senddata();
  }
  else {
    display_notify("Error", "Debe agregar productos a la lista");
    $('#submit1').attr('disabled', false);
  }
});

$(document).on("click", "#editari", function()
{
  $('#submit1').attr('disabled', true);
  $('#editari').attr('disabled', true);
  if($("#mostrardatos tr").length>0)
  {
    senddata2();
  }
  else {
    display_notify("Error", "Debe agregar productos a la lista");
    $('#submit1').attr('disabled', false);
    $('#editari').attr('disabled', false);
  }
});


function senddata()
{
  //Calcular los valores a guardar de cada item del inventario
  var id_cliente = $("#id_cliente").val();
  var id_vendedor = $("#vendedor").val();
  var direccion = $("#direccion").val();
  var select_depa = $("#select_depa").val();
  var select_muni = $("#select_muni").val();
  var i = 0;
  var fallo = 0;
  var precio_compra, precio_venta, cantidad, id_prod;
  var StringDatos = "";
  var id = $("select#tipo_entrada option:selected").val(); //get the value
  var id_pedido = $("#id_pedido").val();
  var origen = $("#origen").val();
  var comentario = $("#comentario").val();


  var verificar = 'noverificar';
  var verificador = [];

  $("#mostrardatos tr").each(function(index) {
    if (index >= 0)
    {

      var id_producto = $(this).find(".id_pps").text();
      var id_presentacion = $(this).find('.sel').val();
      var precio_venta = $(this).find(".pp").val();
      var cantidad = $(this).find("#cant").val();
      var unidad = $(this).find("#unidades").val()
      var subtotal = $(this).find(".subt").val()


      if (id_producto != "" || id_producto == undefined)
      {
        console.log("OK");
        StringDatos += id_producto + "|" + precio_venta + "|" + cantidad + "|" + subtotal +"|" + unidad+"|" +id_presentacion+"#";
        verificador.push(verificar);
        if(cantidad == 0 || cantidad == "" || precio_venta=="")
        {
          fallo += 1;
        }
        else
        {
          i = i + 1;
        }
      }

    }
  });
  // Captura de variables a enviar
  var fecha_movimiento = "";
  var numero_doc = 0;
  var id_sucursal = -1;
  var total_compras = $('#total_gravado').text();

  var fecha_movimiento = $("#fecha1").val();
  var fecha_entrega = $("#fecha_entrega").val();
  var transporte = $("#transporte").val();

  var concepto=$('#concepto').val();

  var dataString = 'process='+ "pedido" + '&stringdatos=' + StringDatos + '&cuantos=' + i + '&fecha_movimiento=' + fecha_movimiento + '&total_compras=' + total_compras + '&id_cliente=' + id_cliente+ '&id_vendedor=' + id_vendedor;
  dataString += '&direccion=' + direccion + '&select_depa=' + select_depa + '&select_muni=' + select_muni + "&id_pedido=" + id_pedido + "&origen=" + origen + '&fecha_entrega=' + fecha_entrega + '&transporte=' + transporte + "&comentario="+comentario;

  urls = "pedido.php";
  if (id_pedido!="")
  {
    urls = "editar_pedido.php";
  }
  else
  {
    urls = "pedido.php";
  }

  cadena = "";
  console.log(fallo);
  if(fallo == 0 && i>0)
  {
    $.ajax({
      type: 'POST',
      url: urls,
      data: dataString,
      dataType: 'json',
      async: false,
      cache: false,
      success: function(datax) {
        process = datax.process;
        //var maxid=datax.max_id;
        display_notify(datax.typeinfo, datax.msg);
        if(datax.typeinfo == "Success")
        {
          cadena = "pedido_pdf.php?id_pedido="+datax.id_pedido;
        }

      }
    });

    if (id_pedido=="")
    {
      window.open(cadena,'',"");
      setTimeout(
        function() {
          location.href = urls;
        }
      , 1000);
    }
    else
    {
      setTimeout(
        function() {
          reload1();
        }
      , 1000);
    }
  }
  else
  {
    display_notify("Error", "Verifique los datos.");
    $('#submit1').attr('disabled',false);
    $('#submit2').attr('disabled',false);
  }
}

function senddata2()
{
  //Calcular los valores a guardar de cada item del inventario
  var id_cliente = $("#id_cliente").val();
  var id_vendedor = $("#vendedor").val();
  var direccion = $("#direccion").val();
  var select_depa = $("#select_depa").val();
  var select_muni = $("#select_muni").val();
  var i = 0;
  var fallo = 0;
  var precio_compra, precio_venta, cantidad, id_prod;
  var StringDatos = "";
  var id = $("select#tipo_entrada option:selected").val(); //get the value
  var id_pedido = $("#id_pedido").val();
  var origen = $("#origen").val();
  var comentario = $("#comentario").val();
  var verificar = 'noverificar';
  var verificador = [];
  $("#mostrardatos tr").each(function(index) {
    if (index >= 0)
    {

      var id_producto = $(this).find(".id_pps").text();
      var id_presentacion = $(this).find('.sel').val();
      var precio_venta = $(this).find(".pp").val();
      var cantidad = $(this).find("#cant").val();
      var unidad = $(this).find("#unidades").val()
      var subtotal = $(this).find(".subt").val()


      if (id_producto != "" || id_producto == undefined)
      {
        console.log("OK");
        StringDatos += id_producto + "|" + precio_venta + "|" + cantidad + "|" + subtotal +"|" + unidad+"|" +id_presentacion+"#";
        verificador.push(verificar);
        if(cantidad == 0 || cantidad == "" || precio_venta=="")
        {
          fallo += 1;
        }
        else
        {
          i = i + 1;
        }
      }

    }
  });
  // Captura de variables a enviar
  var fecha_movimiento = "";
  var numero_doc = 0;
  var id_sucursal = -1;
  var total_compras = $('#total_gravado').text();

  var fecha_movimiento = $("#fecha1").val();
  var fecha_entrega = $("#fecha_entrega").val();
  var transporte = $("#transporte").val();

  var concepto=$('#concepto').val();



  var dataString = 'process='+ "editar" + '&stringdatos=' + StringDatos + '&cuantos=' + i + '&fecha_movimiento=' + fecha_movimiento + '&total_compras=' + total_compras + '&id_cliente=' + id_cliente+ '&id_vendedor=' + id_vendedor;
  dataString += '&direccion=' + direccion + '&select_depa=' + select_depa + '&select_muni=' + select_muni + "&id_pedido=" + id_pedido + "&origen=" + origen + '&fecha_entrega=' + fecha_entrega + '&transporte=' + transporte + "&comentario="+comentario;

  urls = "editar_pedido.php";


  cadena = "";
  console.log(fallo);
  if(fallo == 0 && i>0)
  {
    $.ajax({
      type: 'POST',
      url: urls,
      data: dataString,
      dataType: 'json',
      async: false,
      cache: false,
      success: function(datax) {
        process = datax.process;
        //var maxid=datax.max_id;
        display_notify(datax.typeinfo, datax.msg);
        if(datax.typeinfo == "Success")
        {
          setTimeout(
            function() {
              location.href = "admin_pedido_pendiente.php"
              //location.href = urls+"?id_pedido="+id_pedido;
            }
          , 1000);
        }

      }
    });
  }
  else
  {
    display_notify("Error", "Verifique los datos.");
    $('#submit1').attr('disabled',false);
    $('#submit2').attr('disabled',false);
  }
}

function remover_filas()
{
  $("#inventable tr").remove();
}

function reload1() {
  location.href = "admin_pedido_pendiente.php";
}
$(document).on("click", "#btnEsc2", function(event) {
  $('#clienteModal').modal('hide');
  //reload1();
});

$(document).on('change', '.sel_r', function(event) {
	var a = $(this).closest('tr');
	precio=parseFloat($(this).val());
	a.find('#precio_venta').val(precio);
	a.find("#precio_sin_iva").val(precio/1.13);
	totales();
});
