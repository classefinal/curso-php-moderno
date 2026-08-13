<!--
Roadmap da feature 001-correcoes-revisao.
Delta sobre o legado — nunca redescreve a arquitetura inteira.
-->

# Roadmap: Correções da Revisão (P1–P14)

> Identificador: `001-correcoes-revisao`
> Data: `2026-08-13`
> Requirements: `_reversa_forward/001-correcoes-revisao/requirements.md`
> Confidência: 🟢 CONFIRMADO, 🟡 INFERIDO, 🔴 LACUNA

## 1. Resumo da abordagem

Correção pontual e reversível dos pontos apontados na revisão, tocando 9 arquivos de código, 1 configuração de repositório e 2 migrations, sem adicionar dependências nem mudar contratos HTTP existentes (exceto status 404 e um flash de erro nas rotas de carrinho logado). Estratégia: corrigir as migrations **in-place** (o runner não valida checksum, então ambientes já migrados não são afetados), passar o status `404` ao `response()` no NotFound, remover o hash da sessão no `LoginService`, aplicar `htmlspecialchars` nas views listadas, padronizar o `cost => 16` no `password_hash` do perfil, adicionar validação de estoque/ativo e detecção de "0 linhas" no `CartService` (com flash de erro só no caminho de banco), e criar `.gitignore` para a pasta `logs/`. Decisões P3 (dashboard), P4 (seed) e P8 (cookie) ficam fora — não tocam código.

## 2. Princípios aplicados

Não existe `.reversa/principles.md` — nenhum princípio a verificar nesta feature.

## 3. Decisões técnicas

| ID | Decisão | Justificativa | Alternativas descartadas | Confidência |
|----|---------|----------------|--------------------------|-------------|
| D-01 | Corrigir migrations 7 e 8 **in-place** (reescrever o DDL) | O runner marca `executed=1` por nome de arquivo e **não valida checksum** (`_reversa_sdd/state-machines.md#Migration`) — ambientes já migrados continuam válidos; só bancos limpos recebem o DDL corrigido | Criar migrations 11/12 "fixadoras" (criaria schema duplicado em banco limpo); não fazer nada (schema continua não reproduzível) | 🟢 |
| D-02 | NotFound responde `404` via `$configs['response'](404, $content)` | Mesmo padrão já usado em `validateLogin`/`validateAdminLogin` com 401 (`code-analysis.md#auth`); a view é a mesma | Manter 200 (contrato errado, P9) | 🟢 |
| D-03 | Remover `password` do array antes de gravar `$_SESSION['user']`/`$_SESSION['admin']` no `LoginService` | Alinha com o que o perfil já faz na sessão (`domain.md#Perfil` — "sessão sobrescrita sem password"); elimina exposição do hash (P5) | Guardar só `id` (exigiria recarga em mais pontos; o middleware `auth` já recarrega por id quando precisa) | 🟢 |
| D-04 | Escapar interpolações com `htmlspecialchars(..., ENT_QUOTES)` nas views afetadas | Padrão já existente em `src/Pages/Users/profile.php:33,37`; elimina XSS (P7) | Sanitizar na escrita (não muda leitura/views; menos defensivo) | 🟢 |
| D-05 | `password_hash(..., PASSWORD_BCRYPT, ['cost' => 16])` na atualização de perfil | Uniformiza o cost com seed e `DUMMY_PASSWORD_HASH` (P6); `password_verify` usa o cost embutido, sem quebra | Deixar default 10-12 (inconsistência documentada) | 🟢 |
| D-06 | Validar `stock` na adição e no `increase` (banco e cookie) | P11; evita vender acima do estoque. No cookie, ler `stock` do produto na operação (consulta barata por PK) | Validar só no GET (feedback tardio) | 🟢 |
| D-07 | Validar existência + `active` do produto antes do INSERT (banco e cookie) e filtrar `active = true` no GET do carrinho logado | P12; impede FK quebrada e itens inativos no carrinho | Deletar itens inativos do banco (destrutivo demais; filtro na leitura basta) | 🟢 |
| D-08 | `CartService` (DB) retorna `success: false` em falha/0 linhas; `Cart.php` (logado) seta flash e segue 302; funções de cookie ficam silenciosas | P10 híbrido; `mysqli::affected_rows` dá o "0 linhas"; mantém PRG 302 | Flash também no cookie (rejeitado pelo usuário — cookie é indicador) | 🟢 |
| D-09 | Criar `.gitignore` com `logs/` | P14; não existe `.gitignore` hoje | `git rm -r --cached logs` (logs nem existem no repo) | 🟢 |
| D-10 | Remover a string morta `'Um erro foi detectado'` — sucesso retorna `error = null` | P13; `Login.php`/`AdminLogin.php` redirecionam no sucesso e não usam a string | Manter por fidelidade (contrato enganoso) | 🟢 |

