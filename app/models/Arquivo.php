<?php
class Arquivo
{
    private static function db(): PDO
    {
        return Database::getInstance();
    }

    public static function findByProjeto(int $projetoId): array
    {
        $s = self::db()->prepare(
            'SELECT * FROM arquivos WHERE projeto_id = ? ORDER BY criado_em ASC'
        );
        $s->execute([$projetoId]);
        return $s->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $s = self::db()->prepare('SELECT * FROM arquivos WHERE id = ?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public static function create(int $projetoId, array $fileData): int
    {
        $s = self::db()->prepare(
            'INSERT INTO arquivos (projeto_id, nome_original, caminho, tipo_mime, tamanho_kb)
             VALUES (?, ?, ?, ?, ?)'
        );
        $s->execute([
            $projetoId,
            $fileData['nome_original'],
            $fileData['caminho'],
            $fileData['tipo_mime'],
            $fileData['tamanho_kb'],
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function delete(int $id): bool
    {
        $arq = self::find($id);
        if ($arq) {
            $path = UPLOAD_PATH . '/' . $arq['caminho'];
            if (file_exists($path)) unlink($path);
        }
        return self::db()
            ->prepare('DELETE FROM arquivos WHERE id = ?')
            ->execute([$id]);
    }
}
