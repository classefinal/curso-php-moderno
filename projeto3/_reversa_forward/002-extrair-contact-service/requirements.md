# Requirements: Extrair Contact Service

> Identificador: `002-extrair-contact-service`
> Data: `2026-08-25`
> Pasta da extração reversa: `_reversa_sdd/`
> Confidência: 🟢 CONFIRMADO, 🟡 INFERIDO, 🔴 LACUNA / DÚVIDA

## 1. Resumo executivo

A lógica de envio de formulário de contato (`sendContact`) está misturada com a lógica de exibição no controller `About.php`. Esta feature extrai toda a lógica de negócio (validação, normalização e persistência) para um service dedicado `ContactService`, adiciona validações de tamanho de campo alinhadas ao schema do banco (`VARCHAR(255)` para nome/email, `VARCHAR(20)` para telefone) e refatora o controller para usar early return, eliminando os ifs encadeados.

## 2. Contexto a partir do legado

| Fonte | Trecho relevante | Confidência |
|-------|------------------|-------------|
| `_reversa_sdd/architecture.md#Camadas` | Services de negócio ficam em `src/Services/{Domain}/` | 🟢 |
| `_reversa_sdd/domain.md#Contato` | Campos obrigatórios: nome, email, telefone; email com `FILTER_VALIDATE_EMAIL`; telefone regex `^\(\d{2}\)\d{4,5}-\d{4}$`; normalização `+55<dígitos>` | 🟢 |
| `_reversa_sdd/code-analysis.md#about` | `sendContact` valida, normaliza, insere em `contacts` e redireciona 302 com flash | 🟢 |
| `src/Migrations/10_create_contacts_table.php` | Schema: `name VARCHAR(255) NOT NULL`, `email VARCHAR(255) NOT NULL`, `phone VARCHAR(20) NOT NULL` | 🟢 |
| `_reversa_sdd/addenda/001-correcoes-revisao.md` | Adendo vigente: views já usam `htmlspecialchars` na interpolação de dados | 🟢 |

## 3. Personas e cenários de uso

| Persona | Objetivo | Cenário-chave |
|---------|----------|---------------|
| Visitante | Enviar mensagem de contato pela página Sobre | Preenche nome, email e telefone no formulário e submete |
| Desenvolvedor | Manter o código organizado e validado | ContactService encapsula toda a lógica de negócio do contato |

## 4. Regras de negócio novas ou alteradas

1. **RN-01:** A lógica de validação, normalização e persistência do contato deve estar isolada em `src/Services/Contact/ContactService.php` 🟢
   - Origem no legado: `_reversa_sdd/code-analysis.md#about` (sendContact inline no controller)
   - Tipo: nova
2. **RN-02:** Nome deve ter entre 3 e 255 caracteres (conforme `VARCHAR(255)` na migration e regra existente de perfil) 🟢
   - Origem no legado: `src/Migrations/10_create_contacts_table.php`, `_reversa_sdd/domain.md#Perfil` (3–255 chars)
   - Tipo: alterada (antes só checava vazio)
3. **RN-03:** Email deve ter no máximo 255 caracteres (conforme `VARCHAR(255)` na migration) 🟢
   - Origem no legado: `src/Migrations/10_create_contacts_table.php`
   - Tipo: nova
4. **RN-04:** Telefone deve ter entre 10 e 20 caracteres (conforme `VARCHAR(20)` na migration) 🟢
   - Origem no legado: `src/Migrations/10_create_contacts_table.php`
   - Tipo: nova
5. **RN-05:** O controller `sendContact` deve delegar toda a lógica de negócio ao service e usar early return para cada cenário de flash 🟢
   - Origem no legado: padrão observado em `LoginService.php` (validação retorna array `success/error`)
   - Tipo: alterada

## 5. Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de aceite | Confidência |
|----|-----------|------------|--------------------|-------------|
| RF-01 | Criar `src/Services/Contact/ContactService.php` com função `sendContact` que receba a conexão do banco e retorne array com chaves `success` (bool) e `error` (string ou null) | Must | Arquivo existe, função retorna array com chaves `success` e `error` | 🟢 |
| RF-02 | Validar nome: obrigatório, mínimo 3 caracteres, máximo 255 | Must | Nome com < 3 chars retorna erro; nome > 255 chars retorna erro; vazio retorna erro | 🟢 |
| RF-03 | Validar email: obrigatório, formato válido, máximo 255 caracteres | Must | Email inválido retorna erro; > 255 chars retorna erro | 🟢 |
| RF-04 | Validar telefone: obrigatório, no formato (XX)XXXXX-XXXX ou (XX)XXXX-XXXX, entre 10 e 20 caracteres | Must | Telefone fora do padrão retorna erro | 🟢 |
| RF-05 | Normalizar telefone removendo caracteres não numéricos e antecipando código do país `+55` | Must | Telefone `(11)94878-4541` vira `+5511948784541` | 🟢 |
| RF-06 | Inserir registro na tabela `contacts` com prepared statements parametrizados (parâmetros tipados como string) | Must | INSERT executado com parâmetros corretos; falha retorna `success = false` | 🟢 |
| RF-07 | Controller `sendContact` em `About.php` delega toda lógica de negócio ao service e redireciona com flash de sucesso ou erro | Must | Controller não contém lógica de validação/persistência; usa early return | 🟢 |
| RF-08 | Cada cenário de erro deve retornar imediatamente (early return) com mensagem descritiva, sem if/else encadeado | Must | Código não tem ifs encadeados; cada erro faz redirect com flash e return | 🟢 |

