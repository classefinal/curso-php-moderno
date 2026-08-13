# Sobre (GET /sobre), Tarefas de Implementação

## Pré-requisitos

- [ ] View por `extract()` + buffer disponível
- [ ] Response + sessão (`$_SESSION['flash']`) disponíveis

## Tarefas

- [ ] T-01, Registrar a rota `about` (GET `/sobre`, controller `About`, `makeAbout`, `inMenu`, `order 2`)
  - Origem no legado: `src/Configs/routes.php:26-36`
  - Critério de pronto: GET `/sobre` resolve para `makeAbout`
  - Confiança: 🟢

- [ ] T-02, Implementar `makeAbout` lendo `success`/`error` de `$_SESSION['flash']` e removendo o flash após leitura
  - Origem no legado: `src/Controllers/About.php`
  - Critério de pronto: flash exibido e removido da sessão
  - Confiança: 🟢

- [ ] T-03, Implementar a view `src/Pages/about.php` com formulário (nome, e-mail, telefone), exibição de `success`/`error` e iframe do Google Maps
  - Origem no legado: `src/Pages/about.php`
  - Critério de pronto: HTML renderiza formulário, mensagens e iframe
  - Confiança: 🟢

## Tarefas de Teste

- [ ] TT-01, Happy path: GET `/sobre` retorna 200 com formulário
- [ ] TT-02, Flash presente é exibido e removido da sessão
- [ ] TT-03, Sem flash a página renderiza sem mensagens

## Tarefas de Migração de Dados (se aplicável)

- Nenhuma.

## Ordem Sugerida

1. T-01 (rota) → T-02 (controller) → T-03 (view).

## Lacunas Pendentes (🔴)

- Nenhuma para esta unit.
