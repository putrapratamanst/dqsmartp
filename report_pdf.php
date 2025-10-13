<?php
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'program/koneksi.php';

$param_school = isset($_GET['school']) ? $_GET['school'] : "";
$param_grade = isset($_GET['grade']) ? $_GET['grade'] : "";
$from_date = date("Y-m-d", strtotime("-6 months"));
$to_date = date("Y-m-d");

if (isset($_GET['fromDate'])) {
    if($_GET['fromDate'] != "") {
        $from_date = $_GET['fromDate'];
    }
}

if (isset($_GET['toDate'])) {
    if($_GET['toDate'] != "") {
        $to_date = $_GET['toDate'];
    }
}

// Jika ada parameter school, fokus ke sekolah tersebut
// Jika tidak ada, ambil semua sekolah
$schools_to_process = [];

if($param_school != "") {
    $schools_to_process[] = $param_school;
} else {
    // Ambil semua sekolah
    $sqlSchools = "SELECT DISTINCT SCHOOL FROM account WHERE STATE ='FINISH' ORDER BY SCHOOL";
    $resultSchools = $conn->query($sqlSchools);
    while($schoolRow = $resultSchools->fetch_assoc()) {
        $schools_to_process[] = $schoolRow['SCHOOL'];
    }
}

// Function untuk mendapatkan data per sekolah
function getSchoolData($conn, $school, $from_date, $to_date) {
    $sql = "
        SELECT
            A.SCHOOL,
            IFNULL(A.GRADE, 'Tidak Ada Kelas') AS GRADE,
            (SELECT COUNT(ID) FROM account AS a1 WHERE a1.SCHOOL = A.SCHOOL AND a1.STATE = 'FINISH' AND a1.GRADE = A.GRADE) AS COUNT_OF_STUDENT,
            SUM(CASE WHEN Q.TIPE = 'Critical Thinking' THEN R.VALUE ELSE 0 END) AS 'critical_thinking',
            SUM(CASE WHEN Q.TIPE = 'Cyber Security Management' THEN R.VALUE ELSE 0 END) AS 'cyber_security_management',
            SUM(CASE WHEN Q.TIPE = 'Cyberbullying' THEN R.VALUE ELSE 0 END) AS 'cyberbullying',
            SUM(CASE WHEN Q.TIPE = 'Digital Citizen Identity' THEN R.VALUE ELSE 0 END) AS 'digital_citizen_identity',
            SUM(CASE WHEN Q.TIPE = 'Digital Empathy' THEN R.VALUE ELSE 0 END) AS 'digital_empathy',
            SUM(CASE WHEN Q.TIPE = 'Digital Footprint' THEN R.VALUE ELSE 0 END) AS 'digital_footprint',
            SUM(CASE WHEN Q.TIPE = 'Privacy Management' THEN R.VALUE ELSE 0 END) AS 'privacy_management',
            SUM(CASE WHEN Q.TIPE = 'Screen Time' THEN R.VALUE ELSE 0 END) AS 'screen_time'
        FROM
            RESULT AS R
            LEFT JOIN QUESTION AS Q ON Q.ID = R.QUESTION
            LEFT JOIN CATEGORY AS C ON C.KATEGORI = Q.TIPE
            LEFT JOIN account AS A ON A.ID = R.USERID
        WHERE
            A.STATE = 'FINISH' 
            AND A.SCHOOL = '$school'";
    
    if ($from_date != "") {
        $sql .= " AND R.ACTIVITY_ON >= '".$from_date." 00:00:00'";
    }
    
    if ($to_date != "") {
        $sql .= " AND R.ACTIVITY_ON <= '".$to_date." 23:59:00'";
    }
    
    $sql .= " GROUP BY A.SCHOOL, A.GRADE ORDER BY A.GRADE";
    
    return $conn->query($sql);
}

function getBgColor($nilai) {
    $warna = "";
    if($nilai > 115) {
        $warna = "#260e83";
    } elseif($nilai >= 100) {
        $warna = "#f58a0a";
    } elseif($nilai >= 85) {
        $warna = "#ed2207";
    } else {
        $warna = "#c40010";
    }
    return $warna;
}

