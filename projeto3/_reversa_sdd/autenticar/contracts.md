# Autenticar (POST /login), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| POST | `/login` | pública (exige não autenticado) | `application/x-www-form-urlencoded` | Autentica usuário comum e cria sessão |

> Middleware: `preventLogged`.

## Requisição

**Body (form-urlencoded):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `email` | string | sim | `strtolower(trim(...))`; `FILTER_VALIDATE_EMAIL` |
| `password` | string | sim | regex `/^.{8,}$/` (mín. 8 caracteres) |

## Resposta

### 302 Found — sucesso

- **Location:** `/usuario/perfil`
- **Efeito colateral:** `$_SESSION['user']` definida com a linha completa do usuário autenticado.

### 401 Unauthorized — falha

- **Corpo:** HTML da página de login (`Login/login`) com alerta de erro.
- **Mensagens possíveis:**
  - `Usuário e senha são obrigatórios.`
  - `E-mail inválido.`
  - `A senha deve ter pelo menos 8 caracteres.`
  - `Usuário ou senha incorretos` (usuário inexistente, inativo, admin ou senha errada).
- **Efeito colateral:** evento `LoginRecused` → log `logs/YYYY-MM-DD-loginErrors.txt` com `{date}: {email}` (pós-resposta).

## Códigos de status

| Código | Caso |
|--------|------|
| 302 | Credenciais válidas (usuário `active=true`, `admin=false`) |
| 401 | Qualquer falha (validação, usuário inexistente, senha errada, inativo, admin) |

## Exemplos

```
POST /login
Content-Type: application/x-www-form-urlencoded

email=Maria@Exemplo.com&password=senha1234
→ 302 Location: /usuario/perfil   (sessão user criada; email normalizado maria@exemplo.com)

POST /login
email=nao-existe@x.com&password=senha1234
→ 401 (HTML) "Usuário ou senha incorretos"
→ logs/2026-08-13-loginErrors.txt: 2026-08-13 00:10:00: nao-existe@x.com

POST /login
email=fulano@exemplo.com&password=123
→ 401 (HTML) "A senha deve ter pelo menos 8 caracteres."
```

## Notas

- Não há JSON; resposta é redirect (sucesso) ou HTML (falha).
- Nenhum cookie novo é emitido (sessão PHP + flash apenas).
- Admin (`admin=true`) **não** autentica nesta rota (usa `/admin/login`, unidade `autenticar-admin`).
- A mensagem de sucesso do serviço carrega `error = 'Um erro foi detectado'`, mas nunca é exibida (código morto 🟡).
