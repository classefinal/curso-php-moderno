# Regression Watch: Extrair Contact Service

> Identificador: `002-extrair-contact-service`
> Data: `2026-08-25`

## Watch principal

| ID | Origem (arquivo, seção) | Regra esperada após mudança | Tipo de verificação | Sinal de violação |
|----|-------------------------|-----------------------------|--------------------|--------------------|
| W001 | `_reversa_sdd/domain.md#Contato` | Nome do contato deve ter entre 3 e 255 caracteres | redação | Nome com menos de 3 ou mais de 255 chars é aceito sem erro |
| W002 | `_reversa_sdd/domain.md#Contato` | Email do contato deve ter no máximo 255 caracteres | redação | Email com mais de 255 chars é aceito sem erro |
| W003 | `_reversa_sdd/domain.md#Contato` | Telefone do contato deve ter entre 10 e 20 caracteres | redação | Telefone fora do range 10–20 é aceito sem erro |
| W004 | `_reversa_sdd/domain.md#Contato` | Telefone normalizado para `+55<dígitos>` antes do INSERT | presença | Telefone chega ao banco com caracteres especiais (parênteses, traço) |
| W005 | `_reversa_sdd/code-analysis.md#about` | Controller `sendContact` não contém lógica de validação nem de banco | ausência | Controller contém `filter_var`, `preg_match` ou `dbPrepareAndExecute` |
| W006 | `_reversa_sdd/domain.md#Contato` | Flash de sucesso: "Mensagem enviada com sucesso!" | redação | Mensagem de sucesso alterada sem atualizar esta spec |

## Observações

- `validateContactName`, `validateContactEmail`, `validateContactPhone` são funções auxiliares internas do service — não têm watch próprio pois são implementação, não contrato
- Contrato HTTP POST `/sobre` permanece idêntico (request/response/flash) — ver `interfaces/post-sobre.md`

## Histórico de re-extrações

| Data | Runner | Itens verificados | Itens violados |
|------|--------|-------------------|----------------|
| (nenhuma ainda) | | | |

## Arquivadas

(nenhuma)
