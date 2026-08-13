# Home (GET /), Requisitos

## Visão Geral

Página inicial (landing) estática da loja. É a rota **default** do sistema: quando a URI está vazia, o Router a resolve automaticamente. Exibe o título da página e o menu de navegação.

## Responsabilidades

- Renderizar a página inicial com título e menu de navegação.
- Servir como rota default quando a URI é vazia.
- Responder sempre HTTP 200.

## Regras de Negócio

- URI vazia → rota default `home` (sem passar por `resolveRoute`) 🟢
- Rota `home` exige método **GET** 🟢
- A página é estática (sem interação com banco) 🟢
- Menu montado via `getMenuItens()` (filtra rotas `inMenu` e ordena por `order`) 🟢

## Requisitos Funcionais

| ID | Requisito | Prioridade | Critério de Aceite |
|----|-----------|-----------|-------------------|
| RF-01 | Responder HTTP 200 com a página inicial ao acessar `/` | Must | GET `/` retorna 200 e HTML da página |
| RF-02 | Responder HTTP 200 com a página inicial quando a URI é vazia ou `/` | Must | URI vazia cai na rota default; `/` resolve via `resolveRoute` para `home` |
| RF-03 | Exibir o menu de navegação com as rotas visíveis | Must | Menu renderiza itens `inMenu=true` ordenados por `order` |
| RF-04 | Exibir título da página | Must | `title` presente no HTML renderizado |

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Compatibilidade | Projeto declara mínimo PHP 8.1+ (AGENTS.md); ambiente de desenvolvimento executa PHP 8.5.0 | `AGENTS.md`; confirmado via `php -v` (8.5.0) | 🟢 |
| Segurança | Sessão iniciada com `httponly` antes de qualquer saída | `app.php:5-7` | 🟢 |

## Critérios de Aceitação

```gherkin
Dado um visitante com acesso ao sistema
Quando ele acessa a URL "/"
Então recebe HTTP 200 com a página inicial renderizada

Dado um visitante acessando o sistema com URI vazia
Quando o front controller processa a requisição
Então o Router usa a rota default "home" sem passar por resolveRoute
```

## Prioridade (MoSCoW)

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Renderizar página inicial | Must | Caminho crítico, rota default de entrada |
| Servir de rota default | Must | Comportamento do Router sem alternativa |
| Exibir menu de navegação | Must | Navegação de todas as rotas depende disso |
| Exibir título | Should | Apresentação; sem ele a página ainda funciona |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `src/Configs/routes.php:14-24` | rota `home` (GET `/`, `makeHome`) | 🟢 |
| `src/Controllers/Home.php` | `makeHome` | 🟢 |
| `src/Pages/home.php` | view da página | 🟢 |
| `src/Services/Router.php` | `processRoutes` (rota default) | 🟢 |
| `src/Functions/Functions.php` | `getMenuItens` | 🟢 |