## 4. Premissas

Nenhuma premissa derivada de `[DÚVIDA]` — o requirements não tem marcadores pendentes.

## 5. Delta arquitetural

| Componente | Arquivo de origem no legado | Tipo de mudança | Resumo |
|------------|------------------------------|-----------------|--------|
| `LoginService` | `_reversa_sdd/code-analysis.md#auth` | regra-alterada | Sessão sem hash (D-03); sucesso com `error = null` (D-10) |
| `UsersService` | `_reversa_sdd/code-analysis.md#users` | regra-alterada | `password_hash` com `cost => 16` (D-05) |
| `CartService` | `_reversa_sdd/code-analysis.md#cart` | regra-alterada | Validação de estoque (D-06), produto ativo (D-07), detecção de falha/0 linhas no DB (D-08) |
| `Cart` (controller) | `_reversa_sdd/code-analysis.md#cart` | regra-alterada | Flash de erro em falha de banco, somente logado (D-08) |
| `NotFound` | `_reversa_sdd/code-analysis.md#products` (padrão response) | regra-alterada | Responde HTTP 404 (D-02) |
| Views (`cart`, `products`, `about`, `login`, `profile`) | `_reversa_sdd/code-analysis.md#Alertas` | regra-alterada | `htmlspecialchars` nas interpolações (D-04) |
| Migrations 7 e 8 | `_reversa_sdd/code-analysis.md#Dicionário` | regra-alterada | DDL corrigido in-place (D-01) |
| `.gitignore` | `_reversa_sdd/architecture.md#Camadas` (ausente) | componente-novo | Ignora `logs/` (D-09) |

## 6. Delta no modelo de dados

- **Resumo:** nenhum campo novo em runtime. A migration 8 passa a declarar `email` e `password` no `CREATE TABLE users` (o schema real do banco já os tem); a migration 7 perde as cláusulas `AFTER` cruzadas. Consequência: bancos **limpos** recebem o schema correto; bancos **existentes** não são tocados (runner sem checksum).
- Detalhe completo em: `_reversa_forward/001-correcoes-revisao/data-delta.md`

## 7. Delta de contratos externos

| Contrato | Tipo | Arquivo de detalhe |
|----------|------|--------------------|
| `POST /carrinho/adicionar`, `/atualizar`, `/remover` | HTTP | `_reversa_forward/001-correcoes-revisao/interfaces/carrinho-post.md` |
| GET de URI sem rota (global) | HTTP | comportamento documentado no roadmap (seção 5, D-02) |

## 8. Plano de migração

1. Corrigir `src/Migrations/7_add_product_short_description.php` (remover `AFTER`).
2. Corrigir `src/Migrations/8_create_users_table.php` (adicionar `email` UNIQUE e `password` ao DDL).
3. Validar em banco limpo: `php migrate.php` aplica 10/10 sem erro (ambientes já migrados são indiferentes).
4. Aplicar as demais correções de código (D-02..D-10).
5. Criar `.gitignore` com `logs/`.
6. Verificar manualmente cada cenário do `onboarding.md`.

## 9. Riscos e mitigações

| Risco | Impacto | Probabilidade | Mitigação |
|-------|---------|---------------|-----------|
| Migration corrigida diverge de ambientes existentes | médio | baixa | Runner sem checksum: `executed` já marcado não reexecuta; correção só vale para bancos limpos — documentado em `data-delta.md` |
| `affected_rows` retorna 0 em UPDATE de valor idêntico (falso negativo) | baixo | baixa | Quantidade `+1` sempre altera o valor; `DELETE` sem match dá 0 esperado — ver `investigation.md` |
| Flash de carrinho duplicado com fluxos futuros | baixo | baixa | Reutilizar `$_SESSION['flash']` já usado em `/sobre` (`code-analysis.md#about`) |
| Escape duplicado (duplo `htmlspecialchars`) em dados já tratados | baixo | média | Escapar apenas na view (ponto único); dados crus vêm do banco |
| Validação de estoque no cookie exige leitura de `stock` no DB | baixo | alta | Consulta por PK única (`SELECT stock ... WHERE id = ? AND active = true`), custo desprezível |

## 10. Critério de pronto

- [ ] Todas as ações do `actions.md` marcadas `[X]`
- [ ] `cross-check.md` (se executado) sem CRITICAL nem HIGH
- [ ] `regression-watch.md` gerado
- [ ] Banco limpo aplica 10/10 migrations
- [ ] Cenários do `onboarding.md` verificados manualmente
- [ ] Re-extração reversa executada e sem regressão vermelha (recomendado, não obrigatório)

## 11. Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-13 | Versão inicial gerada por `/reversa-plan` | reversa |
