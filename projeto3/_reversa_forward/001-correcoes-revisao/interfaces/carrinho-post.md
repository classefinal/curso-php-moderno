# Interface HTTP: POST /carrinho/* — Delta da feature 001

> Identificador: `001-correcoes-revisao`
> Data: `2026-08-13`
> Contrato base extraído em: `_reversa_sdd/carrinho-adicionar/contracts.md` (e units `carrinho-atualizar`, `carrinho-remover`)
> Este arquivo descreve **somente o delta** introduzido pelas correções P10/P11/P12; o restante do contrato permanece como documentado nas units.

## Endpoints afetados

| Endpoint | Método | Mudança |
|----------|--------|---------|
| `/carrinho/adicionar` | POST | Flash de erro em falha de banco (logado); estoque/ativo validados |
| `/carrinho/atualizar` | POST | Flash de erro em falha de banco (logado); estoque respeitado no `increase` |
| `/carrinho/remover` | POST | Flash de erro em falha de banco (logado) |

## Comportamento após a correção

### Logado (banco)

- **Sucesso:** 302 `Location: /carrinho`, sem flash (comportamento atual).
- **Falha/0 linhas** (INSERT/UPDATE/DELETE sem efeito ou erro de statement): 302 `Location: /carrinho` **com flash de erro** (P10). O status continua **302** (PRG preservado — ADR-003).
- **Estoque insuficiente ou produto inexistente/inativo** (P11/P12): operação **bloqueada**; o comportamento de resposta segue a mesma regra acima (sem alteração do produto no carrinho).

### Visitante (cookie)

- **Comportamento inalterado e silencioso (P10):** 302 `Location: /carrinho`, sem flash, mesmo quando o item não existe no cookie.
- **Estoque/ativo (P11/P12):** a operação é validada (leitura de `stock`/`active` no banco) e bloqueada se o produto estiver indisponível; o cookie não é alterado.

## Formato do flash

- Reutiliza `$_SESSION['flash']` (mesmo mecanismo da página `/sobre` — `code-analysis.md#about`).
- A view `src/Pages/Cart/cart.php` passa a exibir o flash de erro, com escape `htmlspecialchars` (P7).

## Códigos de status

| Cenário | Status |
|---------|--------|
| Qualquer POST de carrinho | 302 (inalterado) |
| `product_id` inválido (adicionar) | 302 `/produtos` (inalterado) |
| `product_id`/`action` inválidos (atualizar/remover) | 302 `/carrinho` (inalterado) |

## Idempotência e timeouts

- **Idempotência:** inalterada — adicionar/incrementar é naturalmente idempotente por `quantity`; as correções não alteram isso.
- **Timeouts:** nenhum timeout explícito no cliente; sem mudança.

## Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-13 | Versão inicial gerada por `/reversa-plan` | reversa |
