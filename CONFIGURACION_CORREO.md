# 📧 Configuración de Correo Electrónico - Mina Porco

## Problema Común: Los correos no se envían

Si los correos no se están enviando, sigue estos pasos:

## 1. Verificar Configuración en `.env`

Abre tu archivo `.env` y verifica las siguientes variables:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="Mina Porco"
```

## 2. Configuración para Gmail

Si usas Gmail, necesitas:

1. **Habilitar verificación en 2 pasos** en tu cuenta de Google
2. **Generar una contraseña de aplicación:**
   - Ve a: https://myaccount.google.com/apppasswords
   - Selecciona "Correo" y "Otro (nombre personalizado)"
   - Ingresa "Mina Porco" y genera la contraseña
   - Usa esa contraseña en `MAIL_PASSWORD`

## 3. Configuración para Otros Proveedores

### Outlook/Hotmail
```env
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

### Mailtrap (Para pruebas)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu-usuario-mailtrap
MAIL_PASSWORD=tu-password-mailtrap
MAIL_ENCRYPTION=tls
```

## 4. Probar la Configuración

Ejecuta el comando de prueba:

```bash
php artisan email:test tu-email@ejemplo.com
```

O sin parámetros para ingresar el email interactivamente:

```bash
php artisan email:test
```

## 5. Verificar Logs

Si el correo falla, revisa los logs:

```bash
tail -f storage/logs/laravel.log
```

## 6. Modo de Desarrollo (Log)

Para desarrollo, puedes usar el driver `log` que guarda los correos en el log en lugar de enviarlos:

```env
MAIL_MAILER=log
```

Los correos se guardarán en `storage/logs/laravel.log`

## 7. Solución de Problemas

### Error: "Connection could not be established"
- Verifica que `MAIL_HOST` y `MAIL_PORT` sean correctos
- Verifica tu conexión a internet
- Verifica que el firewall no bloquee el puerto

### Error: "Authentication failed"
- Verifica `MAIL_USERNAME` y `MAIL_PASSWORD`
- Si usas Gmail, asegúrate de usar una contraseña de aplicación
- Verifica que la cuenta no tenga restricciones de seguridad

### Error: "Could not instantiate mailer"
- Verifica que todas las variables de `MAIL_*` estén configuradas
- Ejecuta `php artisan config:clear` para limpiar caché

## 8. Limpiar Caché de Configuración

Después de cambiar las variables de entorno:

```bash
php artisan config:clear
php artisan cache:clear
```

## Nota Importante

El sistema ahora usa `send()` en lugar de `queue()` para envío inmediato. Si necesitas usar colas para mejor rendimiento, configura un worker:

```bash
php artisan queue:work
```

