# Guia de Prints para o TCC

> Siga os passos em ordem. Para cada passo há: o que fazer, o que vai aparecer na tela e a sugestão de legenda para o documento.

---

## Fluxo 1 — Cadastro de usuário

### Print 1 — Tela de cadastro (vazia)

**O que fazer:** Acesse `http://localhost/tcc/cadastro`

**O que aparece:** Formulário com os campos Nome completo, E-mail, Senha, Tipo de usuário (Aluno / Professor) e Curso.

**Legenda sugerida:**
> *Figura X — Tela de cadastro da plataforma, com seleção de perfil entre Aluno e Professor.*

---

### Print 2 — Erro de validação no cadastro

**O que fazer:** Tente enviar o formulário deixando o campo de e-mail vazio ou com um e-mail inválido e clique em Cadastrar.

**O que aparece:** A mesma tela de cadastro com uma caixa de alerta vermelha listando os erros. Os campos preenchidos corretamente permanecem com seus valores (o sistema não limpa o formulário).

**Legenda sugerida:**
> *Figura X — Validação de formulário: mensagens de erro exibidas sem perder os dados já digitados.*

---

## Fluxo 2 — Login e sessão

### Print 3 — Tela de login

**O que fazer:** Acesse `http://localhost/tcc/login`

**O que aparece:** Formulário com campos de e-mail e senha, link "Esqueceu a senha?" e link para a tela de cadastro.

**Legenda sugerida:**
> *Figura X — Tela de login com acesso à recuperação de senha.*

---

### Print 4 — Redirecionamento após login (home logado)

**O que fazer:** Faça login com um usuário válido.

**O que aparece:** A página inicial com o nome do usuário visível no menu superior (canto direito), confirmando que a sessão foi iniciada. O botão "Publicar projeto" aparece no menu.

**Legenda sugerida:**
> *Figura X — Interface após autenticação: nome do usuário e opções exclusivas de conta exibidos no cabeçalho.*

---

## Fluxo 3 — Página inicial e listagem

### Print 5 — Página inicial sem filtros

**O que fazer:** Acesse `http://localhost/tcc` ou `http://localhost/tcc/projetos`

**O que aparece:** Barra de busca no topo, coluna lateral com filtros (Ordenar por, Status, Área, Tecnologia) e grade de cartões de projetos com título, área, status, nome do autor e data.

**Legenda sugerida:**
> *Figura X — Página inicial: listagem de projetos com barra de busca e painel de filtros.*

---

### Print 6 — Busca por palavra-chave

**O que fazer:** Digite uma palavra na barra de busca (ex: "sistema") e pressione Buscar.

**O que aparece:** A lista é atualizada mostrando apenas projetos com aquela palavra no título ou descrição. A URL muda para `/projetos?busca=sistema`.

**Legenda sugerida:**
> *Figura X — Resultado de busca por palavra-chave: a URL reflete os parâmetros do filtro ativo.*

---

### Print 7 — Filtro por área ou tecnologia

**O que fazer:** Na coluna lateral, selecione uma área ou tecnologia no menu suspenso.

**O que aparece:** A lista é filtrada automaticamente (sem clicar em botão). Vários filtros podem estar ativos ao mesmo tempo — todos aparecem na URL.

**Legenda sugerida:**
> *Figura X — Filtragem combinada por área e tecnologia aplicada via query string.*

---

## Fluxo 4 — Publicar projeto

### Print 8 — Formulário de novo projeto (vazio)

**O que fazer:** Clique em "Publicar projeto" no menu ou acesse `http://localhost/tcc/projeto/novo`

**O que aparece:** Formulário com campos de Título, Status, Área, Descrição, imagem de capa, campo de tags de tecnologias (com autocomplete), link do repositório e upload de arquivos anexos.

**Legenda sugerida:**
> *Figura X — Formulário de publicação de projeto com campo de tags e upload de arquivos.*

---

### Print 9 — Formulário preenchido antes de enviar

**O que fazer:** Preencha todos os campos com dados de um projeto fictício. Adicione pelo menos duas tecnologias na área de tags. Não envie ainda.

**O que aparece:** Formulário completo, com tags aparecendo como chips coloridos abaixo do campo e, se houver upload de capa, uma miniatura da imagem.

**Legenda sugerida:**
> *Figura X — Formulário preenchido: tags de tecnologia exibidas como chips e prévia da imagem de capa.*

---

### Print 10 — Projeto criado (página de detalhe)

**O que fazer:** Envie o formulário.

**O que aparece:** Redirecionamento automático para a página do projeto recém-criado, com uma mensagem de sucesso verde no topo ("Projeto publicado com sucesso!"), a imagem de capa, o título, status, descrição e as tecnologias como links.

**Legenda sugerida:**
> *Figura X — Página do projeto após publicação: mensagem de confirmação e exibição completa dos dados.*

---

