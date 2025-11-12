<?php
require_once 'DB.php';

class QuizManager {
    private $conn;
    public function __construct() {
        $this->conn = (new DB())->conn();
    }

    public function addQuestion($q,$a,$b,$c,$d,$correct) {
        $stmt = $this->conn->prepare(
            "INSERT INTO quiz_questions (question, option_a, option_b, option_c, option_d, correct_answer)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->bind_param("ssssss",$q,$a,$b,$c,$d,$correct);
        return $stmt->execute();
    }

    public function getQuestions() {
        $res = $this->conn->query(
            "SELECT id,question,option_a,option_b,option_c,option_d,correct_answer FROM quiz_questions ORDER BY id ASC"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function grade($answers) {
        if (!is_array($answers)) $answers = [];
        $score = 0;

        $res = $this->conn->query("SELECT id, correct_answer FROM quiz_questions ORDER BY id ASC");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $qid = $row['id'];
                $correct = strtoupper(trim($row['correct_answer']));
                $sel = isset($answers[$qid]) ? $answers[$qid] : null;

                if (is_array($sel)) $sel = reset($sel);
                $sel = strtoupper(trim($sel ?? ''));

                if ($sel !== '' && $sel === $correct) $score++;
            }
        }
        return $score;
    }
}
?>