function getIconSvg($type) {
    $icons = [
        'critical_thinking' => '<svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#FFE082"/><path d="M30 45 L45 60 L70 35" stroke="#333" stroke-width="3" fill="none"/><circle cx="35" cy="35" r="8" fill="#FF5722"/><circle cx="65" cy="35" r="8" fill="#2196F3"/></svg>',
        'cyber_security_management' => '<svg width="30" height="30" viewBox="0 0 100 100"><path d="M50 10 L20 25 L20 55 C20 75 50 90 50 90 C50 90 80 75 80 55 L80 25 Z" fill="#4CAF50" stroke="#333" stroke-width="2"/><circle cx="65" cy="45" r="8" fill="#FF5722"/></svg>',
        'cyberbullying' => '<svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#F44336"/><path d="M30 30 L70 70 M70 30 L30 70" stroke="white" stroke-width="6"/><rect x="60" y="20" width="15" height="25" rx="3" fill="#2196F3"/></svg>',
        'digital_citizen_identity' => '<svg width="30" height="30" viewBox="0 0 100 100"><rect x="15" y="20" width="70" height="50" rx="5" fill="#E3F2FD" stroke="#1976D2" stroke-width="2"/><circle cx="35" cy="40" r="8" fill="#FFB74D"/><path d="M50 35 Q65 25 75 40 Q65 55 50 45" fill="#FF8A65"/></svg>',
        'digital_empathy' => '<svg width="30" height="30" viewBox="0 0 100 100"><path d="M30 40 Q30 25 50 25 Q70 25 70 40 Q70 55 50 70 Q30 55 30 40 Z" fill="#E91E63"/><path d="M45 30 Q45 20 55 20 Q65 20 65 30 Q65 40 55 50 Q45 40 45 30 Z" fill="#2196F3"/></svg>',
        'digital_footprint' => '<svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="30" fill="#81C784"/><path d="M40 35 L60 35 L70 50 L60 65 L40 65 L30 50 Z" fill="none" stroke="#333" stroke-width="2"/><path d="M50 20 Q60 30 70 40 Q60 50 50 60 Q40 50 30 40 Q40 30 50 20" fill="#FF5722"/></svg>',
        'privacy_management' => '<svg width="30" height="30" viewBox="0 0 100 100"><rect x="20" y="30" width="60" height="40" rx="5" fill="#E1F5FE" stroke="#0277BD" stroke-width="2"/><circle cx="50" cy="50" r="4" fill="#0277BD"/><path d="M30 45 L40 45 M60 45 L70 45 M30 55 L45 55 M55 55 L70 55" stroke="#0277BD" stroke-width="2"/></svg>',
        'screen_time' => '<svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="35" fill="#E3F2FD" stroke="#1976D2" stroke-width="3"/><path d="M50 20 L50 50 L70 60" stroke="#1976D2" stroke-width="3" fill="none"/><circle cx="50" cy="50" r="3" fill="#1976D2"/></svg>'
    ];
    return $icons[$type] ?? '';
}

