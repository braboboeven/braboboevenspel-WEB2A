<?php

    class AwnserSystem
    {
        private $host = "localhost";
        private $port = 3306;
        private $user = "root";
        private $pass = "";
        private $database = "braboboeven";

        private $conn;

        public function __construct()
        {
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->database, $this->port);

            if ($this->conn->connect_error) {
                die("Fout: " . $this->conn->connect_error);
            }
        }

        public function __destruct()
        {
            $this->conn->close();
        }

        public function GetQuestions()
        {
            $result = $this->conn->query("SELECT vraag_sleutel_id, vraag_tekst, correcte_query, verwacht_resultaat_aantal FROM Alle_Boeven_Database_Vragen ORDER BY vraag_sleutel_id ASC");
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        private function VerifyQueryExecution($query)
        {
            try {
                return $this->conn->query($query);
            } catch (Exception $e) {
                return false;
            }
        }

        private function IsHalfCorrect($student, $expected, $margin = 1)
        {
            if (is_numeric($student) && is_numeric($expected)) {
                $student_val = intval($student);
                $expected_val = intval($expected);
                return abs($student_val - $expected_val) <= $margin;
            }
            return false;
        }

        private function NormalizeQueryStructure($query)
        {
            $q = strtolower($query);
            $q = preg_replace("/'[^']*'/", "?", $q);
            $q = preg_replace("/\d{4}-\d{2}-\d{2}/", "?", $q);
            return trim(preg_replace('/\s+/', ' ', $q));
        }

        public function CheckQuery($student_query, $correcte_query, $verwacht_aantal, $student_antwoord = null)
        {
            $score = 0;
            $feedback = "";

            $result = $this->VerifyQueryExecution($student_query);

            if ($result === false) {

                if ($student_antwoord !== null && is_numeric($verwacht_aantal)) {

                    if (intval($student_antwoord) == intval($verwacht_aantal)) {
                        return [
                            "score" => 500,
                            "feedback" => "✓ Correct aantal rijen via handmatige invoer! Je krijgt €500\n\n<strong>Juiste query:</strong>\n" . $correcte_query
                        ];
                    }

                    if ($this->IsHalfCorrect($student_antwoord, $verwacht_aantal, 2)) {
                        return [
                            "score" => 250,
                            "feedback" => "⚠️ Bijna correct aantal rijen via handmatige invoer. Je krijgt €250\n\n<strong>Juiste query:</strong>\n" . $correcte_query
                        ];
                    }
                }

                return [
                    "score" => 0,
                    "feedback" => "❌ Query bevat een fout. Je krijgt €0\n\n<strong>Juiste query:</strong>\n" . $correcte_query
                ];
            }

            $aantal_rijen = $result->num_rows;

            $student_clean  = strtolower(trim(preg_replace('/\s+/', ' ', $student_query)));
            $correcte_clean = strtolower(trim(preg_replace('/\s+/', ' ', $correcte_query)));

            if ($student_clean === $correcte_clean) {
                return [
                    "score" => 1000,
                    "feedback" => "✓ Query perfect! 💰 Je krijgt €1000!"
                ];
            }

            if ($aantal_rijen == $verwacht_aantal || $this->IsHalfCorrect($aantal_rijen, $verwacht_aantal, 1)) {
                return [
                    "score" => 500,
                    "feedback" => "✓ Correct aantal rijen! Je krijgt €500\n\n<strong>Juiste query:</strong>\n" . $correcte_query
                ];
            }

            // if ($this->IsHalfCorrect($aantal_rijen, $verwacht_aantal, 1)) {
            //     return [
            //         "score" => 250,
            //         "feedback" => "⚠️ Bijna correct aantal rijen. Kleine fout in de query. Je krijgt €250\n\n<strong>Juiste query:</strong>\n" . $correcte_query
            //     ];
            // }

            // $student_struct = $this->NormalizeQueryStructure($student_query);
            // $correct_struct = $this->NormalizeQueryStructure($correcte_query);

            // if ($student_struct === $correct_struct) {
            //     return [
            //         "score" => 250,
            //         "feedback" => "⚠️ Query-structuur is correct, maar waarden (zoals datums) zijn fout. Je krijgt €250\n\n<strong>Juiste query:</strong>\n" . $correcte_query
            //     ];
            // }

            return [
                "score" => 0,
                "feedback" => "❌ Fout aantal rijen (verwacht: $verwacht_aantal, gekregen: $aantal_rijen). Je krijgt €0\n\n<strong>Juiste query:</strong>\n" . $correcte_query
            ];
        }


        public function GetConnection()
        {
            return $this->conn;
        }
    }


?>
