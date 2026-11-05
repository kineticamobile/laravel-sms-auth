# laravel-sms-auth

Paquete Laravel (`kineticamobile/laravel-sms-auth`) para iniciar sesión con un código enviado por SMS a través de la API de Ubicual.

## Estructura

- `src/SmsAuthServiceProvider.php`: carga config, migraciones, vistas y rutas (las rutas solo si `sms-auth.enable` es `true`).
- `src/Controllers/SmsAuthController.php`: flujo `sms_auth` (formulario) → `sms_login` (crea y envía el código) → `sms_verify` (valida e inicia sesión).
- `src/SmsToken.php`: modelo del código; `create()` lo genera con `random_int`, `send()` llama a Ubicual con `Http::`.
- `config/sms-auth.php`, `routes/routes.php`, `databases/migrations/`, `resources/views/`.
- `tests/`: tests con Orchestra Testbench y SQLite en memoria; `tests/views/layouts/app.blade.php` sustituye al layout de la app.

## Comandos

```bash
composer install
composer test
```

## Compatibilidad

Laravel 6–13, PHP 7.4–8.5. Al tocar `composer.json`, prueba cada versión de Laravel que se soporta, no solo la última:

```bash
composer update -W --with "illuminate/auth:^12.0" --with "orchestra/testbench:^10.0" --with "phpunit/phpunit:^11.0" && composer test
```

| Laravel | testbench | PHPUnit | PHP mínimo |
|---|---|---|---|
| 7  | 5  | 8  | 7.4 (requiere `guzzlehttp/promises:^1.4`) |
| 8  | 6  | 9  | 7.4 |
| 9  | 7  | 9  | 8.0 |
| 10 | 8  | 10 | 8.1 |
| 11 | 9  | 11 | 8.2 |
| 12 | 10 | 11 | 8.2 |
| 13 | 11 | 12 | 8.3 |

Usa Herd (`php74`, `php81`…`php85`) para ejecutar cada versión con un PHP que la soporte.

## Convenciones y trampas

- Las claves de `withErrors()` deben coincidir con las que pintan las vistas: `phone` en `sms_auth`, `token` en `sms_verify`. Con otra clave el mensaje no se ve.
- El campo del usuario es `config('sms-auth.username')`; no escribas `phone` a fuego en controlador, modelo ni migración.
- Usa siempre `SmsToken::getValidSmsTokenByToken()` para validar; comprueba la caducidad (`available_at`).
- `sms_verify` está limitada a 5 intentos/minuto por IP (`ThrottleRequests`).
- Visitar `sms_auth` borra `sms_auth_user` de la sesión: tras un error en `sms_login` el usuario pierde el código pendiente.
- `token_length` > 18 desborda `pow()`; no es compatible aunque la config hable de 255.
- Los mensajes al usuario están en español.
