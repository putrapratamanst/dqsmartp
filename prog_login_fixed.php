<?php
include 'program/koneksi.php';

// Pastikan session sudah dimulai di awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sanitasi input
$username = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : "";
$password = isset($_POST['password']) ? $conn->real_escape_string(trim($_POST['password'])) : "";
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : "id";

try {
    // Prepared statement untuk mencegah SQL injection
    $stmt = $conn->prepare("SELECT * FROM `account` WHERE USERNAME = ? ORDER BY ID DESC LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = ($lang == "id") 
            ? "Akun dengan nama pengguna <b>" . htmlspecialchars($username) . "</b> tidak terdaftar"
            : "Account with username <b>" . htmlspecialchars($username) . "</b> not registered";
        header("location: index.php?error=" . urlencode($error));
        exit();
    }

    // Verifikasi password
    $stmt = $conn->prepare("SELECT * FROM `account` WHERE USERNAME = ? AND password = ? ORDER BY ID DESC LIMIT 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = ($lang == "id") 
            ? "Nama pengguna atau kata sandi salah"
            : "Invalid username or password";
        header("location: index.php?error=" . urlencode($error));
        exit();
    }

    $user = $result->fetch_assoc();
    $_SESSION['ID'] = $user['ID'];

    // Check verification status
    $stmt = $conn->prepare("SELECT * FROM VERIVICATION WHERE USERNAME = ? AND STAT = '0'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $verif_result = $stmt->get_result();

    if ($verif_result->num_rows > 0) {
        $error = ($lang == "id")
            ? "Akun dengan nama pengguna " . htmlspecialchars($username) . " telah berhasil dibuat. Silakan periksa email Anda untuk memverifikasi."
            : "The account with username " . htmlspecialchars($username) . " has been created successfully. Please check your email to verify.";
        header("location: index.php?error=" . urlencode($error));
        exit();
    }

    // Redirect based on user role and state
    if ($user['RANK'] == 'student') {
        switch($user['STATE']) {
            case 'ujian':
                header("location: start.php");
                break;
            case 'upload':
                header("location: upload.php");
                break;
            default:
                header("location: result.php");
                break;
        }
    } else {
        header("location: admin.php");
    }
    exit();

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    $error = ($lang == "id")
        ? "Terjadi kesalahan sistem. Silakan coba beberapa saat lagi."
        : "System error occurred. Please try again later.";
    header("location: index.php?error=" . urlencode($error));
    exit();
}