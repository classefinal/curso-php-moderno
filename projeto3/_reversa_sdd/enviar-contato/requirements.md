# Enviar Contato (POST /sobre), Requisitos

## Visão Geral

Recebe o formulário de contato da página Sobre (nome, e-mail, telefone), valida os dados, persiste em `contacts` e redireciona de volta com flash de sucesso ou erro.

## Responsabilidades

- Validar campos obrigatórios, formato de e-mail e telefone brasileiro.
- Normalizar o telefone para `+55<dígitos>`.
- Inserir o contato na tabela `contacts`.
- Redirecionar 302 para `/sobre` com flash de sucesso/erro.

## Regras de Negócio

- Nome obrigatório 🟢
- E-mail obrigatório e válido (`FILTER_VALIDATE_EMAIL`) 🟢
- Telefone obrigatório e no formato `^\(\d{2}\)\d{4,5}-\d{4}$` 🟢
- Telefone normalizado: `'+55' + somente dígitos` antes do INSERT 🟢
- Flash: `success` ou `error` gravado em `$_SESSION['flash']` 🟢
- Redireciona 302 para `/sobre` em todos os desfechos 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Validar nome não vazio | Must | Nome vazio → flash error "O nome é obrigatório" |
| RF-02 | Validar e-mail obrigatório | Must | E-mail vazio → flash error "O e-mail é obrigatório" |
| RF-03 | Validar formato do e-mail | Must | E-mail inválido → flash error "E-mail inválido" |
| RF-04 | Validar telefone obrigatório | Must | Telefone vazio → flash error "O telefone é obrigatório" |
| RF-05 | Validar formato do telefone `(00)00000-0000` | Must | Formato errado → flash error "Telefone inválido" |
| RF-06 | Normalizar telefone para `+55<dígitos>` | Must | `(11)99999-9999` vira `+5511999999999` |
| RF-07 | Inserir contato em `contacts` | Must | INSERT executado com nome, email, phone |
| RF-08 | Redirecionar 302 para `/sobre` com flash | Must | Sempre redireciona; flash reflete sucesso/erro |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Prepared statements para o INSERT | `src/Controllers/About.php:76-84` (inline via `dbPrepareAndExecute`) | 🟢 |
| Segurança | Sessão `httponly` | `app.php:5-7` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um formulário de contato preenchido com nome, e-mail válido e telefone válido
Quando o usuário envia POST /sobre
Então o contato é inserido e o usuário é redirecionado para /sobre com flash de sucesso

Dado um formulário com e-mail inválido
Quando o usuário envia POST /sobre
Então nada é inserido e o usuário é redirecionado para /sobre com flash de erro "E-mail inválido"

Dado um formulário com telefone fora do padrão brasileiro
Quando o usuário envia POST /sobre
Então nada é inserido e o usuário é redirecionado com flash de erro "Telefone inválido"
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Validações de obrigatórios/formato | Must | Impedem dados inválidos sem fallback |
| Normalização do telefone | Must | Contrato de armazenamento de `contacts.phone` |
| INSERT em `contacts` | Must | Persistência da unit |
| Flash + redirect | Must | Feedback obrigatório do fluxo |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:38-48` | rota `about_send` (POST `/sobre`, `sendContact`) | 🟢 |
| `src/Controllers/About.php:38-93` | `sendContact` (validações + INSERT inline) | 🟢 |
| `src/Migrations/10_create_contacts_table.php` | schema `contacts` | 🟢 |
| `src/Services/DB.php` | `dbPrepareAndExecute` | 🟢 |
