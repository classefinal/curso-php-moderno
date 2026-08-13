# Spec Impact Matrix — projeto3

> Gerado pelo **Arquiteto** em 2026-08-12.
> Relação de impacto entre componentes: **linha** impacta **colunas**.

## Componentes

| Cód. | Componente | Detalhe |
|------|-----------|---------|
| CORE | Core (Environment, DB, Defer, Response, View, EventDispatcher) | infraestrutura |
| RTR | Router + RouteResolver | resolução de rotas |
| CFG | Configs (routes.php, events.php) | declaração de rotas/eventos |
| CTL | Controllers (7) | callbacks por rota |
| SVC | Services de negócio | Products, Categories, Login, Users, Cart, Contact |
| MW | Middlewares (auth, preventLogged) | controle de acesso |
| LIS | Listeners (2) | log de login recusado |
| FUN | Functions (menu, paths) | helpers |
| PAG | Pages (7 views) | renderização |
| CMP | Components (13 partials) | reutilizáveis |
| MIG | Migrations (10) + runner CLI | schema |
| TYP | types.php (Psalm) | contratos de dados |

## Matriz de impacto

| Componente | CORE | RTR | CFG | CTL | SVC | MW | LIS | FUN | PAG | CMP | MIG | TYP |
|-----------|:----:|:---:|:---:|:---:|:---:|:--:|:---:|:---:|:---:|:---:|:---:|:---:|
| **CORE** | • | X | X | X | X | X | X | X | X | X | X | X |
| **RTR** | X | • | X | X | | | | X | | | | X |
| **CFG** | X | X | • | X | | X | X | X | | | | X |
| **CTL** | X | | | • | X | | | X | X | | | X |
| **SVC** | X | | | | • | | | | | | X | X |
| **MW** | X | | | X | X | • | | | X | | | X |
| **LIS** | X | | | | | | • | | | | | |
| **FUN** | X | | | X | | | | • | X | | | |
| **PAG** | X | | | | | | | | • | X | | X |
| **CMP** | X | | | | | | | | X | • | | X |
| **MIG** | X | | | | X | | | | | | • | X |
| **TYP** | X | | X | X | X | X | | | X | X | X | • |

(X = impacto; • = próprio)

## Leitura

- **CORE** é o componente mais crítico: tudo depende dele (banco, resposta, view, eventos).
- **CFG (routes.php)** impacta RTR, CTL, MW e FUN (menu) — adicionar/remover rota propaga para navbar e middlewares.
- **MIG** impacta SVC e TYP diretamente (schema ↔ queries ↔ shapes) — as regressões 7/8 quebram o contrato (ADR-008/009).
- **TYP** é o contrato transversal: mudar shape de `Product`/`User` exige atualizar SVC, PAG, CMP e CTL.
- **PAG** e **CMP** são consumidores (baixo impacto de saída); mudanças neles não afetam serviços.

## Zonas de risco

| Zona | Componentes | Risco |
|------|-------------|-------|
| Crítica | CORE, SVC, MIG, TYP | Quebra em cadeia; sem testes automatizados |
| Alta | CFG, RTR, MW | Mudança de rota/middleware altera fluxos de acesso |
| Média | CTL, LIS, FUN | Regras de negócio dependentes de services |
| Baixa | PAG, CMP | Apenas apresentação; impacto isolado |
