<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="../images/ispt_kin.png">

        <title>Admin Login</title>

        <link href="css/bootstrap-theme.css" rel="stylesheet">
        <link href="css/elegant-icons-style.css" rel="stylesheet" />
        <link href="css/style.css" rel="stylesheet">
        <link href="pages_admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="css/style-responsive.css" rel="stylesheet" />

        <style>
            .btn-primary {
                color: #ffffff;
                background-color: #007aff;
                border-color: #007aff;
                font-weight: 500;
                font-size:medium;
            }
            .btn-primary:hover,
            .btn-primary:focus,
            .btn-primary:active,
            .btn-primary.active,
            .open .dropdown-toggle.btn-primary {
                color: #ffffff;
                background-color: #007aff;
                border-color: #007aff;
                font-weight: 800;
                font-size:medium;
            }
        </style>
    </head>
    <img src="" alt="" srcset="">
    <body class="login-img3-body" style="background-image: url('../images/Black-Dark-Spot-White-Texture-Background-Pattern-WallpapersByte-com-3840x2160.jpg');background-repeat: repeat;background-size: auto;">
        <div class="container">
            <form class="login-form" action="" style="margin-top:10%;border-radius: 5px;background-color:#343a40" method="post" id="form-admin">
                <div class="login-wrap">
                    <p class="login-img"><i class="fa fa-user-secret" aria-hidden="true"></i> </p>
                    <p class="" style="text-align: center;font-size: medium;">Login</p>
                    <div class="input-group">
                        <p class="input-group-addon">
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </p>
                        <input type="email" class="form-control" placeholder="Username" autofocus name="email" id="email">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-anchor" aria-hidden="true"></i>
                        </span>
                        <select class="form-control" name="user_function" id="user_function">
                            <option disabled> Fonction</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <p class="input-group-addon">
                            <i class="fa fa-user-secret" aria-hidden="true"></i>
                        </p>
                        <input type="password" class="form-control" placeholder="Password" name="password" id="password">
                    </div>
                    <p id="error"></p>
                    <button class="btn btn-primary btn-md btn-block" type="submit" type="submit" id="btn-submit">Login</button>
                </div> 
            </form>
        </div>
        <!-- js -->
        <script src="../js/jquery-3.6.0.min.js"></script>
        <script src="js/auth_admin.js"></script>
        <script src="js/list_user_admin.js"></script>
    </body>
</html>
