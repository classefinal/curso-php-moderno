# Adendo — Correções da Revisão (P1–P14)

> Identificador: `001-correcoes-revisao`
> Data: `2026-08-13T01:10:00Z`
> Cenário: `legado`
> Feature-dir: `_reversa_forward/001-correcoes-revisao/`

## Vigência

Vigente desde 2026-08-13.

## Resumo da entrega

Correção pontual dos pontos apontados pelo Revisor (P1–P14) sem reimplementar o sistema: migrations 7 e 8 passam a reproduzir o schema real em banco limpo, página inexistente responde HTTP 404, sessão de autenticação deixa de conter o hash de senha, saída das views passa a ser escapada com `htmlspecialchars`, bcrypt uniformizado com `cost => 16` no perfil, carrinho valida estoque e produto ativo (banco e cookie), falhas/0 linhas das operações de banco do carrinho geram flash de erro (logado) e a pasta `logs/` sai do versionamento. Todas as 14 ações do `actions.md` foram concluídas (`progress.jsonl`: 14 `done` + 2 `corrected`).

## Impacto por artefato da extração

| Artefato | Seção | Tipo de impacto | Delta |
|----------|-------|-----------------|-------|
| `_reversa_sdd/architecture.md` | `Dívidas técnicas (síntese)` | regra-alterada | As dívidas de migrations 7/8, XSS nas views e carrinho sem validação foram corrigidas nesta feature; leia `legacy-impact.md` da feature para o detalhe |
| `_reversa_sdd/domain.md` | `Autenticação` | regra-alterada | Sessão `$_SESSION['user']`/`$_SESSION['admin']` nunca contém `password` após login; sucesso de login retorna `error = null`; toda escrita de senha usa `PASSWORD_BCRYPT` com `cost => 16` |
| `_reversa_sdd/domain.md` | `Perfil` | regra-alterada | `password_hash` da troca de senha passa a usar `cost => 16` (uniforme com seed e dummy) |
| `_reversa_sdd/domain.md` | `Carrinho` | regra-alterada | Adicionar/`increase` respeita `products.stock`; produto inexistente/inativo não entra no carrinho; GET do carrinho logado filtra `active = true`; falha/0 linhas no banco → flash de erro (cookie permanece silencioso) |
| `_reversa_sdd/domain.md` | `Seed inicial` | regra-alterada | 🔴 ADR-008 resolvida: `CREATE TABLE users` declara `email` (UNIQUE) e `password`; migration 7 sem `AFTER` cruzados (ADR-009) |
| `_reversa_sdd/architecture.md` | `Camadas (diretórios em `src/`)` | componente-novo | `.gitignore` criado com `logs/` — pasta de runtime fora do versionamento |
| `_reversa_sdd/architecture.md` | `Fluxo de uma requisição (resumo)` | regra-alterada | URI sem rota responde HTTP 404 (view `not_found.php` preservada) |
| `_reversa_sdd/home/design.md` | fluxo alternativo | regra-alterada | Página não encontrada passa a responder HTTP 404 (antes 200) |
| `_reversa_sdd/produtos/requirements.md` | escape das views | regra-alterada | Interpolações de dados do banco nas views `cart.php`, `about.php`, `products.php` (+ componentes de produto/categoria) e `login.php`/`profile.php` usam `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` |
| `_reversa_sdd/carrinho-adicionar/requirements.md` | RF-07/RF-08 | regra-alterada | Estoque e `active` validados na adição; cookie permanece silencioso |
| `_reversa_sdd/carrinho-atualizar/requirements.md` | RF-07 | regra-alterada | Estoque respeitado no `increase`; falha de banco → flash (logado) |
| `_reversa_sdd/carrinho-remover/requirements.md` | RF-05 | regra-alterada | Falha/0 linhas no DELETE do banco → flash de erro (logado) |

## Regras sob vigilância

- `W001`–`W011` — apontador: `_reversa_forward/001-correcoes-revisao/regression-watch.md`

## Fontes

- `_reversa_forward/001-correcoes-revisao/requirements.md`
- `_reversa_forward/001-correcoes-revisao/roadmap.md`
- `_reversa_forward/001-correcoes-revisao/legacy-impact.md`
- `_reversa_forward/001-correcoes-revisao/regression-watch.md`
- `_reversa_forward/001-correcoes-revisao/progress.jsonl`
- `_reversa_forward/001-correcoes-revisao/interfaces/carrinho-post.md`
