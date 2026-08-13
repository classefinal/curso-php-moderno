# Sobre (GET /sobre), Design Técnico

## Interface

| Método | Caminho | Entrada | Saída | Status codes |
|--------|---------|---------|-------|--------------|
| GET | `/sobre` | — (lê `$_SESSION['flash']`) | HTML da página Sobre | 200 |

Parâmetros da view (`makeAbout`): `title`, `routes`, `success`, `error`. (O `action="/sobre"` do formulário é fixo no HTML da view, não é parâmetro.)

## Fluxo Principal

1. Requisição GET `/sobre` → `Router` → `resolveRoute` → rota `about` (sem middlewares). `src/Services/RouteResolver.php`
2. `makeAbout($configs, $route, $uri)` lê o flash: `success` e `error` de `$_SESSION['flash']`. `src/Controllers/About.php`
3. Se existia flash, `unset($_SESSION['flash'])` — leitura única. `src/Controllers/About.php`
4. Monta args (`title`, `routes`, `action='/sobre'`, `success`, `error`) e chama `$configs['view']('about', $args)`. `src/Controllers/About.php`
5. `createView` extrai as variáveis e carrega `src/Pages/about.php` com buffer. `src/Services/View.php`
6. `$configs['response'](content: $content)` → HTTP 200 + flush + defer. `src/Services/Response.php`

## Fluxos Alternativos

- **Sem flash na sessão:** `success` e `error` ficam `null`; a página não mostra mensagens.
- **Flash com um dos campos:** apenas o campo presente é exibido; ambos são limpos da sessão.

## Dependências

- **Router/RouteResolver**, para resolução da rota.
- **View** (`createView`), para renderização.
- **Response** (`response`), para envio e flush.
- **Functions** (`getMenuItens`), para o menu.
- **Session** (`$_SESSION['flash']`), para o feedback pós-POST.

## Decisões de Design Identificadas

| Decisão | Evidência no código | Confiança |
|---------|---------------------|-----------|
| Flash message de leitura única via sessão | `src/Controllers/About.php` | 🟢 |
| Formulário posta para a mesma URL com método POST | `src/Pages/about.php` (`action='/sobre'`) | 🟢 |
| iframe do Google Maps embutido sem API key | `src/Pages/about.php` | 🟢 |

## Estado Interno

- **Sessão:** `$_SESSION['flash']` (`success`/`error`), criado pelo POST (`sendContact`) e consumido/removido aqui.

## Observabilidade

- Nenhum log emitido por esta rota.

## Riscos e Lacunas

- 🟢 Nenhuma lacuna conhecida.
- 🟡 O iframe do Google Maps depende de conectividade externa; falha de rede degrada a página silenciosamente.
