<?php

namespace App\Messages;

class Messages
{
    public const EMAIL_ALREADY_REGISTERED = "Este e-mail já está registrado";
    public const INVALID_CREDENTIALS = "Credenciais inválidas";
    public const INVALID_DATA = "Dados inválidos";
    public const INTERNAL_SERVER_ERROR = "Erro interno do servidor";
    public const EXPENSE_NOT_FOUND = "Gasto não encontrado";
    public const CATEGORY_NOT_FOUND = "Categoria não encontrada";
    public const NO_CATEGORIES_FOUND = "Nenhuma categoria encontrada";
    public const SUBCATEGORY_DOES_NOT_BELONG_TO_CATEGORY = "A subcategoria não pertence à categoria informada";
    public const FORBIDDEN_NOT_ADMIN = "Falha ao validar usuário";
    public const NOT_FOUND = "Recurso não encontrado";
    public const PASSWORD_RESET_EMAIL_SENT = "Se o e-mail estiver cadastrado, você receberá um código em breve.";
    public const PASSWORD_RESET_CODE_INVALID = "Código inválido ou expirado.";
    public const PASSWORD_RESET_TOO_MANY_ATTEMPTS = "Muitas tentativas incorretas. Solicite um novo código.";
}
