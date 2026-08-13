<!--
Template de corpo do requirements.md
Gerado por /reversa-requirements em 2026-08-13.
Escopo: aplicar SOMENTE as correções apontadas na revisão (P1–P14), ancoradas nas specs já atualizadas.
-->

# Requirements: Correções da Revisão (P1–P14)

> Identificador: `001-correcoes-revisao`
> Data: `2026-08-13`
> Pasta da extração reversa: `_reversa_sdd/`
> Confidência: 🟢 CONFIRMADO, 🟡 INFERIDO, 🔴 LACUNA / DÚVIDA

## 1. Resumo executivo

Corrigir os pontos apontados pelo Revisor na fase de revisão do projeto legado `projeto3`, sem reimplementar o sistema. Entrega um conjunto fechado de correções de código e configuração: migrations 7 e 8 reproduzindo o schema real, HTTP 404 na página inexistente, sessão de autenticação sem hash de senha, escape de saída nas views, cost 16 consistente no bcrypt, validação de estoque e produto ativo no carrinho, feedback de erro nas falhas de banco do carrinho e exclusão da pasta `logs/` do versionamento. Nenhuma mudança de funcionalidade além dos itens listados.

## 2. Contexto a partir do legado

| Fonte | Trecho relevante | Confidência |
|-------|------------------|-------------|
| `_reversa_sdd/architecture.md#Dívidas-técnicas` | Migrations 7 e 8 não reproduzem o schema real (ADR-008/009); XSS nas views; `dbPrepareAndExecute` sem tratamento de erro; `DUMMY_PASSWORD_HASH` público; carrinho sem validação de estoque | 🟢 |
| `_reversa_sdd/domain.md#Autenticação` | Sessões separadas `$_SESSION['user']`/`$_SESSION['admin']`; hash `PASSWORD_BCRYPT` (seed cost 16); email normalizado | 🟢 |
| `_reversa_sdd/domain.md#Seed-inicial` | Migration 8 insere `Administrador` mas o DDL não cria `email`/`password` — INSERT referencia colunas inexistentes | 🟢 |
| `_reversa_sdd/domain.md#Carrinho` | Logado → banco; visitante → cookie; `decrease` com quantidade ≤ 1 remove; itens do cookie enriquecidos apenas com produtos ativos | 🟢 |
| `_reversa_sdd/domain.md#Admin` | `/admin/dashboard` é rota planejada (decisão P3) — fora do escopo de código desta feature | 🟢 |
| `_reversa_sdd/gaps.md#Resolvidos` | Decisões P1–P14 registradas com a regra de spec correspondente | 🟢 |
| `_reversa_sdd/questions.md` | Perguntas P1–P14 respondidas pelo usuário (todas ✅) | 🟢 |
| `_reversa_sdd/carrinho-adicionar/requirements.md` | RF-07 (estoque/ativo), RF-08 (feedback de falha de banco), cookie silencioso | 🟢 |
| `_reversa_sdd/carrinho-atualizar/requirements.md` | RF-07 (feedback de falha de banco), estoque no `increase` | 🟢 |
| `_reversa_sdd/carrinho-remover/requirements.md` | RF-05 (feedback de falha de banco), cookie silencioso | 🟢 |
| `_reversa_sdd/autenticar/requirements.md` | Sessão sem hash (P5), `error = null` no sucesso (P13), cost 16 (P6), `logs/` não versionado (P14) | 🟢 |
| `_reversa_sdd/produtos/requirements.md` | Escaping das views (P7), migration 7 sem `AFTER` (P2) | 🟢 |

## 3. Personas e cenários de uso

| Persona | Objetivo | Cenário-chave |
|---------|----------|---------------|
| Desenvolvedor (ops) | Aplicar migrations em banco limpo sem falhas | Rodar `php migrate.php` do zero → 10 migrations aplicam sem erro |
| Usuário cliente | Navegar sem expor dados e sem estourar estoque | Adicionar mais itens que o estoque é bloqueado; sessão não guarda hash |
| Visitante | Comprar sem conta | Produto inativo não aparece no carrinho; carrinho em cookie permanece silencioso |
| Usuário/Admin autenticado | Ser informado quando a ação de carrinho falhou no banco | Falha de INSERT/UPDATE/DELETE → flash de erro em vez de silêncio |
| Crawler/usuário | Receber status correto para URL inexistente | URI sem rota → HTTP 404 |

## 4. Regras de negócio novas ou alteradas

1. **RN-01:** O `CREATE TABLE users` da migration 8 deve declarar as colunas `email` (UNIQUE) e `password` que o próprio seed usa. 🟢
   - Origem no legado: `_reversa_sdd/domain.md#Seed-inicial` (regra confirmada como bug ADR-008)
   - Tipo: alterada (correção de regressão)