## Fluxo 5 — Visualizar projeto

### Print 11 — Página de detalhe de projeto

**O que fazer:** Acesse qualquer projeto clicando no seu cartão na listagem.

**O que aparece:**
- Imagem de capa (se houver)
- Título, badges de status e área
- Contador de visualizações e data relativa ("há 2 dias")
- Descrição completa
- Tags de tecnologias clicáveis
- Link do repositório (se houver)
- Coluna lateral com informações do autor (nome, tipo, curso, bio, links)
- Lista de arquivos para download
- Seção de comentários

**Legenda sugerida:**
> *Figura X — Página de detalhe do projeto: informações completas, autor, arquivos anexos e seção de comentários.*

---

### Print 12 — Botões de editar/deletar (visível apenas para o autor)

**O que fazer:** Acesse um projeto que pertence ao usuário logado.

**O que aparece:** Abaixo do título aparece uma barra com os botões "Editar" e "Deletar". Esses botões não aparecem para outros usuários que visualizem o mesmo projeto.

**Legenda sugerida:**
> *Figura X — Controle de autorização: ações de edição e exclusão visíveis apenas para o autor do projeto.*

---

## Fluxo 6 — Comentários

### Print 13 — Envio de comentário

**O que fazer:** No rodapé de um projeto, escreva um comentário e clique em "Comentar".

**O que aparece:** A página recarrega e o comentário aparece na lista com nome do autor, avatar com iniciais e data relativa.

**Legenda sugerida:**
> *Figura X — Sistema de comentários: exibição com avatar gerado por iniciais e data relativa.*

---

### Print 14 — Exclusão de comentário (professor)

**O que fazer:** Faça login como Professor e acesse um projeto com comentários de outros usuários.

**O que aparece:** Todos os comentários têm um botão de excluir disponível para o professor. Para alunos, o botão de excluir aparece apenas nos próprios comentários.

**Legenda sugerida:**
> *Figura X — Controle de acesso por papel: professores podem moderar comentários de qualquer usuário.*

---

## Fluxo 7 — Editar projeto

### Print 15 — Formulário de edição

**O que fazer:** No seu projeto, clique em "Editar".

**O que aparece:** O mesmo formulário de criação, mas com todos os campos já preenchidos com os valores atuais do projeto. Tags e imagem atual são exibidos.

**Legenda sugerida:**
> *Figura X — Formulário de edição de projeto com dados pré-carregados.*

---

## Fluxo 8 — Perfil de usuário

### Print 16 — Perfil próprio

**O que fazer:** Clique no nome do usuário no menu e acesse "Meu Perfil", ou vá em `http://localhost/tcc/perfil/{id}`.

**O que aparece:** Avatar com iniciais ou foto, nome, tipo (Aluno/Professor), curso, bio, links do LinkedIn e GitHub, e a grade de projetos publicados pelo usuário.

**Legenda sugerida:**
> *Figura X — Página de perfil do usuário com projetos publicados.*

---

### Print 17 — Editar perfil

**O que fazer:** Acesse `http://localhost/tcc/perfil/editar`.

**O que aparece:** Formulário com campos de nome, bio, curso, LinkedIn, GitHub e upload de foto de perfil. Uma prévia da foto atual é exibida.

**Legenda sugerida:**
> *Figura X — Tela de edição de perfil com prévia da foto atual.*

---

## Fluxo 9 — Recuperação de senha

### Print 18 — Solicitar recuperação

**O que fazer:** Acesse `http://localhost/tcc/recuperar-senha`.

**O que aparece:** Formulário com apenas o campo de e-mail. Ao enviar, aparece uma mensagem informando que as instruções foram enviadas (independente de o e-mail existir ou não — por segurança).

**Legenda sugerida:**
> *Figura X — Recuperação de senha: resposta genérica para não revelar quais e-mails estão cadastrados.*

---

## Resumo — Ordem de prints no documento

| Nº | Tela | Fluxo que demonstra |
|---|---|---|
| 1 | Tela de cadastro | Entrada de novos usuários |
| 2 | Erros de validação | Feedback de formulário |
| 3 | Tela de login | Autenticação |
| 4 | Home logado | Sessão ativa |
| 5 | Listagem de projetos | Tela principal |
| 6 | Resultado de busca | Busca por texto |
| 7 | Filtro por área | Filtros combinados |
| 8 | Formulário de projeto | Publicação |
| 9 | Formulário preenchido | Tags e upload |
| 10 | Projeto criado | Fluxo completo de criação |
| 11 | Detalhe do projeto | Visualização |
| 12 | Botões autor | Controle de autorização |
| 13 | Comentário enviado | Interação |
| 14 | Moderação (professor) | Papéis de usuário |
| 15 | Editar projeto | Atualização |
| 16 | Perfil do usuário | Identidade |
| 17 | Editar perfil | Configuração de conta |
| 18 | Recuperar senha | Segurança |
