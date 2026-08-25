# Legacy Impact: Extrair Contact Service

> Identificador: `002-extrair-contact-service`
> Data: `2026-08-25`
> Cenário: legado

## Arquivos afetados

| Arquivo afetado | Componente | Tipo | Severidade | Justificativa |
|-----------------|------------|------|------------|---------------|
| `src/Services/Contact/ContactService.php` | Contact Service | componente-novo | LOW | Novo service com validação e persistência de contato |
| `src/Controllers/About.php` | About Controller | regra-alterada | MEDIUM | `sendContact` delega toda lógica de negócio ao service; controller ficou com 54 linhas (antes 93) |

## Diff conceitual por componente

### Contact Service (novo)

`src/Services/Contact/ContactService.php` — 99 linhas

Funções criadas:
- `validateContactName(string $name): array` — obrigatório, 3–255 chars
- `validateContactEmail(string $email): array` — obrigatório, formato válido, ≤ 255 chars
- `validateContactPhone(string $phone): array` — obrigatório, regex电话, 10–20 chars
- `processContact(mysqli $connection, string $name, string $email, string $phone): array` — orquestra validações, normalização e INSERT

Decisão de nomenclatura: função `processContact` em vez de `sendContact` para evitar conflito com a função controller `sendContact` (ambas carregadas na mesma request).

### About Controller (modificado)

`src/Controllers/About.php` — 54 linhas (antes 93)

- `sendContact` agora extrai dados do `$_POST`, chama `processContact` do service, interpreta retorno e redireciona com flash
- Validação inline removida (5 if/else → 1 chamada ao service + 1 if)
- `makeAbout` permanece idêntico (GET não afetado)
- Adicionado `require_once` do `ContactService.php`

## Preservadas

| Regra | Arquivo | Confidência |
|-------|---------|-------------|
| GET `/sobre` exibe flash de sucesso/erro e formulário | `_reversa_sdd/domain.md#Contato` | 🟢 |
| Flash messages usam `$_SESSION['flash']['success']`/`['error']` | `_reversa_sdd/domain.md#Contato` | 🟢 |
| Telefone normalizado para `+55<dígitos>` antes do INSERT | `_reversa_sdd/domain.md#Contato` | 🟢 |
| Redirect pós-POST usa 302 | `_reversa_sdd/domain.md#Autenticação` | 🟢 |

## Modificadas

| Regra | Arquivo | Tipo de mudança | Confidência |
|-------|---------|-----------------|-------------|
| Validação de contato: apenas `empty()` + regex | `_reversa_sdd/domain.md#Contato` | regra-alterada — agora inclui validação de tamanho (3–255 para nome, ≤ 255 para email, 10–20 para telefone) | 🟢 |
| Controller `sendContact` continha lógica de negócio inline | `_reversa_sdd/code-analysis.md#about` | regra-alterada — lógica extraída para `ContactService` | 🟢 |
| Type `LoginInfo` era restrito a autenticação | `types.php` | regra-alterada — renomeado para `OperationResult` com campo `data` opcional; annotations atualizadas em LoginService, ContactService e UsersService | 🟢 |
