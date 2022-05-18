$("#form_admin_user").submit(function(e) {
    e.preventDefault();

    var pseudo = $("#user_name");
    var fonction = $("#fonction option:selected");
    var Access = $("#Access option:selected");
    var a = $("#mail_user");
    var Accpass_useress = $("#Accpass_useress");

    if (Accpass_useress.val() != "" && a.val() != "" && pseudo.val() != "" && fonction.text() != "" && Access.text() != "") {
        const data = {
            pseudo_user: $("#user_name").val(),
            fonction: $("#fonction option:selected").text(),
            Access: $("#Access option:selected").text(),
            Accpass_useress: $("#Accpass_useress").val(),
            mail_user: $("#mail_user").val()
        };
        $.ajax({
            type: "POST",
            url: "../../includes/user_admin.php",
            data: data,
        }).done(function(data) {
            if (data == "success") {
                $(pseudo).val("");
                $(fonction).val("");
                $(Access).val("");
                $(email_user).val("");
                $(Accpass_useress).val("");
                $("#modelId").modal('toggle');
                window.location.reload();

            } else {
                $("#error_s")
                    .addClass('text-danger text-center mt-1')
                    .css({
                        'font-weight': '500'
                    })
                    .text("Erreur [" + data + "]:\n l'utilisateur n'est pas inseré dans la base de donnees.");
            }
        }).fail(function(data) {
            $("#error_s")
                .addClass('text-warning text-center mt-1')
                .css({
                    'font-weight': '500'
                })
                .text("Erreur: Veuillez rverifier votre connexion svp ...");
        });
    } else {
        $("#error_s")
            .addClass('text-danger text-center mt-1')
            .css({
                'font-weight': '800'
            })
            .text("Certains champs sont vide");
    }
});