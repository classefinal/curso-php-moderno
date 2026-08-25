# Roadmap: Extrair Contact Service

> Identificador: `002-extrair-contact-service`
> Data: `2026-08-25`
> Requirements: `_reversa_forward/002-extrair-contact-service/requirements.md`
> Confidência: 🟢 CONFIRMADO, 🟡 INFERIDO, 🔴 LACUNA

## 1. Resumo da abordagem

Extrair a lógica de negócio do controller `sendContact` (validação, normalização e persistência) para um novo service `src/Services/Contact/ContactService.php`, seguindo o padrão procedural já adotado por `LoginService`, `CartService` e `UsersService`. O controller `About.php` ficará responsável apenas por orquestrar: chamar o service, interpretar o retorno e redirecionar com flash. As validações de tamanho de campo serão adicionadas com base nas dimensões `VARCHAR` da migration 10, usando early return em cada cenário de erro.

## 2. Princípios aplicados

| Princípio | Como a feature se relaciona | Status |
|-----------|------------------------------|--------|
| Arquitetura procedural (funções globais, sem OOP) | Novo service segue o padrão de funções procedurais em arquivo dedicado | respeita |
| Separação de responsabilidades (Controller ↔ Service) | Objetivo principal da feature: controller delega, service executa | respeita |
| Prepared statements tipados | INSERT usa `['type' => 's']` para todos os parâmetros string | respeita |
| Flash messages via `$_SESSION['flash']` | Contrato mantido — service retorna, controller grava flash | respeita |

## 3. Decisões técnicas

| ID | Decisão | Justificativa | Alternativas descartadas | Confidência |
|----|---------|----------------|--------------------------|-------------|
| D-01 | Criar `src/Services/Contact/ContactService.php` com função `sendContact(mysqli $connection, string $name, string $email, string $phone): array` | Padrão do projeto: services recebem dependências por parâmetro (ver `LoginService.loginAuthenticate`, `CartService.addToCart`) | Injetar `$configs` inteiro (violaria granularidade); usar classe OOP (fora do paradigma) | 🟢 |
| D-02 | Função retorna `['success' => bool, 'error' => ?string]` | Contrato idêntico ao de `validateLoginInfo` em `LoginService.php` — consistência interna | Retornar excessão (não há tratamento no projeto); retornar string pura (perde semântica) | 🟢 |
| D-03 | Validações em sequência com early return (nome → email → telefone → normalização → INSERT) | Cada falha retorna imediatamente, eliminando if/else encadeado no controller e no service | Validar tudo e retornar lista de erros (mais complexo, desnecessário para formulário simples) | 🟢 |
| D-04 | Validar tamanho mínimo do nome (≥ 3) com base na regra de perfil (`_reversa_sdd/domain.md#Perfil`) | Consistência: contato e perfil usam a mesma regra de nome | Validar só vazio (deixaria passar nomes de 1-2 chars) | 🟢 |
| D-05 | Não criar migração — schema `contacts` já suporta os tamanhos validados (`VARCHAR(255)`, `VARCHAR(20)`) | Validação.application-level, banco já aceita os limites | Criar migration para adicionarchecks (desnecessário, o banco já impõe o limite) | 🟢 |

## 4. Premissas

Nenhuma premissa adotada — o requirements não contém marcadores `[DÚVIDA]`.

## 5. Delta arquitetural

| Componente | Arquivo de origem no legado | Tipo de mudança | Resumo |
|------------|------------------------------|-----------------|--------|
| Contact Service | (não existe) | componente-novo | `src/Services/Contact/ContactService.php` com `sendContact`, `validateContactName`, `validateContactEmail`, `validateContactPhone` |
| About Controller | `_reversa_sdd/code-analysis.md#about` | regra-alterada | `sendContact` em `About.php` delega ao service; lógica de validação/persistência removida do controller |

## 6. Delta no modelo de dados

- Resumo das mudanças: nenhuma. Schema `contacts` já possui `name VARCHAR(255)`, `email VARCHAR(255)`, `phone VARCHAR(20)` — suficiente para as validações propostas.
- Detalhe completo em: `_reversa_forward/002-extrair-contact-service/data-delta.md`

## 7. Delta de contratos externos

| Contrato | Tipo | Arquivo de detalhe |
|----------|------|--------------------|
| POST `/sobre` | HTTP | `_reversa_forward/002-extrair-contact-service/interfaces/post-sobre.md` |

## 8. Plano de migração

n/a — nenhuma mudança de schema necessária.

## 9. Riscos e mitigações

| Risco | Impacto | Probabilidade | Mitigação |
|-------|---------|---------------|-----------|
| View `about.php` depende de variáveis `success`/`error` que antes vinham do controller direto | baixo | baixo | Controller continua extraindo flash e injetando na view; comportamento visual idêntico |
| Telefone com formato fora do regex mas com caracteres a mais pode causar INSERT com truncação | baixo | baixo | Validação de tamanho (≤ 20 chars) roda antes do INSERT; `VARCHAR(20)` no banco é limite duro |

## 10. Critério de pronto

- [ ] `src/Services/Contact/ContactService.php` criado com funções de validação e persistência
- [ ] `src/Controllers/About.php` refatorado: `sendContact` delega ao service, usa early return
- [ ] Validações de tamanho (nome 3–255, email ≤ 255, telefone 10–20) implementadas
- [ ] `php -l` em todos os arquivos alterados sem erro de sintaxe
- [ ] Comportamento visual da página Sobre inalterado (flash de sucesso/erro funcionando)
- [ ] `regression-watch.md` gerado

## 11. Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-25 | Versão inicial gerada por `/reversa-plan` | reversa |
