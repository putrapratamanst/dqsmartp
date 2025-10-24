<?php
// generate_report.php (final - 4 pages per school)
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'program/koneksi.php'; // $conn (mysqli)

// params
$param_school = isset($_GET['school']) ? $_GET['school'] : "";
$param_grade  = isset($_GET['grade']) ? $_GET['grade'] : "";
$from_date = date("Y-m-d", strtotime("-6 months"));
$to_date   = date("Y-m-d");

if (isset($_GET['fromDate']) && $_GET['fromDate'] != "") $from_date = $_GET['fromDate'];
if (isset($_GET['toDate']) && $_GET['toDate'] != "") $to_date = $_GET['toDate'];

// ambil list sekolah sesuai param (atau semua)
$schools_to_process = [];
if ($param_school != "") {
    $schools_to_process[] = $param_school;
} else {
    $sqlSchools = "SELECT DISTINCT SCHOOL FROM account WHERE STATE ='FINISH' ORDER BY SCHOOL";
    $resultSchools = $conn->query($sqlSchools);
    while ($r = $resultSchools->fetch_assoc()) {
        $schools_to_process[] = $r['SCHOOL'];
    }
}

// fungsi query per sekolah
function getSchoolData($conn, $school, $from_date, $to_date) {
    $school_safe = $conn->real_escape_string($school);
    $sql = "
        SELECT
            A.SCHOOL,
            IFNULL(A.GRADE, 'Tidak Ada Kelas') AS GRADE,
            (SELECT COUNT(ID) FROM account AS a1 WHERE a1.SCHOOL = A.SCHOOL AND a1.STATE = 'FINISH' AND a1.GRADE = A.GRADE) AS COUNT_OF_STUDENT,
            SUM(CASE WHEN Q.TIPE = 'Critical Thinking' THEN R.VALUE ELSE 0 END) AS critical_thinking,
            SUM(CASE WHEN Q.TIPE = 'Cyber Security Management' THEN R.VALUE ELSE 0 END) AS cyber_security_management,
            SUM(CASE WHEN Q.TIPE = 'Cyberbullying' THEN R.VALUE ELSE 0 END) AS cyberbullying,
            SUM(CASE WHEN Q.TIPE = 'Digital Citizen Identity' THEN R.VALUE ELSE 0 END) AS digital_citizen_identity,
            SUM(CASE WHEN Q.TIPE = 'Digital Empathy' THEN R.VALUE ELSE 0 END) AS digital_empathy,
            SUM(CASE WHEN Q.TIPE = 'Digital Footprint' THEN R.VALUE ELSE 0 END) AS digital_footprint,
            SUM(CASE WHEN Q.TIPE = 'Privacy Management' THEN R.VALUE ELSE 0 END) AS privacy_management,
            SUM(CASE WHEN Q.TIPE = 'Screen Time' THEN R.VALUE ELSE 0 END) AS screen_time
        FROM
            RESULT AS R
            LEFT JOIN QUESTION AS Q ON Q.ID = R.QUESTION
            LEFT JOIN CATEGORY AS C ON C.KATEGORI = Q.TIPE
            LEFT JOIN account AS A ON A.ID = R.USERID
        WHERE
            A.STATE = 'FINISH'
            AND A.SCHOOL = '{$school_safe}'
    ";

    if ($from_date != "") {
        $sql .= " AND R.ACTIVITY_ON >= '".$conn->real_escape_string($from_date)." 00:00:00' ";
    }
    if ($to_date != "") {
        $sql .= " AND R.ACTIVITY_ON <= '".$conn->real_escape_string($to_date)." 23:59:59' ";
    }

    $sql .= " GROUP BY A.SCHOOL, A.GRADE ORDER BY A.GRADE";
    return $conn->query($sql);
}

// helper untuk kelas warna
function getScoreClassName($score) {
    if (!is_numeric($score)) $score = 0;
    $s = (int)$score;
    if ($s > 115) return "score-purple";
    if ($s >= 100) return "score-orange";
    if ($s >= 85)  return "score-blue";
    return "score-red";
}

