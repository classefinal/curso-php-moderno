# Investigation: Correções da Revisão (P1–P14)

> Identificador: `001-correcoes-revisao`
> Data: `2026-08-13`
> Feature-dir: `_reversa_forward/001-correcoes-revisao/`

## 1. Pergunta de investigação

Confirmar a mecânica das correções no legado para que cada decisão do roadmap seja executável sem ambigüidade: como o runner de migrations marca execuções, como `mysqli` reporta linhas afetadas, qual o padrão de escape e de flash já existentes no código.

## 2. Fatos confirmados no legado

| Fato | Fonte no `_reversa_sdd/` | Implicação |
|------|--------------------------|------------|
| O runner de migrations marca `migrations.executed = 1` por arquivo e executa em ordem numérica do nome (`1_`, `2_`, …); **não há checksum** | `state-machines.md#Migration`, `architecture.md#Pilares` | Corrigir migration in-place não reexecuta em ambientes já migrados; só bancos limpos são afetados (D-01) |
| `response()` aceita status code (ex.: `validateLogin` usa `response(401, $content)`) | `code-analysis.md#auth`, `login/design.md` | `NotFound` pode chamar `response(404, $content)` sem nova infraestrutura (D-02) |
| O perfil já grava a sessão **sem** a coluna `password` após atualização | `domain.md#Perfil`, `atualizar-perfil/design.md` | O mesmo recorte pode ser aplicado no `LoginService` (D-03) |
| `htmlspecialchars` é o padrão de escape já usado em `profile.php:33,37` | `code-analysis.md#Alertas` (item 6: só o perfil escapa) | Replicar com `ENT_QUOTES` nas views afetadas (D-04) |
| Seed e `DUMMY_PASSWORD_HASH` usam `PASSWORD_BCRYPT` com `cost => 16` | `domain.md#Autenticação`, `autenticar-admin/requirements.md` | Normalizar o `password_hash` do perfil para o mesmo cost (D-05) |
| `decrease` com `quantity <= 1` remove; `quantity` nunca fica 0 ou negativo | `state-machines.md#Item-de-carrinho` | Validação de estoque interfere apenas em `add`/`increase` (D-06) |
| Itens do cookie são enriquecidos apenas com produtos **ativos**; carrinho logado lê `cart_items` via JOIN | `code-analysis.md#cart`, `carrinho/design.md` | Filtrar `active = true` também no JOIN do carrinho logado (D-07) |
| Flash de sucesso/erro usa `$_SESSION['flash']` na página Sobre | `code-analysis.md#about` | Reutilizar o mesmo mecanismo no carrinho (D-08) |
| Não existe `.gitignore` no projeto | verificação direta (glob) | Criar com `logs/` (D-09) |

## 3. Alternativas avaliadas

### Migrations: in-place vs. fixadoras novas
- **In-place (escolhido):** reescrever o DDL dos arquivos 7 e 8. Bancos já migrados: indiferentes (sem checksum). Bancos limpos: schema correto. Vantagem: nenhuma migration fantasma com DDL duplicado.
- **Novas migrations 11/12:** em banco limpo, a 7/8 criariam o schema errado e a 11/12 corrigiriam — o `migrate.php` nunca falharia, mas deixaria `email`/`password` criadas duas vezes (erro de sintaxe) ou exigiria `IF NOT EXISTS`. Rejeitada: não reproduz o schema real e polui a história.
- **Não fazer nada:** mantém ADR-008/009 abertas e o projeto não boota de banco limpo. Rejeitada.

### 404: status code vs. redirect
- **`response(404, $content)` (escolhido):** padrão já existente; mantém a view `not_found.php`.
- **Redirect para `/`:** esconderia o erro de roteamento. Rejeitado.
- **Manter 200:** contrato HTTP incorreto (P9 rejeitou).

### Sessão: remover `password` vs. guardar só `id`
- **Remover `password` do array (escolhido):** campos seguros continuam na sessão; o middleware `auth` já recarrega o usuário do banco quando precisa de dados frescos.
- **Guardar só `id`:** exigiria recarga explícita em todos os pontos que leem sessão (perfil usa `$configs['user']` do middleware; carrinho logado usa `$_SESSION['user']['id']`). Mais invasivo. Rejeitado.

### Cookie do carrinho: assinar vs. silencioso
- **Silencioso (escolhido):** decisão do usuário (P8/P10) — cookie é mero indicador de quantidade; validar estoque/ativo na operação.
- **HMAC/assinatura:** adicionaria custo e quebraria cookies existentes. Rejeitado pelo usuário.

### Feedback de erro: flash vs. sem ação
- **Flash no caminho de banco (escolhido):** o usuário escolheu o modelo híbrido (P10) — avisar no logado, silenciar no visitante.
- **Silêncio total:** mantém o bug UX do legado. Rejeitado.
- **Flash em ambos:** contraria a decisão do usuário. Rejeitado.

## 4. Semântica do `mysqli` para "0 linhas"

- `UPDATE cart_items SET quantity = quantity + 1 WHERE ...` sempre muda o valor → `affected_rows = 1` quando o item existe; se o item não existe, `0`.
- `UPDATE quantity = quantity - 1` para `quantity > 1`: idem.
- `DELETE ... WHERE cart_id = ? AND product_id = ?`: `1` se removeu, `0` se não encontrou.
- `INSERT`: `affected_rows = 1` (ou erro).
- Falha de prepared statement / execução: `dbPrepareAndExecute` hoje não trata erro (`code-analysis.md` alerta 4) — a correção D-08 exige checar o retorno da função (false/null) para reportar falha no caminho de banco.

## 5. Padrões aplicáveis

- **PRG (Post/Redirect/Get):** manter 302 após POST em todas as rotas de carrinho (ADR-003).
- **Flash:** `$_SESSION['flash']` + leitura/limpeza na view (padrão `/sobre`).
- **Escape:** `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` na view, nunca na escrita.
- **Consultas tipadas:** `dbPrepareAndExecute` com `['type' => 's'/'i', 'value' => ...]` (ADR-004).

## 6. Fontes externas

- Documentação PHP: `password_hash` / `PASSWORD_BCRYPT` `cost` (padrão 10–12; 16 é válido, porém mais lento).
- Documentação PHP: `mysqli_stmt::$affected_rows` (linhas afetadas, não encontradas).

## 7. Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-13 | Versão inicial gerada por `/reversa-plan` | reversa |
