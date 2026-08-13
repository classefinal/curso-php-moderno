# Home (GET /), Tarefas de Implementação

## Pré-requisitos

- [ ] Router com suporte a rota default (URI vazia) disponível
- [ ] View por `extract()` + output buffering disponível
- [ ] Response com flush + defer disponível

## Tarefas

- [ ] T-01, Registrar a rota `home` (GET `/`, controller `Home`, `makeHome`, `inMenu`, `order 0`, sem middlewares)
  - Origem no legado: `src/Configs/routes.php:14-24`
  - Critério de pronto: GET `/` resolve para `makeHome` sem erro
  - Confiança: 🟢

- [ ] T-02, Implementar `makeHome` chamando a view `home` com `title` e `routes = getMenuItens(...)`
  - Origem no legado: `src/Controllers/Home.php`
  - Critério de pronto: a view recebe `title` e `routes` e renderiza
  - Confiança: 🟢

- [ ] T-03, Implementar a view `src/Pages/home.php` consumindo `$title` e `$routes` (menu)
  - Origem no legado: `src/Pages/home.php`
  - Critério de pronto: HTML renderiza título e menu
  - Confiança: 🟢

- [ ] T-04, Garantir rota default quando a URI é vazia em `processRoutes`
  - Origem no legado: `src/Services/Router.php`
  - Critério de pronto: path vazio renderiza a home
  - Confiança: 🟢

- [ ] T-05, Integrar menu com `getMenuItens` (filtra `inMenu`, ordena por `order`)
  - Origem no legado: `src/Functions/Functions.php`
  - Critério de pronto: menu mostra Home/Sobre/Produtos/Login na ordem correta
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Teste do happy path: GET `/` retorna 200 com HTML
- [ ] TT-02, Teste do path vazio retornando a home
- [ ] TT-03, Teste de menu: itens `inMenu=false` não aparecem

## Tarefas de Migração de Dados (se aplicável)

- Nenhuma (página estática, sem dados).

## Ordem Sugerida

1. T-04 (rota default) → T-01 (rota) → T-05 (menu) → T-02 (controller) → T-03 (view), pois a view depende do controller, que depende da rota e do menu.

## Lacunas Pendentes (🔴)

- Nenhuma para esta unit.
