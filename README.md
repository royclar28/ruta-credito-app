# Ruta de Crédito - Sistema Autónomo y Desacoplado

Sistema independiente, modular y centralizado para la gestión operativa, workflow de estatus, trazabilidad histórica, reportería consolidada y dashboard ejecutivo de KPIs del módulo **Ruta de Crédito**.

Diseñado desde cero para despliegues empresariales autónomos, sin acoplamiento a infraestructuras externas propietarias (sin dependencia de LDAP BDT, SIGEFIRRHH ni ESB Mule).

---

## 🚀 Stack Tecnológico

- **Backend:** Laravel 12 (PHP 8.4)
- **Persistencia:** PostgreSQL 16 estructurado bajo la **Tercera Forma Normal (3FN)** con claves foráneas, índices de alto rendimiento y auditoría inmutable.
- **Frontend / UI/UX:** Blade + Tailwind CSS + Alpine.js + ApexCharts + SweetAlert2 + Lucide Icons.
- **Contenedores:** Docker & Docker Compose (servicios `app` y `db` totalmente aislados y portables).
- **Control de Versiones:** Git con rama `main` listo para GitLab.

---

## 📦 Despliegue Rápido con Docker

### 1. Iniciar los Servicios
```bash
docker compose up -d
```
El contenedor de PostgreSQL (`db`) y la aplicación (`app`) levantarán automáticamente:
- **Aplicación Web:** [http://localhost:8095](http://localhost:8095)
- **Base de Datos PostgreSQL:** `localhost:5439` (usuario: `postgres`, password: `postgres`, BD: `db_ruta_credito`)

### 2. Migraciones y Seeders (Ya ejecutados en el setup inicial)
Si deseas reiniciar la base de datos limpia con datos de prueba:
```bash
docker exec -it ruta_credito_app php artisan migrate:fresh --seed
```

---

## 👥 Cuentas de Acceso y Simulador de Roles

Todas las cuentas vienen preconfiguradas con la contraseña: **`password123`**

| Perfil / Rol | Correo Electrónico | Alcance Operativo |
| :--- | :--- | :--- |
| **Administrador General** | `admin@empresa.com` | Control total, liquidación final y gobernanza del sistema. |
| **Analista Carterizados** | `analista.carterizados@empresa.com` | Gestión de bandeja especializada de Grandes Clientes Carterizados. |
| **Analista PYME** | `analista.pyme@empresa.com` | Evaluación de solicitudes de Personas y PYMES. |
| **Analista Consumo** | `analista.consumo@empresa.com` | Préstamos personales y nómina. |
| **Comité de Crédito** | `comite@empresa.com` | Aprobación colegiada, diferimiento o rechazo. |
| **Vicepresidencia de Crédito** | `vp.credito@empresa.com` | Aprobación ejecutiva y dictamen de alto nivel. |
| **Gerente de Agencia** | `gerente.agencia@empresa.com` | Radicación de solicitudes en sucursales territoriales. |
| **Auditor de Riesgo** | `auditor@empresa.com` | Supervisión de auditoría cronológica y KPIs. |

> **Nota UI/UX:** El sistema cuenta con un **Simulador de Perfil (1-Click Switcher)** en el navbar superior que permite alternar entre perfiles instantáneamente sin necesidad de reloguearse.

---

## 🏛️ Estructura de Base de Datos (3FN)

El diseño relacional elimina redundancias y garantiza integridad referencial:
- `roles` & `users`: Control de acceso basado en roles (RBAC).
- `regions`, `states` & `agencies`: Catálogo geográfico y red de sucursales.
- `credit_products` & `credit_subproducts`: Productos y subproductos bancarios.
- `customers`: Clientes naturales y jurídicos normalizados.
- `credit_statuses` & `workflow_transitions`: Motor de estados configurable con transiciones gobernadas por rol.
- `credit_requests`: Solicitud principal con montos en divisas y equivalencia UVC indexada.
- `credit_request_histories`: Trazabilidad inmutable (timestamp, usuario, acción, estatus previo, estatus destino, observaciones, IP, User-Agent).
- `credit_legal_documents`: Expediente digital adjunto.

---

## 🦊 Publicación en GitLab (`gitlab.bdt.com.ve`)

Para subir el proyecto a un repositorio nuevo en GitLab:

```bash
# 1. Agregar el remote de GitLab
git remote add origin https://gitlab.bdt.com.ve/developers/bicentenariodevs/ruta-credito-app.git

# 2. Subir la rama principal
git push -u origin main
```
