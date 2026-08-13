# Logout Admin (GET /admin/logout), Requisitos

## Visão Geral

Encerra a sessão do administrador. Remove `$_SESSION['admin']` e redireciona para a home. Se a sessão ativa for de usuário comum, delega para `/logout`, mantendo a separação de papéis.

## Responsabilidades

- Remover `$_SESSION['admin']`.
- Redirecionar para `/` após o logout.
- Delegar o logout de usuário comum para `/logout`.

## Regras de Negócio

- Rota `admin_logout` é **GET**, sem middlewares 🟢
- Se `$_SESSION['user']` estiver definido → redirect **303** para `/logout` (não faz logout parcial) 🟢
- Caso contrário → `unset($_SESSION['admin'])` 🟢
- Redireciona para `/` com status **303** 🟢
- Não destrói a sessão completa — apenas remove a chave `admin` 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Remover a sessão do admin e voltar para a home | Must | GET `/admin/logout` com `$_SESSION['admin']` → 303 `/` e `admin` removida |
| RF-02 | Delegar logout de usuário comum | Must | GET `/admin/logout` com `$_SESSION['user']` → 303 `/logout` |
| RF-03 | Não falhar quando não há sessão | Should | GET `/admin/logout` sem sessão → 303 `/` sem erro |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | HTTP 303 para evitar re-submissão | `src/Controllers/Admin/Login/AdminLogin.php:64-71` | 🟢 |
| Segurança | Separação de sessões `user`/`admin` preservada (ADR-007) | `src/Controllers/Admin/Login/AdminLogin.php:63-67` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um admin com sessão ativa
Quando acessa GET "/admin/logout"
Então recebe 303 com Location "/" e $_SESSION['admin'] é removida

Dado um usuário comum com sessão ativa
Quando acessa GET "/admin/logout"
Então recebe 303 com Location "/logout"
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Remover sessão do admin | Must | Núcleo do logout admin |
| Redirecionar para home | Must | Comportamento pós-logout |
| Delegar logout de usuário | Must | Evita logout cruzado de papéis |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:95-106` | rota `admin_logout` (GET `/admin/logout`, `logoutAdminLogin`) | 🟢 |
| `src/Controllers/Admin/Login/AdminLogin.php:61-72` | `logoutAdminLogin` | 🟢 |
