$(document).ready(function() {
    // les inputs
    var email = $("#email");
    var user_function = $("#user_function option:selected").text();
    var password = $("#password");

    $("#form-admin").submit(function(e) {
        e.preventDefault();

        $(document).ajaxStart(function() {
            $("#btn-submit").html('<img src="../img_loader/Eclipse-1s-90px.gif" width="35" height="35"> Patienter ...').addClass('btn-primary btn-sm');
        });

        $(document).ajaxComplete(function() {
            $("#btn-submit").html('Login');
        });

        if ($(email).val() != "" && $(password).val() != "" && $(user_function).val() != "") {
            $.ajax({
                type: "POST",
                url: "auth.php",
                data: $('#form-admin').serializeArray(),
                beforeSend: function() {
                    $("#btn-submit").html('<img src="../img_loader/Ripple-1s-90px.gif" width="35" height="35"> Veuillez patienter ...').addClass('btn-primary btn-sm');

                    $("#error").addClass('text-primary text-center');
                    $("#error").html("Un instant ...");
                }
            }).done(function(data) {
                if (data != "<b>l'adresee email et/ou le mot de passe est incorect</b>") {
                    $("#btn-submit").html('<img src="../img_loader/Ripple-1s-90px.gif" width="35" height="35"> Veuillez patienter ...').addClass('btn-primary btn-sm');
                    const obj_data = JSON.parse(data);
                    // alert(data);

                    window.location.href = "pages_admin/index.php?Ff=" + obj_data.fonction + "&i=" + obj_data.id;
                } else {
                    $("#error").addClass('text-warning text-center');
                    $("#error").html(data);
                }
            }).fail(function(data) {
                $("#error").addClass('text-warning text-center');
                $("#error").html("Veuillez verifier votre connexion svp ...");
            });
        } else {
            $(email).css({
                'border-color': 'orange'
            });

            $(password).css({
                'border-color': 'orange'
            });

            $("#user_function ").css({
                'border-color': 'orange'
            });
        }
    });
});