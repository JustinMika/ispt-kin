$(document).ready(function() {
    // chargement des donnes de base
    $.ajax({
        type: "POST",
        url: "../../includes/caisse.php",
        data: { all: "all" },
        success: function(response) {
            if (response != "") {
                $("#list_all").append(response);
            } else {
                $("#list_all").parent().append("<caption>pas des donnees pour l'instant ...</caption>");
            }
        },
        error: function(data) {
            alert("Une erreur est survenue : " + data);
        }
    });

    // recherche par date de coupure
    $("#form_search_date_pour").submit(function(e) {
        e.preventDefault();

        fac = $("#fac_ss option:selected").text();
        promotion = $("#promotion_ss option:selected").text();
        poste = $("#poste_s option:selected").text();
        annee_acad = $("#annee_acad_s option:selected").text();
        montant_search = $("#montant_search").val();
        date_1_ = $("#date_1_ss").val();
        date_2_ = $("#date_2").val();

        if (fac != "" && promotion != "" && montant_search != "" && poste != "" && annee_acad != "" && date_1_ != "" && date_2_ != "") {
            $.ajax({
                type: "POST",
                url: "../../includes/recherche_.php",
                data: { search_all: "data", fac_: fac, promotion_: promotion, poste_: poste, annee_acad_: annee_acad, montant_search: montant_search, date_2_: date_2_, date_1_: date_1_ },
                success: function(response) {
                    $("#t3").empty();
                    $("#t3").append(response);
                },
                error: function() {
                    $("#mod_search_all").modal('toggle');
                    alert("Une erreur est survenue");
                }
            });

            $(document).ajaxComplete(function() { $("#mod_search_all").modal('toggle'); });
        } else {
            alert("Certain champs sont vide");
        }
    });

    // formulaire de recherche de payement d un etudiants => ok finish
    $("#form_search_etud").submit(function(e) {
        e.preventDefault();

        mat = $("#mat").val();
        fac = $("#fac option:selected").text();
        promotion = $("#promotion option:selected").text();
        poste = $("#poste_frais option:selected").text();
        annee_acad = $("#annee_acad option:selected").text();

        if ($('#ck_ck_all:checked') && $('#ck_ck_all:checked').val() == "All") {
            if (mat != "" && fac != "" && promotion != "" && annee_acad != "") {
                const data = {
                        type_f: "All",
                        mat: mat,
                        fac: fac,
                        promotion: promotion,
                        annee_acad: annee_acad
                    }
                    // window.location.href = "rapport_pdf/stat_etudiants.php?type_f=All&mat=" + mat + "&fac=" + fac + "&promotion=" + promotion + "&annee_acad=" + annee_acad;
                window.open("rapport_pdf/stat_etudiants.php?type_f=All&mat=" + mat + "&fac=" + fac + "&promotion=" + promotion + "&annee_acad=" + annee_acad, "_blank")
            } else {
                alert("Certains champs sont vide 1.");
            }
        } else {
            if (mat != "" && fac != "" && promotion != "" && annee_acad != "" && poste != "") {
                const data = {
                        type_f: "nan",
                        mat: mat,
                        fac: fac,
                        promotion: promotion,
                        poste: poste,
                        annee_acad: annee_acad
                    }
                    // window.location.href = "rapport_pdf/stat_etudiants.php?mat=" + mat + "&fac=" + fac + "&promotion=" + promotion + "&annee_acad=" + annee_acad + "&type_frais=" + poste;
                window.open("rapport_pdf/stat_etudiants.php?mat=" + mat + "&fac=" + fac + "&promotion=" + promotion + "&annee_acad=" + annee_acad + "&type_frais=" + poste, '_blank');
            } else {
                alert("Certains champs sont vide. 2");
            }
        }
    });

    $("#form_search_fac_prom_s").submit(function(e) {
        e.preventDefault();
        fac = $("#fac_search_xy option:selected").text();
        promotion = $("#promotion_search_xy option:selected").text();
        poste = $("#poste_search_xy option:selected").text();
        annee_acad = $("#annee_acad_search_xy option:selected").text();

        $.ajax({
            type: "POST",
            url: "../../includes/caisse.php",
            data: { data: "data", fac: fac, promotion: promotion, poste_: poste, annee_acad: annee_acad },
            success: function(response) {
                if (response != "") {
                    $("#t3").empty();
                    $("#t3").append(response);
                    $("#mod_search_fac_prom").modal('toggle');
                }
            },
            error: function(e) {
                alert("" + e);
            }
        });

        $(document).ajaxComplete(function() {
            // $("#mod_search_fac_prom").modal('toggle');
        });
    });

    // chercher tous selon le type de frais, date de coupure, montant min, montant max
    $("#payement_tf_d_m_m").submit(function(e) {
        e.preventDefault();
        var annee_acad = $("#annee_acad_search_s option:selected").text();
        var promotion_search_s = $("#promotion_search_s option:selected").text();
        var facul_search_s = $("#facul_search_s option:selected").text();
        // checkbox pour le type  de frais
        var all_type_frais = $("#all_type_frais").val();
        // list de frais selectionner
        var poste_search_lst = $("#poste_search_lst option:selected").text();
        // checkbox pour la date de cououre
        var ch_ch_date = $("#ch_ch_date").val();
        // date debut et date fin
        var d1 = $("#date_debut").val();
        var d2 = $("#date_fin").val();

        // check box pour le montant min
        var ch_m_min = $("#ch_m_min").val();
        // montant min
        var montant_minimum  = $("#montant_minimum").val();

        // check box pour le montant max
        var ch_ch_m_max = $("#ch_ch_m_max").val();
        // montant max
        var montant_maximum = $("#montant_maximum").val();

        if (annee_acad != "" && promotion_search_s != "" && facul_search_s != "") {
            // alert("ok");
            var url = "rapport_pdf/rapport_t_payement.php?a=" + annee_acad + "&pr=" + promotion_search_s + "&fac=" + facul_search_s + "&al_fc=" + all_type_frais + "&typ_f=" + poste_search_lst + "&ch_date=" + ch_ch_date + "&d1=" + d1 + "&d2=" + d2 + "&ch_min=" + ch_m_min + "&m_min=" + montant_minimum + "&ch_max=" + ch_ch_m_max + "&m_max=" + montant_maximum;
            window.open(url, "_blank");
        } else {
            alert("une erreur est survenue");
            $("#helpId_error_").text("Veuillez complet au moins les champs requis").css({ color: "red" });
        }
    });
});