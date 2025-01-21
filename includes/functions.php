<?php
require_once 'config.php';
require_once 'database.php';

class GameManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getRandomWord() {
        $stmt = $this->db->prepare("
            SELECT id_mot, suite_carac
            FROM mot
            WHERE LENGTH(suite_carac) BETWEEN ? AND ?
            ORDER BY RAND()
            LIMIT 1
        ");
        $stmt->execute([MIN_WORD_LENGTH, MAX_WORD_LENGTH]);
        return $stmt->fetch();
    }

    public function verifyWord($word) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM mot
            WHERE suite_carac = ?
        ");
        $stmt->execute([$word]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function startGame($joueur1, $joueur2, $mot_id) {
        $stmt = $this->db->prepare("
            INSERT INTO partie (id_joueur1, id_joueur2, id_mot, debut_partie)
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$joueur1, $joueur2, $mot_id]);
    }

    public function endGame($partie_id, $score, $nombre_essais, $vainqueur) {
        $stmt = $this->db->prepare("
            UPDATE partie
            SET fin_partie = NOW(),
                score_vainqueur = ?,
                nombre_essais = ?,
                vainqueur = ?
            WHERE id_partie = ?
        ");
        return $stmt->execute([$score, $nombre_essais, $vainqueur, $partie_id]);
    }

    public function getTopScores($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                j1.login as joueur1_login,
                j2.login as joueur2_login,
                m.suite_carac as mot
            FROM partie p
            JOIN joueur j1 ON p.id_joueur1 = j1.id_joueur
            LEFT JOIN joueur j2 ON p.id_joueur2 = j2.id_joueur
            JOIN mot m ON p.id_mot = m.id_mot
            WHERE p.score_vainqueur IS NOT NULL
            ORDER BY p.score_vainqueur DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function calculateScore($attempts, $time_taken) {
        // Score = 1 / (nombre d'essais * temps en secondes)
        // Multiplié par 100 pour avoir un pourcentage
        return (1 / ($attempts * $time_taken)) * 100;
    }

    public function validateWordLength($word) {
        $length = strlen($word);
        return $length >= MIN_WORD_LENGTH && $length <= MAX_WORD_LENGTH;
    }

    public function checkLetters($attempt, $target) {
        $result = [];
        $target_array = str_split($target);
        $attempt_array = str_split($attempt);
        
        // Premier passage : marquer les lettres correctes
        for ($i = 0; $i < strlen($target); $i++) {
            if (isset($attempt_array[$i]) && $attempt_array[$i] === $target_array[$i]) {
                $result[$i] = 'correct';
                $target_array[$i] = null; // Marquer comme utilisée
                $attempt_array[$i] = null;
            }
        }
        
        // Deuxième passage : marquer les lettres mal placées
        for ($i = 0; $i < strlen($attempt); $i++) {
            if (!isset($result[$i]) && $attempt_array[$i] !== null) {
                $pos = array_search($attempt_array[$i], $target_array);
                if ($pos !== false) {
                    $result[$i] = 'wrong-position';
                    $target_array[$pos] = null; // Marquer comme utilisée
                } else {
                    $result[$i] = 'incorrect';
                }
            }
        }
        
        return $result;
    }
}

class UserManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($login, $password) {
        $stmt = $this->db->prepare("
            SELECT *
            FROM joueur
            WHERE login = ?
        ");
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }

    public function register($login, $password, $email) {
        // Vérifier si le login existe déjà
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM joueur
            WHERE login = ?
        ");
        $stmt->execute([$login]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            return false;
        }

        // Créer le nouvel utilisateur
        $stmt = $this->db->prepare("
            INSERT INTO joueur (login, mot_de_passe, email)
            VALUES (?, ?, ?)
        ");
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        return $stmt->execute([$login, $hashed_password, $email]);
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("
            SELECT id_joueur, login, email
            FROM joueur
            WHERE id_joueur = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>