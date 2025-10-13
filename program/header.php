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
                echo "<br>";
            }
echo '
<div class="navbar-custom">
    <div class="container-fluid">
         <ul class="list-unstyled topnav-menu float-end mb-0">
            
            
    
            <li class="dropdown notification-list topbar-dropdown">
                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="pro-user-name ms-1">
                        ' . $username . ' <i class="mdi mdi-chevron-down"></i> 
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                    <!-- item-->
                    ';

                    
    echo '
                    <div class="dropdown-divider"></div>
                    <a href="admin.php" class="dropdown-item notify-item">
                        <i class="mdi mdi-account-group me-1"></i>
                        <span>Student Account</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="voucher.php" class="dropdown-item notify-item">
                        <i class="mdi mdi-ticket-account me-1"></i>
                        <span>Voucher</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="question.php" class="dropdown-item notify-item">
                        <i class="mdi mdi-alphabet-latin me-1"></i>
                        <span>Question</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="answer.php" class="dropdown-item notify-item">
                        <i class="mdi mdi-book-lock-open-outline me-1"></i>
                        <span>Answer</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="report.php" class="dropdown-item notify-item">
                        <i class="mdi mdi-ticket-account me-1"></i>
                        <span>Report</span>
                    </a>
                    
                    <div class="dropdown-divider"></div>
    
                    <!-- item-->
                    <a href="logout.php" class="dropdown-item notify-item">
                        <i class="fe-log-out"></i>
                        <span>Logout</span>
                    </a>
    
                </div>
            </li>
    
        </ul>
    
        <!-- LOGO -->
        <div class="logo-box">
            <a href="index.html" class="logo logo-light text-center">
                <span class="logo-sm">
                    <img src="img/logo.png" alt="" height="70">
                </span>
                <span class="logo-lg">
                    <img src="img/logo.png" alt="" height="70">
                </span>
            </a>
            <a href="index.html" class="logo logo-dark text-center">
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
                <!-- Mobile menu toggle (Horizontal Layout)-->
                <a class="navbar-toggle nav-link" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
                <!-- End mobile menu toggle-->
            </li>
             
        </ul>

        <div class="clearfix"></div> 
        
    </div>
              
</div>
';
?>