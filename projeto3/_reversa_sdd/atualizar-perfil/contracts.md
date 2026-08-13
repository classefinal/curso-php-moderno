# Atualizar Perfil (POST /usuario/perfil), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| POST | `/usuario/perfil` | sessão de usuário (`auth`) | `application/x-www-form-urlencoded` | Atualiza nome e, opcionalmente, senha do usuário autenticado |

> Middleware: `auth`.

## Requisição

**Body (form-urlencoded):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `name` | string | sim | `strip_tags(trim(...))`; 3–255 caracteres |
| `old_password` | string | só com `new_password` | deve conferir com `users.password` (`password_verify`) |
| `new_password` | string | não | se preenchido: mínimo 8, igual a `password_confirmation` |
| `password_confirmation` | string | não | deve ser igual a `new_password` |

## Resposta

### 302 Found — sucesso

- **Location:** `/usuario/perfil`.
- **Efeitos:** `users.name` atualizado; `users.password` re-hash **somente** se `new_password` preenchido; `$_SESSION['user']` atualizada **sem** hash; `$_SESSION['profile_updated'] = true`.

### 422 Unprocessable Entity — falha de validação

- **Corpo:** HTML do perfil re-renderizado com alert de erro.
- **Mensagens possíveis:**
  - `O nome deve ter entre 3 e 255 caracteres`
  - `Senha atual incorreta`
  - `Preencha a senha`
  - `A confirmaçao de senha deve ser igual a nova senha` *(sic, preservado do legado)*
  - `A senha deve ter pelo menos 8 caracteres`

### 303 See Other — não autenticado

- **Location:** `/logout` (via middleware `auth`).

## Códigos de status

| Código | Caso |
|--------|------|
| 302 | Nome válido (com ou sem troca de senha) |
| 422 | Qualquer falha de validação |
| 303 | Sem sessão válida ou usuário inativo (→ `/logout`) |

## Exemplos

```
POST /usuario/perfil
name=Fulano&new_password=&old_password=&password_confirmation=
→ 302 Location: /usuario/perfil   (só nome)

POST /usuario/perfil
name=Fulano&old_password=senhaantiga&new_password=novasenha1&password_confirmation=novasenha1
→ 302 Location: /usuario/perfil   (nome + senha re-hash)

POST /usuario/perfil
name=Fulano&old_password=errada&new_password=novasenha1&password_confirmation=novasenha1
→ 422 "Senha atual incorreta"
```

## Notas

- Não há JSON. Respostas são redirect (302), HTML (422) ou redirect (303).
- Nenhuma alteração de e-mail é possível nesta rota.
- No 422, os valores digitados não são preservados nos campos (a view usa os dados do banco).
- Mensagens de erro em pt-br, preservadas do legado (incluindo o typo "confirmaçao").
