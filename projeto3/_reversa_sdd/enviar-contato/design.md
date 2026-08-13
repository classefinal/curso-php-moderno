# Enviar Contato (POST /sobre), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| POST | `/sobre` | `name`, `email`, `phone` (form-urlencoded) | Redirect Location: `/sobre` | 302 (sempre) |

## Fluxo Principal

1. `sendContact` lê `name`, `email`, `phone` de `$_POST` com `trim`. `src/Controllers/About.php:40-42`
2. Validações em cadeia, cada falha grava `$_SESSION['flash']['error']` e redireciona 302:
   - nome vazio → "O nome é obrigatório." (`:44-48`)
   - e-mail vazio → "O e-mail é obrigatório." (`:50-54`)
   - e-mail inválido (`filter_var FILTER_VALIDATE_EMAIL`) → "E-mail inválido." (`:56-60`)
   - telefone vazio → "O telefone é obrigatório." (`:62-66`)
   - telefone não casa `^\(\d{2}\)\d{4,5}-\d{4}$` → "Telefone inválido. Use o formato (00)94878-4541." (`:68-72`)
3. Normaliza: `'+55' . preg_replace('/\D/', '', $phone)` → `+5511999999999`. `:74`
4. INSERT em `contacts (name, email, phone)` via `dbPrepareAndExecute` com tipos `s,s,s`. `:76-84`
5. Resultado: `$result` truthy → flash success "Mensagem enviada com sucesso!"; senão flash error "Erro ao enviar mensagem. Tente novamente.". `:86-90`
6. Redireciona 302 para `/sobre` (dispara defer pós-flush). `:92`

## Fluxos Alternativos

- **Qualquer validação falha:** redirect imediato 302 com flash error; nenhuma escrita no banco.
- **INSERT falha** (ex.: banco indisponível): `dbPrepareAndExecute` retorna `false` → flash error genérico. 🟡 (o erro de prepared statement não é tratado — ver `src/Services/DB.php`)

## Dependências

- **DB** (`dbPrepareAndExecute`), para o INSERT.
- **Response** (`redirect`), para o redirect 302 + defer.
- **Session**, para o flash.
- Tabela **contacts** (migration 10).

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Lógica de validação/persistência inline no controller (sem service) | `src/Controllers/About.php:38-93` | 🟢 |
| Telefone armazenado normalizado com DDI `+55` | `src/Controllers/About.php:74` | 🟢 |
| Flash único por campo, sem acumular erros | `src/Controllers/About.php` | 🟢 |

## Estado Interno

- **Sessão:** `$_SESSION['flash']['success'|'error']`, consumido pela página `about` na próxima renderização.

## Observabilidade

- Nenhum log emitido. Falha de INSERT é refletida apenas como flash de erro.

## Riscos e Lacunas

- 🟡 Falha de prepared statement retorna `false` sem detalhe do erro — diagnóstico difícil (`src/Services/DB.php`).
- 🟢 Nenhuma outra lacuna: contrato de validação totalmente coberto pelo código.
