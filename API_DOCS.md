# DrDentix SaaS API — Documentación

Base URL: `http://localhost:8000/api`

Autenticación: Bearer Token (Sanctum)  
Header requerido en rutas protegidas: `Authorization: Bearer {token}`

---

## Índice

1. [Autenticación](#1-autenticación)
2. [Sedes](#2-sedes)
3. [Odontólogos](#3-odontólogos)
4. [Pacientes](#4-pacientes)
5. [Procedimientos](#5-procedimientos)
6. [Citas](#6-citas)
7. [Productos](#7-productos)
8. [Promociones](#8-promociones)
9. [Reportes](#9-reportes)

---

## Permisos por rol

| Prefijo | Roles permitidos |
|---|---|
| `/auth/*` | Público / cualquier autenticado |
| `/admin/*` | Administrator |
| `/staff/*` | Administrator, Dentist |
| `/dentist/*` | Dentist |
| `/patient/*` | Patient |

---

## 1. Autenticación

### Login Paciente
`POST /auth/login/patient`  
Acceso público. El paciente entra solo con su número de cédula (sin contraseña).

**Body**
```json
{
  "document": "1234567890"
}
```

**Respuesta 200**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "document": "1234567890",
    "email": null,
    "type_user": "Patient",
    "photo": "/images/default.jpg",
    "state": "Activo"
  }
}
```

**Errores**
- `401` — Cédula no encontrada o cuenta inactiva

---

### Login Staff (Admin / Odontólogo)
`POST /auth/login/staff`  
Acceso público.

**Body**
```json
{
  "email": "admin@drdentix.com",
  "password": "secreto123"
}
```

**Respuesta 200**
```json
{
  "token": "2|xyz789...",
  "user": {
    "id": 2,
    "document": "9876543210",
    "email": "admin@drdentix.com",
    "type_user": "Administrator",
    "state": "Activo"
  }
}
```

**Errores**
- `401` — Credenciales incorrectas
- `403` — Cuenta inactiva

---

### Logout
`POST /auth/logout`  
🔒 Cualquier rol autenticado.

**Respuesta 200**
```json
{ "message": "Sesión cerrada." }
```

---

### Usuario autenticado
`GET /auth/me`  
🔒 Cualquier rol autenticado.

**Respuesta 200** — Objeto `user`

---

### Actualizar foto de perfil
`POST /auth/photo`  
🔒 Cualquier rol autenticado.  
`Content-Type: multipart/form-data`

| Campo | Tipo | Reglas |
|---|---|---|
| `photo` | file | imagen, máx 3 MB |

**Respuesta 200**
```json
{ "photo": "/storage/uploads/foto.jpg" }
```

---

## 2. Sedes

### Listar sedes
`GET /admin/branches`  
🔒 Administrator  
Paginado (15 por página).

**Query params opcionales**
| Param | Descripción |
|---|---|
| `page` | Número de página |

**Respuesta 200**
```json
{
  "data": [
    { "id": 1, "name": "Sede Norte", "address": "Cra 15 #23-10", "contact": "3001234567", "city": "Bogotá", "state": "Activo" }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 1 }
}
```

---

### Crear / Editar sede
`POST /admin/branches`  
🔒 Administrator  
Si se envía `id`, actualiza. Si no, crea.

**Body**
```json
{
  "id": null,
  "name": "Sede Sur",
  "address": "Calle 50 #10-20",
  "contact": "3109876543",
  "city": "Medellín",
  "state": "Activo"
}
```

**Respuesta 200** — Objeto `branch`

---

### Cambiar estado de sede
`POST /admin/branches/state`  
🔒 Administrator

**Body**
```json
{ "id": 1, "state": "Inactivo" }
```

---

### Lista desplegable de sedes
`GET /staff/branches/select`  
🔒 Administrator, Dentist  
Devuelve solo sedes activas para usar en formularios.

**Respuesta 200**
```json
[{ "id": 1, "name": "Sede Norte" }]
```

---

## 3. Odontólogos

### Listar odontólogos
`GET /admin/dentists`  
🔒 Administrator  
Paginado. Incluye usuario y procedimientos.

**Query params opcionales**
| Param | Descripción |
|---|---|
| `name` | Filtrar por nombre |
| `city` | Filtrar por ciudad |
| `document` | Filtrar por cédula |

---

### Crear / Editar odontólogo
`POST /admin/dentists`  
🔒 Administrator  
Al crear, genera el `User` y el `Dentist` en la misma transacción. Sincroniza los procedimientos asignados.

**Body**
```json
{
  "id": null,
  "name": "Dr. Juan Pérez",
  "city": "Bogotá",
  "document": "80123456",
  "email": "juan@clinica.com",
  "birth": "1985-03-20",
  "password": "pass1234",
  "procedure_ids": [1, 3]
}
```

**Respuesta 200** — Objeto `dentist` con `user` y `procedures`

---

### Ver odontólogo
`GET /admin/dentists/{id}`  
🔒 Administrator

---

### Cambiar estado de odontólogo
`POST /admin/dentists/state`  
🔒 Administrator

**Body**
```json
{ "id": 1, "state": "Inactivo" }
```

---

### Ver horario de odontólogo (admin)
`GET /admin/dentists/{id}/schedule`  
🔒 Administrator

**Respuesta 200**
```json
[
  { "id": 1, "day": 1, "attend": true, "hour_start": "08:00", "hour_end": "17:00", "break": true, "break_start": "12:00", "break_end": "13:00" }
]
```

> `day`: 1=Lunes, 2=Martes, ..., 6=Sábado

---

### Guardar horario (admin)
`POST /admin/dentists/schedule`  
🔒 Administrator

**Body**
```json
{
  "dentist_id": 1,
  "schedules": [
    { "day": 1, "attend": true, "hour_start": "08:00", "hour_end": "17:00", "break": true, "break_start": "12:00", "break_end": "13:00" },
    { "day": 2, "attend": true, "hour_start": "08:00", "hour_end": "17:00", "break": false, "break_start": null, "break_end": null },
    { "day": 6, "attend": false, "hour_start": null, "hour_end": null, "break": false, "break_start": null, "break_end": null }
  ]
}
```

---

### Ver mi horario (odontólogo)
`GET /dentist/schedule`  
🔒 Dentist  
Devuelve el horario del odontólogo autenticado.

---

### Guardar mi horario (odontólogo)
`POST /dentist/schedule`  
🔒 Dentist  
Mismo body que el endpoint de admin pero `dentist_id` debe corresponder al odontólogo autenticado.

---

### Mis citas (odontólogo)
`GET /dentist/appointments`  
🔒 Dentist

**Query params opcionales**
| Param | Descripción |
|---|---|
| `date` | Filtrar por fecha `YYYY-MM-DD` |

---

### Lista desplegable de odontólogos
`GET /staff/dentists/select`  
🔒 Administrator, Dentist

**Respuesta 200**
```json
[{ "id": 1, "name": "Dr. Juan Pérez" }]
```

---

## 4. Pacientes

### Listar pacientes
`GET /admin/patients`  
🔒 Administrator  
Paginado. Ordena por cantidad de citas pagadas (los más frecuentes primero).

**Query params opcionales**
| Param | Descripción |
|---|---|
| `name` | Filtrar por nombre |
| `city` | Filtrar por ciudad |
| `document` | Filtrar por cédula |

---

### Crear / Editar paciente
`POST /admin/patients`  
🔒 Administrator  
Al crear: genera el `User` con contraseña por defecto `1234`, prefija `+57` al teléfono.

**Body**
```json
{
  "id": null,
  "name": "María García",
  "city": "Cali",
  "telephone": "3156789012",
  "document": "52987654",
  "email": "maria@gmail.com",
  "birth": "1990-07-15"
}
```

---

### Ver paciente
`GET /admin/patients/{id}`  
🔒 Administrator

---

### Desactivar paciente
`POST /admin/patients/deactivate`  
🔒 Administrator  
Cambia estado e **anonimiza** cédula y email (cumplimiento de privacidad).

**Body**
```json
{ "id": 1, "state": "Inactivo" }
```

---

### Buscar por cédula
`POST /admin/patients/find-by-document`  
🔒 Administrator

**Body**
```json
{ "document": "52987654" }
```

**Respuesta — encontrado**
```json
{ "status": 200, "id": 5 }
```

**Respuesta — no encontrado**
```json
{ "status": 422, "document": "52987654" }
```

---

### Lista desplegable de pacientes
`GET /admin/patients/select`  
🔒 Administrator

**Respuesta 200**
```json
[{ "id": 5, "text": "52987654 - María García" }]
```

---

### Mi perfil (paciente)
`GET /patient/me`  
🔒 Patient

---

## 5. Procedimientos

### Listar procedimientos
`GET /admin/procedures`  
🔒 Administrator  
Paginado, ordenado por nombre.

---

### Crear / Editar procedimiento
`POST /admin/procedures`  
🔒 Administrator

**Body**
```json
{
  "id": null,
  "name": "Limpieza dental",
  "duration": 30,
  "state": "Activo"
}
```

> `duration` en minutos — se usa para calcular los slots de tiempo disponibles.

---

### Cambiar estado de procedimiento
`POST /admin/procedures/state`  
🔒 Administrator

**Body**
```json
{ "id": 2, "state": "Inactivo" }
```

---

### Lista desplegable de procedimientos
`GET /staff/procedures/select`  
🔒 Administrator, Dentist

**Respuesta 200**
```json
[{ "id": 1, "name": "Limpieza dental", "duration": 30 }]
```

---

## 6. Citas

### Datos para el formulario de agendamiento
`POST /staff/appointments/form-data`  
`POST /patient/appointments/form-data`  
🔒 Administrator, Dentist, Patient

**Respuesta 200**
```json
{
  "branches": [{ "id": 1, "name": "Sede Norte" }],
  "procedures": [{ "id": 1, "name": "Limpieza dental", "duration": 30 }],
  "patients": [{ "id": 5, "text": "52987654 - María García" }],
  "min_date": "2026-07-19"
}
```

---

### Slots disponibles
`POST /staff/appointments/slots`  
`POST /patient/appointments/slots`  
🔒 Administrator, Dentist, Patient  
Calcula las horas libres para un odontólogo-procedimiento en una fecha dada, descontando el descanso y las citas ya agendadas.

**Body**
```json
{
  "dentist_procedure_id": 3,
  "date": "2026-07-25"
}
```

**Respuesta 200**
```json
{
  "slots": [
    { "hour_start": "8:00 am", "hour_end": "8:30 am" },
    { "hour_start": "8:30 am", "hour_end": "9:00 am" }
  ]
}
```

**Errores**
- `422` — La fecha es domingo

---

### Odontólogos por procedimiento
`POST /staff/appointments/by-procedure`  
`POST /patient/appointments/by-procedure`  
🔒 Administrator, Dentist, Patient

**Body**
```json
{ "procedure_id": 1 }
```

**Respuesta 200** — Lista de `dentist_procedure` con el odontólogo y su horario activo.

---

### Listar citas (admin)
`GET /admin/appointments`  
🔒 Administrator  
Paginado. Por defecto muestra las citas de hoy.

**Query params opcionales**
| Param | Descripción |
|---|---|
| `patient` | Filtrar por nombre o cédula del paciente |
| `date_from` | Fecha inicio `YYYY-MM-DD` |
| `date_to` | Fecha fin `YYYY-MM-DD` |
| `dentist_id` | Filtrar por odontólogo |
| `state` | `Activo` / `Recordado` / `Cancelado` / `No asistio` / `Pagado` |
| `advance` | Desplazamiento en días desde hoy (ej. `1` = mañana) |

**Respuesta 200**
```json
{
  "data": { "data": [...], "meta": {...} },
  "income": 450000,
  "pending": 5
}
```

---

### Ver cita
`GET /admin/appointments/{id}`  
🔒 Administrator  
Incluye paciente, sede, odontólogo, procedimiento, facturas.

---

### Crear cita (admin)
`POST /admin/appointments`  
🔒 Administrator

**Body**
```json
{
  "day": "2026-07-25",
  "hour": "8:00 am",
  "branch_id": 1,
  "patient_id": 5,
  "dentist_procedure_id": 3,
  "type": 1
}
```

> `type: 1` — Permite al admin agendar aunque el paciente ya tenga una cita activa.

---

### Crear cita (paciente)
`POST /patient/appointments`  
🔒 Patient  
Mismo body sin `type`. Bloquea si el paciente ya tiene una cita activa o recordada.

---

### Cambiar estado + facturar
`POST /admin/appointments/state`  
`POST /dentist/appointments/state`  
🔒 Administrator, Dentist

**Body — marcar como pagado con factura**
```json
{
  "id": 10,
  "state": "Asistio",
  "payments": [
    { "price": 120000, "procedure_id": 1 },
    { "price": 30000,  "procedure_id": 4 }
  ]
}
```

**Body — marcar como no asistió**
```json
{ "id": 10, "state": "No asistio" }
```

> Estados válidos: `Activo`, `Recordado`, `Cancelado`, `No asistio`, `Asistio` (→ se guarda como `Pagado`)

---

### Eliminar cita
`POST /admin/appointments/delete`  
🔒 Administrator

**Body**
```json
{ "id": 10 }
```

> No se puede eliminar una cita en estado `Pagado`.

---

### Cancelar cita (paciente)
`POST /patient/appointments/cancel`  
🔒 Patient  
Solo puede cancelar sus propias citas.

**Body**
```json
{ "id": 10 }
```

---

### Citas del paciente autenticado
`GET /patient/appointments`  
🔒 Patient  
Paginado, ordenado por fecha descendente.

---

### Citas por paciente (admin)
`GET /admin/appointments/by-patient?patient_id=5`  
🔒 Administrator

---

### Historial por cédula
`GET /admin/appointments/by-document?document=52987654`  
🔒 Administrator

---

### Recordatorio WhatsApp
`POST /admin/appointments/whatsapp`  
🔒 Administrator  
Registra que se envió el recordatorio (`type_state = 1`) y devuelve el texto del mensaje para que el frontend abra el enlace `wa.me`.

**Body**
```json
{ "id": 10 }
```

**Respuesta 200**
```json
{
  "message": "Recordatorio DrDentix:\nFecha: 25/07/2026\nHora: 8:00 am\nProcedimiento: Limpieza dental\nOdontólogo: Dr. Juan Pérez\nSede: Sede Norte — Cra 15 #23-10"
}
```

---

### Recordatorio por llamada
`POST /admin/appointments/phone`  
🔒 Administrator  
Registra que se hizo la llamada (`type_state = 2`).

**Body**
```json
{ "id": 10 }
```

---

## 7. Productos

### Listar productos
`GET /admin/products`  
🔒 Administrator  
Paginado, ordenado por principio activo.

---

### Crear / Editar producto
`POST /admin/products`  
🔒 Administrator  
Calcula automáticamente el semáforo de vencimiento.

**Body**
```json
{
  "id": null,
  "active_principle": "Amoxicilina",
  "concentration": "500mg",
  "amount": 100,
  "pharmaceutical_form": "Cápsulas",
  "commercial_presentation": "Caja x 10",
  "medication_unit": "Cápsula",
  "batch": "LOT2025A",
  "health_register_invima": "INVIMA2024M-123",
  "expiration_date": "2027-06-30",
  "date_of_admission": "2026-01-10"
}
```

**Semáforo calculado automáticamente**
| Meses hasta vencimiento | `semaphore` |
|---|---|
| ≥ 12 meses | `verde` |
| 3 – 11 meses | `amarillo` |
| < 3 meses | `rojo` |

---

### Eliminar producto
`DELETE /admin/products/{id}`  
🔒 Administrator  
Soft delete (recuperable).

---

## 8. Promociones

### Listar promociones
`GET /admin/promotions`  
🔒 Administrator  
Paginado, ordenado por fecha de inicio descendente.

---

### Crear / Editar promoción
`POST /admin/promotions`  
🔒 Administrator

**Body**
```json
{
  "id": null,
  "date_start": "2026-08-01",
  "date_end": "2026-08-31",
  "details": "20% de descuento en blanqueamiento dental durante agosto.",
  "discount": 20,
  "limit_patients": 50
}
```

---

### Desactivar promoción
`POST /admin/promotions/deactivate`  
🔒 Administrator

**Body**
```json
{ "id": 3 }
```

---

## 9. Reportes

### Resumen de staff (para gráficas)
`GET /admin/reports/staff`  
🔒 Administrator  
Devuelve todos los usuarios activos con su relación de odontólogo, para alimentar dashboards.

---

### Historial de pagos por paciente
`GET /admin/reports/billing?patient_id=5`  
🔒 Administrator  
Paginado. Solo citas en estado `Pagado`, con sus facturas y procedimientos.

---

## Estados de cita

| Estado | Descripción |
|---|---|
| `Activo` | Cita agendada, pendiente |
| `Recordado` | Se envió recordatorio al paciente |
| `Cancelado` | Cancelada por el paciente |
| `No asistio` | El paciente no se presentó |
| `Pagado` | Cita completada y facturada |
| `Eliminado` | Eliminada por el administrador (no aparece en listados) |

## Valores de `type_state`

| Valor | Significado |
|---|---|
| `0` | Sin recordatorio enviado |
| `1` | Recordatorio por WhatsApp registrado |
| `2` | Recordatorio por llamada registrado |
