<!--
Template de corpo do actions.md
Carregado por /reversa-to-do e atualizado por /reversa-coding.

REGRAS DE PREENCHIMENTO:
- IDs estáveis: T001, T002, ..., zero-padded três dígitos. Nunca recicle.
- Marcador de paralelismo é [//] no início da linha de ID. Tarefas [//] não compartilham arquivo alvo.
- Coluna "Dependências" lista IDs separados por vírgula. Ações sem dependência usam "-".
- Status inicial é [ ]. /reversa-coding muda para [X] ao concluir.
- /reversa-add acrescenta uma seção "## Emendas" ao final, com o mesmo formato de tabela, IDs E001, E002, ... e status já [X].
- Toda ação precisa ser ATÔMICA: cabe num turno do agente, sem precisar de feedback humano no meio.
-->

# Actions: Extrair Contact Service

> Identificador: `002-extrair-contact-service`
> Data: `2026-08-25`
> Roadmap: `_reversa_forward/002-extrair-contact-service/roadmap.md`

## Resumo

| Métrica | Valor |
|---------|-------|
| Total de ações | 6 |
| Paralelizáveis (`[//]`) | 2 |
| Maior cadeia de dependência | 3 (T001 → T002 → T004) |

## Fase 1, Preparação

<!-- Scaffolding do novo service -->

| ID | Descrição | Dependências | Paralelismo | Arquivo alvo | Confidência | Status |
|----|-----------|--------------|-------------|--------------|-------------|--------|
| T001 | Criar diretório `src/Services/Contact/` e arquivo `ContactService.php` com `declare(strict_types=1)`, import Psalm `@psalm-import-type` e assinatura da função `processContact(mysqli $connection, string $name, string $email, string $phone): array` retornando `['success' => bool, 'error' => ?string]` | - | - | `src/Services/Contact/ContactService.php` | 🟢 | `[X]` |

## Fase 2, Testes

<!-- Omitida — o projeto não pratica TDD (sem framework/Composer). -->

## Fase 3, Núcleo

<!-- Validações, normalização e persistência no service; refatoração do controller -->

| ID | Descrição | Dependências | Paralelismo | Arquivo alvo | Confidência | Status |
|----|-----------|--------------|-------------|--------------|-------------|--------|
| T002 | Implementar as funções de validação dentro de `ContactService.php`: `validateContactName` (obrigatório, 3–255 chars), `validateContactEmail` (obrigatório, formato válido, ≤ 255 chars), `validateContactPhone` (obrigatório, regex `^\(\d{2}\)\d{4,5}-\d{4}$`, 10–20 chars). Cada função retorna `['success' => bool, 'error' => ?string]` com early return | T001 | - | `src/Services/Contact/ContactService.php` | 🟢 | `[X]` |
| T003 | Implementar a lógica de persistência em `processContact`: chamar as três validações em sequência com early return; normalizar telefone removendo não-dígitos e antecipando `+55`; executar `INSERT INTO contacts (name, email, phone) VALUES (?, ?, ?)` via `dbPrepareAndExecute` com parâmetros tipados como string; retornar `success = true` no sucesso ou `success = false` com mensagem de erro em falha de banco | T001 | - | `src/Services/Contact/ContactService.php` | 🟢 | `[X]` |
| T004 | Refatorar `sendContact` em `src/Controllers/About.php`: extrair `$name`, `$email`, `$phone` do `$_POST`; chamar `processContact($configs['connection'], $name, $email, $phone)` do service; em `success = true`, gravar flash de sucesso; em `success = false`, gravar flash de erro; redirecionar 302 para `/sobre` com early return; remover toda a lógica de validação, normalização e INSERT que estava inline no controller; adicionar `require_once` do `ContactService.php` | T002, T003 | - | `src/Controllers/About.php` | 🟢 | `[X]` |

## Fase 4, Integração

<!-- Nenhuma integração externa necessária — o contrato HTTP POST /sobre permanece idêntico -->

| ID | Descrição | Dependências | Paralelismo | Arquivo alvo | Confidência | Status |
|----|-----------|--------------|-------------|--------------|-------------|--------|
| T005 | Verificar que `makeAbout` (GET `/sobre`) em `About.php` permanece inalterado — flash de sucesso/erro continua extraindo de `$_SESSION['flash']` e injetando na view | T004 | `[//]` | `src/Controllers/About.php` | 🟢 | `[X]` |

## Fase 5, Polimento

<!-- Verificação sintaxe e relatório de regressão -->

| ID | Descrição | Dependências | Paralelismo | Arquivo alvo | Confidência | Status |
|----|-----------|--------------|-------------|--------------|-------------|--------|
| T006 | Rodar `php -l` em `src/Services/Contact/ContactService.php` e `src/Controllers/About.php` e confirmar sintaxe válida | T004 | - | (verificação) | 🟢 | `[X]` |

## Notas de execução

- T001/T002/T003: função do service renomeada de `sendContact` para `processContact` para evitar conflito de nome com a função controller `sendContact` (ambas seriam carregadas na mesma request via `require_once`)
- T004: adicionado `require_once SERVICES . getRequirePath('Contact/ContactService.php')` no topo do controller, seguindo o padrão de `Login.php`, `Cart.php` etc.

## Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-25 | Versão inicial gerada por `/reversa-to-do` | reversa |

## Emendas

| ID | Descrição | Dependências | Paralelismo | Arquivo alvo | Confidência | Status |
|----|-----------|--------------|-------------|--------------|-------------|--------|
| E001 | Renomear `LoginInfo` → `OperationResult` em `types.php`, adicionar campo `data: mixed` opcional, atualizar imports/annotations em LoginService, ContactService e UsersService | - | - | `types.php`, `src/Services/Login/LoginService.php`, `src/Services/Contact/ContactService.php`, `src/Services/Users/UsersService.php` | 🟢 | `[X]` |
