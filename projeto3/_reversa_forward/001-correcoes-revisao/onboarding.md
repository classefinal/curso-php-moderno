# Onboarding: Correções da Revisão (P1–P14)

> Identificador: `001-correcoes-revisao`
> Data: `2026-08-13`
> Objetivo: passo a passo para um humano validar cada correção sem conhecimento prévio do projeto.

## Pré-requisitos

- PHP 8.1+ (ambiente do projeto: 8.5), MySQL/MariaDB acessível, Apache ou `php -S`.
- Banco vazio disponível para o teste de migrations (pode ser um banco descartável).
- Credenciais do banco no `.env` (copiar de `.env.example`).

## Passo 1 — Migrations em banco limpo (RF-01, RF-02)

1. Crie um banco vazio (ex.: `projeto3_clean`).
2. Ajuste `.env` para apontar para esse banco.
3. Rode `php migrate.php`.
4. Esperado: 10 migrations aplicam **sem erro**.
5. Confira o schema:
   - `DESCRIBE users;` → deve listar `email` e `password`.
   - `SELECT email FROM users WHERE id = 1;` → deve retornar `admin@admin.com` (seed funciona).
   - `DESCRIBE products;` → deve listar `short_description` e `description_line`.

## Passo 2 — HTTP 404 (RF-03)

1. Suba o servidor (ex.: `php -S localhost:8000 -t public`).
2. Acesse uma URI inexistente, ex.: `http://localhost:8000/pagina-que-nao-existe`.
3. Esperado: página "não encontrado" com status **404** (ferramenta de desenvolvedor/`curl -i`).

## Passo 3 — Sessão sem hash (RF-04) e sucesso sem string morta (RF-05)

1. Faça login de usuário (`/login`) com credenciais válidas.
2. Inspecione o cookie de sessão (ou `var_dump($_SESSION['user'])` temporário): **não** deve conter `password`.
3. Repita para `/admin/login` com `admin@admin.com` / `admin123`: `$_SESSION['admin']` também sem `password`.

## Passo 4 — Escape nas views (RF-06)

1. No banco, edite um produto para conter HTML no nome/descrição (ex.: `<script>alert(1)</script>` no nome) e uma mensagem de contato com caracteres `<`.
2. Acesse `/produtos`, a página do produto, `/carrinho` (com o item no cookie) e `/sobre`.
3. Esperado: o conteúdo aparece como **texto puro** (o script não executa); o HTML é exibido na tela.

## Passo 5 — Cost 16 na troca de senha (RF-07)

1. Logado em `/usuario/perfil`, troque a senha.
2. No banco: `SELECT password FROM users WHERE id = <id>;` → hash bcrypt com `$16$` (cost 16).

## Passo 6 — Estoque e produto ativo (RF-08, RF-09)

1. Escolha um produto com `stock` baixo (ex.: 2).
2. Logado: tente adicionar até ultrapassar o estoque → adição **bloqueada**.
3. Na página `/carrinho`, use `action=increase` acima do estoque → bloqueado.
4. Repita os passos 2–3 como **visitante** (cookie): mesmo bloqueio.
5. No banco, marque um produto do carrinho como `active=false`:
   - Logado: ele **não aparece** no `/carrinho`.
   - Visitante: ele **não aparece** no `/carrinho`.

## Passo 7 — Flash em falha de banco (RF-10)

1. Logado, com o banco **fora do ar** (ou via item inexistente no carrinho do banco), envie POST para `/carrinho/remover` (ou `/atualizar`).
2. Esperado: 302 para `/carrinho` **com mensagem de erro** visível.
3. Como **visitante**, repita removendo um item que não está no cookie → 302 sem mensagem (silêncio preservado).

## Passo 8 — logs/ fora do versionamento (RF-11)

1. Force um login errado para gerar `logs/YYYY-MM-DD-loginErrors.txt`.
2. `git status` → a pasta `logs/` **não** aparece como não-versionada.

## Passo 9 — Regressão básica

- `/` (home), `/sobre` (enviar contato), `/produtos` (listagem + página de produto), `/login` e `/admin/login` (sucesso e falha), `/carrinho` (adicionar/atualizar/remover) seguem funcionando como antes.

## Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-13 | Versão inicial gerada por `/reversa-plan` | reversa |
