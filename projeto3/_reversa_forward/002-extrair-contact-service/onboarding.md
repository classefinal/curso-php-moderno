# Onboarding: Extrair Contact Service

> Identificador: `002-extrair-contact-service`
> Data: `2026-08-25`

## Pré-requisitos

- Projeto rodando localmente (Apache + PHP 8.5 + MySQL)
- Banco de dados já migrado (`php migrate.php`)
- Página Sobre acessível em `http://localhost/sobre`

## Passo a passo para testar

### 1. Verificar que a página Sobre carrega normalmente

1. Acesse `GET /sobre`
2. Confirme que o formulário de contato aparece (nome, email, telefone)
3. Confirme que o iframe do Google Maps carrega

### 2. Testar envio com sucesso

1. Preencha: nome `João Silva`, email `joao@teste.com`, telefone `(11)94878-4541`
2. Clique em "Enviar"
3. Esperado: redirecionamento para `/sobre` com flash verde "Mensagem enviada com sucesso!"
4. Verifique no banco: `SELECT * FROM contacts ORDER BY id DESC LIMIT 1;`
5. Confirme: `name = "João Silva"`, `email = "joao@teste.com"`, `phone = "+5511948784541"`

### 3. Testar validações de erro

| Campo | Valor inválido | Esperado |
|-------|---------------|----------|
| nome | vazio | Flash "O nome é obrigatório." |
| nome | "Jo" (2 chars) | Flash "O nome deve ter no mínimo 3 caracteres." |
| email | vazio | Flash "O e-mail é obrigatório." |
| email | "invalido" | Flash "E-mail inválido." |
| telefone | vazio | Flash "O telefone é obrigatório." |
| telefone | "12345" | Flash "Telefone inválido. Use o formato (00)94878-4541." |

### 4. Verificar sintaxe

```bash
php -l src/Services/Contact/ContactService.php
php -l src/Controllers/About.php
```

Ambos devem retornar `No syntax errors detected`.

### 5. Verificar que nada quebrou

1. Acesse outras páginas (home, produtos, login) — devem funcionar normalmente
2. Faça login/logout — deve funcionar normalmente
3. Adicione item ao carrinho — deve funcionar normalmente
