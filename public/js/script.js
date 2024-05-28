$(document).ready(function() {

    $('body .wrapper').css('visibility', 'visible');
    $('body .wrapper').css('opacity', 1);

    $(".page-preloader").fadeOut("slow");


  $('.datetimepicker').datetimepicker({
      format: 'DD-MM-YYYY',
      //useCurrent: false,
      locale: 'es',
      maxDate: 'now'
  });

  select2 = $(".select2").select2({
    language: "es",
    theme: "bootstrap4",
    placeholder: ""
  });

  $(".group-checkable").prop("checked", "");
  $(".group-checkable").change(function() {
    var set = $("#dataTable").find('tbody > tr > td input[type="checkbox"]');
    var checked = $(this).prop("checked");

    $(set).each(function() {
      $(this).prop("checked", checked);
    });
  });
});

function select_menu(menu_id) {
  var element = "#" + menu_id;

  if (!$(element).hasClass("active")) {

    $(element).removeClass("active");
    $(element).removeClass("expand");
    $(element).addClass("active expand");
    $(element).find('.collapse').addClass('show');

  }
}
