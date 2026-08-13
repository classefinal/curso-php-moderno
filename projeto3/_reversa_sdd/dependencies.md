# Dependências — projeto3

> Gerado pelo **Scout** em 2026-08-12.
> Nível de confiança: 🟢 **CONFIRMADO** — extraído diretamente do código-fonte.

## Dependências PHP

**Nenhuma.** O projeto não possui `composer.json`, `vendor/` ou qualquer dependência externa de PHP. Todo o código é PHP 8.1+ puro (stdlib + extensão `mysqli`).

### Extensões PHP exigidas

| Extensão | Uso | Evidência |
|----------|-----|-----------|
| `mysqli` | Conexão com MySQL/MariaDB | `src/Services/DB.php`, `migrate.php` |

## Assets vendored (sem CDN)

| Recurso | Versão | Origem |
|---------|--------|--------|
| Bootstrap | **5.3.8** | `public/assets/bootstrap/` (CSS + JS, map files) |
| Font Awesome | **7** | `public/assets/fontawesome/` (CSS, JS, webfonts, SVGs, metadata) |
| Imagens | — | `public/images/` (ex.: imagem de produto `mouse-gamer-redragon-...jpg`) |

## Configuração de ambiente

| Variável | Padrão (`.env.example`) | Uso |
|----------|-------------------------|-----|
| `DB_SERVER` | `localhost` | Host do MySQL |
| `DB_PORT` | `3306` | Porta do MySQL |
| `DB_DATABASE` | `projeto` | Nome do banco |
| `DB_USER` | `root` | Usuário |
| `DB_PASSWORD` | `` | Senha |

## Infraestrutura

- **Servidor web:** Apache com `mod_rewrite` (`.htaccess` em `public/`).
- **Ambiente de execução:** PHP 8.1+ (não há `composer.json` para declarar requisitos; confirmado pelo uso de `match`, typed properties em arrays, `str_contains`, `str_starts_with` etc.).

## Plataforma de runtime

- Sem Docker, sem CI/CD, sem scripts de build/test definidos.
- Comandos disponíveis: `php migrate.php` (CLI de migrações).
