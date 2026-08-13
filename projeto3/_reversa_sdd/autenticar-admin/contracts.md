# Autenticar Admin (POST /admin/login), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| POST | `/admin/login` | pública (exige não autenticado) | `application/x-www-form-urlencoded` | Autentica administrador e cria sessão admin |

> Middleware: `preventLogged`.

## Requisição

**Body (form-urlencoded):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `email` | string | sim | `strtolower(trim(...))`; `FILTER_VALIDATE_EMAIL` |
| `password` | string | sim | regex `/^.{8,}$/` (mín. 8 caracteres) |

## Resposta

### 302 Found — sucesso

- **Location:** `/admin/dashboard` (rota inexistente no `routes.php` — ADR-010).
- **Efeito colateral:** `$_SESSION['admin']` definida com a linha completa do usuário.

### 401 Unauthorized — falha

- **Corpo:** HTML da página de login (`Login/login`, título "Login administrativo") com alerta de erro.
- **Mensagens possíveis:**
  - `Usuário e senha são obrigatórios.`
  - `E-mail inválido.`
  - `A senha deve ter pelo menos 8 caracteres.`
  - `Usuário ou senha incorretos` (inexistente, inativo, comum ou senha errada).
- **Efeito colateral:** evento `AdminLoginRecused` → log `logs/YYYY-MM-DD-adminLoginErrors.txt` com `{date}: {email}` (pós-resposta).

## Códigos de status

| Código | Caso |
|--------|------|
| 302 | Credenciais válidas (usuário `active=true`, `admin=true`) |
| 401 | Qualquer falha (validação, inexistente, comum, inativo, senha errada) |

## Exemplos

```
POST /admin/login
Content-Type: application/x-www-form-urlencoded

email=admin@admin.com&password=admin123
→ 302 Location: /admin/dashboard   (sessão admin criada)

POST /admin/login
email=fulano@exemplo.com&password=senha1234
→ 401 (HTML) "Usuário ou senha incorretos"
→ logs/2026-08-13-adminLoginErrors.txt: 2026-08-13 00:20:00: fulano@exemplo.com
```

## Notas

- Não há JSON; resposta é redirect (sucesso) ou HTML (falha).
- Usuário comum (`admin=false`) **não** autentica nesta rota (usa `/login`, unidade `autenticar`).
- O seed admin da migration 8 é `admin@admin.com` / `admin123` (depende das colunas `email`/`password`, ver ADR-008).
