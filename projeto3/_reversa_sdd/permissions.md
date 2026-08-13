# Permissões e Papéis (RBAC/ACL) — projeto3

> Gerado pelo **Detetive** em 2026-08-12.
> Escala: 🟢 CONFIRMADO (routes/middlewares) | 🟡 INFERIDO | 🔴 LACUNA

## Papéis

| Papel | Identificação | Persistência do carrinho | Sessão |
|-------|---------------|---------------------------|--------|
| **Visitante** | sem `$_SESSION['user']`/`$_SESSION['admin']` | cookie `cart_items` (30 dias) | nenhuma |
| **Usuário** | `$_SESSION['user']` (SELECT `admin=false`) | banco (`carts`/`cart_items`) | `user` |
| **Admin** | `$_SESSION['admin']` (SELECT `admin=true`) | — (sem carrinho de admin no fluxo atual) | `admin` |

🟢 Todos os três papéis derivam da coluna `users.admin` + estado de sessão.

## Matriz de permissões

| Recurso (rota) | Visitante | Usuário | Admin |
|----------------|:---------:|:-------:|:-----:|
| GET `/` (home) | ✅ | ✅ | ✅ |
| GET `/sobre` (formulário) | ✅ | ✅ | ✅ |
| POST `/sobre` (enviar contato) | ✅ | ✅ | ✅ |
| GET `/produtos` (listagem) | ✅ | ✅ | ✅ |
| GET `/produtos/{id}` (detalhe) | ✅ | ✅ | ✅ |
| GET `/login` (form user) | ✅ (block p/ logado via `preventLogged`) | 🔒 → `/usuario/perfil` | 🔒 → `/admin/dashboard`* |
| POST `/login` | ✅ | 🔒 | 🔒 |
| GET `/logout` | — | ✅ | 🔒 → delega `/admin/logout` |
| GET `/usuario/perfil` | 🔒 → `/logout` (middleware `auth`) | ✅ | 🔒 |
| POST `/usuario/perfil` | 🔒 → `/logout` | ✅ | 🔒 |
| GET `/carrinho` | ✅ | ✅ | ✅ |
| POST `/carrinho/adicionar` | ✅ | ✅ | ✅ |
| POST `/carrinho/atualizar` | ✅ | ✅ | ✅ |
| POST `/carrinho/remover` | ✅ | ✅ | ✅ |
| GET `/admin/login` (form admin) | ✅ | 🔒 → `/usuario/perfil` | 🔒 → `/admin/dashboard`* |
| POST `/admin/login` | ✅ | 🔒 | 🔒 |
| GET `/admin/logout` | — | — | ✅ |

\* 🔴 Rota `/admin/dashboard` **não existe** em `routes.php` — o redirect cai no NotFound.

## Mecanismos de controle

1. **Middlewares** 🟢
   - `auth` — rotas de perfil: exige sessão de usuário ativa E usuário ainda ativo no banco (`getUserById`); injeta `$configs['user']`. Falha → redirect 303 `/logout`.
   - `preventLogged` — rotas de login: já logado é redirecionado (admin→`/admin/dashboard`*, user→`/usuario/perfil`, ambos 302).
2. **Filtro por query SQL** 🟢 — o *papel* é determinado no SELECT de autenticação (`admin=false` vs `admin=true`), não por checagem de permissão por rota.
3. **Menu (navbar)** 🟢 — `isMenuAllowed` esconde o item "Login" quando há sessão de usuário ou admin; `getMenuItens` ordena por `order` e filtra `inMenu`.

## Lacunas 🔴

- **Painel admin ausente**: o papel Admin tem apenas autenticação; sem dashboard, CRUD de produtos/categorias/usuários (rota referenciada mas inexistente).
- **Sem ACL granular**: não há tabela/lista de permissões por recurso; tudo é binário por papel + rota.
- **Sem middlewares de admin**: nenhuma rota de admin (exceto login) usa o middleware `auth`; se futuramente existir dashboard, ele estará exposto a qualquer um que navegue direto.
- **Admin pode abrir carrinho** (nenhuma rota de carrinho bloqueia admin), mesmo sem carrinho persistente no fluxo atual — comportamento provavelmente não intencional 🟡.
