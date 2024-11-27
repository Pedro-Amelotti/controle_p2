<?php
// database.php
class Database {
    private $pdo;

    
    public function __construct() {
        $this->pdo = new PDO('sqlite:/var/www/html/controle_p2/controle_visitantes.db');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function createTables() {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS visitantes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            age INTEGER,
            cpf VARCHAR(255)
            /*rank TEXT,
            dob DATE,
            nome_guerra VARCHAR(255)*/
        )");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS registros (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            visitante_id INTEGER NOT NULL,
            secao TEXT NOT NULL,
            date DATE NOT NULL,
            hora_entrada TEXT,
            hora_saida TEXT,
            additional_fields TEXT,
            FOREIGN KEY(visitante_id) REFERENCES visitantes(id)
        )");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY,
            username TEXT,
            password TEXT,
            role TEXT,
           /* full_name TEXT,
            rank TEXT,
            dob DATE,
            idt_mil VARCHAR(255),
            nome_guerra VARCHAR(255),*/
            approved INTEGER DEFAULT 0
        )");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS secoes (
            id INTEGER PRIMARY KEY,
            secao_id TEXT,
            name TEXT
        )");
            }
}

$db = new Database();
$db->createTables();