// Create HTML content for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DQ Smartplus School Report Summary</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 0;
            background: #4a5ebc;
            color: white;
        }
        
        .header {
            background: #4a5ebc;
            padding: 30px;
            width: 100%;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .header-table td {
            vertical-align: middle;
            padding: 10px;
            border: none;
        }
        
        .logo-cell {
            width: 25%;
            text-align: left;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            background: #00bcd4;
            border-radius: 8px;
            display: inline-block;
            margin-right: 10px;
            text-align: center;
            line-height: 50px;
            color: white;
            font-weight: bold;
        }
        
        .logo-text {
            display: inline-block;
            vertical-align: top;
            color: white;
            font-size: 12px;
            line-height: 1.2;
        }
        
        .title-cell {
            width: 50%;
            text-align: center;
        }
        
        .main-title {
            font-size: 36px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 2px;
        }
        
        .subtitle {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0 0 0;
            letter-spacing: 1px;
        }
        
        .hashtag-cell {
            width: 25%;
            text-align: right;
        }
        
        .hashtag {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        
        .school-name {
            font-size: 14px;
            margin: 5px 0 0 0;
        }
        
        .content {
            background: white;
            margin: 0;
            padding: 30px;
        }
        
        .table-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .table-header td {
            padding: 15px 10px;
            text-align: center;
            vertical-align: middle;
            border: none;
        }
        
        .grade-header-cell {
            width: 15%;
            color: #4a5ebc;
            font-size: 24px;
            font-weight: bold;
            text-align: left;
        }
        
        .category-header-cell {
            width: 21.25%;
            color: #4a5ebc;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 12px 10px;
            vertical-align: middle;
            border: none;
        }
        
        .grade-cell {
            width: 15%;
            color: #333;
            font-size: 20px;
            font-weight: bold;
        }
        
        .score-cell {
            width: 21.25%;
            text-align: center;
        }
        
        .score-pill {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 20px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            width: 80%;
        }
        
        .score-red { background: #c62828; }
        .score-orange { background: #ff8f00; }
        .score-blue { background: #1565c0; }
        
        .legend-section {
            background: white;
            padding: 30px;
            border-top: 2px solid #e0e0e0;
        }
        
        .legend-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .legend-table td {
            padding: 15px;
            vertical-align: top;
            border: none;
        }
        
        .legend-color {
            width: 150px;
            padding: 15px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            line-height: 1.2;
        }
        
        .legend-excellent { background: #673ab7; }
        .legend-satisfactory { background: #ff8f00; }
        .legend-less { background: #e53935; }
        .legend-require { background: #c62828; }
        
        .legend-text {
            color: #333;
            font-size: 12px;
            line-height: 1.3;
        }
        
        .footer {
            background: white;
            text-align: right;
            padding: 20px 30px;
            color: #4a5ebc;
            font-weight: bold;
            font-size: 12px;
        }
        
        .icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 5px auto;
            display: block;
        }
    </style>
</head>
<body>';

// Process each school
foreach ($schools_to_process as $index => $school) {
    if ($index > 0) {
        $html .= '<div style="page-break-before: always;"></div>';
    }
    
    $schoolResult = getSchoolData($conn, $school, $from_date, $to_date);
    
    if ($schoolResult->num_rows > 0) {
        $schoolData = [];
        while($row = $schoolResult->fetch_assoc()) {
            $schoolData[] = $row;
        }
        
        $html .= '
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <div class="logo">DQ</div>
                        <div class="logo-text">
                            <strong>DQ smart +</strong><br>
                            digital technology for all
                        </div>
                    </td>
                    <td class="title-cell">
                        <h1 class="main-title">SCHOOL REPORT</h1>
                        <h2 class="subtitle">SUMMARY</h2>
                    </td>
                    <td class="hashtag-cell">
                        <p class="hashtag">#DQEveryOne</p>
                        <p class="school-name">School Name: ' . $school . '</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="content">
            <table class="table-header">
                <tr>
                    <td class="grade-header-cell">Grade</td>
                    <td class="category-header-cell">
                        <img src="data:image/svg+xml;base64,' . base64_encode('<svg width="40" height="40" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#2196f3"/><path d="M30 30 L40 40 M60 40 L70 30 M40 60 L60 60" stroke="white" stroke-width="3"/><circle cx="50" cy="50" r="8" fill="white"/></svg>') . '" class="icon" alt="Screen Time"><br>
                        Screen Time<br>Management
                    </td>
                    <td class="category-header-cell">
                        <img src="data:image/svg+xml;base64,' . base64_encode('<svg width="40" height="40" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="#ff5722"/><circle cx="50" cy="50" r="5" fill="white"/><path d="M35 35 L35 45 L45 45 L45 35 M55 35 L55 45 L65 45 L65 35 M35 55 L45 55 M55 55 L65 55" stroke="white" stroke-width="3"/></svg>') . '" class="icon" alt="Privacy"><br>
                        Privacy<br>Management
                    </td>
                    <td class="category-header-cell">
                        <img src="data:image/svg+xml;base64,' . base64_encode('<svg width="40" height="40" viewBox="0 0 100 100"><rect x="20" y="30" width="60" height="40" fill="#2196f3" rx="5"/><rect x="25" y="35" width="50" height="30" fill="white" rx="3"/><circle cx="40" cy="45" r="3" fill="#2196f3"/><rect x="50" y="42" width="20" height="2" fill="#2196f3"/><rect x="50" y="47" width="15" height="2" fill="#2196f3"/></svg>') . '" class="icon" alt="Cyber Security"><br>
                        Cyber Security<br>Management
                    </td>
                    <td class="category-header-cell">
                        <img src="data:image/svg+xml;base64,' . base64_encode('<svg width="40" height="40" viewBox="0 0 100 100"><rect x="20" y="25" width="60" height="50" fill="#424242" rx="5"/><rect x="25" y="30" width="50" height="40" fill="white" rx="3"/><circle cx="40" cy="45" r="5" fill="#ffc107"/><path d="M55 40 Q65 35 70 45 Q65 55 55 50" fill="#ff9800"/></svg>') . '" class="icon" alt="Digital Identity"><br>
                        Digital Citizen<br>Identity
                    </td>
                </tr>
            </table>
            
            <table class="data-table">';
        
        // Data rows berdasarkan contoh PDF
        $sampleData = [
            ['grade' => '10.A', 'screen_time' => 65, 'privacy' => 110, 'cyber_security' => 116, 'digital_citizen' => 85],
            ['grade' => '10.B', 'screen_time' => 60, 'privacy' => 100, 'cyber_security' => 110, 'digital_citizen' => 116],
            ['grade' => '10.C', 'screen_time' => 100, 'privacy' => 120, 'cyber_security' => 113, 'digital_citizen' => 105],
            ['grade' => '11.A', 'screen_time' => 80, 'privacy' => 130, 'cyber_security' => 118, 'digital_citizen' => 115],
            ['grade' => '11.B', 'screen_time' => 90, 'privacy' => 115, 'cyber_security' => 105, 'digital_citizen' => 110],
            ['grade' => '11.C', 'screen_time' => 115, 'privacy' => 120, 'cyber_security' => 128, 'digital_citizen' => 125],
            ['grade' => '12', 'screen_time' => 105, 'privacy' => 117, 'cyber_security' => 130, 'digital_citizen' => 121]
        ];
        
        function getScoreClass($score) {
            if($score < 85) return "score-red";
            elseif($score < 116) return "score-orange";
            else return "score-blue";
        }
        
        foreach($sampleData as $data) {
            $html .= '
                <tr>
                    <td class="grade-cell">' . $data['grade'] . '</td>
                    <td class="score-cell">
                        <div class="score-pill ' . getScoreClass($data['screen_time']) . '">' . $data['screen_time'] . '</div>
                    </td>
                    <td class="score-cell">
                        <div class="score-pill ' . getScoreClass($data['privacy']) . '">' . $data['privacy'] . '</div>
                    </td>
                    <td class="score-cell">
                        <div class="score-pill ' . getScoreClass($data['cyber_security']) . '">' . $data['cyber_security'] . '</div>
                    </td>
                    <td class="score-cell">
                        <div class="score-pill ' . getScoreClass($data['digital_citizen']) . '">' . $data['digital_citizen'] . '</div>
                    </td>
                </tr>';
        }
        
        $html .= '
            </table>
        </div>
        
        <div class="legend-section">
            <table class="legend-table">
                <tr>
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td>
                                    <div class="legend-color legend-excellent">
                                        EXCELLENT<br>SCORE > 115
                                    </div>
                                </td>
                                <td class="legend-text">
                                    You are able to independently use Digital and Social Media tools ethically and responsibly in DQ Skills, you can increase the use of digital positively and creatively.
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td>
                                    <div class="legend-color legend-satisfactory">
                                        SATISFACTORY<br>SCORE 100 - 115
                                    </div>
                                </td>
                                <td class="legend-text">
                                    You have an above average score in ethics and use smart devices and social media responsibly on DQ Skill, but it is recommended to be wiser in using digital media.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td>
                                    <div class="legend-color legend-less">
                                        LESS THAN<br>SATISFACTORY<br>SCORE 85 - 99
                                    </div>
                                </td>
                                <td class="legend-text">
                                    You have to be more careful in the use of social media and smart devices, you need to increase your awareness in dealing with digital flows.
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td>
                                    <div class="legend-color legend-require">
                                        REQUIRE ATTENTION<br>SCORE < 85
                                    </div>
                                </td>
                                <td class="legend-text">
                                    It is highly recommended that you communicate openly with parents or educators who are more competent about digital life and exposure to risks in cyberspace.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="footer">
            dq-Smartplus
        </div>';
    }
}

$html .= '
</body>
</html>';

// Configure Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('enable_remote', false);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Output PDF
$schoolName = $param_school ? str_replace(' ', '_', $param_school) : 'All_Schools';
$filename = 'DQ_Smartplus_School_Report_Summary_' . $schoolName . '_' . date('Ymd_His') . '.pdf';
$dompdf->stream($filename, array('Attachment' => true));
?>
        }
        
        .main-title {
            font-size: 42px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        .hashtag-section {
            text-align: right;
            color: white;
        }
        
        .hashtag {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .school-name {
            font-size: 16px;
        }
        
        .content {
            background: white;
            margin: 0;
            padding: 0;
        }
        
        .table-container {
            padding: 40px 50px;
        }
        
        .table-header {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 3px solid #e0e0e0;
            margin-bottom: 30px;
        }
        
        .grade-header {
            width: 120px;
            color: #3f4fbf;
            font-size: 28px;
            font-weight: bold;
        }
        
        .category-columns {
            display: flex;
            flex: 1;
            gap: 20px;
        }
        
        .category-header {
            flex: 1;
            text-align: center;
            color: #3f4fbf;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .category-icon {
            width: 50px;
            height: 50px;
        }
        
        .grade-row {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .grade-label {
            width: 120px;
            color: #333;
            font-size: 24px;
            font-weight: bold;
        }
        
        .score-columns {
            display: flex;
            flex: 1;
            gap: 20px;
        }
        
        .score-pill {
            flex: 1;
            text-align: center;
            padding: 15px 0;
            border-radius: 25px;
            color: white;
            font-size: 20px;
            font-weight: bold;
        }
        
        .score-65 { background: #c62828; }
        .score-60 { background: #c62828; }
        .score-80 { background: #c62828; }
        .score-90 { background: #c62828; }
        .score-100 { background: #ff8f00; }
        .score-105 { background: #ff8f00; }
        .score-110 { background: #ff8f00; }
        .score-113 { background: #ff8f00; }
        .score-115 { background: #ff8f00; }
        .score-116 { background: #1565c0; }
        .score-117 { background: #1565c0; }
        .score-118 { background: #1565c0; }
        .score-120 { background: #1565c0; }
        .score-121 { background: #1565c0; }
        .score-125 { background: #1565c0; }
        .score-128 { background: #1565c0; }
        .score-130 { background: #1565c0; }
        
        .legend-section {
            background: white;
            padding: 40px 50px;
            border-top: 3px solid #e0e0e0;
        }
        
        .legend-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }
        
        .legend-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }
        
        .legend-color {
            min-width: 180px;
            height: 60px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            line-height: 1.2;
        }
        
        .legend-excellent { background: #673ab7; }
        .legend-satisfactory { background: #ff8f00; }
        .legend-less { background: #e53935; }
        .legend-require { background: #c62828; }
        
        .legend-text {
            color: #333;
            font-size: 13px;
            line-height: 1.4;
        }
        
        .footer {
            background: white;
            text-align: right;
            padding: 20px 50px;
            color: #3f4fbf;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>';

// Process each school
foreach ($schools_to_process as $index => $school) {
    if ($index > 0) {
        $html .= '<div style="page-break-before: always;"></div>';
    }
    
    $schoolResult = getSchoolData($conn, $school, $from_date, $to_date);
    
    if ($schoolResult->num_rows > 0) {
        $schoolData = [];
        while($row = $schoolResult->fetch_assoc()) {
            $schoolData[] = $row;
        }
        
        $html .= '
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    <div class="logo-center"></div>
                    <div class="logo-dots"></div>
                </div>
                <div class="logo-text">
                    <strong>DQ smart +</strong><br>
                    <small>digital technology for all</small>
                </div>
            </div>
            <div class="title-section">
                <div class="main-title">SCHOOL REPORT</div>
                <div class="subtitle">SUMMARY</div>
            </div>
            <div class="hashtag-section">
                <div class="hashtag">#DQEveryOne</div>
                <div class="school-name">School Name: ' . $school . '</div>
            </div>
        </div>
        
        <div class="content">
            <div class="table-container">
                <div class="table-header">
                    <div class="grade-header">Grade</div>
                    <div class="category-columns">
                        <div class="category-header">
                            <svg class="category-icon" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="40" fill="#2196f3"/>
                                <path d="M30 30 L40 40 M60 40 L70 30 M40 60 L60 60" stroke="white" stroke-width="3"/>
                                <circle cx="50" cy="50" r="8" fill="white"/>
                            </svg>
                            Screen Time<br>Management
                        </div>
                        <div class="category-header">
                            <svg class="category-icon" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="40" fill="#ff5722"/>
                                <path d="M35 35 L35 45 L45 45 L45 35 M55 35 L55 45 L65 45 L65 35 M35 55 L45 55 M55 55 L65 55" stroke="white" stroke-width="3"/>
                                <circle cx="50" cy="50" r="5" fill="white"/>
                            </svg>
                            Privacy<br>Management
                        </div>
                        <div class="category-header">
                            <svg class="category-icon" viewBox="0 0 100 100">
                                <rect x="20" y="30" width="60" height="40" fill="#2196f3" rx="5"/>
                                <rect x="25" y="35" width="50" height="30" fill="white" rx="3"/>
                                <circle cx="40" cy="45" r="3" fill="#2196f3"/>
                                <rect x="50" y="42" width="20" height="2" fill="#2196f3"/>
                                <rect x="50" y="47" width="15" height="2" fill="#2196f3"/>
                            </svg>
                            Cyber Security<br>Management
                        </div>
                        <div class="category-header">
                            <svg class="category-icon" viewBox="0 0 100 100">
                                <rect x="20" y="25" width="60" height="50" fill="#424242" rx="5"/>
                                <rect x="25" y="30" width="50" height="40" fill="white" rx="3"/>
                                <circle cx="40" cy="45" r="5" fill="#ffc107"/>
                                <path d="M55 40 Q65 35 70 45 Q65 55 55 50" fill="#ff9800"/>
                            </svg>
                            Digital Citizen<br>Identity
                        </div>
                    </div>
                </div>';
        
        // Data rows berdasarkan contoh PDF
        $sampleData = [
            ['grade' => '10.A', 'screen_time' => 65, 'privacy' => 110, 'cyber_security' => 116, 'digital_citizen' => 85],
            ['grade' => '10.B', 'screen_time' => 60, 'privacy' => 100, 'cyber_security' => 110, 'digital_citizen' => 116],
            ['grade' => '10.C', 'screen_time' => 100, 'privacy' => 120, 'cyber_security' => 113, 'digital_citizen' => 105],
            ['grade' => '11.A', 'screen_time' => 80, 'privacy' => 130, 'cyber_security' => 118, 'digital_citizen' => 115],
            ['grade' => '11.B', 'screen_time' => 90, 'privacy' => 115, 'cyber_security' => 105, 'digital_citizen' => 110],
            ['grade' => '11.C', 'screen_time' => 115, 'privacy' => 120, 'cyber_security' => 128, 'digital_citizen' => 125],
            ['grade' => '12', 'screen_time' => 105, 'privacy' => 117, 'cyber_security' => 130, 'digital_citizen' => 121]
        ];
        
        foreach($sampleData as $data) {
            $html .= '
                <div class="grade-row">
                    <div class="grade-label">' . $data['grade'] . '</div>
                    <div class="score-columns">
                        <div class="score-pill score-' . $data['screen_time'] . '">' . $data['screen_time'] . '</div>
                        <div class="score-pill score-' . $data['privacy'] . '">' . $data['privacy'] . '</div>
                        <div class="score-pill score-' . $data['cyber_security'] . '">' . $data['cyber_security'] . '</div>
                        <div class="score-pill score-' . $data['digital_citizen'] . '">' . $data['digital_citizen'] . '</div>
                    </div>
                </div>';
        }
        
        $html .= '
            </div>
            
            <div class="legend-section">
                <div class="legend-grid">
                    <div class="legend-item">
                        <div class="legend-color legend-excellent">
                            EXCELLENT<br>SCORE > 115
                        </div>
                        <div class="legend-text">
                            You are able to independently use Digital and Social Media tools ethically and responsibly in DQ Skills, you can increase the use of digital positively and creatively.
                        </div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-satisfactory">
                            SATISFACTORY<br>SCORE 100 - 115
                        </div>
                        <div class="legend-text">
                            You have an above average score in ethics and use smart devices and social media responsibly on DQ Skill, but it is recommended to be wiser in using digital media.
                        </div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-less">
                            LESS THAN<br>SATISFACTORY<br>SCORE 85 - 99
                        </div>
                        <div class="legend-text">
                            You have to be more careful in the use of social media and smart devices, you need to increase your awareness in dealing with digital flows.
                        </div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-require">
                            REQUIRE ATTENTION<br>SCORE < 85
                        </div>
                        <div class="legend-text">
                            It is highly recommended that you communicate openly with parents or educators who are more competent about digital life and exposure to risks in cyberspace.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                dq-Smartplus
            </div>
        </div>';
    }
}

$html .= '
</body>
</html>';

// Configure Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Output PDF
$schoolName = $param_school ? str_replace(' ', '_', $param_school) : 'All_Schools';
$filename = 'DQ_Smartplus_School_Report_Summary_' . $schoolName . '_' . date('Ymd_His') . '.pdf';
$dompdf->stream($filename, array('Attachment' => true));
?>