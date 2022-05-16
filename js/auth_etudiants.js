//on attend chargement de la page
$(document).ready(function() {
    // verification des champs via js

    var mat_etud = $("#mat_etud");
    var p_mat_etud = $("#mat_etud").parent();
    var img = p_mat_etud.find("img")[0];
    // var erreur_mat = ;

    var password_etud = $("#password_etud");
    var p_password_etud = $("#password_etud").parent();
    var img_p = p_password_etud.find("img")[0];

    // verification sur le matricule
    mat_etud.keyup(function() {
        var img = p_mat_etud.find("img")[0];
        $(img).parent().show(1000).css({
            display: "block"
        });
        if ($(this).val().length >= 4) {
            $(img).attr('src', 'img_loader/ok_ok.png');
            $(this).css({
                'border-color': 'green'
            });
        } else {
            $(img).attr('src', 'img_loader/Gear-0.2s-90px.gif');
            $(this).css({
                'border-color': 'red'
            });
        }
    });
    mat_etud.focus(() => {
        $(img).parent().show(1000).css({
            display: "block"
        });
    });
    mat_etud.blur(() => {
        $(img).parent().hide(1000);
    });

    // verification sur le mot de passe de l etudiants
    password_etud.keyup(function() {
        var img = p_password_etud.find("img")[0];
        $(img).parent().show(1000).css({
            display: "block"
        });
        if ($(this).val().length >= 4) {
            $(img).attr('src', 'img_loader/ok_ok.png');
            $(this).css({
                'border-color': 'green'
            });
        } else {
            $(img).attr('src', 'img_loader/Gear-0.2s-90px.gif');
            $(this).css({
                'border-color': 'red'
            });
        }
    });
    password_etud.focus(() => {
        $(img_p).parent().show(1000).css({
            display: "block"
        });
    });
    password_etud.blur(() => {
        $(img_p).parent().hide(1000);
    });

    // une fonction de verification

    function verification(element) {
        if ($(element).val() != "" && $(element).val().length >= 4) {
            return true;
        } else {
            $(element).css({
                'border-color': 'red'
            });
            return false;
        }
    }

    // soumettre le formulaire
    $("#form-auth-etud").submit(function(event) {
        event.preventDefault();
        if (verification(mat_etud) && verification(password_etud)) {
            // ajax start event
            $(document).ajaxStart(function() {
                $("#sub-etud").html('<img src="img_loader/Eclipse-1s-90px.gif" width="40" class="img"> Veuillez patienter ...');
            });

            $(document).ajaxComplete(function() {
                $("#sub-etud").html("Se connecter");
            });

            $.ajax('auth.php', {
                type: "POST",
                data: $(this).serializeArray(),
                success: function(data) {
                    window.location.href = '/pages/index.php';
                    // alert(data);
                },
                error: function() {
                    $("#erreur_auth").html("Vos identifiants sont incorects. réesayer plus tard.")
                        .addClass("text-danger text-center").show(2000);
                    // alert("une erreur est survenue");
                }
            });
        } else {
            $("#erreur_auth").html("Veuillez completer tous les champs du formulaire svp")
                .addClass("text-danger text-center").show(2000);
        }
    });
});