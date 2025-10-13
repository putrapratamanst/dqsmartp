<?php
    include 'program/koneksi.php';
    $sql = "SELECT * FROM `account` WHERE ID = " . $_SESSION['ID'];
    $result = $conn->query($sql);
    $ID = $_SESSION['ID'];
    $usernameHeader = '';
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $usernameHeader = $row['USERNAME'];
        }
    }

    $_SESSION['lang'] = isset($_GET['lang'])?$_GET['lang']:"id";
    $lang = isset($_SESSION['lang'])?$_SESSION['lang']:"id";
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dqnavbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo.png" alt="DQ Smartplus" height="70">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $lang=="en"?' active':""; ?>" href="result.php?lang=en">EN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $lang=="id"?' active':""; ?>" href="result.php?lang=id">ID</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $usernameHeader; ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <?php
                            $sql = "SELECT * FROM `account` WHERE EMAIL = (SELECT EMAIL FROM `account` WHERE ID = " . $_SESSION['ID'] . ") AND ID != " . $_SESSION['ID'];
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo '<li><a class="dropdown-item" href="changeuser.php?id=' . $row['ID'] . '"><i class="fe-user"></i>'.$row['USERNAME'].'</a></li>';
                                }

                                echo '<li><hr class="dropdown-divider"></li>';
                            }
                        ?>
                        
                        <li>
                            <a class="dropdown-item" onclick="senddata()">
                                <i class="fa fa-envelope"></i> Send Result
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="logout.php">
                            <i class="fa fa-sign-out"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
