# Carrinho Remover (POST /carrinho/remover), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Falhas silenciosas 🟡

Carrinho ou item inexistentes geram erro no serviço, mas o controller ignora e redireciona 302 sem mensagem. Manter o comportamento legado ou exibir feedback?

## Q2. Remoção total sem confirmação 🟢 (confirmação)

O botão "remover" executa o DELETE imediatamente (sem diálogo de confirmação). Confirmar que não há requisito de confirmação/undo.

## Q3. DELETE sem verificar `active` do produto 🟢 (confirmação)

A remoção não verifica status do produto — remove pelo `product_id` informado. Confirmar que não há regra adicional de remoção condicionada a status do produto.
