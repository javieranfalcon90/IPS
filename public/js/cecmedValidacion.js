
$(document).ready(function() {


    $("#form").validate({

        errorElement: 'div',
        errorPlacement: function (error, element) {
            var elem = $(element).parent().find('span.error');

            if(elem.length == 0){
                elem = $(element).parent().parent().find('span.error');
            }

            error.insertAfter(elem);

            $('div .error').addClass('invalid-feedback');
        },

        highlight: function (element) { // hightlight error inputs
            $(element).addClass('is-invalid'); // set error class to the control group
        },

        unhighlight: function (element) { // revert the change done by hightlight
            $(element).removeClass('is-invalid'); // set error class to the control group
        },

    });

    //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
    $('#form .select2').change(function () {
        $('#form').validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
    });

});
