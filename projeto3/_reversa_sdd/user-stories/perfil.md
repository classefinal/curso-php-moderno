# User Story — Perfil

> Fluxo de usuário cobrindo as units: `perfil/` e `atualizar-perfil/`.

## Narrativa

Um usuário autenticado acessa a área do perfil para conferir seus dados e, se quiser, alterar o nome e a senha. O e-mail é exibido apenas em leitura (campo desabilitado). Alterações exigem a senha atual apenas quando a senha será trocada.

## Persona

- **Usuário autenticado**: com `$_SESSION['user']` ativa e usuário ativo no banco.

## Jornada

1. Acessa `GET /usuario/perfil` — o middleware `auth` valida a sessão e **recarrega o usuário do banco** (`getUserById`). 🟢 `src/Middlewares/auth.php`
2. Sem sessão válida ou usuário inativo → 303 `/logout`. 🟢
3. Página exibe form com nome (editável), e-mail (`disabled`) e seção "Alterar senha". 🟢 `src/Pages/Users/profile.php`
4. Altera só o nome → `POST /usuario/perfil` com `new_password` vazio; somente `UPDATE users SET name`. 🟢 `src/Services/Users/UsersService.php:92-110`
5. Altera nome + senha → valida `old_password` (`password_verify`), confirmação e tamanho (≥ 8), gera hash bcrypt e atualiza. 🟢 `src/Services/Users/UsersService.php:112-141`
6. Sucesso → sessão atualizada (sem hash), flash `profile_updated` e 302 de volta ao perfil. 🟢 `src/Controllers/Users/Users.php:47-51`
7. Falha → 422 com a página re-renderizada e mensagem de erro (sem flash). 🟢 `src/Controllers/Users/Users.php:53-61`
8. No GET seguinte, o alerta "Perfil atualizado com sucesso" é exibido e a flag é limpa via `defer`. 🟢 `src/Controllers/Users/Users.php:28-32`

## Regras observadas no código

| Regra | Evidência | Confiança |
|-------|-----------|-----------|
| Usuário recarregado do banco a cada request (sessão não é fonte de verdade) | `src/Middlewares/auth.php:27` | 🟢 |
| Nome sanitizado com `strip_tags` e validado 3–255 | `src/Services/Users/UsersService.php:82-90` | 🟢 |
| Troca de senha exige senha atual correta | `src/Services/Users/UsersService.php:43-45` | 🟢 |
| Hash bcrypt sempre gerado para nova senha | `src/Services/Users/UsersService.php:122` | 🟢 |
| E-mail não editável na view (`disabled`) | `src/Pages/Users/profile.php:37` | 🟢 |
| Sessão pós-update não guarda hash | `src/Services/Users/UsersService.php:68` | 🟢 |

## Critérios de Aceite

```gherkin
Dado um usuário autenticado
Quando acessa /usuario/perfil
Então vê seus dados com nome editável e e-mail somente leitura

Dado um usuário autenticado
Quando envia POST /usuario/perfil com nome válido e sem nova senha
Então o nome é atualizado e a sessão recebe o novo nome

Dado um usuário autenticado
Quando envia POST /usuario/perfil com senha atual errada
Então recebe 422 com "Senha atual incorreta" e nada é alterado
```

## Métricas de sucesso (sugeridas)

- Taxa de conclusão de atualização de perfil.
- Tempo médio para alterar senha.
- Recuperação após 422 (reenvio sem alteração).

## Pontos de atenção

- 🔴 `users.email`/`users.password` ausentes na migration 8 (ADR-008) — a unit inteira depende do schema real.
- 🟡 Cost de bcrypt inconsistente (seed cost 16 vs. atualização com cost padrão).
- 🟡 No 422, os campos digitados não são repopulados.
- 🟡 Middleware `auth` repõe o hash na sessão no request seguinte (dado trafega até a view).
