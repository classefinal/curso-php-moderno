# C4 — Diagrama de Containers (Nível 2)

> Gerado pelo **Arquiteto** em 2026-08-12. 🟢 CONFIRMADO

```mermaid
flowchart TD
    subgraph Browser
        FE["Navegador Web<br/>HTML + CSS + JS<br/>Bootstrap 5.3.8 + Font Awesome"]
    end

    subgraph Servidor
        WS["Apache + mod_rewrite<br/>HTTP/HTTPS"]
        APP["Aplicação PHP 8.5 (procedural)<br/>front controller public/index.php → app.php"]
        CLI["CLI: php migrate.php<br/>runner de migrations"]
        FS[("Filesystem<br/>src/ + public/ assets + logs/")]
    end

    DB[(MySQL/MariaDB<br/>schema projeto3<br/>utf8mb4)]

    FE -- "HTTP GET/POST<br/>sessão via cookie" --> WS
    WS --> APP
    APP -- "mysqli (prepared statements)" --> DB
    APP -- "leitura: views/pages/assets<br/>escrita: logs/ (defer)" --> FS
    CLI -- "mysqli (DDL/DML)" --> DB
    CLI -- "lê src/Migrations/" --> FS
```

## Containers

| Container | Tecnologia | Responsabilidade |
|-----------|------------|------------------|
| **Navegador** | HTML/CSS/JS, Bootstrap 5.3.8 + Font Awesome 7 (vendored) | Renderização server-side; sem SPA/AJAX |
| **Apache + mod_rewrite** | Web server | Roteia tudo para `public/index.php` (front controller) |
| **Aplicação PHP** | PHP 8.5 procedural, `mysqli`, sessões, sem Composer | Todo o comportamento do sistema |
| **CLI migrate** | `php migrate.php` | Executa migrations pendentes (runner próprio) |
| **MySQL** | MariaDB/MySQL | Persistência: 7 tabelas de negócio + `migrations` |
| **Filesystem** | Disco | `src/` (código), `public/assets` (estático), `logs/` (runtime) |

## Fluxos

- **HTTP**: Browser → Apache → Aplicação (sessão por cookie; `httponly`, sem `samesite`/`secure` 🟡).
- **Dados**: Aplicação/CLI → MySQL via `mysqli`; prepared statements com tipos `s`/`i`.
- **Logs**: Aplicação → Filesystem `logs/` após flush da resposta (defer).
