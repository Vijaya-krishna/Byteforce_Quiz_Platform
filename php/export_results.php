<?php
require_once 'DB.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="ByteForce_Results.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Username', 'Attempts', 'High Score', 'Last Attempt (IST)']);

$db = (new DB())->conn();
$res = $db->query("SELECT u.username, COUNT(r.id) AS attempts, MAX(r.score) AS highscore, MAX(r.created_at) AS last_time 
                   FROM results r 
                   JOIN users u ON u.id = r.user_id 
                   GROUP BY r.user_id 
                   ORDER BY highscore DESC");

if ($res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $dt = new DateTime($r['last_time'], new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
        fputcsv($output, [
            $r['username'],
            $r['attempts'],
            $r['highscore'],
            $dt->format('d M Y, H:i:s')
        ]);
    }
}
fclose($output);
?>
