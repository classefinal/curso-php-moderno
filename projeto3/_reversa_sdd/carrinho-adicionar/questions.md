# Carrinho Adicionar (POST /carrinho/adicionar), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Validação de estoque 🟡

A adição não verifica `stock` — o cliente pode adicionar mais do que existe. Confirmar se o controle de estoque é intencionalmente ausente ou deve ser validado na adição (e no UPDATE de quantidade da unit `carrinho-atualizar`).

## Q2. Produto inexistente/inativo no DB 🟡

- Logado: `INSERT` em `cart_items` com `product_id` inexistente viola a FK `products(id)` → erro não tratado.
- Inativo: a FK aceita (só valida existência); o produto inativo entra no carrinho e só é filtrado no enrich do GET para visitante.
Confirmar o comportamento desejado (ex.: validar existência + `active` antes de escrever).

## Q3. Cookie de visitante não assinado 🟢 (confirmação)

O carrinho de visitante é um cookie `id:qtd` em texto puro, editável pelo cliente. Confirmar que não há requisito de integridade/assinatura para este cookie.
