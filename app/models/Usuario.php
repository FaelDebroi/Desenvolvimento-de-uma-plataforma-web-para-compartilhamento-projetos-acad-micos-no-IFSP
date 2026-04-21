<?php
class Usuario
{
    private static function db(): PDO
    {
        return Database::getInstance();
    }

    public static function find(int $id): ?array
    {
        $s = self::db()->prepare('SELECT * FROM usuarios WHERE id = ? AND ativo = 1');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $s = self::db()->prepare('SELECT * FROM usuarios WHERE email = ? AND ativo = 1');
        $s->execute([$email]);
        return $s->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $s = self::db()->prepare(
            'INSERT INTO usuarios (nome, email, senha, tipo, curso) VALUES (?, ?, ?, ?, ?)'
        );
        $s->execute([
            trim($data['nome']),
            strtolower(trim($data['email'])),
            password_hash($data['senha'], PASSWORD_BCRYPT, ['cost' => 12]),
            $data['tipo'],
            trim($data['curso']),
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $allowed = ['nome', 'curso', 'bio', 'foto_perfil', 'linkedin', 'github'];
        $fields  = [];
        $values  = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) return false;

        $values[] = $id;
        return self::db()
            ->prepare('UPDATE usuarios SET ' . implode(', ', $fields) . ' WHERE id = ?')
            ->execute($values);
    }

    public static function updatePassword(int $id, string $senha): bool
    {
        return self::db()
            ->prepare('UPDATE usuarios SET senha = ? WHERE id = ?')
            ->execute([password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }

    public static function emailExists(string $email, int $excludeId = 0): bool
    {
        $s = self::db()->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ?');
        $s->execute([$email, $excludeId]);
        return (bool) $s->fetch();
    }

    public static function verifyPassword(array $usuario, string $senha): bool
    {
        return password_verify($senha, $usuario['senha']);
    }
}
