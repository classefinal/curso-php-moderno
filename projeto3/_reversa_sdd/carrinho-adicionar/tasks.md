# Carrinho Adicionar (POST /carrinho/adicionar), Tarefas de Implementação

## Pré-requisitos

- [ ] Migration 9 aplicada (carts/cart_items)
- [ ] Unit `carrinho` (GET) com os serviços de leitura implementados

## Tarefas

- [ ] T-01, Registrar a rota `cart_add` (POST `/carrinho/adicionar`, controller `Cart/Cart`, `doAddToCart`, sem middlewares)
  - Origem no legado: `src/Configs/routes.php:179-190`
  - Critério de pronto: POST `/carrinho/adicionar` invoca `doAddToCart`
  - Confiança: 🟢

- [ ] T-02, Implementar `createCart` (INSERT `carts (user_id)`) e `getOrCreateCart`
  - Origem no legado: `src/Services/Cart/CartService.php:26-51`
  - Critério de pronto: cria ou reutiliza o carrinho do usuário
  - Confiança: 🟢

- [ ] T-03, Implementar `addToCart` (busca item; UPDATE `quantity + 1` ou INSERT com `quantity = 1`)
  - Origem no legado: `src/Services/Cart/CartService.php:53-89`
  - Critério de pronto: item único por produto, quantidade incrementada
  - Confiança: 🟢

- [ ] T-04, Implementar `saveCartCookie` (setcookie 30 dias) e `addToCartCookie` (incremento ou insert no cookie)
  - Origem no legado: `src/Services/Cart/CartService.php:241-273`
  - Critério de pronto: cookie regravado corretamente
  - Confiança: 🟢

- [ ] T-05, Implementar `doAddToCart` com validação de `product_id` (`FILTER_VALIDATE_INT`, `min_range => 1`) e redirect 302 (`/carrinho` válido; `/produtos` inválido)
  - Origem no legado: `src/Controllers/Cart/Cart.php:45-63`
  - Critério de pronto: desvio logado/cookie e PRG corretos
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, 1ª adição logado → cria `carts` e insere `cart_items` qtd 1 → 302 `/carrinho`
- [ ] TT-02, 2ª adição do mesmo produto → `quantity` vira 2 (sem linha duplicada)
- [ ] TT-03, Adição de visitante → cookie `cart_items` com `id:qtd`
- [ ] TT-04, Cookie com produto repetido → incremento
- [ ] TT-05, `product_id` inválido/ausente → 302 `/produtos` sem escrita
- [ ] TT-06, Produto inexistente (DB) → verificar comportamento de FK (esperado: erro não tratado — registrar lacuna)

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Nenhuma (schema da migration 9 já cobre a unit)

## Ordem Sugerida

1. T-02 → T-03 (serviço DB) → T-04 (serviço cookie)
2. T-01 (rota) → T-05 (controller)
3. Testes TT-01–TT-06 ao final

## Lacunas Pendentes (🔴)

- 🔴 Nenhuma humana para esta unit. (Lacunas 🟡: estoque não validado; FK sem tratamento em produto inexistente.)
