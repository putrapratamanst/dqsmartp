<?php
include 'program/koneksi.php';
$sql = "SELECT * FROM `account` WHERE ID = " . $_SESSION['ID'];
$result = $conn->query($sql);
$ID = $_SESSION['ID'];
$username = '';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $username = $row['USERNAME'];
    }
} else {
    echo "<br/>";
}

$_SESSION['lang'] = isset($_GET['lang'])?$_GET['lang']:"id";
$lang = isset($_SESSION['lang'])?$_SESSION['lang']:"id";
?>

<!-- <nav class="navbar navbar-expand-lg navbar-dark bg-dark p-0 m-0">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo.png" alt="" height="70">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">User</a>

                    <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
                        <?php
                            // $sql = "SELECT * FROM `account` WHERE EMAIL = (SELECT EMAIL FROM `account` WHERE ID = " . $_SESSION['ID'] . ") AND ID != " . $_SESSION['ID'];
                            // $result = $conn->query($sql);
                            // $username = '';
                            // if ($result->num_rows > 0) {
                            //     echo '<li><a class="dropdown-item" href="#">Account</a></li>';
                            //     echo '<li><hr class="dropdown-divider"></li>';
                            //     while($row = $result->fetch_assoc()) {
                            //         $url = 'changeuser.php?id=' . $row['ID'];
                            //         echo '<li><a class="dropdown-item" href="'.$url.'">'.$row['USERNAME'].'</a></li>';
                            //     }
                            //     echo '<li><hr class="dropdown-divider"></li>';
                            // }
                        ?>
                        <li><a class="dropdown-item" href="logout.php"><i class="fe-log-out"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav> -->

<style>
    .topnav-menu > li > a.active {
        color: #FFFFFF !important;
    }
</style>

<div class="navbar-custom">
    <div class="container-fluid">
        <ul class="list-unstyled topnav-menu float-end mb-0">
            <li>
                <a class="nav-link m-0<?php echo $lang=="en"?' active':""; ?>" href="result.php?lang=en">EN</a>
            </li>
            <li>
                <a class="nav-link m-0<?php echo $lang=="id"?' active':""; ?>" href="result.php?lang=id">ID</a>
            </li>
            <li class="dropdown notification-list topbar-dropdown">
                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="changeuser.php?id=<?php echo $ID; ?>" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="pro-user-name ms-1">
                        <?php echo $username; ?> <i class="mdi mdi-chevron-down"></i> 
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                    <?php
                        $sql = "SELECT * FROM `account` WHERE EMAIL = (SELECT EMAIL FROM `account` WHERE ID = " . $_SESSION['ID'] . ") AND ID != " . $_SESSION['ID'];
                        $result = $conn->query($sql);
                        $username = '';
                        if ($result->num_rows > 0) {
                            echo '<div class="dropdown-header noti-title">';
                            echo '<h6 class="text-overflow m-0">Account :</h6>';
                            echo '</div>';
                            echo '<div class="dropdown-divider"></div>';
                            while($row = $result->fetch_assoc()) {
                                echo '<a href="changeuser.php?id=' . $row['ID'] . '" class="dropdown-item notify-item"> <i class="fe-user"></i> <span>' . $row['USERNAME'] . '</span> </a>';
                            }
                            echo '<div class="dropdown-divider"></div>';
                        }
                    ?>
                    <a onclick="senddata()" class="dropdown-item notify-item">
                        <i class="mdi mdi-email-send me-1"></i><span>Send Result</span>
                    </a>
                    <a href="logout.php" class="dropdown-item notify-item">
                        <i class="fe-log-out"></i><span>Logout</span>
                    </a>
                </div>
            </li>
        </ul>

        <div class="logo-box">
            <a href="index.php" class="logo logo-light text-center">
                <span class="logo-sm">
                    <img src="img/logo.png" alt="" height="70">
                </span>
                <span class="logo-lg">
                    <img src="img/logo.png" alt="" height="70">
                </span>
            </a>
            <a href="index.php" class="logo logo-dark text-center">
                <span class="logo-sm">
                    <img src="img/logo.png" alt="" height="70">
                </span>
                <span class="logo-lg">
                    <img src="img/logo.png" alt="" height="70">
                </span>
            </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
            <li>
                <a class="navbar-toggle nav-link" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
            </li>
        </ul>
        <div class="clearfix"></div> 
    </div>
</div>