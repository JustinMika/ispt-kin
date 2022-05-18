$(document).ready(function() {
    // on affiche tous les elemensts
    show_data();
    $("#f_poste").submit(function(e) {
        e.preventDefault();

        var property = document.getElementById('fichier_excel').files[0];
        var image_name = property.name;
        var image_extension = image_name.split('.').pop().toLowerCase();

        var form_data = new FormData();
        form_data.append("file", property);

        $.ajax({
            url: '../../includes/ajout_poste_depense.php',
            method: 'POST',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                console.log("avant l'envoi des donnees");
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
                    $("#fichier_excel").val();
                    window.location.reload();
                } else {
                    $("#spinner").css({ display: 'none' });
                    $("#span").html(r);
                    $("#fichier_excel").val();
                }
            },
            error: function(e) {
                alert("Erreur de connexion, veuillez reesayer svp,...");
            }
        });
    });

    // ajout dun poste de depense
    $("#ajout_poste_depense").submit((event) => {
        event.preventDefault();
        var p_depense = $("#p_depense");
        var annee_acad = $("#annee_acad>option");
        var a_montant = $("#a_montant");

        if ($(p_depense).val() != "" && $(annee_acad).val() != "" && $(a_montant).val() != "") {
            // AJAX
            $.ajax({
                url: '../../includes/ajout_poste_depense_ajout.php',
                method: 'POST',
                data: {
                    p_depense: $(p_depense).val(),
                    annee_acad: $(annee_acad).val(),
                    a_montant: $(a_montant).val()
                },
                beforeSend: function() {

                },
                success: function(data) {
                    if (data == "Donnees inserer avec succes") {
                        $("#r").text(data).addClass('text-center text-secondary text-success');
                        $(p_depense).val("");
                        $(a_montant).val("");
                        window.location.reload();
                    } else {
                        // donc il y peut y avoir une erreur et on l'affiche
                        $("#r").text(data).addClass('text-center text-danger');
                    }
                },
                error: function() {
                    // une erreur lie a la connexion soit / une erreur inconue.
                    $("#r").text("une erreur de connexion est survenue veuilles reessayer svp !.").addClass('text-center text-danger');
                }
            });
        } else {
            // on arrete le traitement
            $("#r").text("Veuillez remplir tous les champs requis svp!").addClass('text-center text-danger');
        }
    });

    // transactions -> poste de depense
    $("#form_transaction").submit(function(e) {
        e.preventDefault();
        // alert($("#num_op_num").val());
        var date_r = $("#date_r");
        var motif = $("#motif");
        var update_montant_ = $("#update_montant_");
        var dep_post_ = $("#dep_post_");
        var num_op = $("#num_op_num");
        var tot_m = parseInt(parseInt($(update_montant_).val(), 10) + parseInt($("#tot_m").val(), 10), 10);
        // alert($(date_r).val() + " -" + $(motif).val() + " -" + $(num_op).val() + " -" + $(update_montant_).val())

        if ($(date_r).val() != "" && $(motif).val() != "" && $(num_op).val() != "" && $(update_montant_).val() != "" &&
            $(dep_post_).val() != "" && tot_m != "") {
            // traitement ajax
            $.ajax({
                url: '../../includes/update_depense.php',
                method: 'POST',
                data: {
                    date_r: $(date_r).val(),
                    motif: $(motif).val(),
                    motif: $(motif).val(),
                    update_montant_: $(update_montant_).val(),
                    dep_post_: $(dep_post_).val(),
                    num_op: $("#num_op_num").val(),
                    tot_m: tot_m
                },
                beforeSend: function() {
                    $("#rr").text("Veuillez patienter").addClass('text-primary text-center');
                },
                success: function(data) {
                    if (data == "ok") {
                        $("#rr").text("[" + data + "]").addClass('text-success text-center h4');
                        window.location.reload();
                    } else {
                        $("#rr").html("Une erreur : [" + data + "] est survenue. reessayer plus tard. ou bien corriger l'erreur en question ...").addClass('text-danger text-center');
                    }
                },
                error: function() {
                    $("#rr").html("Une erreur de connexion est survenue.").addClass('text-danger text-center h4');
                }
            });
        } else {
            $("#rr").text("#Veuillez remplir tous les champs").addClass("text-danger text-center");
        }
    });

    $("#form_search").submit(function(e) {
        e.preventDefault();
        $("#f_poste_depense").show();
        var poste = $("#poste>option:selected").text();
        var annee_acad = $("#annee_acad>option:selected").text();
        var pourc = $("#pourc").val();
        var ch_sh = $("#ch_sh").val();
        if ($('#ch_sh:checked').val()) {
            if ($("#pourc").val() == "") {
                $("#pourc").css({
                    'border-color': 'red',
                    'color': 'red'
                });
                $("#ErreurModal").modal('toggle');
            } else {
                search_poste(poste, annee_acad, pourc, "true");
            }
        } else {
            search_poste(poste, annee_acad, pourc, "false");
        }
    });

    function search_poste(poste, annee_acad, pourcent, ch_sh) {
        $.ajax({
            type: "GET",
            url: "../../includes/poste_depense_search_trans.php",
            data: { p: "search", poste: poste, annee_acad: annee_acad, pourcent: pourcent, ch_sh: ch_sh },
            success: function(response) {
                if (response != "") {
                    $("#tbody_poste").empty();
                    $("#tbody_poste").html(response);
                    $("#mod_search").modal('toggle');
                } else {
                    $("#tbody_poste").empty();
                    $("#tbody_poste").html(response);
                    $("#mod_search").modal('toggle');
                }
            },
            error: function(data) {
                alert("une errreur est survenue : " + data)
                $("#mod_search").modal('toggle');
            }
        });
    }

    function show_data() {
        $.ajax({
            type: "GET",
            url: "../../includes/poste_depense_search_trans.php",
            data: { data: "data" },
            success: function(response) {
                if (response != "") {
                    $("#tbody_poste").empty();
                    $("#tbody_poste").html(response);
                } else {
                    $("#tbody_poste").empty();
                    $("#tbody_poste").html(response);
                }
            }
        });
    }

    // chercher u poste par date de coupure
    $("#form_search_date").submit(function(e) {
        e.preventDefault();
        var poste = $("#poste_search option:selected").text();
        var annee_acad = $("#annee_acad_search option:selected").text();
        var date_1 = $("#date_search_1").val();
        var date_2 = $("#date_search_2").val();

        if (poste != "" && annee_acad != "" && date_1 != "" && date_2 != "") {
            $.ajax({
                type: "POST",
                url: "../../includes/poste_depense_search_trans.php",
                data: { search_poste: "search", annee_acad: annee_acad, date_2: date_2, date_1: date_1, poste: poste },
                success: function(response) {
                    if (response != "") {
                        // $("#f_poste_depense").hide();
                        // $("#mod_search_par_date").modal('toggle');
                        // $("#tbody_transaction").empty();
                        // $("#tbody_transaction").append(response);
                        window.location.reload();
                        alert(response);
                    } else {
                        $("#mod_search_par_date").modal('toggle');
                        $("#span_error").html("l'erreur : " + response + " est survenue");
                        $("#ErreurModal").modal('toggle');
                        // alert("Une erreur est survenue");
                    }
                },
                error: function(e) {
                    alert("Une erreur est survenue : \nErreur : " + e);
                }
            });
        } else {
            alert("certains champssont vide, veuillez les remplir svp!");
        }
    });
});