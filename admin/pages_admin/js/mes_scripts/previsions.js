$(document).ready(function() {
    // prevision de frais pour les etudiants
    $("#previson_frais").submit(function(e) {
        e.preventDefault();

        var property = document.getElementById('fichier_excel').files[0];
        var image_name = property.name;
        var image_extension = image_name.split('.').pop().toLowerCase();

        var form_data = new FormData();
        form_data.append("files", property);

        $.ajax({
            url: '../../includes/add_prev_frais.php',
            method: 'POST',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                $("#t3_form_").css({ display: 'block' });
            },
            success: function(data) {
                console.log(data);
                var r = data;
                if (r == "les donnees existe deja dans la base de donnees") {
                    $("#t3_form_ span").empty();
                    $("#t3_form_ span").text(r);
                    $("#spinner").css({ display: 'none' });
                } else if (r == "Traitement reussi avec succes") {
                    $("#spinner").css({ display: 'none' });
                    $("#span").html(data);
                    $("#fichier_excel").val();
                    window.location.reload();
                } else {
                    $("#spinner").css({ display: 'none' });
                    $("#span").html(data);
                    $("#t3_form_ span").text(r);
                    $("#fichier_excel").val();
                }
            }
        });

        $(document).ajaxComplete(function() {
            // $("#t3_form_").css({ display: 'none' });
            // $("#span").html("Traitement reussi avec succes");
            $("#spinner").css({ display: 'none' });
            $("#fichier_excel").val();
            // window.location.reload();
        });
    });

    // poste de recette administrative
    $("#form_poste_recette").submit(function(e) {
        e.preventDefault();
        var property = document.getElementById('fichier_excel_').files[0];
        var image_name = property.name;
        var image_extension = image_name.split('.').pop().toLowerCase();

        var form_data = new FormData();
        form_data.append("files_post_d", property);

        $.ajax({
            url: '../../includes/add_prev_frais.php',
            method: 'POST',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                $("#t3_form").css({ display: 'block' });
            },
            success: function(data) {
                console.log(data);
                var r = data;
                if (r == "les donnees existe deja dans la base de donnees") {
                    $("#t3_form span").empty();
                    $("#t3_form span").text(r);
                    $("#spinner").css({ display: 'none' });
                } else if (r == "Traitement reussi avec succes") {
                    $("#spinner").css({ display: 'none' });
                    $("#span").html("Traitement reussi avec succes");
                    $("#fichier_excel_").val("");
                    window.location.reload();
                } else {
                    $("#t3_form span").empty();
                    $("#t3_form span").text(r).addClass('text-danger');
                    $("#spinner").css({ display: 'none' });
                    $("#fichier_excel_").val("");
                }
            }
        });

        $(document).ajaxComplete(function() {
            // $("#t3_form").css({ display: 'none' });
            // $("#span").html("Traitement reussi avec succes");
            // $("#spinner").css({ display: 'none' });
            $("#fichier_excel_").val("");
            // window.location.reload();
        });
    });

    // poste universitaire
    $("#add_univ_poste_").submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "../../includes/add_poste_recette_univ.php",
            data: $('#add_univ_poste_').serializeArray(),
            success: function(response) {
                if (response == "insertion reussi avec succes") {
                    $("#errorId").text(response).css({ color: 'text-primary' });
                    $("#add_poste").modal('toggle');
                    window.location.reload();
                    $("#poste_recette_univ_").val('');
                    $("#montant_poste_univ").val('');
                } else {
                    $("#errorId").text(response).addClass('text-danger');
                    $("#poste_recette_univ_").val('');
                    $("#montant_poste_univ").val('');
                }
            }
        });
    });

    // ajouter une prevision pour les etudiants
    $("#Ajout_frais_etud").submit(function(e) {
        e.preventDefault();
        if ($("#_type_Frais_pay_").val() != "") {
            $.ajax({
                type: "POST",
                url: "../../includes/add_prev_f.php",
                data: $('#Ajout_frais_etud').serializeArray(),
                beforeSend: function() {
                    $("#Erreor_s").text("Patienter un moment").css({ color: 'blue' });
                },
                success: function(response) {
                    if (response == "ok") {
                        $("#Erreor_s").text("le type de frais '" + $("#type_Frais_pay_").val() + "' est ajouté avec succès").css({ color: 'green' });
                        window.location.reload();
                    } else {
                        $("#Erreor_s").text("Erreur : " + response).addClass('text-danger');
                    }
                },
                error: function(response) {
                    $("#Erreor_s").text("Une erreur est survenue ... : " + response).addClass('text-danger');
                }
            });
        } else {
            $("#Erreor_s").html("Veuillez renseigner le type de frais a payé par les étudiant(e)s").addClass('text-danger');
        }
    });

    // update poste de rectte universitaire
    $("#id_update_poste").submit(function(e) {
        e.preventDefault();
        const data = {
            id_id_id: $("#id_id_id").val(),
            montant_ru: $("#montant_ru").val()
        };
        $.ajax({
            type: "post",
            url: "../../includes/update_pru.php",
            data: data,
            beforeSend: function() {
                $("#Erreor_sss").css({ color: 'green' }).text("Un instant ...");
            },
            success: function(response) {
                if (response != "" && response == "ok") {
                    $("#Erreor_sss").css({ color: 'green' }).text("mise à jour réussie...");
                    window.location.reload();
                } else {
                    $("#Erreor_sss").css({ color: 'orange' }).text("Erreur : mise à jour échouée, ... : ");
                }
            },
            error: function(response) {
                $("#Erreor_sss").css({ color: 'orange' }).text("Une erreur s'est produite, try leter ...");
            }
        });
    });

    // update_prevision et affectation
    $("#update_prevision_ffrais").submit(function(e) {
        e.preventDefault();
        const data = {
            type_frais_: $("#type_frais").val(),
            montant_: $("#montant_").val(),
            promotion: $("#Promotion").val(),
            faculte: $("#fac_update").val(),
            annee_acad: $("#annee_acad_").val()
        };
        $.ajax({
            type: "POST",
            url: "../../includes/update_affectation_frais.php",
            data: data,
            beforeSend: function() {
                $("#e_update").html('Patienter un instant ...').css({ color: 'green' }).addClass('text-success');
            },
            success: function(response) {
                if (response == "ok") {
                    $("#e_update").css({ color: '' });
                    $("#e_update").html('mise à jour reussi ...').css({ color: 'green' });
                    window.location.reload();
                } else {
                    $("#e_update").html('ECHEC : Un souci de la connexion [' + response + ']').css({ color: 'red' }).addClass('text-success');
                }
            },
            error: function(e) {
                $("#e_update").html('Un souci de la connexion... =>' + e).css({ color: 'red' }).addClass('text-success');
            }
        });
    });
});