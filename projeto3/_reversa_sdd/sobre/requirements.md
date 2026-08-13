# Sobre (GET /sobre), Requisitos

## Visão Geral

Página institucional "Sobre" da loja. Exibe o formulário de contato (nome, e-mail, telefone) com feedback via flash message e um iframe do Google Maps embutido.

## Responsabilidades

- Renderizar a página Sobre com formulário de contato.
- Exibir mensagens de sucesso/erro vindas da sessão (flash) e limpar o flash após leitura.
- Exibir iframe do Google Maps.

## Regras de Negócio

- A rota aceita apenas **GET** 🟢
- Flash é lido de `$_SESSION['flash']` e **removido** da sessão após leitura 🟢
- O formulário posta para `POST /sobre` (unit `enviar-contato`) 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Responder HTTP 200 com a página Sobre | Must | GET `/sobre` retorna 200 e HTML |
| RF-02 | Exibir formulário de contato (nome, e-mail, telefone) | Must | Campos presentes no HTML |
| RF-03 | Exibir flash de sucesso/erro quando houver | Must | Sessão com `flash[success/error]` aparece na página |
| RF-04 | Limpar o flash da sessão após a leitura | Must | Após renderizar, `$_SESSION['flash']` é removido |
| RF-05 | Exibir iframe do Google Maps | Could | Elemento iframe presente no HTML |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Sessão `httponly` habilitada | `app.php:5-7` | 🟢 |
| Compatibilidade | PHP 8.5+ | `app.php`, serviços | 🟢 |
| Segurança | Interpolações da view escapadas com `htmlspecialchars` (P7) | `src/Pages/about.php:15,18,22` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um visitante
Quando acessa a URL "/sobre"
Então recebe HTTP 200 com a página contendo o formulário de contato

Dado que existe uma mensagem de flash na sessão
Quando a página Sobre é renderizada
Então a mensagem é exibida e o flash é removido da sessão
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Renderizar página + formulário | Must | Caminho crítico da unit |
| Exibir/limpar flash | Must | Feedback do POST sem fallback |
| iframe do Google Maps | Could | Decorativo, sem impacto funcional |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:26-36` | rota `about` (GET `/sobre`, `makeAbout`) | 🟢 |
| `src/Controllers/About.php` | `makeAbout` | 🟢 |
| `src/Pages/about.php` | view da página | 🟢 |
| `src/Functions/Functions.php` | `getMenuItens` | 🟢 |
