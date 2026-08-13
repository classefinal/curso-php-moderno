# User Story — Contato

> Fluxo de usuário cobrindo as units: `sobre/` (GET) e `enviar-contato/` (POST `/sobre`).

## Narrativa

Um visitante acessa a página Sobre, encontra o formulário de contato e envia uma mensagem com nome, e-mail e telefone. Após o envio, é redirecionado de volta com um flash de sucesso ou erro, conforme a validação.

## Persona

- **Visitante**: usuário anônimo (não precisa estar autenticado).

## Jornada

1. Acessa `GET /sobre` e vê o formulário de contato. 🟢 `src/Controllers/About.php`
2. Preenche nome, e-mail e telefone no formato `(11)99999-9999`. 🟢
3. Envia `POST /sobre`. 🟢 `src/Configs/routes.php:38-48`
4. Validações: nome obrigatório, e-mail obrigatório/válido, telefone obrigatório no padrão `^\(\d{2}\)\d{4,5}-\d{4}$`. 🟢 `src/Controllers/About.php:38-93`
5. Telefone normalizado para `+55<dígitos>` antes do INSERT em `contacts`. 🟢
6. Sucesso → flash de sucesso e 302 `/sobre`. 🟢
7. Falha → flash de erro (mensagem específica) e 302 `/sobre` — **nada é inserido**. 🟢
8. A página Sobre re-renderiza exibindo o flash. 🟢

## Regras observadas no código

| Regra | Evidência | Confiança |
|-------|-----------|-----------|
| INSERT tipado com `dbPrepareAndExecute` | `src/Controllers/About.php` | 🟢 |
| Flash em `$_SESSION['flash']` (success/error) | `src/Controllers/About.php` | 🟢 |
| Telefone armazenado normalizado `+55<dígitos>` | `src/Controllers/About.php` | 🟢 |
| Redirect 302 em todos os desfechos | `src/Controllers/About.php` | 🟢 |

## Critérios de Aceite

```gherkin
Dado um formulário com nome, e-mail válido e telefone válido
Quando envia POST /sobre
Então o contato é inserido em contacts e há flash de sucesso

Dado um formulário com e-mail inválido
Quando envia POST /sobre
Então nada é inserido e há flash de erro "E-mail inválido"

Dado um formulário com telefone fora do padrão (00)00000-0000
Quando envia POST /sobre
Então nada é inserido e há flash de erro "Telefone inválido"
```

## Métricas de sucesso (sugeridas)

- Taxa de envio concluído vs. erros de validação.
- Quantidade de mensagens recebidas (contagens em `contacts`).

## Pontos de atenção

- 🟢 Tabela `contacts` criada na migration 10 — schema confiável.
- 🟡 Sem captcha/rate-limit no formulário (público, sem autenticação).
- 🟡 Sem confirmação por e-mail para o visitante (feedback apenas via flash na página).
