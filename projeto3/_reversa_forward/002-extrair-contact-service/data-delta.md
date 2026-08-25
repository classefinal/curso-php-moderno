# Data Delta: Extrair Contact Service

> Identificador: `002-extrair-contact-service`
> Data: `2026-08-25`

## Resumo

Nenhuma mudança no schema do banco de dados. A tabela `contacts` já possui as colunas com os tamanhos corretos para as validações propostas.

## Schema atual da tabela `contacts`

Fonte: `src/Migrations/10_create_contacts_table.php`

```sql
CREATE TABLE contacts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE = InnoDB CHARSET = utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Validações application-level propostas

| Campo | Tipo no banco | Validação application-level | Justificativa |
|-------|---------------|----------------------------|---------------|
| name | VARCHAR(255) NOT NULL | obrigatório, 3–255 chars | Consistência com regra de perfil (`domain.md#Perfil`) |
| email | VARCHAR(255) NOT NULL | obrigatório, formato válido, ≤ 255 chars | Proteção contra strings longas; `FILTER_VALIDATE_EMAIL` já existia |
| phone | VARCHAR(20) NOT NULL | obrigatório, regex `(XX)XXXXX-XXXX`, 10–20 chars | Regex existente; tamanho alinhado ao `VARCHAR(20)` |

## Migrações necessárias

n/a

## Índices

n/a — não há consultas novas; o SELECT existente no controller (INSERT seguido de redirect) não precisa de índice adicional.

## Dados existentes

n/a — não há dados na tabela `contacts` que precisem de limpeza ou transformação.
