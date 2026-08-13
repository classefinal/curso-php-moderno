# Enviar Contato (POST /sobre), Tarefas de Implementação

## Pré-requisitos

- [ ] Tabela `contacts` criada (migration 10)
- [ ] `dbPrepareAndExecute` disponível
- [ ] Response `redirect` com flash de sessão disponível

## Tarefas

- [ ] T-01, Registrar a rota `about_send` (POST `/sobre`, `About.sendContact`, `inMenu=false`)
  - Origem no legado: `src/Configs/routes.php:38-48`
  - Critério de pronto: POST `/sobre` resolve para `sendContact`
  - Confiança: 🟢

- [ ] T-02, Implementar `sendContact` com as 5 validações em cadeia (nome, e-mail obrigatório, formato e-mail, telefone obrigatório, formato telefone)
  - Origem no legado: `src/Controllers/About.php:44-72`
  - Critério de pronto: cada falha grava flash error específico e redireciona 302
  - Confiança: 🟢

- [ ] T-03, Implementar normalização do telefone (`+55` + dígitos)
  - Origem no legado: `src/Controllers/About.php:74`
  - Critério de pronto: `(11)99999-9999` → `+5511999999999`
  - Confiança: 🟢

- [ ] T-04, Implementar INSERT em `contacts` e flash de sucesso/erro conforme o resultado
  - Origem no legado: `src/Controllers/About.php:76-92`
  - Critério de pronto: INSERT com `s,s,s`; flash reflete resultado; redirect 302
  - Confiança: 🟢

- [ ] T-05, Criar a tabela `contacts` (migration 10: `id, name, email, phone, created_at`)
  - Origem no legado: `src/Migrations/10_create_contacts_table.php`
  - Critério de pronto: DDL roda sem erro e registra `executed=1`
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Happy path: POST com dados válidos → 302 + registro em `contacts` + flash sucesso
- [ ] TT-02, E-mail inválido → 302 sem INSERT + flash "E-mail inválido."
- [ ] TT-03, Telefone inválido → 302 sem INSERT + flash "Telefone inválido. Use o formato (00)94878-4541."
- [ ] TT-04, Normalização: telefone persistido com `+55` e só dígitos
- [ ] TT-05, INSERT falhando → 302 + flash "Erro ao enviar mensagem. Tente novamente."

## Tarefas de Migração de Dados (se aplicável)

- Nenhuma.

## Ordem Sugerida

1. T-05 (schema) → T-01 (rota) → T-02 → T-03 → T-04 (controller, na ordem das validações).

## Lacunas Pendentes (🔴)

- Nenhuma. Observação 🟡: `dbPrepareAndExecute` não propaga erro de prepared statement — validar com equipe se precisa de logs.
