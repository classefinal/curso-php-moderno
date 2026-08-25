# Contrato HTTP: POST /sobre

> Identificador: `002-extrair-contact-service`
> Data: `2026-08-25`
> Tipo: HTTP POST (form-urlencoded)

## Request

| Campo | Tipo | Obrigatório | Validação | Exemplo |
|-------|------|-------------|-----------|---------|
| name | string | sim | 3–255 chars | `João Silva` |
| email | string | sim | formato válido, ≤ 255 chars | `joao@teste.com` |
| phone | string | sim | regex `^\(\d{2}\)\d{4,5}-\d{4}$`, 10–20 chars | `(11)94878-4541` |

Content-Type: `application/x-www-form-urlencoded`

## Response (sucesso)

- HTTP 302
- Location: `/sobre`
- Session flash: `{ "success": "Mensagem enviada com sucesso!" }`
- Side effect: INSERT na tabela `contacts` com phone normalizado para `+55XXXXXXXXXXX`

## Response (erro de validação)

- HTTP 302
- Location: `/sobre`
- Session flash: `{ "error": "<mensagem descritiva>" }`
- Nenhum INSERT executado

## Response (erro de banco)

- HTTP 302
- Location: `/sobre`
- Session flash: `{ "error": "Erro ao enviar mensagem. Tente novamente." }`
- INSERT pode ter falhado parcialmente (raro com prepared statements)

## Idempotência

- Não idempotente: cada POST cria um novo registro em `contacts`
- Sem proteção contra duplicate submit (sem token CSRF neste endpoint)

## Timeout

- Não aplicável: operação síncrona com banco local
