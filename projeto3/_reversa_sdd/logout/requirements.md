# Logout (GET /logout), Requisitos

## Visão Geral

Encerra a sessão do usuário comum. Remove `$_SESSION['user']` e redireciona para a home. Se a sessão ativa for de admin, delega para `/admin/logout` (unidade `logout-admin`), mantendo a separação de papéis.

## Responsabilidades

- Remover `$_SESSION['user']`.
- Redirecionar para `/` após o logout.
- Delegar o logout de admin para `/admin/logout`.

## Regras de Negócio

- Rota `logout` é **GET**, sem middlewares 🟢
- Se `$_SESSION['admin']` estiver definido → redirect **303** para `/admin/logout` (não faz logout parcial) 🟢
- Caso contrário → `unset($_SESSION['user'])` 🟢
- Redireciona para `/` com status **303** 🟢
- Não destrói a sessão completa — apenas remove a chave `user` 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Remover a sessão do usuário e voltar para a home | Must | GET `/logout` com `$_SESSION['user']` → 303 `/` e `user` removida |
| RF-02 | Delegar logout de admin | Must | GET `/logout` com `$_SESSION['admin']` → 303 `/admin/logout` |
| RF-03 | Não falhar quando não há sessão | Should | GET `/logout` sem sessão → 303 `/` sem erro |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Usa HTTP 303 See Other pós-POST/GET para evitar re-submissão | `src/Controllers/Login/Login.php:65-72` | 🟢 |
| Segurança | Separação de sessões `user`/`admin` preservada (ADR-007) | `src/Controllers/Login/Login.php:64-68` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um usuário comum com sessão ativa
Quando acessa GET "/logout"
Então recebe 303 com Location "/" e $_SESSION['user'] é removida

Dado um admin com sessão ativa
Quando acessa GET "/logout"
Então recebe 303 com Location "/admin/logout"
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Remover sessão do usuário | Must | Núcleo do logout |
| Redirecionar para home | Must | Comportamento pós-logout |
| Delegar logout de admin | Must | Evita logout cruzado de papéis |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:131-142` | rota `logout` (GET `/logout`, `logoutLogin`) | 🟢 |
| `src/Controllers/Login/Login.php:62-73` | `logoutLogin` | 🟢 |
