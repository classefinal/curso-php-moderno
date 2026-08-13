# Carrinho Atualizar (POST /carrinho/atualizar), Perguntas e Lacunas

> Marcador 🔴 LACUNA — dependem de validação humana. Preencha abaixo e avise o Reversa.

## Q1. Limite de estoque 🟡

`increase` não verifica `stock` — é possível ultrapassar a disponibilidade. Confirmar se há requisito de validar quantidade máxima contra `stock` (mesma dúvida da unit `carrinho-adicionar`).

## Q2. Falhas silenciosas 🟡

Quando o carrinho ou o item não existe, o serviço retorna erro, mas o controller ignora e redireciona 302 sem mensagem. Manter esse comportamento legado ou exibir feedback?

## Q3. `decrease` como remoção 🟢 (confirmação)

Em `quantity = 1`, `decrease` **remove** o item (mesmo efeito de `/carrinho/remover`). Confirmar que esse é o comportamento desejado (não há botão distinto de "zerar").
