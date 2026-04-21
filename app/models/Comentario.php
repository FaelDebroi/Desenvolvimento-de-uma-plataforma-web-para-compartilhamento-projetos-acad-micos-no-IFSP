<?php
class Comentario
{
    private static function db(): PDO
    {
        return Database::getInstance();
    }

    public static function findByProjeto(int $projetoId): array
    {
        $s = self::db()->prepare(
            "SELECT c.*, u.nome AS autor_nome, u.tipo AS autor_tipo, u.foto_perfil AS autor_foto
             FROM comentarios c
             JOIN usuarios u ON u.id = c.usuario_id
             WHERE c.projeto_id = ?
             ORDER BY c.criado_em ASC"
        );
        $s->execute([$projetoId]);
        return $s->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $s = self::db()->prepare('SELECT * FROM comentarios WHERE id = ?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public static function create(int $projetoId, int $usuarioId, string $conteudo): int
    {
        $s = self::db()->prepare(
            'INSERT INTO comentarios (projeto_id, usuario_id, conteudo) VALUES (?, ?, ?)'
        );
        $s->execute([$projetoId, $usuarioId, trim($conteudo)]);
        return (int) self::db()->lastInsertId();
    }

    public static function delete(int $id): bool
    {
        return self::db()
            ->prepare('DELETE FROM comentarios WHERE id = ?')
            ->execute([$id]);
    }
}