## 6. Requisitos Não Funcionais

| Tipo | Requisito | Evidência ou justificativa | Confidência |
|------|-----------|----------------------------|-------------|
| Manutenibilidade | Service deve seguir o padrão procedural do projeto (funções globais, sem classes) | Padrão observado em `LoginService.php`, `CartService.php` | 🟢 |
| Segurança | Prepared statements tipados para evitar SQL injection | Padrão do projeto via `dbPrepareAndExecute` | 🟢 |
| Compatibilidade | Flash messages devem manter o formato `$_SESSION['flash']['success']`/`['error']` | Contrato existente com a view `about.php` | 🟢 |

## 7. Critérios de Aceitação

```gherkin
Cenário: Contato enviado com sucesso
  Dado que o visitante preenche nome "João Silva" (10 chars), email "joao@teste.com" e telefone "(11)94878-4541"
  Quando submete o formulário
  Então o service retorna success = true
  E o controller redireciona para /sobre com flash de sucesso

Cenário: Nome muito curto
  Dado que o visitante preenche nome "Jo" (2 chars)
  Quando submete o formulário
  Então o service retorna success = false com erro "O nome deve ter no mínimo 3 caracteres."

Cenário: Nome excede 255 caracteres
  Dado que o visitante preenche um nome com 256 caracteres
  Quando submete o formulário
  Então o service retorna success = false com erro "O nome deve ter no máximo 255 caracteres."

Cenário: Email excede 255 caracteres
  Dado que o visitante preenche um email com 256 caracteres
  Quando submete o formulário
  Então o service retorna success = false com erro "O e-mail deve ter no máximo 255 caracteres."

Cenário: Telefone fora do padrão
  Dado que o visitante preenche telefone "12345"
  Quando submete o formulário
  Então o service retorna success = false com erro "Telefone inválido. Use o formato (00)94878-4541."

Cenário: Falha ao inserir no banco
  Dado que a inserção no banco falha
  Quando o service tenta persistir
  Então o service retorna success = false com erro "Erro ao enviar mensagem. Tente novamente."

Cenário: Normalização do telefone
  Dado que o visitante preenche telefone "(11)94878-4541"
  Quando o service processa o telefone
  Então o telefone é normalizado para "+5511948784541" antes do INSERT

Cenário: Controller delega ao service
  Dado que o formulário é submetido com dados válidos
  Quando o controller `sendContact` é executado
  Então ele chama o service `sendContact` e redireciona com flash de sucesso
  E não contém lógica de validação nem de banco

Cenário: Early return em cada erro
  Dado que o nome é vazio
  Quando o service valida os campos
  Então retorna imediatamente com erro "O nome é obrigatório."
  E não valida email nem telefone
```

## 8. Prioridade MoSCoW

| Item | MoSCoW | Justificativa |
|------|--------|---------------|
| RF-01 (criar service) | Must | Separação de responsabilidades, objetivo principal da feature |
| RF-02 a RF-04 (validações) | Must | Proteção contra dados inválidos no banco |
| RF-05 (normalização) | Must | Telefone já é normalizado no código atual |
| RF-06 (INSERT) | Must | Funcionalidade existente, migrada para o service |
| RF-07 (controller delega) | Must | Elimina lógica de negócio do controller |
| RF-08 (early return) | Should | Melhora legibilidade, mas não é funcional |

## 9. Esclarecimentos

> Nenhuma sessão de dúvidas registrada ainda. Rode `/reversa-clarify` quando houver `[DÚVIDA]` pendente.

## 10. Lacunas

Nenhuma lacuna identificada. Todas as informações foram extraídas do código-fonte e da migration.

## 11. Histórico de alterações

| Data | Alteração | Autor |
|------|-----------|-------|
| 2026-08-25 | Versão inicial gerada por `/reversa-requirements` | reversa |

## Emendas

### E001, 2026-08-25

O que muda: O type `LoginInfo` é renomeado para `OperationResult` (nome genérico) e ganha campo `data` opcional. Annotations `@return` e `@psalm-import-type` são atualizadas em LoginService, ContactService e UsersService.
Motivo: `LoginInfo` era restrito ao contexto de login; o mesmo padrão `success/error` é usado em contato e outros services. Campo `data` permite transportar dados auxiliares sem quebrar contratos existentes.
Arquivos previstos: `types.php`, `src/Services/Login/LoginService.php`, `src/Services/Contact/ContactService.php`, `src/Services/Users/UsersService.php`
