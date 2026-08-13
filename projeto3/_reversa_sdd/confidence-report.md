# Relatório de Confiança — projeto3

> Gerado pelo Revisor em 2026-08-12
> Atualizado em 2026-08-13 — **todas as 14 perguntas respondidas** (P1–P14) e aplicadas às specs.
> Contagem de marcadores em `requirements.md` + `design.md` de cada unit (exclui `tasks.md`, `contracts.md` e `questions.md`).

---

## Resumo Geral

| Nível | Quantidade | Percentual |
|-------|-----------|------------|
| 🟢 CONFIRMADO | 404 | 94% |
| 🟡 INFERIDO   | 28  | 6% |
| 🔴 LACUNA     | 0   | 0% |
| **Total**     | 432 | 100% |

**Confiança geral:** **97%** — fórmula: (404 + 28×0,5) / 432 = 418/432.
**Revisão concluída:** as 14 perguntas (P1–P14) foram respondidas e aplicadas; nenhuma decisão pendente.

---

## Por Spec

| Spec | 🟢 | 🟡 | 🔴 | Confiança |
|------|----|----|-----|-----------|
| `home` | 15 | 1 | 0 | 97% |
| `sobre` | 14 | 1 | 0 | 97% |
| `enviar-contato` | 16 | 2 | 0 | 94% |
| `produtos` | 32 | 4 | 0 | 94% |
| `produto` | 25 | 5 | 0 | 92% |
| `login` | 23 | 1 | 0 | 98% |
| `autenticar` | 38 | 3 | 0 | 96% |
| `logout` | 13 | 1 | 0 | 96% |
| `login-admin` | 17 | 1 | 0 | 97% |
| `autenticar-admin` | 33 | 1 | 0 | 99% |
| `logout-admin` | 13 | 1 | 0 | 96% |
| `perfil` | 24 | 1 | 0 | 98% |
| `atualizar-perfil` | 30 | 3 | 0 | 96% |
| `carrinho` | 29 | 2 | 0 | 97% |
| `carrinho-adicionar` | 31 | 1 | 0 | 98% |
| `carrinho-atualizar` | 29 | 0 | 0 | 100% |
| `carrinho-remover` | 22 | 0 | 0 | 100% |

Todas as unidades acima de 90%. As units de **carrinho** ficaram com a maior confiança após a decisão P10 (feedback de banco documentado, cookie silencioso).

---

## Lacunas Pendentes 🔴

Nenhuma lacuna 🔴 permanece nas specs após a validação do usuário.

### Decisões de revisão concluídas
- **P10 — Falhas silenciosas de carrinho:** decisão **híbrida** aplicada — feedback (flash de erro) para falhas de banco (INSERT/UPDATE/DELETE sem efeito); silêncio mantido para operações de cookie. Registrada em `questions.md#pergunta-10` e nas RFs das units `carrinho-adicionar` (RF-08), `carrinho-atualizar` (RF-07) e `carrinho-remover` (RF-05).

---

## Recomendações

- [x] Responder as 14 perguntas de revisão (P1–P14) — **concluído**.
- [ ] Ao reimplementar, aplicar as decisões registradas nas specs (migrações 7 e 8 corrigidas, escape nas views, sessão sem hash, cost 16, 404 no NotFound, validações de estoque/ativo no carrinho, flash de erro nas falhas de banco de carrinho).
- [ ] Considerar criar a unit `not-found/` quando o endpoint de 404 for implementado (hoje documentado nos fluxos de `home`/`sobre`).

---

## Histórico de Reclassificações

| De | Para | Afirmação | Evidência |
|----|------|-----------|-----------|
| 🟡 | 🟢 | PHP 8.5+ como requisito de ambiente (evidência original incorreta) | `php -v` → 8.5.0; AGENTS.md → mínimo 8.1+ |
| 🔴 | 🟢 | `users.email`/`users.password` existem no schema (P1) | resposta do usuário; migration 8 deve ser corrigida |
| 🔴 | 🟢 | Migration 7: colunas reais, remover `AFTER` (P2) | resposta do usuário |
| 🔴 | 🟢 | `/admin/dashboard` é rota planejada (P3) | resposta do usuário |
| 🟡 | 🟢 | Sessão de auth sem hash de senha (P5) | resposta do usuário |
| 🟡 | 🟢 | Views escapam saída com `htmlspecialchars` (P7) | resposta do usuário |
| 🔴 | 🟢 | NotFound responde HTTP 404 (P9) | resposta do usuário |
| 🟡 | 🟢 | Validação de estoque e produto ativo no carrinho (P11/P12) | resposta do usuário |
| 🟡 | 🟢 | Falhas de banco de carrinho com flash de erro; cookie silencioso (P10) | resposta do usuário |
| 🟡 | ✅ | `enviar-contato` sem referência a `ContactService.php` (inexistente) | `About.php:38-93` — lógica inline |
| 🔴 | ✅ | `RandomProductsService` desmapeado de `home/` | `Products.php:11`, components |
| 🟢 | 🟡 | Home `RF-02` (`/index.php` → home) era incorreto | `Router.php:84-118` → NotFound |
| 🟢 | 🟡 | `sobre/design` alegava parâmetro `action` na view | `About.php:22-27` — sem `action` |
