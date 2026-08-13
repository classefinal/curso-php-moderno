# ADR-001 — PHP procedural, sem OOP e sem Composer

- **Status:** Aceito 🟢
- **Data:** 2026-08-12 (retroativo — projeto: 2026-03 a 2026-05)
- **Origem:** estrutura do projeto, `AGENTS.md`, histórico Git ("wip", "add agents md", "fixed folder")

## Contexto

O projeto nasceu como exercício educacional de PHP. O autor (ClasseFinal) construiu um framework próprio totalmente procedural: funções globais, closures e arrays associativos — sem classes, sem autoload e sem gerenciador de dependências.

## Decisão

- Não usar OOP nem Composer.
- Estrutura por camadas com pastas semânticas: `Controllers`, `Services`, `Pages`, `Components`, `Listeners`, `Middlewares`, `Configs`, `Migrations`.
- Componentes próprios para router, view (`extract()` + output buffering), DB (`mysqli` + prepared statements), eventos e resposta.
- Sem dependências externas (Bootstrap/Font Awesome vendored em `public/assets`).

## Consequências

- Zero lock-in de bibliotecas; comportamento totalmente determinístico no ambiente.
- Código de infraestrutura reimplementado à mão (router, view, eventos, migrações).
- PHP 8.5 é requisito (pipe `|>` e `array_last()`), confirmado por `php -l`.
