function createDelete(rutaEliminar, rutaRedirect, token) {
  Swal.fire({
    title: "CONFIRMACIÓN",
    text:
      "Esta acción no se podrá deshacer. Seguro que desea eliminar este elemento?",
    type: "error",
    showCancelButton: true,
    confirmButtonClass: "btn btn-danger",
    cancelButtonClass: "btn btn-light",
    cancelButtonText: "No, cancelar!",
    confirmButtonText: "Si, eliminar!",
    showLoaderOnConfirm: true,

    preConfirm: function() {
      $.ajax({
        url: rutaEliminar,
        data: {_token: token},
        method: "POST"
      }).always(function() {
          swal.close();
          window.location.href = rutaRedirect;
        });
    }
  });


}

function createDeleteAll(dataTable, route) {
  var ch = [];
  var i = 0;

  var set = $("#dataTable").find('tbody > tr > td input[type="checkbox"]');

  $(set).each(function() {
    if ($(this).is(":checked")) {
      ch[i] = $(this).attr("value");
      i = i + 1;
    }
  });

  if (i != 0) {
    Swal.fire({
      title: "CONFIRMACIÓN",
      text:
        "Esta acción no se podrá deshacer. Seguro que desea eliminar los elementos seleccionados?",
      type: "error",
      showCancelButton: true,
      confirmButtonClass: "btn btn-danger",
      cancelButtonClass: "btn btn-light",
      cancelButtonText: "No, cancelar!",
      confirmButtonText: "Si, eliminar!",
      showLoaderOnConfirm: true,

      preConfirm: function() {
        $.ajax({
          url: Routing.generate(route, { data: ch }),
          method: "POST"
        })
          .done(function(e) {
            toastr.success(e.texto, "Notificación");
            $(".group-checkable").prop("checked", "");
            dataTable.ajax.reload();
          })
          .fail(function(e, status, errorThrown) {
            if (errorThrown == "Forbidden") {
              var r =
                "No tienes los permisos necesarios para realizar esta operación";
              toastr.error(r, "Notificación");
            } else {
              var r = eval("(" + e.responseText + ")");
              toastr.error(r.texto, "Notificación");
            }
          })
          .always(function() {
            swal.close();
          });
      }
    });
  } else {
    toastr["warning"]("No existen elementos seleccionados", "Notificación");
  }
}

/* Funcionalidad para el comportamiento del boton insertar */
$(document).on("click", "#boton_insertar", function() {

  $("#operacion").attr("value", "Insertar");
  $(".modal-title").html('<i class="fas fa-plus"></i> Insertar Registro');

  $(".form-control").removeClass("is-invalid");
  $(".invalid-feedback").remove();
  
  $(".form").trigger("reset");
  $(".form textarea").val("");
  $(".form .select2").val("").trigger("change"); //permite limpiar el componente select2 xq no se resetea con el formulario
  $(".ROLE_PROCESO").addClass("d-none");
});

/* Funcionalidad para el comportamiento de los botones de editar */
$(document).on("click", ".editar", function() {
  var element_id = $(this).attr("id");

  $("#operacion").attr("value", "Editar");

  $(".form-control").removeClass("is-invalid");
  $(".invalid-feedback").remove();

  $(".form").trigger("reset");
  if ($(".form textarea")) {
    $(".form textarea").val("");
  }

  if ($(".form .select2")) {
    $(".form .select2")
      .val("")
      .trigger("change");
  } //permite limpiar el componente select2 xq no se resetea con el formulario

  $("#usuario_password_first").removeAttr("required");
  $("#usuario_password_second").removeAttr("required");

  $(".ROLE_PROCESO").addClass("d-none");

  modal = $(this).attr("modal");

  $.ajax({
    url: Routing.generate($(this).attr("route")),
    method: "POST",
    data: { id: element_id },
    dataType: "json",
    success: function(datos) {
      if(modal){
        $("#" + modal).modal("show");
        $("#" + modal +" #id").val(element_id);
      }else{
        $(".modal").modal("show");
        $(".modal #id").val(element_id);
      }
      
      $(".modal-title").html(
        '<i class="fas fa-pencil-alt"></i> Editar Registro'
      );

      

      $.each(datos, function(a) {
        if ($(".form input[ident=" + a + "]")) {
          $(".form input[ident=" + a + "]").val(datos[a]);
        }
        if ($(".form select[ident=" + a + "]")) {
          $(".form select[ident=" + a + "]")
            .val(datos[a])
            .trigger("change");
        }
        if ($(".form textarea[ident=" + a + "]")) {
          $(".form textarea[ident=" + a + "]").val(datos[a]);
        }
      });

      if ($(".proceso").is(":checked")) {
        $(".ROLE_PROCESO").removeClass("d-none");
      }
    }
  });
});
