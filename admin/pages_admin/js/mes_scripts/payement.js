$(document).ready(function() {
    // effectuer_apayement.php
    $("#f_payement").submit(function(e) {
        e.preventDefault();

        var property = document.getElementById('fichier_excel').files[0];
        var image_name = property.name;
        var image_extension = image_name.split('.').pop().toLowerCase();

        var form_data = new FormData();
        form_data.append("fichier_payement", property);

        $.ajax({
            // url: '../../includes/payement.php',
            url: '../../includes/payement.php',
            method: 'POST',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                $("#t3_form").css({ display: 'block' });
            },
            success: function(data) {
                // console.log(data);
                var r = data;
                if (r == "les donnees existe deja dans la base de donnees") {
                    $("#t3_form span").empty();
                    $("#t3_form span").text(r);
                    $("#spinner").css({ display: 'none' });
                } else if (r == "donnees inserer avec succes.") {
                    // s
                } else if (r == "Traitement reussi avec succes") {
                    $("#spinner").css({ display: 'none' });
                    $("#span").html("Traitement reussi avec succes");
                    $("#fichier_excel").val();
                    // window.location.reload();
                } else {
                    $("#erreur_t3_erreur").append('<li class="list-group-item text-danger">' + data + ', </li>');
                }
            },
            error: function(e) {
                $("#erreur_t3_erreur").append('<br/><hr/><li class="list-group-item text-warning">' + e + '</li>');
            }
        });

        $(document).ajaxComplete(function() {
            $("#t3_form").css({ display: 'none' });
            $("#span").html("Traitement reussi avec succes");
            $("#spinner").css({ display: 'none' });
            $("#fichier_excel").val();
            // window.location.reload();
            $("#myModal").modal('toggle');
            $("#modal_error").modal('toggle');
            // window.location.reload();
        });
    });

    function afficher_payement() {
        $.ajax({
            type: "GET",
            url: "../../includes/payement_effectuer.php",
        }).done(function(data) {
            if (data != "") {
                $($("#t_inscription_etudiants")).empty();
                $("#t_inscription_etudiants").append(data);
            } else {
                $($("#t_inscription_etudiants")).empty();
                $("#no-data").html("<marquee>Pas de donnees pour l'instant.</marquee>");
            }
        }).fail(function(data) {});
    }

    // paye,ent form
    $("#Payement_etudiant_form").submit(function(e) {
        e.preventDefault();
        // payement_form_etudiants.php
        $.ajax({
            type: "POST",
            url: "../../includes/payement_form_etudiants.php",
            data: $('#Payement_etudiant_form').serializeArray(),
            success: function(response) {
                if (response == "Donnees insere") {
                    $("#error_t3").html("ERROR: " + response).css({ color: "green" });
                    $("#mat_etud_payement").val('');
                    $("#promotion_etud_p").val('');
                    $("#fac_etud_payemt").val('');
                    $("#type_frais_p_etud").val('');
                    $("#pay_etud_p").val('');
                    $("#pay_num_bordereau").val('');
                    $("#date_p").val('');
                    $("#annee_acad_pay_etud").val('');
                    window.location.reload();
                } else {
                    $("#error_t3").html("ERROR: " + response).css({ color: "red" });
                }
            },
            beforeSend: function() {
                $("#error_t3").html("status : veuillez patienter").css({ color: "green" });
            },
            error: function(response) {
                $("#error_t3").html("ERROR: " + response);
            }
        });
    });

    //
    $("#update_payement_etud").submit(function(e) {
        e.preventDefault();
        const data = {
            num_b_update: $("#num_b_update").val(),
            date_p_update: $("#date_p_update").val(),
            montant_update: $("#montant_update").val(),
            id_payement_etud: $("#id_payement_etud").val()
        };
        $.ajax({
            type: "POST",
            url: "../../includes/update_payement_etud.php",
            data: data,
            beforeSend: function() {
                $("#r").text('Un instant svp ...').css({ color: 'green' });
            },
            success: function(response) {
                if (response == "ok") {
                    $("#r").text('Ok ...').css({ color: 'green' });
                    window.location.reload();
                } else {
                    $("#r").text('Echec : ' + response).css({ color: 'red' });
                }
            },
            error: function(e) {
                $("#r").text('').css({ color: '' });
                $("#r").text('Erreur de connexion :' + e).css({ color: 'red' });
                $("#r").css({ color: '' });
            }
        });
    });
});