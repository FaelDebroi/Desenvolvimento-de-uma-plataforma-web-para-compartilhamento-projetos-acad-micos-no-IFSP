<?php
class Database
{
    // Singleton para gerenciar a conexão PDO
    private static ?PDO $instance = null;
    // Impede a criação de instâncias diretamente
    private function __construct()
    {
    }
    // Método para obter a instância da conexão PDO
    public static function getInstance(): PDO
    {
        // Se a instância ainda não foi criada, cria uma nova conexão
        if (self::$instance === null) {
            // Configura o DSN (Data Source Name) para a conexão PDO
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            // Cria a instância da conexão PDO com as opções de configuração
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                // Configurações para melhorar a segurança e o desempenho
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        // Retorna a instância da conexão PDO
        return self::$instance;
    }
}
