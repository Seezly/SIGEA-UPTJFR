<x-mail::message>

# Nuevo mensaje de contacto

Has recibido un nuevo mensaje desde el formulario web de la plataforma.

**Detalles del remitente:**

- **Nombre:** {{ $name }}
- **Correo:** {{ $email }}
- **Asunto:** {{ $subject }}

---

### Mensaje:

> {{ $message }}

<x-mail::button :url="'mailto:' . $email" :color="'blue'">
Responder directamente
</x-mail::button>

Gracias,<br/>
{{ config('app.name') }}
</x-mail::message>