2. **RN-02:** O `ALTER TABLE products` da migration 7 não deve usar `AFTER` de colunas criadas na mesma instrução. 🟢
   - Origem no legado: `_reversa_sdd/adrs/009-regressao-migration-7-after.md`
   - Tipo: alterada (correção de regressão)
3. **RN-03:** A sessão de autenticação (`$_SESSION['user']`/`$_SESSION['admin']`) nunca contém o hash de senha. 🟢
   - Origem no legado: `_reversa_sdd/domain.md#Perfil` ("sessão sobrescrita sem password")
   - Tipo: alterada (P5 — remove exposição)
4. **RN-04:** Todo dado de banco interpolado em view é escapado com `htmlspecialchars`. 🟢
   - Origem no legado: `_reversa_sdd/architecture.md#Dívidas-técnicas` (XSS — cross-site scripting)
   - Tipo: nova (P7)
5. **RN-05:** Toda escrita de senha usa `PASSWORD_BCRYPT` com `cost => 16` (seed, dummy e atualização de perfil). 🟢
   - Origem no legado: `_reversa_sdd/domain.md#Autenticação`
   - Tipo: alterada (P6 — normaliza atualização de perfil)
6. **RN-06:** Adicionar ou aumentar quantidade no carrinho respeita `stock` do produto (banco e cookie). 🟢
   - Origem no legado: `_reversa_sdd/carrinho-adicionar/requirements.md` (RF-07), `carrinho-atualizar` (P11)
   - Tipo: nova (P11)
7. **RN-07:** Produto inexistente ou inativo não é adicionado ao carrinho e itens inativos não são exibidos nele (banco e cookie). 🟢
   - Origem no legado: `_reversa_sdd/carrinho/requirements.md` (P12)
   - Tipo: nova (P12)
8. **RN-08:** Falha ou ausência de efeito em operações de carrinho no **banco** (logado) produz flash de erro; operações de **cookie** (visitante) permanecem silenciosas. 🟢
   - Origem no legado: `_reversa_sdd/carrinho-remover/requirements.md` (RF-05), `carrinho-adicionar` (RF-08), `carrinho-atualizar` (RF-07)
   - Tipo: nova (P10)
9. **RN-09:** Página não encontrada responde HTTP 404. 🟢
   - Origem no legado: `_reversa_sdd/home/design.md` (fluxo alternativo)
   - Tipo: alterada (P9)
10. **RN-10:** A pasta `logs/` (runtime) não é versionada. 🟢
    - Origem no legado: `_reversa_sdd/autenticar/requirements.md` (P14)
    - Tipo: nova (P14)

## 5. Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de aceite | Confidência |
|----|-----------|------------|--------------------|-------------|
| RF-01 | Corrigir migration 8 — `CREATE TABLE users` inclui `email` (UNIQUE) e `password` | Must | `php migrate.php` em banco limpo aplica 8/10 sem erro; seed `admin@admin.com` gravado | 🟢 |
| RF-02 | Corrigir migration 7 — remover as cláusulas `AFTER` cruzadas | Must | Migration 7 aplica em banco limpo sem erro; colunas `short_description`/`description_line` existem | 🟢 |
| RF-03 | Página não encontrada responde HTTP 404 | Must | GET de URI sem rota → status 404 (antes 200) | 🟢 |
| RF-04 | Sessão de auth sem hash de senha | Must | `$_SESSION['user']`/`$_SESSION['admin']` não contêm `password` após login | 🟢 |
| RF-05 | Sucesso de login retorna `error = null` | Must | `adminLoginAuthenticate`/`loginAuthenticate` no sucesso retornam sem a string morta | 🟢 |
| RF-06 | Escapar saída de dados do banco nas views | Must | Views `cart.php`, `products.php`, `about.php`, `login.php`, `profile.php` usam `htmlspecialchars` em toda interpolação de dados | 🟢 |
| RF-07 | Hash de senha do perfil com `cost => 16` | Must | Troca de senha no perfil gera hash bcrypt cost 16 (idêntico ao seed) | 🟢 |
| RF-08 | Validar estoque na adição e no aumento de quantidade | Must | Adicionar ou `increase` acima do `stock` é bloqueado (banco e cookie) | 🟢 |
| RF-09 | Validar existência e `active` do produto antes de adicionar; inativo não é exibido no carrinho | Must | Produto inexistente/inativo não entra no carrinho; inativos não aparecem no GET | 🟢 |
| RF-10 | Flash de erro em falha de banco do carrinho (logado) | Must | INSERT/UPDATE/DELETE com falha ou 0 linhas → flash de erro + 302 `/carrinho` | 🟢 |
| RF-11 | Ignorar pasta `logs/` no versionamento | Should | `logs/` listada em `.gitignore`; `git status` não a mostra | 🟢 |

## 6. Requisitos Não Funcionais

