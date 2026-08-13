# Fluxograma — users

> Gerado pelo **Arqueólogo** em 2026-08-12. 🟢 CONFIRMADO

## viewProfile (GET /usuario/perfil, middleware auth)

```mermaid
flowchart TD
    A[authMiddleware injeta $configs['user'] do banco] --> B[view Users/profile com user, routes]
    B --> C[agenda defer: limpa $_SESSION['profile_updated']]
    C --> D[response 200 + flush + dispatcher]
```

## updateProfile (POST /usuario/perfil)

```mermaid
flowchart TD
    A[POST name, old_password, new_password, password_confirmation] --> B[updateUserProfile]
    B --> C[name = strip_tags trim]
    C --> D{len name entre 3 e 255?}
    D -->|não| E1[falha 'O nome deve ter entre 3 e 255 caracteres']
    E1 --> V1[view perfil + response 422]
    D -->|sim| F{new_password vazio?}
    F -->|sim| G1[UPDATE users SET name=? WHERE id=?]
    G1 --> H1[session atualizada: unset password + profile_updated]
    H1 --> S[success true]
    F -->|não| I[validateUpdateUserPassword]
    I --> J{senha atual correta?}
    J -->|não| E2[falha 'Senha atual incorreta']
    E2 --> V1
    I --> K{new_password não vazio?}
    K -->|não| E3[falha 'Preencha a senha']
    E3 --> V1
    I --> L{new_password === confirmação?}
    L -->|não| E4[falha 'A confirmação de senha deve ser igual a nova senha']
    E4 --> V1
    I --> M{len new_password >= 8?}
    M -->|não| E5[falha 'A senha deve ter pelo menos 8 caracteres']
    E5 --> V1
    I -->|válido| N[hash = password_hash new_password PASSWORD_BCRYPT]
    N --> O[UPDATE users SET name=?, password=? WHERE id=?]
    O --> P[session atualizada: unset password + profile_updated]
    P --> S
    S --> Q[redirect 302 /usuario/perfil]
```

> 🔴 LACUNA: o `$_POST['email']` é apenas exibido (input disabled) e não é atualizado.