// png icons (base64 static untuk performa) - dipakai di header kategori
function pngIcon($key) {
    // Static base64 encoded images untuk menghindari file_get_contents berulang
    static $base64Icons = null;
    
    if ($base64Icons === null) {
        $base64Icons = [];
        $iconKeys = ['screen_time', 'privacy_management', 'cyber_security_management', 'digital_citizen_identity', 
                     'digital_empathy', 'digital_footprint', 'cyberbullying', 'critical_thinking'];
        
        foreach ($iconKeys as $iconKey) {
            $imagePath = 'img/' . $iconKey . '.png';
            if (file_exists($imagePath)) {
                $imageData = file_get_contents($imagePath);
                $base64Icons[$iconKey] = base64_encode($imageData);
            }
        }
    }
    
    if (isset($base64Icons[$key])) {
        return '<img class="category-icon" src="data:image/png;base64,' . $base64Icons[$key] . '" alt="' . ucwords(str_replace('_', ' ', $key)) . '">';
    }
    
    return '<div class="category-icon" style="background:#ccc;border-radius:4px;width:40px;height:40px;"></div>'; // fallback jika gambar tidak ada
}


// mulai bangun HTML (gabungkan semua 4 halaman per sekolah)
$html = '<!doctype html><html><head><meta charset="utf-8"><title>DQ Smartplus - School Report</title>
<style>
  @page { size:A4 landscape; margin:18mm; }
  body{margin:0;font-family:Arial,Helvetica,sans-serif;background:#f9f9f9}
  .sheet{width:297mm;min-height:210mm;background:#fff;box-sizing:border-box}
  .header-table{width:100%;border-collapse:collapse;margin-bottom:8px}
  .header-table td{vertical-align:middle}
  .logo{width:58px;height:58px;border-radius:8px;background:linear-gradient(180deg,#2b28a8,#4b45c9);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px}
  .logo-text{font-size:12px;line-height:1.05}
  .main-title{font-size:32px;font-weight:700;text-align:center,color:#fff}
  .subtitle{font-size:12px;text-align:center;color:#666;margin-top:4px}
  .hashtag{font-size:18px;text-align:right;font-weight:600}
  .school-name{font-size:13px;text-align:right;color:#333;margin-top:6px}
  .category-icon{width:40px;height:40px;display:block;margin:0 auto 6px}
  .data-rows{width:100%;border-collapse:collapse;margin-top:6px}
  .data-rows td{padding:6px;font-size:12px;text-align:center}
  .grade-col{font-weight:700;text-align:left;padding-left:6px;width:90px}
  .score-pill{display:inline-block;min-width:48px;padding:8px 12px;border-radius:18px;font-weight:700;font-size:12px;color:#fff}
  .score-purple{background:#260e83}.score-orange{background:#f58a0a}.score-blue{background:#2196f3}.score-red{background:#c40010}
  .legend-color{width:120px;height:70px;border-radius:6px;color:#fff;font-weight:700;padding:8px;text-align:center}
  .legend-excellent{background:#260e83}.legend-satisfactory{background:#f58a0a}.legend-less{background:#2196f3}.legend-require{background:#c40010}
  .legend-text{font-size:11px;color:#333;padding-left:12px;line-height:1.25}
  .footer{text-align:right;color:#666;margin-top:12px}
  .page-break{page-break-after:always}
  table{border-spacing:0}
</style>
</head><body>';

// iterate schools
foreach ($schools_to_process as $idx => $school_name) {
    $safeSchool = htmlspecialchars($school_name);
    // fetch data per grade
    $res = getSchoolData($conn, $school_name, $from_date, $to_date);

    // Prepare arrays to compute averages for page 3
    $agg = [
        'screen_time' => ['sum'=>0,'count'=>0],
        'privacy_management' => ['sum'=>0,'count'=>0],
        'cyber_security_management' => ['sum'=>0,'count'=>0],
        'digital_citizen_identity' => ['sum'=>0,'count'=>0],
        'digital_empathy' => ['sum'=>0,'count'=>0],
        'digital_footprint' => ['sum'=>0,'count'=>0],
        'cyberbullying' => ['sum'=>0,'count'=>0],
        'critical_thinking' => ['sum'=>0,'count'=>0],
    ];
    $rows = [];

    if ($res && $res->num_rows > 0) {
        while ($r = $res->fetch_assoc()) {
            $row = [
                'grade' => $r['GRADE'],
                'screen_time' => (int)($r['screen_time'] ?? 0),
                'privacy_management' => (int)($r['privacy_management'] ?? 0),
                'cyber_security_management' => (int)($r['cyber_security_management'] ?? 0),
                'digital_citizen_identity' => (int)($r['digital_citizen_identity'] ?? 0),
                'digital_empathy' => (int)($r['digital_empathy'] ?? 0),
                'digital_footprint' => (int)($r['digital_footprint'] ?? 0),
                'cyberbullying' => (int)($r['cyberbullying'] ?? 0),
                'critical_thinking' => (int)($r['critical_thinking'] ?? 0),
            ];
            $rows[] = $row;
            // aggregate sums & counts (we'll use count of grades)
            foreach ($agg as $k => &$v) {
                $v['sum'] += $row[$k];
                $v['count']++;
            }
            unset($v);
        }
    } else {
        // fallback sample rows (keperluan tampilan)
        $rows = [
            ['grade'=>'10.A','screen_time'=>65,'privacy_management'=>110,'cyber_security_management'=>116,'digital_citizen_identity'=>85,'digital_empathy'=>105,'digital_footprint'=>99,'cyberbullying'=>70,'critical_thinking'=>95],
            ['grade'=>'10.B','screen_time'=>60,'privacy_management'=>100,'cyber_security_management'=>110,'digital_citizen_identity'=>116,'digital_empathy'=>95,'digital_footprint'=>105,'cyberbullying'=>80,'critical_thinking'=>110],
            ['grade'=>'10.C','screen_time'=>100,'privacy_management'=>120,'cyber_security_management'=>113,'digital_citizen_identity'=>105,'digital_empathy'=>116,'digital_footprint'=>115,'cyberbullying'=>95,'critical_thinking'=>105],
            ['grade'=>'11.A','screen_time'=>80,'privacy_management'=>130,'cyber_security_management'=>118,'digital_citizen_identity'=>115,'digital_empathy'=>114,'digital_footprint'=>102,'cyberbullying'=>85,'critical_thinking'=>82],
            ['grade'=>'11.B','screen_time'=>90,'privacy_management'=>115,'cyber_security_management'=>105,'digital_citizen_identity'=>110,'digital_empathy'=>108,'digital_footprint'=>70,'cyberbullying'=>80,'critical_thinking'=>83],
            ['grade'=>'11.C','screen_time'=>115,'privacy_management'=>120,'cyber_security_management'=>128,'digital_citizen_identity'=>125,'digital_empathy'=>115,'digital_footprint'=>110,'cyberbullying'=>123,'critical_thinking'=>119],
            ['grade'=>'12','screen_time'=>105,'privacy_management'=>117,'cyber_security_management'=>130,'digital_citizen_identity'=>121,'digital_empathy'=>98,'digital_footprint'=>120,'cyberbullying'=>95,'critical_thinking'=>95],
        ];
        // aggregate
        foreach ($rows as $row) {
            foreach ($agg as $k => &$v) {
                $v['sum'] += $row[$k];
                $v['count']++;
            }
            unset($v);
        }
    }
    $logoBase64 = 'data:image/jpeg;base64,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';
    // ---------- PAGE 1 (Screen Time, Privacy, Cyber Security, Digital Citizen) ----------
    $html .= '<div class="sheet">';
    $html .= '<table class="header-table" style="background-color:#1e3b7e;padding:18px"><tr>';
    $html .= '<td style="width:33%"><div class="logo"><img src="' . $logoBase64 . '" style="width:58px;height:58px;border-radius:8px;"></div><div class="logo-text"> </td>';
    $html .= '<td style="width:34%"><div class="main-title" style="color:#fff;text-align:center">SCHOOL REPORT SUMMARY</div></td>';
    $html .= '<td style="width:33%;"><div class="hashtag" style="color:#fff;text-align:right">#DQEveryOne</div><div class="hashtag" style="color:#fff;text-align:right">School Name: ' . $safeSchool . '</div></td>';
    $html .= '</tr></table>';

    $html .= '<div class="table-container"><table class="category-header"><tr>';
    $html .= '<td class="grade-col">Grade</td>';
    $html .= '<td>' . pngIcon('screen_time') . '<div>Screen Time<br>Management</div></td>';
    $html .= '<td>' . pngIcon('privacy_management') . '<div>Privacy<br>Management</div></td>';
    $html .= '<td>' . pngIcon('cyber_security_management') . '<div>Cyber Security<br>Management</div></td>';
    $html .= '<td>' . pngIcon('digital_citizen_identity') . '<div>Digital Citizen<br>Identity</div></td>';
    $html .= '</tr></table>';

    // rows
    $html .= '<table class="data-rows">';
    foreach ($rows as $r) {
        $classScreen = getScoreClassName($r['screen_time']);
        $classPrivacy = getScoreClassName($r['privacy_management']);
        $classCyber = getScoreClassName($r['cyber_security_management']);
        $classDigital = getScoreClassName($r['digital_citizen_identity']);
        $html .= '<tr>';
        $html .= '<td class="grade-col">' . htmlspecialchars($r['grade']) . '</td>';
        $html .= '<td><span class="score-pill ' . $classScreen . '">' . $r['screen_time'] . '</span></td>';
        $html .= '<td><span class="score-pill ' . $classPrivacy . '">' . $r['privacy_management'] . '</span></td>';
        $html .= '<td><span class="score-pill ' . $classCyber . '">' . $r['cyber_security_management'] . '</span></td>';
        $html .= '<td><span class="score-pill ' . $classDigital . '">' . $r['digital_citizen_identity'] . '</span></td>';
        $html .= '</tr>';
    }
    $html .= '</table></div>'; // end table-container

    // legend
    $html .= '<div class="legend-section"><table class="legend-grid">';
    $html .= '<tr><td class="legend-item"><div style="display:flex;align-items:center"><div class="legend-color legend-excellent">EXCELLENT<br>SCORE &gt; 115</div><div class="legend-text">You are able to independently use Digital and Social Media tools ethically and responsibly in DQ Skills, you can increase the use of digital positively and creatively.</div></div></td>';
    $html .= '<td class="legend-item"><div style="display:flex;align-items:center"><div class="legend-color legend-satisfactory">SATISFACTORY<br>SCORE 100 - 115</div><div class="legend-text">You have an above average score in ethics and use smart devices and social media responsibly on DQ Skill, but it is recommended to be wiser in using digital media.</div></div></td></tr>';
    $html .= '<tr><td class="legend-item"><div style="display:flex;align-items:center"><div class="legend-color legend-less">LESS THAN<br>SATISFACTORY<br>SCORE 85 - 99</div><div class="legend-text">You have to be more careful in the use of social media and smart devices, you need to increase your awareness in dealing with digital flows.</div></div></td>';
    $html .= '<td class="legend-item"><div style="display:flex;align-items:center"><div class="legend-color legend-require">REQUIRE ATTENTION<br>SCORE &lt; 85</div><div class="legend-text">It is highly recommended that you communicate openly with parents or educators who are more competent about digital life and exposure to risks in cyberspace.</div></div></td></tr>';
    $html .= '</table></div>';

    $html .= '<div class="footer">dq-Smartplus</div>';
    $html .= '</div>'; // end sheet page 1

    $html .= '<div class="page-break"></div>';

    // ---------- PAGE 2 (Digital Empathy, Footprint, Cyberbullying, Critical Thinking) ----------
    $html .= '<div class="sheet">';
    $html .= '<table class="header-table"><tr>';
    $html .= '<td style="width:33%"><div class="logo"><img src="' . $logoBase64 . '" style="width:58px;height:58px;border-radius:8px;"></div><div class="logo-text"> <strong>DQ smart +</strong><br><small>digital technology for all</small></div></td>';
    $html .= '<td style="width:34%"><div class="main-title">SCHOOL REPORT</div><div class="subtitle">SUMMARY</div></td>';
    $html .= '<td style="width:33%"><div class="hashtag">#DQEveryOne</div><div class="school-name">School Name: ' . $safeSchool . '</div></td>';
    $html .= '</tr></table>';

    $html .= '<div class="table-container"><table class="category-header"><tr>';
    $html .= '<td class="grade-col">Grade</td>';
    $html .= '<td>' . pngIcon('digital_empathy') . '<div>Digital<br>Empathy</div></td>';
    $html .= '<td>' . pngIcon('digital_footprint') . '<div>Digital<br>Footprint</div></td>';
    $html .= '<td>' . pngIcon('cyberbullying') . '<div>Cyberbulling<br>Management</div></td>';
    $html .= '<td>' . pngIcon('critical_thinking') . '<div>Critical<br>Thinking</div></td>';
    $html .= '</tr></table>';

    $html .= '<table class="data-rows">';
    foreach ($rows as $r) {
        $classEmpathy = getScoreClassName($r['digital_empathy']);
        $classFoot = getScoreClassName($r['digital_footprint']);
        $classCyberbully = getScoreClassName($r['cyberbullying']);
        $classCritical = getScoreClassName($r['critical_thinking']);
        $html .= '<tr>';
        $html .= '<td class="grade-col">' . htmlspecialchars($r['grade']) . '</td>';
        $html .= '<td><span class="score-pill ' . $classEmpathy . '">' . $r['digital_empathy'] . '</span></td>';
        $html .= '<td><span class="score-pill ' . $classFoot . '">' . $r['digital_footprint'] . '</span></td>';
        $html .= '<td><span class="score-pill ' . $classCyberbully . '">' . $r['cyberbullying'] . '</span></td>';
        $html .= '<td><span class="score-pill ' . $classCritical . '">' . $r['critical_thinking'] . '</span></td>';
        $html .= '</tr>';
    }
    $html .= '</table></div>';
    $html .= '<div class="footer">dq-Smartplus</div>';
    $html .= '</div>'; // end sheet page 2

    $html .= '<div class="page-break"></div>';

    // ---------- PAGE 3 (Average DQ Score for School) ----------
    $html .= '<div class="sheet">';
    $html .= '<table class="header-table"><tr>';
    $html .= '<td style="width:33%"><div class="logo"><img src="' . $logoBase64 . '" style="width:58px;height:58px;border-radius:8px;"></div><div class="logo-text"> <strong>DQ smart +</strong><br><small>digital technology for all</small></div></td>';
    $html .= '<td style="width:34%"><div class="main-title">SCHOOL REPORT</div><div class="subtitle">Average DQ Score for School</div></td>';
    $html .= '<td style="width:33%"><div class="hashtag">#DQEveryOne</div><div class="school-name">School Name: ' . $safeSchool . '</div></td>';
    $html .= '</tr></table>';

    $html .= '<div class="table-container"><table class="data-rows">';
    // calculate averages safely (avoid division by zero)
    foreach ($agg as $metric => $data) {
        $avg = $data['count'] > 0 ? round($data['sum'] / $data['count']) : 0;
        $label = str_replace('_',' ', ucwords($metric, '_'));
        $classAvg = getScoreClassName($avg);
        $html .= '<tr><td class="grade-col">' . htmlspecialchars($label) . '</td><td><span class="score-pill ' . $classAvg . '">' . $avg . '</span></td></tr>';
    }
    $html .= '</table></div>';
    $html .= '<div class="footer">dq-Smartplus</div>';
    $html .= '</div>'; // end sheet page 3

    $html .= '<div class="page-break"></div>';

    // ---------- PAGE 4 (Report / Cover) ----------
    $html .= '<div class="sheet">';
    $html .= '<table class="header-table"><tr>';
    $html .= '<td style="width:33%"><div class="logo"><img src="' . $logoBase64 . '" style="width:58px;height:58px;border-radius:8px;"></div><div class="logo-text"> <strong>DQ smart +</strong><br><small>digital technology for all</small></div></td>';
    $html .= '<td style="width:34%"><div class="main-title">SCHOOL REPORT</div><div class="subtitle">REPORT</div></td>';
    $html .= '<td style="width:33%"><div class="hashtag">#DQEveryOne</div><div class="school-name">School Name: ' . $safeSchool . '</div></td>';
    $html .= '</tr></table>';

    $html .= '<div style="text-align:center;margin-top:40px;"><h1 style="font-size:48px;margin:0">SUMMARY</h1><p style="margin:8px 0 0 0;font-size:18px;color:#666">dq-Smartplus</p></div>';
    $html .= '<div style="height:200px"></div>';
    $html .= '<div class="footer">dq-Smartplus</div>';
    $html .= '</div>'; // end sheet page 4

    // page break between schools (if multiple)
    if ($idx !== array_key_last($schools_to_process)) {
        $html .= '<div class="page-break"></div>';
    }
} // end foreach schools

$html .= '</body></html>';

// Output HTML langsung ke browser (untuk preview/debug)
echo $html;
exit;

// Uncomment bagian di bawah ini jika ingin generate PDF
/*
// Dompdf config & render
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('enable_remote', false);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Output - tampil di browser tanpa download
$schoolNameSafe = $param_school ? preg_replace('/\s+/', '_', $param_school) : 'All_Schools';
$filename = 'DQ_Smartplus_School_Report_' . $schoolNameSafe . '_' . date('Ymd_His') . '.pdf';
$dompdf->stream($filename, ['Attachment' => false]); // false = tampil di browser, true = download
*/
exit;
?>
