<x-mail::message>
# ¡Bienvenido a Mina Porco! ⚒️

Hola **{{ $trabajador->nombre_completo }}**,

Tu cuenta ha sido creada exitosamente en el **Sistema de Gestión de Personal de Mina Porco**.

## 🔐 Tus credenciales de acceso:

| Campo | Valor |
|-------|-------|
| **Usuario (Email)** | {{ $trabajador->email }} |
| **Contraseña temporal** | `{{ $password }}` |

<x-mail::button :url="url('/login')" color="primary">
Ingresar al Sistema
</x-mail::button>

<x-mail::panel>
⚠️ **Importante:** Por seguridad, te recomendamos **cambiar tu contraseña** inmediatamente después del primer inicio de sesión desde tu perfil.
</x-mail::panel>

### 📋 Información adicional:

- **Área:** {{ $trabajador->area->nombre ?? 'No asignada' }}
- **Cargo:** {{ $trabajador->cargo->nombre ?? 'No asignado' }}
- **Turno:** {{ ucfirst($trabajador->turno ?? 'No asignado') }}

Si tienes alguna duda o necesitas asistencia, contáctanos a: **admin@minaporco.com**

---

Saludos cordiales,  
**Equipo Administrativo - Mina Porco**  
© {{ date('Y') }} Mina Porco - Todos los derechos reservados
</x-mail::message>