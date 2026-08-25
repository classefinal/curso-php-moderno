# Investigation: Extrair Contact Service

> Identificador: `002-extrair-contact-service`
> Data: `2026-08-25`

## Padrão de services no projeto

Análise dos services existentes para identificar o padrão a ser seguido:

| Service | Arquivo | Funções principais | Padrão de retorno |
|---------|---------|--------------------|-------------------|
| LoginService | `src/Services/Login/LoginService.php` | `validateLoginInfo`, `loginAuthenticate`, `adminLoginAuthenticate` | `['success' => bool, 'error' => ?string]` |
| CartService | `src/SerServices/Cart/CartService.php` | `addToCart`, `removeFromCart`, `updateCartItemQuantity` | `['success' => bool, ...]` |
| UsersService | `src/Services/Users/UsersService.php` | `updateUserProfile` | `['success' => bool, 'error' => ?string]` |
| ProductsService | `src/Services/Products/ProductsService.php` | `getProducts`, `getProductById` | arrays ou `null` |

**Padrão confirmado:** 🟢 Services são funções procedurais em arquivo dedicado dentro de `src/Services/{Domain}/`. Recebem dependências (`mysqli $connection`) como parâmetro. Funções de validação retornam `['success' => bool, 'error' => ?string]`.

## Controller About.php — estado atual

```php
// src/Controllers/About.php (93 linhas)
function sendContact(array $configs, array $route, string $uri): void
{
    // Validação inline (40+ linhas)
    // INSERT inline via dbPrepareAndExecute
    // Flash + redirect no final
}
```

**Problemas identificados:**
1. Validação de 5 campos misturada com persistência e controle de fluxo
2. Sem validação de tamanho — apenas checagem de vazio e regex
3. `if/else` encadeado sem early return (cada validação tem `if ... { flash; redirect; return; }`)

## Valores de validação extraídos da migration 10

```sql
CREATE TABLE contacts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

| Campo | Tipo | Min | Max | Validação atual | Validação proposta |
|-------|------|-----|-----|-----------------|-------------------|
| name | VARCHAR(255) | — | 255 | `empty()` | `strlen()` 3–255 |
| email | VARCHAR(255) | — | 255 | `empty()` + `FILTER_VALIDATE_EMAIL` | + `strlen()` ≤ 255 |
| phone | VARCHAR(20) | 10 | 20 | `empty()` + regex | + `strlen()` 10–20 |

## Alternativas avaliadas

### 1. Service com função única `sendContact`
- **Prós:** simples, uma entrada, uma saída; consistente com `loginAuthenticate`
- **Contras:** validação e persistência no mesmo ponto
- **Decisão:** aceitar — a complexidade é baixa o bastante

### 2. Service com funções separadas `validateContact` + `persistContact`
- **Prós:** separação máxima
- **Contras:** introduces duas chamadas no controller, desnecessário para fluxo linear
- **Descartado:** complexidade desnecessária para formulário de 3 campos

### 3. Validar no controller e só persistir no service
- **Prós:** controller continua "inteligente"
- **Contras:** não resolve o problema de acoplamento
- **Descartado:** vai contra o objetivo da feature

## Referências externas

Nenhuma. O projeto não usa dependências PHP; validações são nativas do PHP (`filter_var`, `strlen`, `preg_match`).
