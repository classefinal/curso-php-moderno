# Lacunas e Riscos — projeto3

> Gerado pelo Revisor em 2026-08-12
> Atualizado em 2026-08-13 — decisões do usuário aplicadas (P1–P14). **Sem lacunas pendentes.**
> Classificação por severidade. Itens em **Resolvidos** tiveram decisão tomada e incorporada às specs.

---

## Pendente

Nenhuma lacuna pendente — as 14 perguntas de validação foram respondidas.

---

## Resolvidos (decisão do usuário incorporada às specs)

| # | Lacuna | Decisão | Regra na spec |
|---|--------|---------|---------------|
| P10 | Falhas silenciosas de carrinho (cookie e banco) | **Híbrido:** banco com flash de erro; cookie silencioso (P10) | 🟢 em `carrinho-adicionar` (RF-08), `carrinho-atualizar` (RF-07), `carrinho-remover` (RF-05) |
| C1 | Migration 8 sem `email`/`password` no CREATE TABLE (ADR-008) | Schema real tem as colunas; **corrigir a migration** (P1) | 🟢 em `autenticar`, `autenticar-admin`, `perfil`, `atualizar-perfil` |
| C2 | `/admin/dashboard` sem rota (ADR-010) | **Manter rota** — página será criada posteriormente (P3) | 🟢 rota planejada em `login-admin`, `autenticar-admin`; nota de resolução em ADR-010 e `flowcharts/admin.md` |
| C3 | Migration 7 com `AFTER` cruzado (ADR-009) | **Corrigir**: remover as cláusulas `AFTER`; colunas são reais (P2) | 🟢 em `produtos`, `produto` |
| C4 | Views sem `htmlspecialchars` (XSS) | **Corrigir** — escapar todas as interpolações (P7) | 🟢 em `produtos`, `produto`, `carrinho`, `sobre`, `login`, `perfil` |
| M1 | Sessão guarda hash de senha | **Remover hash** da sessão (P5) | 🟢 sessão com campos seguros em `autenticar`, `autenticar-admin`, `perfil` |
| M2 | Seed admin hardcoded | **Manter** credenciais padrão (P4) | 🟢 regra de seed em `autenticar-admin` |
| M3 | Cookie `cart_items` sem assinatura | **Aceito** — é apenas indicador de quantidade (P8) | 🟢 em `carrinho`, `carrinho-adicionar` |
| M4 | Sem validação de estoque | **Adicionar** validação de `stock` (P11) | 🟢 em `carrinho-adicionar`, `carrinho-atualizar` |
| M5 | Produto inativo entra no carrinho | **Filtrar ativos** — inativo nem aparece (P12) | 🟢 em `carrinho`, `carrinho-adicionar` |
| M6 | 404 retorna HTTP 200 | **Mudar para 404** (P9) | 🟢 fluxo alternativo em `home/design.md`; matriz atualizada |
| M7 | Cost bcrypt inconsistente | **Manter 16 em todos os lugares** (P6) | 🟢 em `autenticar`, `autenticar-admin`; nota em `atualizar-perfil/design.md` |
| M8 | Logs de login com append indefinido | **Não versionar** a pasta `logs/` (P14) | 🟢 regra operacional em `autenticar`, `autenticar-admin` |
| M9 | String morta `"Um erro foi detectado"` | **Remover** — `error = null` no sucesso (P13) | 🟢 em `autenticar`, `autenticar-admin` |

---

## Remanescentes (🟡 — observações documentadas, sem decisão pendente)

| # | Observação | Onde |
|---|-----------|------|
| R1 | `DUMMY_PASSWORD_HASH` público (const) com hash de senha conhecida | `LoginService.php:7` |
| R2 | `$configs['user']` carrega o hash até a view no fluxo de perfil (view não exibe) | `perfil/design.md` |
| R3 | E-mail do usuário não editável (fluxo próprio de troca de e-mail não existe) | `perfil/design.md` |
| R4 | No 422 do perfil os campos digitados não são repopulados | `atualizar-perfil/design.md` |
| R5 | Middlewares executam em LIFO (`array_pop`) | `login/design.md` |
| R6 | `ORDER BY RAND()` nos destaques (O(n) no banco) | `produtos` |
| R7 | Reuso da view `Login/login` acopla os dois logins | `login-admin/design.md` |
| R8 | Primeira atribuição redundante de `$productId` em `makeProduct` | `produto` |

---

## Resolvidos na revisão (correções de spec)

- `enviar-contato` referenciava `Services/Contact/ContactService.php` inexistente → corrigido para `About.php:76-84` (inline).
- Suspeita de `Services/Home`/`Services/About` inexistentes → confirmado que as units não os referenciam.
- Matriz `code-spec-matrix.md`: `RandomProductsService` estava mapeado para `home/` → corrigido para `produtos/`+`produto/`.
- `home` RF-02 alegava que `/index.php` resolve para home → corrigido (vai para NotFound).
- `home` NFR citava "pipe `|>`" como evidência de PHP 8.5+ → corrigido (evidência real: `php -v` 8.5.0).
- `sobre/design` alegava parâmetro `action` na view → corrigido (fixo no HTML).
