# Carrinho (GET /carrinho), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Sessão sem recarga no carrinho 🟡

A rota do carrinho é pública e usa `$_SESSION['user']` direto (sem middleware `auth`). Isso diverge do perfil, que recarrega via `getUserById`. Confirmar se o carrinho deve validar `active`/existência do usuário.

## Q2. Segurança da view (XSS) 🟡

`name` e `image` são renderizados sem `htmlspecialchars` em `src/Pages/Cart/cart.php`. Nomes de produto são dados de banco (seed local), mas se um produto for criado via admin com HTML, haveria XSS. Manter o comportamento legado ou escapar?

## Q3. Cookie `cart_items` em texto puro 🟡

O carrinho de visitante é um cookie sem assinatura (`id:qtd,id:qtd`), editável pelo cliente. Sem validação de estoque no GET. Confirmar o modelo pretendido de carrinho de visitante (persistência em banco é exclusiva de logados).

## Q4. Migração 9 aplicada 🟢 (confirmação)

`carts`/`cart_items` são criados na migration 9 com FKs CASCADE. Confirmar que o ambiente real segue esse schema (a unit assume as tabelas existentes).
