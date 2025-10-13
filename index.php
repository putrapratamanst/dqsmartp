<?php
    include 'program/koneksi.php';
    if(isset($_SESSION['ID'])) {
        if($_SESSION['ID']!= "") {
            $sql = "select * from account where ID = '".$_SESSION['ID']."'";
            $result = $conn->query($sql);
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row['RANK'] == 'student')
                {
                    if ($row['STATE'] == 'ujian')
                    {
                        header("location: start.php");
                        exit();
                    }
                    elseif ($row['STATE'] == 'upload')
                    {
                        header("location: upload.php");
                        exit();
                    }
                    else
                    {
                        header("location: result.php?");
                        exit();
                    }
                }
                else
                {
                    header("location: admin.php");
                    exit();
                }
            }
        }
    }
    
    $lang = isset($_SESSION['lang'])?$_SESSION['lang']:"";
    if($lang == "") {
        $lang = "id";
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>DQ-Smartplus</title>
        <meta name="description" content="Admin, Dashboard, Bootstrap, Bootstrap 4, Angular, AngularJS" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- for ios 7 style, multi-resolution icon of 152x152 -->
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-barstyle" content="black-translucent" />
        <link rel="apple-touch-icon" href="assets/images/logo.png" />
        <meta name="apple-mobile-web-app-title" content="Flatkit" />
        <!-- for Chrome on Android, multi-resolution icon of 196x196 -->
        <meta name="mobile-web-app-capable" content="yes" />
        <link rel="shortcut icon" sizes="196x196" href="assets/images/logo.png" />

        <!-- style -->
        <link rel="stylesheet" href="assets/animate.css/animate.min.css" type="text/css" />
        <link rel="stylesheet" href="assets/glyphicons/glyphicons.css" type="text/css" />
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" type="text/css" />
        <link rel="stylesheet" href="assets/material-design-icons/material-design-icons.css" type="text/css" />

        <link rel="stylesheet" href="assets/bootstrap/dist/css/bootstrap.min.css" type="text/css" />
        <!-- build:css assets/styles/app.min.css -->
        <link rel="stylesheet" href="assets/styles/app.css" type="text/css" />
        <!-- endbuild -->
        <link rel="stylesheet" href="assets/styles/font.css" type="text/css" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="app" id="app">
            <!-- ############ LAYOUT START-->
            <div class="center-block w-xxl w-auto-xs p-y-md">
                <div class="navbar">
                    <div class="pull-center">
                        <div ui-include="'views/blocks/navbar.brand.html'"></div>
                    </div>
                </div>
                <div class="p-a-md box-color r box-shadow-z1 text-color m-a">
                    <div id="errmsg"></div>
                    <div class="m-b text-sm">
                        <img src="img/logos.jpeg" width="240" height="222" />
                        <br />
                        <br />
                        <?php echo $lang=="id"?"Masuk dengan Akun Anda":"Sign in with your Account"; ?>
                    </div>
                    <form name="form" method="POST" action="prog_login.php">
                        <div class="md-form-group float-label">
                            <input type="text" class="md-input" ng-model="user.email" name="email" id="email" required />
                            <label><?php echo $lang=="id"?"Nama Pengguna":"Username"; ?></label>
                        </div>
                        <div class="md-form-group float-label">
                            <input type="password" class="md-input" ng-model="user.password" name="password" id="password" required />
                            <label><?php echo $lang=="id"?"Kata sandi":"Password"; ?></label>
                        </div>
                        <button type="submit" class="btn primary btn-block p-x-md"><?php echo $lang=="id"?"Masuk":"Sign In"; ?></button>
                    </form>
                </div>

                <div class="p-v-lg text-center">
                    <div class="m-b">
                        <a ui-sref="access.forgot-password" href="forgot-password.html" class="text-primary _600">
                            <?php echo $lang=="id"?"Lupa Kata Sandi?":"Forgot password?"; ?>
                        </a>
                    </div>
                    <div>
                        <?php echo $lang=="id"?"Belum memiliki akun?":"Do not have an account?"; ?>
                        <a ui-sref="access.signup" href="signup.php" class="text-primary _600">
                            <?php echo $lang=="id"?"Daftar":"Sign up"; ?>
                        </a>
                    </div>
                    <div>
                        <a href="#" onclick="changeLang('en')">EN</a> / <a href="#" onclick="changeLang('id')">ID</a>
                    </div>
                </div>
            </div>

            <!-- ############ LAYOUT END-->
        </div>
        <script>
            $(document).ready(function () {
                reset();
                checkerror();
                checksukses();
            });

            function reset() {
                $('#email').val('');
                $('#password').val('');
            }

            function checkerror() {
                var error = '<?php if(isset($_GET['error'])){echo $_GET['error'];}else{echo 'N';}?>';
                if (error != 'N')
                {
                    $('#errmsg').html('<div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">' + error + '</div>');
                }
            }

            function checksukses() {
                var sukses = '<?php if(isset($_GET['sukses'])){echo $_GET['sukses'];}else{echo 'N';}?>';
                if (sukses != 'N')
                {
                    $('#errmsg').html('<div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show" role="alert">' + sukses + '</div>');
                }
            }
            
            function changeLang(lg) {
                window.location.href = 'changelang.php?lang=' + lg;
            }
        </script>
        <!-- build:js scripts/app.html.js -->
        <!-- jQuery -->
        <script src="libs/jquery/jquery/dist/jquery.js"></script>
        <!-- Bootstrap -->
        <script src="libs/jquery/tether/dist/js/tether.min.js"></script>
        <script src="libs/jquery/bootstrap/dist/js/bootstrap.js"></script>
        <!-- core -->
        <script src="libs/jquery/underscore/underscore-min.js"></script>
        <script src="libs/jquery/jQuery-Storage-API/jquery.storageapi.min.js"></script>
        <script src="libs/jquery/PACE/pace.min.js"></script>

        <script src="scripts/config.lazyload.js"></script>

        <script src="scripts/palette.js"></script>
        <script src="scripts/ui-load.js"></script>
        <script src="scripts/ui-jp.js"></script>
        <script src="scripts/ui-include.js"></script>
        <script src="scripts/ui-device.js"></script>
        <script src="scripts/ui-form.js"></script>
        <script src="scripts/ui-nav.js"></script>
        <script src="scripts/ui-screenfull.js"></script>
        <script src="scripts/ui-scroll-to.js"></script>
        <script src="scripts/ui-toggle-class.js"></script>

        <script src="scripts/app.js"></script>

        <!-- ajax -->
        <script src="libs/jquery/jquery-pjax/jquery.pjax.js"></script>
        <script src="scripts/ajax.js"></script>
        <!-- endbuild -->
    </body>
</html>