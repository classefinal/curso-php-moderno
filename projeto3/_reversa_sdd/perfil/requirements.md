# Perfil (GET /usuario/perfil), Requisitos

## Visão Geral

Página privada do perfil do usuário comum autenticado. Protegida pelo middleware `auth`, que valida a sessão e recarrega o usuário do banco. Exibe o formulário de edição (nome, e-mail somente leitura e alteração de senha) e o flash de sucesso quando o perfil foi atualizado.

## Responsabilidades

- Proteger a rota com o middleware `auth` (sessão válida + usuário ativo).
- Recarregar o usuário do banco a cada acesso (sessão não é fonte de verdade).
- Renderizar o formulário de perfil com nome, e-mail (disabled) e campos de senha.
- Exibir flash de sucesso "Perfil atualizado com sucesso" quando `$_SESSION['profile_updated']` estiver definido.
- Limpar o flash `profile_updated` de forma adiada (pós-resposta).

## Regras de Negócio

- Middleware `auth`: exige `$_SESSION['user']['id']` e `$_SESSION['user']['active']` não vazio 🟢
- Middleware `auth` recarrega o usuário via `getUserById` (`WHERE id = ? AND active = true`); inexistente/inativo → redirect `/logout` 🟢
- Usuário recarregado é injetado em `$configs['user']` e consumido pela view 🟢
- View lê `$_SESSION['profile_updated']` diretamente para o flash de sucesso 🟢
- `profile_updated` é removido via `defer` (após a resposta), no próprio `viewProfile` 🟢
- Campo e-mail é `disabled` — não editável no formulário 🟢
- Nome e e-mail renderizados com `htmlspecialchars` (escape correto) 🟢
- Formulário envia POST `/usuario/perfil` (unidade `atualizar-perfil`) 🟢
- Página depende de `users.email` — coluna **confirmada no schema** (P1); a migration 8 deve ser corrigida para incluí-la no CREATE TABLE 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Exibir o perfil do usuário autenticado | Must | GET `/usuario/perfil` logado → 200 com form nome/e-mail/senha |
| RF-02 | Redirecionar não autenticado | Must | sem `$_SESSION['user']['id']` → 303 `/logout` |
| RF-03 | Redirecionar usuário inativo/inexistente | Must | `getUserById` retorna null → 303 `/logout` |
| RF-04 | Exibir flash de sucesso após atualização | Must | `$_SESSION['profile_updated']` → alert "Perfil atualizado com sucesso" |
| RF-05 | Limpar o flash pós-resposta | Should | após a resposta, `profile_updated` é removida |
| RF-06 | Exibir e-mail somente leitura | Must | campo `email` com `disabled` e valor atual |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Acesso restrito por sessão + recarga do banco | `src/Middlewares/auth.php:19-38` | 🟢 |
| Segurança | `htmlspecialchars` em todas as interpolações da view (P7) | `src/Pages/Users/profile.php:23,33,37` | 🟢 |
| Segurança | Flash de atualização é dado transitório em sessão | `src/Controllers/Users/Users.php:28-32` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um usuário autenticado e ativo
Quando acessa GET "/usuario/perfil"
Então recebe 200 com o formulário preenchido (nome, e-mail disabled, campos de senha)

Dado um visitante sem sessão válida
Quando acessa GET "/usuario/perfil"
Então recebe 303 com Location "/logout"

Dado um usuário com $_SESSION['profile_updated'] definida
Quando acessa GET "/usuario/perfil"
Então o alerta "Perfil atualizado com sucesso" é exibido e a flag é limpa após a resposta
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Renderizar perfil autenticado | Must | Núcleo da página |
| Guarda do middleware `auth` | Must | Segurança sem alternativa |
| Flash de sucesso | Must | Feedback do POST |
| E-mail somente leitura | Should | Regra de negócio atual |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:143-154` | rota `user_profile` (GET `/usuario/perfil`, `viewProfile`, middleware `auth`) | 🟢 |
| `src/Controllers/Users/Users.php:18-35` | `viewProfile` | 🟢 |
| `src/Middlewares/auth.php` | `authMiddleware` | 🟢 |
| `src/Services/Users/UsersService.php:13-31` | `getUserById` | 🟢 |
| `src/Pages/Users/profile.php` | view do perfil | 🟢 |
