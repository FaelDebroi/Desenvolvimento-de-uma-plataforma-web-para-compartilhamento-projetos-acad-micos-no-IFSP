<?php
class RecuperacaoSenha
{
    private static function db(): PDO
    {
        return Database::getInstance();
    }

    public static function create(int $usuarioId): string
    {
        $token  = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Invalida tokens anteriores
        self::db()
            ->prepare('UPDATE recuperacao_senha SET usado = 1 WHERE usuario_id = ?')
            ->execute([$usuarioId]);

        self::db()
            ->prepare('INSERT INTO recuperacao_senha (usuario_id, token, expira_em) VALUES (?, ?, ?)')
            ->execute([$usuarioId, $token, $expira]);

        return $token;
    }

    public static function findValid(string $token): ?array
    {
        $s = self::db()->prepare(
            'SELECT r.*, u.email, u.nome
             FROM recuperacao_senha r
             JOIN usuarios u ON u.id = r.usuario_id
             WHERE r.token = ? AND r.usado = 0 AND r.expira_em > NOW()'
        );
        $s->execute([$token]);
        return $s->fetch() ?: null;
    }

    public static function markUsed(string $token): void
    {
        self::db()
            ->prepare('UPDATE recuperacao_senha SET usado = 1 WHERE token = ?')
            ->execute([$token]);
    }
}
