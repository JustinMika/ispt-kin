$(document).ready(function() {
    show_data();
    // effectuer_apayement.php
    $("#f_poste").submit(function(e) {
        e.preventDefault();

        var property = document.getElementById('fichier_excel').files[0];
        var image_name = property.name;
        var image_extension = image_name.split('.').pop().toLowerCase();

        var form_data = new FormData();
        form_data.append("file", property);

        $.ajax({
            url: '../../includes/ajout_poste_depense_facultaire.php',
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
                    alert(data);
                }
            }
        });

        $(document).ajaxComplete(function() {
            // $("#t3_form").css({display:'none'});
            $("#span").html("Traitement reussi avec succes");
            $("#spinner").css({ display: 'none' });
            $("#fichier_excel").val();
            window.location.reload();
        });
    });

    // ajout dun poste de depense
    $("#ajout_poste_depense").submit((event) => {
        event.preventDefault();
        var p_depense = $("#p_depense");
        var annee_acad = $("#annee_acad option:selected");
        var faculte = $("#faculte");
        var promotion = $("#promotion option:selected");
        var a_montant = $("#a_montant");


        if ($(p_depense).val() != "" && $(annee_acad).val() != "" && $(a_montant).val() != "" && $(faculte).val() != "" && $(promotion).val() != "") {
            // AJAX
            $.ajax({
                url: '../../includes/ajout_poste_depense_facultaire.php',
                method: 'POST',
                data: {
                    p_depense: $(p_depense).val(),
                    annee_acad: $(annee_acad).val(),
                    faculte: $(faculte).val(),
                    promotion: $(promotion).val(),
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
                eroor: function() {
                    // une erreur lie a la connexion soit / une erreur inconue.
                    $("#r").text("une erreur est survenue veuilles reessayer svp !.").addClass('text-center text-danger');
                }
            });
            $(document).ajaxStart(() => {});
            $(document).ajaxComplete(() => {});
        } else {
            // on arrete le traitement
            $("#r").text("Veuillez remplir tous les champs requis svp!").addClass('text-center text-danger');
        }
    });

    $("#form_t").submit(function(e) {
        e.preventDefault();
        var date_r = $("#date_r");
        var motif = $("#motif");
        var update_montant_ = $("#update_montant_");
        var dep_post_ = $("#dep_post_");
        var tot_m = $("#tot_m");

        alert(tot_m.val());

        if ($(date_r).val() != "" && $(motif).val() != "" && $(update_montant_).val() != "" && $(dep_post_).val() != "" && $(tot_m).val() != "") {
            // traitement ajax
            $.ajax({
                url: '../../includes/update_depense.php',
                method: 'POST',
                data: {
                    date_r: $(date_r).val(),
                    motif: $(motif).val(),
                    update_montant_: $(update_montant_).val(),
                    dep_post_: $(dep_post_).val(),
                    tot_m: $(tot_m).val()
                },
                beforeSend: function() {
                    $("#rr").text("Veuillez patienter").addClass('text-primary text-center');
                },
                success: function(data) {
                    if (data == "ok") {
                        $("#rr").text("[" + data + "]").addClass('text-success text-center h4');
                        window.location.reload();
                    } else {
                        $("#rr").text("Une erreur <i>[" + data + "]</i> est survenue. reessayer plus tard.").addClass('text-danger text-center');
                    }
                },
                error: function() {
                    $("#rr").html("Une erreur est survenue.").addClass('text-danger text-center h4');
                }
            });
            $(document).ajaxComplete(function() {
                window.location.reload();
            });
        } else {
            $("#rr").text("Veuillez remplir tous les champs").addClass("text-danger text-center");
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
            url: "../../includes/poste_depense_search_trans_fac.php",
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
            url: "../../includes/poste_depense_search_trans_fac.php",
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
                url: "../../includes/poste_depense_search_trans_fac.php",
                data: { search_poste: "search", annee_acad: annee_acad, date_2: date_2, date_1: date_1, poste: poste },
                success: function(response) {
                    if (response != "") {
                        $("#f_poste_depense").hide();
                        $("#mod_search_par_date").modal('toggle');
                        $("#tbody_transaction").empty();
                        $("#tbody_transaction").append(response);
                        // alert(response);
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

    // transaction sur le poste de depense facultaire
    $("#form_transaction_pf").submit(function(e) {
        e.preventDefault();
        var fac_pf = $("#fac_pf");
        var promotion_pf = $("#promotion_pf");
        var depense_pf = $("#depense_pf");
        var date_r = $("#date_r");
        var motif = $("#motif");
        var update_montant_ = $("#update_montant_");
        var dep_post_ = $("#dep_post_");
        $(promotion_pf).val('-');

        if ($(fac_pf).val() != "" && $(promotion_pf).val() != "" && $(depense_pf).val() != "" && $(date_r).val() != "" &&
            $(motif).val() != "" && $(update_montant_).val() != "" && $(dep_post_).val() != "") {
            var tot = eval(parseFloat($("#tot_m").val()) + parseFloat($("#depense_pf").val()));
            const data = {
                update_pf: "data",
                fac_pf: fac_pf,
                promotion_pf: promotion_pf,
                depense_pf: depense_pf,
                date_r: date_r,
                motif: motif,
                dep_post_: dep_post_,
                tot_montant: tot
            };

            $.ajax({
                type: "POST",
                url: "../../includes/update_depense_pf.php",
                data: $('#form_transaction_pf').serializeArray(),
                success: function(response) {
                    if (response == "ok") {
                        $("#rr").html(response).css({ color: "green" });
                        window.location.reload();
                        show_data();
                        $("#date_r").val();
                        $("#motif").val();
                        $("#update_montant_").val();
                        $("#transactions").modal('toggle');
                    } else {
                        $("#rr").html(response).css({ color: "red" });
                    }
                },
                error: function(response) {
                    $("#rr").html("ERROR : " + response.data).css({ color: "red" });
                }
            });
        } else {
            $("#rr").html("| veuillez completer tous les champs svp...").css({ color: "red" });
        }
    });
});