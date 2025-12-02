# Candidaturas & Evaluadores – Backend Senior Test (Laravel)

Este proyecto implementa una API modular, escalable y desacoplada en Laravel, siguiendo principios de Clean Architecture, Domain-Driven Design y prácticas profesionales orientadas a manejabilidad, testabilidad y extensibilidad.

---

# 📑 Índice

1. 🎯 Objetivo del proyecto  
2. 🏗 Arquitectura y diseño  
3. 📂 Estructura del proyecto  
4. 🧠 Dominio  
5. ⚙ Funcionalidades principales  
6. 🧪 Tests automáticos  
7. 📊 Exportación de Excel asíncrona  
8. ⚡ Escalabilidad horizontal  
9. ▶️ Cómo ejecutar el proyecto  
10. 📬 Endpoints

---

# 🎯 Objetivo del proyecto

Implementar una API REST completa capaz de gestionar candidatos, validarlos mediante un sistema extensible, asignar evaluadores, generar un listado consolidado con SQL optimizado y exportarlo a Excel mediante procesos asíncronos en colas.

---

# 🏗 Arquitectura y diseño

Este proyecto utiliza una **Arquitectura Limpia / Hexagonal**, dividiendo el código en capas:

- **Domain:** reglas de negocio puras  
- **Application:** casos de uso  
- **Infrastructure:** repositorios, controladores, Eloquent, Jobs  
- **Interfaces:** API HTTP

### Patrones utilizados

- Value Objects  
- Entidades ricas  
- Repositorio (Interfaces + Implementaciones)  
- Chain of Responsibility (validación extensible)  
- Use Case  
- DTOs  
- Jobs y colas  

---

# 📂 Estructura del proyecto

```
app/
 ├── Domain/
 ├── Application/
 └── Infrastructure/
```

---

# 🧠 Dominio

Incluye:

- Entidades: Candidate, Evaluator, Assignment  
- Value Objects: CandidateEmail, YearsOfExperience  
- Reglas de validación extensibles: HasCvRule, ValidEmailRule, MinExperienceRule  

---

# ⚙ Funcionalidades principales

- Registro de candidatos  
- Validación extensible  
- Gestión de evaluadores  
- Asignación evaluador → candidato  
- Listado SQL consolidado  
- Resumen de candidatura  
- Exportación Excel paginada (50 registros por hoja)  
- Proceso asíncrono con colas y email de notificación  

---

# 🧪 Tests automáticos

- Tests unitarios (reglas + use cases)  
- Test feature  
- Test de integración con base de datos real  

Ejecutar:

```
php artisan test
```

---

# 📊 Exportación de Excel asíncrona 

Flujo:

1. `/api/candidates/consolidated/export/async`  
2. Job `ExportConsolidatedCandidatesJob`  
3. Excel generado en `storage/app/private/exports`  
4. Email opcional enviado al usuario  ->  (ESTA PARTE AÚN NO ESTA IMPLEMENTADA)

Worker:

```
php artisan queue:work
```

---

# ⚡ Escalabilidad horizontal

El sistema soporta:

- Colas distribuidas  
- Cache  
- Jobs idempotentes  
- Dominio desacoplado del framework  
- SQL optimizado  

---

# ▶️ Cómo ejecutar el proyecto

```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
php artisan queue:work
```

---

# 📬 Endpoints

### Ping
- `GET /api/ping`

### Candidatos
- `POST /api/candidates`
- `GET /api/candidates/{id}`
- `GET /api/candidates/{id}/summary`
- `POST /api/candidates/{id}/validate`

### Asignación
- `POST /api/candidates/{id}/assign-evaluator`

### Listado consolidado
- `GET /api/candidates/consolidated`
- `GET /api/candidates/consolidated/export`
- `POST /api/candidates/consolidated/export/async`
