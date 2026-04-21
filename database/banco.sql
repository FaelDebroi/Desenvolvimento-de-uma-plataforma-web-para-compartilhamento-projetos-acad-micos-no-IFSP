-- ============================================================
-- Plataforma de Compartilhamento de Projetos Acadêmicos IFSP
-- Banco de Dados MySQL 8.x
-- ============================================================

CREATE DATABASE IF NOT EXISTS tcc_ifsp
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE tcc_ifsp;

-- ------------------------------------------------------------
-- USUÁRIOS (alunos e professores)
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(120)        NOT NULL,
    email         VARCHAR(180)        NOT NULL UNIQUE,
    senha         VARCHAR(255)        NOT NULL,           -- bcrypt hash
    tipo          ENUM('aluno','professor') NOT NULL,
    curso         VARCHAR(120)        NOT NULL,
    bio           TEXT                DEFAULT NULL,
    foto_perfil   VARCHAR(255)        DEFAULT NULL,       -- caminho relativo ao upload
    linkedin      VARCHAR(255)        DEFAULT NULL,
    github        VARCHAR(255)        DEFAULT NULL,
    ativo         TINYINT(1)          NOT NULL DEFAULT 1,
    criado_em     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                      ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- RECUPERAÇÃO DE SENHA
-- ------------------------------------------------------------
CREATE TABLE recuperacao_senha (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED  NOT NULL,
    token      VARCHAR(100)  NOT NULL UNIQUE,            -- token gerado com bin2hex(random_bytes)
    expira_em  DATETIME      NOT NULL,
    usado      TINYINT(1)    NOT NULL DEFAULT 0,
    criado_em  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_rec_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- PROJETOS
-- ------------------------------------------------------------
CREATE TABLE projetos (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id    INT UNSIGNED        NOT NULL,
    titulo        VARCHAR(200)        NOT NULL,
    descricao     TEXT                NOT NULL,
    area          VARCHAR(100)        DEFAULT NULL,       -- ex: Web, Mobile, IA, Hardware
    status        ENUM('em_desenvolvimento','beta','concluido') NOT NULL DEFAULT 'em_desenvolvimento',
    imagem_capa   VARCHAR(255)        DEFAULT NULL,
    repositorio   VARCHAR(255)        DEFAULT NULL,       -- link GitHub/GitLab
    visualizacoes INT UNSIGNED        NOT NULL DEFAULT 0,
    publicado     TINYINT(1)          NOT NULL DEFAULT 1,
    criado_em     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                      ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_proj_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    FULLTEXT INDEX ft_proj_busca (titulo, descricao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TECNOLOGIAS / TAGS
-- ------------------------------------------------------------
CREATE TABLE tecnologias (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de junção projeto <-> tecnologia (N:N)
CREATE TABLE projeto_tecnologias (
    projeto_id    INT UNSIGNED NOT NULL,
    tecnologia_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (projeto_id, tecnologia_id),

    CONSTRAINT fk_pt_projeto
        FOREIGN KEY (projeto_id) REFERENCES projetos(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_pt_tecnologia
        FOREIGN KEY (tecnologia_id) REFERENCES tecnologias(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ARQUIVOS ANEXADOS AOS PROJETOS
-- ------------------------------------------------------------
CREATE TABLE arquivos (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    projeto_id   INT UNSIGNED  NOT NULL,
    nome_original VARCHAR(255) NOT NULL,                 -- nome exibido ao usuário
    caminho      VARCHAR(255)  NOT NULL,                 -- caminho no servidor
    tipo_mime    VARCHAR(100)  NOT NULL,                 -- ex: application/pdf
    tamanho_kb   INT UNSIGNED  NOT NULL,
    criado_em    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_arq_projeto
        FOREIGN KEY (projeto_id) REFERENCES projetos(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- COMENTÁRIOS
-- ------------------------------------------------------------
CREATE TABLE comentarios (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    conteudo   TEXT         NOT NULL,
    criado_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    editado_em DATETIME     DEFAULT NULL,

    CONSTRAINT fk_com_projeto
        FOREIGN KEY (projeto_id) REFERENCES projetos(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_com_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ÍNDICES ADICIONAIS
-- ============================================================
CREATE INDEX idx_projetos_usuario   ON projetos(usuario_id);
CREATE INDEX idx_projetos_status    ON projetos(status);
CREATE INDEX idx_projetos_area      ON projetos(area);
CREATE INDEX idx_projetos_criado    ON projetos(criado_em DESC);
CREATE INDEX idx_comentarios_proj   ON comentarios(projeto_id);
CREATE INDEX idx_arquivos_proj      ON arquivos(projeto_id);
CREATE INDEX idx_rec_token          ON recuperacao_senha(token);

-- ============================================================
-- DADOS INICIAIS (seeds)
-- ============================================================

-- Tecnologias pré-cadastradas
INSERT INTO tecnologias (nome) VALUES
    ('PHP'), ('MySQL'), ('JavaScript'), ('HTML5'), ('CSS3'),
    ('Python'), ('Java'), ('C'), ('C++'), ('C#'),
    ('React'), ('Vue.js'), ('Angular'), ('Node.js'), ('Laravel'),
    ('Arduino'), ('Raspberry Pi'), ('Machine Learning'),
    ('Android'), ('Flutter'), ('Docker'), ('Git');

-- Usuário administrador / professor de exemplo
-- Senha: Admin@123  (hash gerado com password_hash no PHP)
INSERT INTO usuarios (nome, email, senha, tipo, curso) VALUES
    ('Administrador IFSP', 'admin@ifsp.edu.br',
     '$2y$12$examplehashplaceholderXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
     'professor', 'Tecnologia em Análise e Desenvolvimento de Sistemas');
