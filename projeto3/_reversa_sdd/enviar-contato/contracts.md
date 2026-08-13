# Enviar Contato (POST /sobre), Contrato HTTP

## Endpoint

| Método | Path | Auth | Content-Type | Descrição |
|--------|------|------|--------------|-----------|
| POST | `/sobre` | pública | `application/x-www-form-urlencoded` | Envia mensagem de contato |

## Requisição

**Body (form-urlencoded):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `name` | string | sim | não vazio após trim |
| `email` | string | sim | `filter_var FILTER_VALIDATE_EMAIL` |
| `phone` | string | sim | `^\(\d{2}\)\d{4,5}-\d{4}$` |

## Resposta

### 302 Found (sempre)

- **Location:** `/sobre`
- **Efeito colateral:** `$_SESSION['flash']['success'|'error']` definido conforme o resultado.

## Códigos de status

| Código | Caso |
|--------|------|
| 302 | Todos os desfechos (validação, sucesso ou falha de INSERT) |

## Exemplos

**Sucesso:**

```
POST /sobre
Content-Type: application/x-www-form-urlencoded

name=Maria&email=maria@exemplo.com&phone=(11)99999-9999

→ 302 Location: /sobre   (flash success)
→ contacts: (Maria, maria@exemplo.com, +5511999999999)
```

**Erro de validação:**

```
POST /sobre
email=nao-eh-email&phone=x

→ 302 Location: /sobre   (flash error "E-mail inválido.")
```

## Notas

- Não há JSON: resposta é sempre um redirect.
- Nenhum cookie novo é emitido (apenas o flash em sessão).
