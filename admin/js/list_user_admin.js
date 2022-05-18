$(document).ready(function() {
    $.ajax({
        type: "GET",
        url: "../includes/list_user_admin.php",
    }).done(function(data) {
        $("#user_function").empty();
        $("#user_function").append(data);
    }).fail(function(data) {
        $("#error").addClass('text-danger text-center').text("Auncun utulisateur disponible pour le moment.");
    });
});