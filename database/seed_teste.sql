USE tcc_ifsp;

-- Professor — Senha: Prof@123
INSERT INTO usuarios (nome, email, senha, tipo, curso, bio, linkedin, github)
VALUES ('Prof. Rafael Souza', 'professor@teste.com',
        '$2y$12$txs/LBUS1DKGli1uwkCxqul/ygn2t5Nu9ZgI/NBVYDm5c/02XBMYa',
        'professor', 'Tecnologia em Análise e Desenvolvimento de Sistemas',
        'Professor de Engenharia de Software e Sistemas Web no IFSP.',
        'https://linkedin.com/in/rafael-souza', 'https://github.com/rafaelsouza')
ON DUPLICATE KEY UPDATE senha = VALUES(senha), nome = VALUES(nome);

-- Aluno — Senha: Aluno@123
INSERT INTO usuarios (nome, email, senha, tipo, curso, bio, linkedin, github)
VALUES ('Ana Clara Lima', 'aluno@teste.com',
        '$2y$12$iWmIF.6bYjViGX.egJKD7.HPowBHba1XMH0gR6ng3CzzcR/J2Fh7O',
        'aluno', 'Tecnologia em Análise e Desenvolvimento de Sistemas',
        'Estudante de ADS apaixonada por desenvolvimento web e machine learning.',
        'https://linkedin.com/in/ana-clara-lima', 'https://github.com/anaclaralima')
ON DUPLICATE KEY UPDATE senha = VALUES(senha), nome = VALUES(nome);

-- IDs dos usuários
SET @id_prof  = (SELECT id FROM usuarios WHERE email = 'professor@teste.com');
SET @id_aluno = (SELECT id FROM usuarios WHERE email = 'aluno@teste.com');

-- Projeto 1
INSERT INTO projetos (usuario_id, titulo, descricao, area, status, repositorio, visualizacoes, publicado)
VALUES (@id_aluno, 'Plataforma de E-commerce com PHP e MySQL',
        'Sistema completo de loja virtual desenvolvido como projeto integrador do 4º semestre. Conta com catálogo de produtos, carrinho de compras, autenticação de usuários e painel administrativo. Arquitetura MVC sem frameworks, feita do zero com PHP puro.',
        'Web', 'concluido', 'https://github.com/anaclaralima/ecommerce-php', 142, 1);
SET @p1 = LAST_INSERT_ID();
INSERT IGNORE INTO projeto_tecnologias VALUES (@p1,1),(@p1,2),(@p1,3),(@p1,4),(@p1,5);

-- Projeto 2
INSERT INTO projetos (usuario_id, titulo, descricao, area, status, repositorio, visualizacoes, publicado)
VALUES (@id_aluno, 'App de Gestão de Tarefas com Flutter',
        'Aplicativo mobile multiplataforma para gerenciamento de tarefas pessoais e em equipe. Possui autenticação via Google, sincronização em tempo real com Firebase e modo offline com SQLite local. Desenvolvido como TCC.',
        'Mobile', 'em_desenvolvimento', 'https://github.com/anaclaralima/taskmanager-flutter', 87, 1);
SET @p2 = LAST_INSERT_ID();
INSERT IGNORE INTO projeto_tecnologias VALUES (@p2,20),(@p2,19);

-- Projeto 3
INSERT INTO projetos (usuario_id, titulo, descricao, area, status, repositorio, visualizacoes, publicado)
VALUES (@id_prof, 'Classificador de Imagens com Machine Learning',
        'Modelo de rede neural convolucional (CNN) treinado com TensorFlow/Keras para classificação de plantas doentes em lavouras. Dataset com 15.000 imagens e acurácia de 94% no conjunto de validação. Parceria com o curso de Agronomia.',
        'IA', 'concluido', 'https://github.com/rafaelsouza/plant-disease-cnn', 310, 1);
SET @p3 = LAST_INSERT_ID();
INSERT IGNORE INTO projeto_tecnologias VALUES (@p3,6),(@p3,18);

-- Projeto 4
INSERT INTO projetos (usuario_id, titulo, descricao, area, status, repositorio, visualizacoes, publicado)
VALUES (@id_prof, 'Estação de Monitoramento Ambiental com Arduino',
        'Dispositivo IoT baseado em Arduino que monitora temperatura, umidade, qualidade do ar e luminosidade. Dados enviados via MQTT para servidor Node.js e exibidos em dashboard com Chart.js. Protótipo em fase beta.',
        'Hardware', 'beta', 'https://github.com/rafaelsouza/arduino-env-monitor', 195, 1);
SET @p4 = LAST_INSERT_ID();
INSERT IGNORE INTO projeto_tecnologias VALUES (@p4,16),(@p4,14),(@p4,8);

-- Confirmação
SELECT id, nome, email, tipo FROM usuarios WHERE email IN ('professor@teste.com','aluno@teste.com');
SELECT p.id, p.titulo, p.area, p.status, u.tipo as autor FROM projetos p JOIN usuarios u ON u.id = p.usuario_id ORDER BY p.id DESC LIMIT 4;
