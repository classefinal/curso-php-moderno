# Logout (GET /logout), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/logout` | sessão (`$_SESSION['user']` ou `$_SESSION['admin']`) | redirect | 303 |

## Fluxo Principal

1. GET `/logout` → rota `logout` (`logoutLogin`), sem middlewares. `src/Configs/routes.php:131-142`
2. `logoutLogin` verifica `$_SESSION['admin']`. `src/Controllers/Login/Login.php:64`
3. Se admin → `$configs['redirect']('/admin/logout', 303)` e encerra. `src/Controllers/Login/Login.php:65-68`
4. Senão → `unset($_SESSION['user'])`. `src/Controllers/Login/Login.php:70`
5. `$configs['redirect']('/', 303)` — `redirect` faz `ob_clean`, `header('Location: /', true, 303)`, flush e roda o dispatcher de deferred. `src/Controllers/Login/Login.php:72`, `src/Services/Response.php:37-46`

## Fluxos Alternativos

- **Sem nenhuma sessão:** `unset` é no-op e o redirect para `/` segue normalmente.
- **Admin acessando `/logout`:** delega para `/admin/logout`, que faz `unset($_SESSION['admin'])` (unidade `logout-admin`).

## Dependências

- **Router** (`processRoutes`), resolução da rota.
- **Response** (`redirect`), emissão do 303 + flush + defer.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Logout por `unset` de chave específica, não `session_destroy` | `src/Controllers/Login/Login.php:70` | 🟢 |
| Delegação de admin para `/admin/logout` mantém papéis separados | `src/Controllers/Login/Login.php:64-68` | 🟢 |
| Uso de 303 See Other para o redirect | `src/Controllers/Login/Login.php:65,72` | 🟢 |

## Estado Interno

- Remove `$_SESSION['user']`. Nunca toca `$_SESSION['admin']` nesta rota.

## Observabilidade

- Nenhum log no logout de usuário (apenas o de admin usa fluxo próprio).

## Riscos e Lacunas

- 🟡 Não invalida o ID da sessão (`session_regenerate_id`) nem apaga cookie — reuso de ID de sessão continua válido.
- 🟢 Comportamento conhecido e estável; sem lacunas humanas pendentes.
