# Carrinho (GET /carrinho), Tarefas de Implementação

## Pré-requisitos

- [ ] Migrations 4-7 (produtos) e 9 (carts/cart_items) aplicadas
- [ ] Função de renderização de views disponível

## Tarefas

- [ ] T-01, Registrar a rota `cart_page` (GET `/carrinho`, controller `Cart/Cart`, `makeCart`, sem middlewares)
  - Origem no legado: `src/Configs/routes.php:167-178`
  - Critério de pronto: GET `/carrinho` invoca `makeCart`
  - Confiança: 🟢

- [ ] T-02, Implementar `getCartByUserId` (`SELECT * FROM carts WHERE user_id = ? LIMIT 1`)
  - Origem no legado: `src/Services/Cart/CartService.php:9-24`
  - Critério de pronto: retorna carrinho ou `null`
  - Confiança: 🟢

- [ ] T-03, Implementar `getCartItems` com `INNER JOIN products` (name, price, image, stock, description_line)
  - Origem no legado: `src/Services/Cart/CartService.php:176-194`
  - Critério de pronto: itens com dados do produto; [] se vazio
  - Confiança: 🟢

- [ ] T-04, Implementar `calculateCartTotal` (Σ `price × quantity` em centavos)
  - Origem no legado: `src/Services/Cart/CartService.php:196-205`
  - Critério de pronto: total inteiro correto
  - Confiança: 🟢

- [ ] T-05, Implementar `getCartItemsFromCookie` (parse `id:qtd,id:qtd` validando ints ≥ 1)
  - Origem no legado: `src/Services/Cart/CartService.php:209-239`
  - Critério de pronto: pares inválidos descartados
  - Confiança: 🟢

- [ ] T-06, Implementar `enrichCartItemsWithProductData` (busca por produto ativo, descarta inativos, monta `CartItem` com `id=0`/`cart_id=0`/datas vazias)
  - Origem no legado: `src/Services/Cart/CartService.php:320-357`
  - Critério de pronto: itens enriquecidos, apenas produtos ativos
  - Confiança: 🟢

- [ ] T-07, Implementar `makeCart` com o desvio logado/cookie e renderizar `Cart/cart` (title, routes, items, total) → 200
  - Origem no legado: `src/Controllers/Cart/Cart.php:12-43`
  - Critério de pronto: 200 com dados corretos por estado
  - Confiança: 🟢

- [ ] T-08, Implementar a view `src/Pages/Cart/cart.php` (estado vazio, tabela com preço/qtd/subtotal formatados em BRL, forms POST para `/carrinho/atualizar` e `/carrinho/remover`, box de total)
  - Origem no legado: `src/Pages/Cart/cart.php`
  - Critério de pronto: HTML coerente com os forms das rotas cart-update/cart-remove
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Logado com carrinho → 200 com itens (JOIN) e total correto
- [ ] TT-02, Logado sem carrinho → 200 vazio "Seu carrinho está vazio."
- [ ] TT-03, Visitante com cookie válido → 200 com itens enriquecidos
- [ ] TT-04, Cookie com par inválido (`abc`) → item descartado
- [ ] TT-05, Cookie com produto inativo → item não exibido
- [ ] TT-06, Formatação `R$ X.XXX,XX` para preço e subtotal
- [ ] TT-07, Forms com `action`/`product_id` corretos nos botões − / + / remover

## Tarefas de Migração de Dados (se aplicável)

- [ ] TM-01, Aplicar migration 9 (carts/cart_items com FKs CASCADE)

## Ordem Sugerida

1. T-02 → T-03 → T-04 → T-05 → T-06 (serviço)
2. T-01 (rota) → T-07 (controller) → T-08 (view)
3. Testes TT-01–TT-07 ao final

## Lacunas Pendentes (🔴)

- 🔴 Nenhuma humana para esta unit. (Melhorias 🟡: escape de `name`/`image`; assinatura do cookie `cart_items`.)