| Tipo | Requisito | Evidência ou justificativa | Confidência |
|------|-----------|----------------------------|-------------|
| Segurança | Nenhum hash de senha em sessão/cookies | `_reversa_sdd/autenticar/design.md` (P5) | 🟢 |
| Segurança | Toda saída HTML de dados é escapada | `_reversa_sdd/gaps.md#Resolvidos` (P7) | 🟢 |
| Segurança | Bcrypt cost 16 uniforme | `_reversa_sdd/autenticar-admin/requirements.md` (P6) | 🟢 |
| Correção | Migrations reproduzem o schema real em banco limpo | `_reversa_sdd/adrs/008` e `009` (P1/P2) | 🟢 |
| Correção | Sem regressão nas rotas/cookies existentes | Contratos nas units `carrinho-*` (cookie silencioso preservado) | 🟢 |
| Operacional | `logs/` de runtime fora do controle de versão | `_reversa_sdd/autenticar/requirements.md` (P14) | 🟢 |
| Compatibilidade | PHP 8.5 mantido; nenhuma dependência nova | `_reversa_sdd/inventory.md#Tecnologias` | 🟢 |

## 7. Critérios de Aceitação

```gherkin
Cenário: Migration 8 aplica em banco limpo
  Dado um banco MySQL vazio
  Quando executo php migrate.php
  Então a migration 8 aplica sem erro e a tabela users contém email e password

Cenário: Migration 7 aplica em banco limpo
  Dado um banco MySQL vazio
  Quando executo php migrate.php
  Então a migration 7 aplica sem erro e products contém short_description e description_line

Cenário: URL inexistente retorna 404
  Dado um servidor rodando o projeto
  Quando acesso uma URI sem rota registrada
  Então recebo HTTP 404 com a view de não encontrado

Cenário: Sessão sem hash após login
  Dado credenciais válidas de usuário
  Quando faço login com sucesso
  Então $_SESSION['user'] não contém o campo password

Cenário: Estoque respeitado no carrinho
  Dado um produto com stock = 2
  Quando um usuário tenta adicionar 3 unidades ou usa increase além do limite
  Então a operação é bloqueada

Cenário: Produto inativo fora do carrinho
  Dado um produto inativo no banco
  Quando um usuário tenta adicioná-lo ou abre o carrinho
  Então ele não é adicionado e não aparece no carrinho

Cenário: Falha de banco do carrinho gera flash (logado)
  Dado um usuário logado
  Quando o INSERT/UPDATE/DELETE de carrinho falha ou afeta 0 linhas
  Então recebe 302 /carrinho com flash de erro

Cenário: Cookie do carrinho permanece silencioso (visitante)
  Dado um visitante com cookie cart_items
  Quando ele remove um item que não existe no cookie
  Então o cookie é regravado inalterado e ele recebe 302 /carrinho sem mensagem

Cenário: Pasta logs ignorada
  Dado o repositório com a pasta logs/ criada em runtime
  Quando executo git status
  Então logs/ não aparece como não-versionada

Cenário: Sucesso de login sem string morta
  Dado credenciais válidas
  Quando o serviço de autenticação conclui com sucesso
  Então o retorno tem error = null

Cenário: Senha do perfil com cost 16
  Dado um usuário logado no perfil
  Quando ele troca a senha
  Então o novo hash é bcrypt com cost 16

Cenário: Saída escapada nas views
  Dado um produto/contato/mensagem com caracteres HTML no nome ou descrição
  Quando a view renderiza esses dados
  Então o conteúdo é exibido como texto puro (sem execução de HTML)
```

## 8. Prioridade MoSCoW

| Item | MoSCoW | Justificativa |
|------|--------|---------------|
| RF-01, RF-02 | Must | Migrations não aplicam em banco limpo — bloco qualquer evolução |
| RF-03 | Must | Contrato HTTP incorreto |
| RF-04, RF-06 | Must | Exposição de dados/hash (segurança) |
| RF-05 | Must | Código morto/enganoso no contrato de sucesso |
| RF-07 | Must | Consistência de segurança do bcrypt |
| RF-08, RF-09 | Must | Regras de negócio de catálogo/carrinho |
| RF-10 | Must | Confiabilidade das operações de carrinho logado |
| RF-11 | Should | Higiene do repositório |

## 9. Esclarecimentos

> Nenhuma sessão de dúvidas necessária: as decisões P1–P14 (perguntas de validação 1 a 14 em `_reversa_sdd/questions.md`) já foram validadas com o usuário durante a revisão. P3 (rota `/admin/dashboard` planejada), P4 (seed mantido) e P8 (cookie sem assinatura) **não geram mudança de código** — foram incorporadas às specs e ficam fora do escopo de execução desta feature.

## 10. Lacunas

- Nenhuma lacuna aberta. Itens de implementação dependentes de escolha técnica (ex.: reutilizar flash existente do carrinho) serão decididos em `/reversa-plan`.

## 11. Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-13 | Versão inicial gerada por `/reversa-requirements` | reversa |
