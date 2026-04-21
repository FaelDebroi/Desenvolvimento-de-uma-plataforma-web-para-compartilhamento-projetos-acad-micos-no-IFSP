# IFSP Projetos — Plataforma de Compartilhamento de Projetos Acadêmicos

Plataforma web desenvolvida como TCC para o IFSP Campinas. Permite que alunos e professores publiquem, visualizem e interajam com projetos acadêmicos.

---

## Índice

1. [Tecnologias](#tecnologias)
2. [Estrutura de diretórios](#estrutura-de-diretórios)
3. [Arquitetura MVC](#arquitetura-mvc)
4. [Fluxo de uma requisição](#fluxo-de-uma-requisição)
5. [Banco de dados](#banco-de-dados)
6. [Rotas disponíveis](#rotas-disponíveis)
7. [Como iniciar o projeto](#como-iniciar-o-projeto)
8. [Configuração](#configuração)
9. [Segurança](#segurança)
10. [Upload de arquivos](#upload-de-arquivos)

---

## Tecnologias

| Camada     | Tecnologia                        |
|------------|-----------------------------------|
| Backend    | PHP 8.x                           |
| Banco      | MySQL 8.x                         |
| Frontend   | HTML5, CSS3, JavaScript (ES6+)    |
| Servidor   | Apache (XAMPP)                    |
| Arquitetura| MVC sem framework, sem Composer   |

---

## Estrutura de diretórios

```
tcc/
│
├── .htaccess                  # Redireciona todas as rotas para index.php
├── index.php                  # Bootstrap: carrega dependências e dispara o roteador
├── helpers.php                # Funções utilitárias globais
│
├── config/
│   └── config.php             # Constantes: banco, URL base, limites de upload, e-mail
│
├── core/                      # Núcleo do framework caseiro
│   ├── Database.php           # Singleton PDO — conexão única com o MySQL
│   ├── Router.php             # Roteador — mapeia URL + método HTTP para controllers
│   └── Controller.php         # Classe base dos controllers (view, redirect, flash, CSRF)
│
├── app/
│   ├── models/                # Camada de dados (SQL puro com PDO)
│   │   ├── Usuario.php
│   │   ├── Projeto.php
│   │   ├── Comentario.php
│   │   ├── Arquivo.php
│   │   └── RecuperacaoSenha.php
│   │
│   ├── controllers/           # Lógica de negócio e controle de fluxo
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── ProjetoController.php
│   │   └── PerfilController.php
│   │
│   └── views/                 # Templates HTML (PHP puro)
│       ├── layout/
│       │   ├── header.php     # Navbar, meta tags, CSS
│       │   └── footer.php     # Scripts JS, fechamento do HTML
│       ├── auth/
│       │   ├── login.php
│       │   ├── cadastro.php
│       │   ├── recuperar.php
│       │   └── redefinir.php
│       ├── home/
│       │   └── index.php      # Listagem pública de projetos com filtros
│       ├── projetos/
│       │   ├── show.php       # Página do projeto com comentários e arquivos
│       │   ├── create.php     # Formulário de criação
│       │   └── edit.php       # Formulário de edição
│       ├── perfil/
│       │   ├── show.php       # Perfil público do usuário
│       │   └── edit.php       # Edição de perfil e senha
│       └── errors/
│           └── 404.php
│
├── public/
│   ├── css/
│   │   └── style.css          # Design system completo (variáveis, componentes, responsivo)
│   ├── js/
│   │   └── main.js            # Nav mobile, dropdowns, previews de upload, modal
│   └── uploads/
│       ├── .htaccess          # Bloqueia execução de PHP na pasta de uploads
│       ├── fotos/             # Fotos de perfil dos usuários
│       ├── capas/             # Imagens de capa dos projetos
│       └── arquivos/          # Arquivos anexados aos projetos (PDF, DOCX, etc.)
│
└── database/
    └── banco.sql              # Script de criação do banco + seeds iniciais
```

---

## Arquitetura MVC

O projeto segue o padrão **Model-View-Controller** sem uso de frameworks externos.

### Model
Cada model é uma classe PHP estática que encapsula as queries SQL do seu domínio.
- Utiliza **PDO com prepared statements** em todas as operações.
- Não contém lógica de apresentação.
- Exemplo: `Projeto::findAll($filtros, $page, $perPage)` retorna array com dados paginados.

### View
Templates PHP simples, sem lógica de negócio.
- Todo output de variáveis usa `h()` (wrapper de `htmlspecialchars`) para prevenir XSS.
- Cada view inclui `header.php` no topo e `footer.php` no final.
- Recebe dados exclusivamente pelo `extract()` chamado em `Controller::view()`.

### Controller
Orquestra a requisição: valida entrada, chama models, passa dados para a view ou redireciona.
- Herdam de `Controller` (classe base em `core/Controller.php`).
- Métodos disponíveis na base:
  - `view(string $view, array $data)` — renderiza uma view passando variáveis
  - `redirect(string $path)` — redireciona para uma rota relativa
  - `requireAuth()` — exige sessão ativa, redireciona para login se não houver
  - `requireGuest()` — exige que não haja sessão (usado nas telas de auth)
  - `flash(string $type, string $message)` — armazena mensagem na sessão
  - `csrfToken()` / `verifyCsrf()` — geração e validação do token CSRF
  - `currentUser()` — retorna dados do usuário logado via sessão

### Router
O `Router` mapeia pares `(método HTTP, padrão de URL)` para `(Controller, action)`.
- Suporta parâmetros dinâmicos: `projeto/{id}` captura o `id` e passa para o método.
- O `.htaccess` redireciona tudo para `index.php`, que instancia o Router e chama `dispatch()`.

---

## Fluxo de uma requisição

```
Browser → Apache
         ↓ .htaccess redireciona para index.php
         ↓ index.php carrega config, core, helpers, models e controllers
         ↓ Router::dispatch() analisa REQUEST_URI e REQUEST_METHOD
         ↓ Instancia o Controller correto e chama o método (action)
         ↓ Action valida dados, chama Models (queries PDO)
         ↓ Chama Controller::view() → extrai variáveis → inclui a view PHP
         ↓ View inclui header.php + conteúdo + footer.php
         → HTML final retornado ao browser
```

---

## Banco de dados

### Tabelas

| Tabela                | Descrição                                          |
|-----------------------|----------------------------------------------------|
| `usuarios`            | Alunos e professores (foto, bio, redes sociais)    |
| `projetos`            | Projetos com status, área, capa, repositório, views|
| `tecnologias`         | Tags de tecnologia (ex: PHP, React, Arduino)       |
| `projeto_tecnologias` | Relação N:N entre projetos e tecnologias           |
| `comentarios`         | Comentários de usuários nos projetos               |
| `arquivos`            | Arquivos anexados (PDF, DOCX, ZIP, etc.)           |
| `recuperacao_senha`   | Tokens com expiração de 1h para reset de senha     |

### Relacionamentos

```
usuarios ──< projetos ──< comentarios >── usuarios
                      ──< arquivos
                      >─< tecnologias (via projeto_tecnologias)
usuarios ──< recuperacao_senha
```

---

## Rotas disponíveis

### Públicas

| Método | URL                          | Ação                                      |
|--------|------------------------------|-------------------------------------------|
| GET    | `/`                          | Lista todos os projetos (home)            |
| GET    | `/projetos`                  | Mesma listagem com suporte a filtros      |
| GET    | `/projeto/{id}`              | Página detalhada de um projeto            |
| GET    | `/perfil/{id}`               | Perfil público de um usuário              |

### Autenticação

| Método | URL                          | Ação                                      |
|--------|------------------------------|-------------------------------------------|
| GET    | `/login`                     | Formulário de login                       |
| POST   | `/login`                     | Processar login                           |
| GET    | `/cadastro`                  | Formulário de cadastro                    |
| POST   | `/cadastro`                  | Processar cadastro                        |
| GET    | `/logout`                    | Encerrar sessão                           |
| GET    | `/recuperar-senha`           | Formulário de recuperação                 |
| POST   | `/recuperar-senha`           | Enviar e-mail com token                   |
| GET    | `/redefinir-senha?token=xxx` | Formulário de nova senha                  |
| POST   | `/redefinir-senha`           | Processar nova senha                      |

### Projetos (requer login)

| Método | URL                          | Ação                                      |
|--------|------------------------------|-------------------------------------------|
| GET    | `/projeto/novo`              | Formulário de criação                     |
| POST   | `/projeto/novo`              | Salvar novo projeto                       |
| GET    | `/projeto/{id}/editar`       | Formulário de edição (só o autor)         |
| POST   | `/projeto/{id}/editar`       | Salvar edições (só o autor)               |
| POST   | `/projeto/{id}/deletar`      | Remover projeto (só o autor)              |
| POST   | `/projeto/{id}/comentario`   | Adicionar comentário                      |
| POST   | `/comentario/{id}/deletar`   | Remover comentário (autor ou professor)   |

### Perfil (requer login)

| Método | URL                          | Ação                                      |
|--------|------------------------------|-------------------------------------------|
| GET    | `/perfil/editar`             | Formulário de edição do perfil            |
| POST   | `/perfil/editar`             | Salvar alterações do perfil               |

### Parâmetros de busca (GET /projetos)

| Parâmetro    | Descrição                                 |
|--------------|-------------------------------------------|
| `busca`      | Texto livre (título ou descrição)         |
| `area`       | Filtro por área (ex: Web, Mobile)         |
| `status`     | `em_desenvolvimento`, `beta`, `concluido` |
| `tecnologia` | Nome de tecnologia (ex: PHP)              |
| `ordem`      | `visualizacoes` ou vazio (mais recentes)  |
| `pagina`     | Número da página (20 projetos por página) |

---

## Como iniciar o projeto

### Pré-requisitos

- [XAMPP](https://www.apachefriends.org/) com Apache e MySQL ativos
- PHP 8.0 ou superior
- `mod_rewrite` habilitado no Apache

### Passo a passo

**1. Clonar / copiar os arquivos**
```
Copie a pasta tcc/ para: C:\xampp\htdocs\tcc\
```

**2. Importar o banco de dados**

Abra o phpMyAdmin em `http://localhost/phpmyadmin`, clique em **Importar** e selecione:
```
tcc/database/banco.sql
```
Ou via terminal:
```bash
mysql -u root -p < database/banco.sql
```

**3. Ajustar configurações (se necessário)**

Edite `config/config.php` conforme seu ambiente:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'tcc_ifsp');
define('DB_USER', 'root');
define('DB_PASS', '');           // senha do MySQL no XAMPP (padrão vazio)

define('BASE_URL',  'http://localhost/tcc');
define('BASE_PATH', '/tcc');
```

**4. Configurar limites de upload no PHP**

Edite `C:\xampp\php\php.ini`:
```ini
upload_max_filesize = 200M
post_max_size       = 210M
max_execution_time  = 300
max_input_time      = 300
```
Reinicie o Apache no painel do XAMPP após salvar.

**5. Habilitar mod_rewrite**

Edite `C:\xampp\apache\conf\httpd.conf` e certifique-se de que a linha abaixo **não** está comentada:
```
LoadModule rewrite_module modules/mod_rewrite.so
```

Também verifique que o bloco do diretório `htdocs` tem `AllowOverride All`:
```apache
<Directory "C:/xampp/htdocs">
    AllowOverride All
    ...
</Directory>
```

**6. Acessar a plataforma**
```
http://localhost/tcc
```

---

## Configuração

Todas as constantes ficam em `config/config.php`:

| Constante            | Padrão                        | Descrição                          |
|----------------------|-------------------------------|------------------------------------|
| `DB_HOST`            | `localhost`                   | Host do MySQL                      |
| `DB_NAME`            | `tcc_ifsp`                    | Nome do banco                      |
| `DB_USER`            | `root`                        | Usuário do MySQL                   |
| `DB_PASS`            | _(vazio)_                     | Senha do MySQL                     |
| `BASE_URL`           | `http://localhost/tcc`        | URL base da aplicação              |
| `BASE_PATH`          | `/tcc`                        | Caminho base para o roteador       |
| `MAX_IMAGE_SIZE`     | `200 MB`                      | Limite de imagens de capa e perfil |
| `MAX_FILE_SIZE`      | `200 MB`                      | Limite de arquivos anexados        |
| `MAIL_FROM`          | `noreply@ifsp.edu.br`         | Remetente dos e-mails              |

---

## Segurança

| Proteção              | Implementação                                                     |
|-----------------------|-------------------------------------------------------------------|
| SQL Injection         | PDO com prepared statements em todas as queries                   |
| XSS                   | Função `h()` (`htmlspecialchars`) em todo output nas views        |
| CSRF                  | Token por sessão gerado em `csrfToken()`, validado em `verifyCsrf()` |
| Senhas                | `password_hash()` com bcrypt (custo 12) + `password_verify()`    |
| Upload malicioso      | Validação de MIME real via `finfo`, nome gerado aleatoriamente    |
| Execução em uploads   | `.htaccess` em `public/uploads/` desabilita PHP e CGI            |
| Autorização           | Autor do projeto verificado antes de editar/deletar               |
| Sessões               | `session_start()` centralizado no `index.php`                     |
| Token de recuperação  | 64 chars hex aleatórios com expiração de 1 hora                   |

---

## Upload de arquivos

Os arquivos são salvos em `public/uploads/` organizados por tipo:

| Subpasta    | Conteúdo                         | Tipos aceitos                      |
|-------------|----------------------------------|------------------------------------|
| `fotos/`    | Fotos de perfil dos usuários     | JPEG, PNG, GIF, WebP               |
| `capas/`    | Imagens de capa dos projetos     | JPEG, PNG, GIF, WebP               |
| `arquivos/` | Anexos dos projetos              | PDF, DOC, DOCX, PPT, PPTX, ZIP, TXT |

O nome original do arquivo **nunca é usado** no servidor — um nome hexadecimal aleatório é gerado via `bin2hex(random_bytes(16))` para evitar colisões e ataques de path traversal.

---

## Helpers disponíveis (`helpers.php`)

| Função                              | Uso                                                    |
|-------------------------------------|--------------------------------------------------------|
| `h($str)`                           | Escapa HTML — usar em todo output nas views            |
| `url($path)`                        | Gera URL absoluta a partir da raiz da aplicação        |
| `asset($path)`                      | URL para arquivos em `public/`                         |
| `upload_url($path)`                 | URL para arquivos em `public/uploads/`                 |
| `time_ago($datetime)`               | "3 dias atrás", "agora mesmo"...                       |
| `status_label($status)`             | Rótulo legível do status do projeto                    |
| `status_class($status)`             | Classe CSS do badge de status                          |
| `tipo_label($tipo)`                 | "Aluno" ou "Professor"                                 |
| `format_bytes($bytes)`              | "2.3 MB", "512 KB"                                     |
| `paginate($total, $perPage, $page)` | Retorna array com dados de paginação                   |
| `upload_file($file, $dir, ...)`     | Valida e move um arquivo enviado, retorna o caminho    |
| `csrf_field($token)`                | Retorna `<input type="hidden">` com o token CSRF       |
| `avatar_initials($nome)`            | Iniciais do nome para avatar (ex: "Rafael Silva" → RS) |
