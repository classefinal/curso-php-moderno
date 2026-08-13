# Fluxograma — about

> Gerado pelo **Arqueólogo** em 2026-08-12. 🟢 CONFIRMADO

## makeAbout (GET /sobre)

```mermaid
flowchart TD
    A[Lê flash success/error de $_SESSION] --> B[unset $_SESSION['flash']]
    B --> C[view 'about' com title, routes, success, error]
    C --> D[response 200 + flush + dispatcher]
```

## sendContact (POST /sobre)

```mermaid
flowchart TD
    A[POST name, email, phone] --> B{name não vazio?}
    B -->|não| E1[flash error 'O nome é obrigatório']
    E1 --> R[redirect 302 /sobre]
    B -->|sim| C{email não vazio?}
    C -->|não| E2[flash error 'O e-mail é obrigatório']
    E2 --> R
    C -->|sim| D{email válido? FILTER_VALIDATE_EMAIL}
    D -->|não| E3[flash error 'E-mail inválido']
    E3 --> R
    D -->|sim| F{phone não vazio?}
    F -->|não| E4[flash error 'O telefone é obrigatório']
    E4 --> R
    F -->|sim| G{phone casa ^\(\d{2}\)\d{4,5}-\d{4}$?}
    G -->|não| E5[flash error 'Telefone inválido. Use (00)94878-4541']
    E5 --> R
    G -->|sim| H[Normaliza phone → '+55' + dígitos]
    H --> I[INSERT INTO contacts name, email, phone]
    I --> J{INSERT ok?}
    J -->|sim| K[flash success 'Mensagem enviada com sucesso!']
    J -->|não| L[flash error 'Erro ao enviar mensagem']
    K --> R
    L --> R
    R --> Z[redirect 302 /sobre]
```
