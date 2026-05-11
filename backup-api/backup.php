<?php
// ════════════════════════════════════════════════
// Vườn Thói Quen — MySQL Backup API
// Upload file này lên hosting
// ════════════════════════════════════════════════

define('DB_HOST', 'localhost');
define('DB_NAME', 'vuon_thoi_quen');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('API_SECRET', 'CHANGE_THIS_SECRET_KEY_123456');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }

$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if ($apiKey !== API_SECRET) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Unauthorized']); exit; }

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid JSON']); exit; }

$uid    = trim($body['uid']   ?? '');
$email  = trim($body['email'] ?? '');
$habits = $body['habits'] ?? null;
$stats  = $body['stats']  ?? null;

if (!$uid || !$habits || !$stats) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Missing fields']); exit; }

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $pdo->beginTransaction();

    // Lưu backup
    $pdo->prepare("INSERT INTO user_backups (uid,email,habits_json,stats_json) VALUES (?,?,?,?)")
        ->execute([$uid, $email, json_encode($habits,JSON_UNESCAPED_UNICODE), json_encode($stats,JSON_UNESCAPED_UNICODE)]);

    // Log từng tick
    if (!empty($body['logs'])) {
        $s = $pdo->prepare("INSERT INTO habit_logs (uid,habit_id,habit_name,log_date,mood,note,streak)
            VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE mood=VALUES(mood),note=VALUES(note),streak=VALUES(streak)");
        foreach ($body['logs'] as $l) {
            if (empty($l['habit_id'])||empty($l['date'])) continue;
            $s->execute([$uid,$l['habit_id'],$l['habit_name']??'',$l['date'],$l['mood']??null,$l['note']??null,(int)($l['streak']??0)]);
        }
    }
    $pdo->commit();
    echo json_encode(['ok'=>true,'backed_up_at'=>date('c')]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
