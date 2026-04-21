<?php
class Projeto
{
    private static function db(): PDO
    {
        return Database::getInstance();
    }

    // ── Listagem com filtros e paginação ──────────────────────

    public static function findAll(array $f = [], int $page = 1, int $perPage = 20): array
    {
        [$where, $params] = self::buildWhere($f);

        $order = match ($f['ordem'] ?? '') {
            'visualizacoes' => 'p.visualizacoes DESC',
            default         => 'p.criado_em DESC',
        };

        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, u.nome AS autor_nome, u.tipo AS autor_tipo,
                       u.foto_perfil AS autor_foto,
                       GROUP_CONCAT(DISTINCT t.nome ORDER BY t.nome SEPARATOR ',') AS tecnologias
                FROM projetos p
                JOIN usuarios u ON u.id = p.usuario_id
                LEFT JOIN projeto_tecnologias pt ON pt.projeto_id = p.id
                LEFT JOIN tecnologias t          ON t.id = pt.tecnologia_id
                {$where}
                GROUP BY p.id
                ORDER BY {$order}
                LIMIT ? OFFSET ?";

        $s = self::db()->prepare($sql);
        $s->execute([...$params, $perPage, $offset]);
        return $s->fetchAll();
    }

    public static function count(array $f = []): int
    {
        [$where, $params] = self::buildWhere($f);

        $sql = "SELECT COUNT(DISTINCT p.id)
                FROM projetos p
                JOIN usuarios u ON u.id = p.usuario_id
                LEFT JOIN projeto_tecnologias pt ON pt.projeto_id = p.id
                LEFT JOIN tecnologias t          ON t.id = pt.tecnologia_id
                {$where}";

        $s = self::db()->prepare($sql);
        $s->execute($params);
        return (int) $s->fetchColumn();
    }

    private static function buildWhere(array $f): array
    {
        $where  = ['p.publicado = 1'];
        $params = [];

        if (!empty($f['busca'])) {
            $where[]  = '(p.titulo LIKE ? OR p.descricao LIKE ?)';
            $like     = '%' . $f['busca'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($f['area'])) {
            $where[]  = 'p.area = ?';
            $params[] = $f['area'];
        }
        if (!empty($f['status'])) {
            $where[]  = 'p.status = ?';
            $params[] = $f['status'];
        }
        if (!empty($f['usuario_id'])) {
            $where[]  = 'p.usuario_id = ?';
            $params[] = (int) $f['usuario_id'];
        }
        if (!empty($f['tecnologia'])) {
            $where[]  = 'EXISTS (
                SELECT 1 FROM projeto_tecnologias pt2
                JOIN tecnologias t2 ON t2.id = pt2.tecnologia_id
                WHERE pt2.projeto_id = p.id AND t2.nome = ?)';
            $params[] = $f['tecnologia'];
        }

        return ['WHERE ' . implode(' AND ', $where), $params];
    }

    // ── Busca individual ──────────────────────────────────────

    public static function find(int $id): ?array
    {
        $s = self::db()->prepare(
            "SELECT p.*, u.nome AS autor_nome, u.tipo AS autor_tipo,
                    u.foto_perfil AS autor_foto, u.linkedin AS autor_linkedin,
                    u.github AS autor_github, u.curso AS autor_curso, u.bio AS autor_bio,
                    GROUP_CONCAT(DISTINCT t.nome ORDER BY t.nome SEPARATOR ',') AS tecnologias
             FROM projetos p
             JOIN usuarios u ON u.id = p.usuario_id
             LEFT JOIN projeto_tecnologias pt ON pt.projeto_id = p.id
             LEFT JOIN tecnologias t          ON t.id = pt.tecnologia_id
             WHERE p.id = ? AND p.publicado = 1
             GROUP BY p.id"
        );
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public static function findByUsuario(int $uid): array
    {
        $s = self::db()->prepare(
            "SELECT p.*, GROUP_CONCAT(DISTINCT t.nome ORDER BY t.nome SEPARATOR ',') AS tecnologias
             FROM projetos p
             LEFT JOIN projeto_tecnologias pt ON pt.projeto_id = p.id
             LEFT JOIN tecnologias t          ON t.id = pt.tecnologia_id
             WHERE p.usuario_id = ? AND p.publicado = 1
             GROUP BY p.id
             ORDER BY p.criado_em DESC"
        );
        $s->execute([$uid]);
        return $s->fetchAll();
    }

    // ── CRUD ──────────────────────────────────────────────────

    public static function create(array $data): int
    {
        $s = self::db()->prepare(
            'INSERT INTO projetos (usuario_id, titulo, descricao, area, status, imagem_capa, repositorio)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $s->execute([
            $data['usuario_id'],
            trim($data['titulo']),
            trim($data['descricao']),
            $data['area']        ?: null,
            $data['status'],
            $data['imagem_capa'] ?: null,
            $data['repositorio'] ?: null,
        ]);
        $id = (int) self::db()->lastInsertId();

        if (!empty($data['tecnologias'])) {
            self::syncTecnologias($id, $data['tecnologias']);
        }

        return $id;
    }

    public static function update(int $id, array $data): bool
    {
        $fields = ['titulo = ?', 'descricao = ?', 'area = ?', 'status = ?', 'repositorio = ?'];
        $values = [
            trim($data['titulo']),
            trim($data['descricao']),
            $data['area']        ?: null,
            $data['status'],
            $data['repositorio'] ?: null,
        ];

        if (!empty($data['imagem_capa'])) {
            $fields[] = 'imagem_capa = ?';
            $values[] = $data['imagem_capa'];
        }

        $values[] = $id;
        $ok = self::db()
            ->prepare('UPDATE projetos SET ' . implode(', ', $fields) . ' WHERE id = ?')
            ->execute($values);

        if ($ok && array_key_exists('tecnologias', $data)) {
            self::syncTecnologias($id, $data['tecnologias']);
        }

        return $ok;
    }

    public static function delete(int $id): bool
    {
        return self::db()
            ->prepare('UPDATE projetos SET publicado = 0 WHERE id = ?')
            ->execute([$id]);
    }

    public static function incrementViews(int $id): void
    {
        self::db()
            ->prepare('UPDATE projetos SET visualizacoes = visualizacoes + 1 WHERE id = ?')
            ->execute([$id]);
    }

    // ── Tecnologias ───────────────────────────────────────────

    private static function syncTecnologias(int $projetoId, array $tags): void
    {
        $db = self::db();
        $db->prepare('DELETE FROM projeto_tecnologias WHERE projeto_id = ?')->execute([$projetoId]);

        foreach (array_unique(array_filter(array_map('trim', $tags))) as $nome) {
            $db->prepare('INSERT IGNORE INTO tecnologias (nome) VALUES (?)')->execute([$nome]);
            $tecId = $db->prepare('SELECT id FROM tecnologias WHERE nome = ?');
            $tecId->execute([$nome]);
            $tecId = $tecId->fetchColumn();
            if ($tecId) {
                $db->prepare('INSERT IGNORE INTO projeto_tecnologias (projeto_id, tecnologia_id) VALUES (?,?)')
                   ->execute([$projetoId, $tecId]);
            }
        }
    }

    // ── Metadados ─────────────────────────────────────────────

    public static function getAllAreas(): array
    {
        return self::db()
            ->query('SELECT DISTINCT area FROM projetos WHERE area IS NOT NULL AND publicado = 1 ORDER BY area')
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getAllTecnologias(): array
    {
        return self::db()
            ->query('SELECT nome FROM tecnologias ORDER BY nome')
            ->fetchAll(PDO::FETCH_COLUMN);
    }
}
