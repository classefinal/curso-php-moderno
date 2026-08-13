# Domínio de Negócio — projeto3

> Gerado pelo **Detetive** em 2026-08-12.
> Escala: 🟢 CONFIRMADO (código/histórico Git) | 🟡 INFERIDO | 🔴 LACUNA

## Glossário

| Termo | Definição | Confiança |
|-------|-----------|-----------|
| **Loja virtual** | Catálogo de produtos com carrinho de compras e autenticação | 🟢 |
| **Usuário (cliente)** | Conta de acesso com `admin = false`; acessa perfil e carrinho persistente | 🟢 |
| **Admin** | Conta de acesso com `admin = true`; separação total de sessão (`$_SESSION['admin']`) | 🟢 |
| **Visitante** | Usuário não autenticado; navega, compra no carrinho via cookie `cart_items` | 🟢 |
| **Produto** | Item comercializável com preço em centavos, estoque, imagem e descrições | 🟢 |
| **Categoria** | Agrupador de produtos; com `active` controla exibição | 🟢 |
| **Carrinho** | Conjunto de itens (produto + quantidade); por usuário (banco) ou cookie (visitante) | 🟢 |
| **Contato** | Mensagem enviada pelo formulário da página Sobre | 🟢 |
| **Defer/Dispatcher** | Execução de ações após o flush da resposta (ex.: escrita de logs) | 🟢 |
| **Evento** | `LoginRecused` / `AdminLoginRecused` — notificam recusas de login | 🟢 |
| **Flash** | Mensagem de feedback persistida em sessão e limpa na próxima leitura | 🟢 |
| **Migration** | Unidade de evolução de schema controlada pela tabela `migrations` | 🟢 |

## Regras de negócio

### Exibição de catálogo (produtos/categorias) 🟢
- Apenas produtos com `active = true` aparecem no catálogo (`WHERE p.active = true`).
- Apenas categorias com `active = true` são listadas no accordion/filtro.
- Produto só é exibido se ele e sua categoria estiverem ativos (`p.active AND c.active`).
- Lista paginada: `limit` 5–30 (default 10), `page` ≥ 1, offset `(page-1)*limit`.
- Filtro por categoria via `?categoryId=`.
- 6 produtos aleatórios em destaque (`ORDER BY RAND() LIMIT 6`) na listagem.

### Precificação 🟢
- Preço armazenado em **centavos** (INT). Exibição: `number_format($price/100, 2, ',', '.')` → `R$ 1.234,56`.
- Total do carrinho = soma de `price * quantity` (centavos).

### Autenticação 🟢
- Login de usuário exige `active = true AND admin = false`.
- Login de admin exige `active = true AND admin = true`.
- Senha mínima: **8 caracteres**; hash `PASSWORD_BCRYPT` (admin seed usa `cost 16`).
- Email normalizado com `strtolower` antes da consulta.
- Login recusado dispara evento (`LoginRecused`/`AdminLoginRecused`) que grava log em `logs/YYYY-MM-DD-{loginErrors|adminLoginErrors}.txt`, **após** a resposta (defer).
- Usuário inexistente executa `password_verify` contra hash dummy para mitigar timing attack (const `DUMMY_PASSWORD_HASH`).
- Sessões separadas: `$_SESSION['user']` e `$_SESSION['admin']`; `/logout` roteia admin para `/admin/logout`.
- Redirects pós-POST usam **303** (mudança introduzida no commit `618579b`); demais usam 302.

### Perfil 🟢
- Nome: `strip_tags`, entre 3 e 255 caracteres.
- Troca de senha: exige senha atual correta, nova ≥ 8 chars e confirmação idêntica.
- Email é exibido no perfil mas **não editável** (input disabled).
- Após atualização, sessão é sobrescrita sem `password` + flag `profile_updated` (limpa via defer após resposta).

### Carrinho 🟢
- Logado → banco (`carts` UNIQUE por `user_id`, `cart_items` com `quantity`).
- Visitante → cookie `cart_items` (formato `id:qty,id:qty`), validade **30 dias**.
- Quantidade mínima **1**; `decrease` com quantidade ≤ 1 remove o item.
- Operações: adicionar (incrementa), aumentar, diminuir, remover.
- Itens do cookie são enriquecidos apenas com produtos **ativos**.

### Contato (página Sobre) 🟢
- Campos obrigatórios: nome, email, telefone.
- Email validado com `FILTER_VALIDATE_EMAIL`.
- Telefone deve casar `^\(\d{2}\)\d{4,5}-\d{4}$`; normalizado para `+55<dígitos>` antes do INSERT.
- Feedback via flash message em `$_SESSION['flash']`.

### Admin — escopo real 🔴
- O módulo admin contém **somente** autenticação (login/logout).
- 🟡 INFERIDO: o sistema aponta constantemente para `/admin/dashboard` (redirect pós-login, navbar, `preventLogged`), indicando **intenção** de um painel administrativo **nunca implementado** (rota inexistente desde o commit `55fad5e`).

### Seed inicial 🔴
- Migration 8 insere `Administrador` (`admin@admin.com` / `admin123`), mas a mesma migration **removeu** as colunas `email`/`password` da tabela (commit `511ca81`) — o INSERT referencia colunas que o próprio DDL não cria.

## Regras identificadas via histórico Git 🟢

- **Projeto didático**: commits como "wip", "add agents.md", pastas reordenadas (`7be5bda`, `ff0f4a8`) indicam evolução por aprendizado, sem processo formal de review.
- **Fluxo de features por branch**: `feat/*` para cada capacidade (products, cart, auth, middlewares, migrations…) com merges frequentes em `main`.
- **Migrations evoluíram de raw DDL para padrão tipado** (commit `8d36eb8` → `433f6fe`), mantendo o mesmo runner.
- **Bugs introduzidos por "fix"**: `20210e4` ("wip: fixed some types") quebrou a migration 7; `511ca81` ("wip: changed table") quebrou a migration 8.

## Eventos de negócio monitorados (logs)

Não há pasta `logs/` no repositório (criada em runtime). Os eventos rastreados são **apenas recusas de login**:
- `logs/YYYY-MM-DD-loginErrors.txt` — tentativas falhas de login de usuário.
- `logs/YYYY-MM-DD-adminLoginErrors.txt` — tentativas falhas de login de admin.
- Formato da linha: `{date}: {email}`.

🟢 CONFIRMADO (definido nos listeners). Não há monitoramento de produtos, vendas ou carrinho.
