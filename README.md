# Candidaturas & Evaluadores – Backend Senior Test (Laravel)

Este proyecto implementa una API modular, escalable y desacoplada en Laravel, siguiendo principios de Clean Architecture, Domain-Driven Design y prácticas profesionales orientadas a manejabilidad, testabilidad y extensibilidad.

---

# 📑 Índice

1. 🎯 Objetivo del proyecto
2. 🏗 Arquitectura y diseño
   - Capas del sistema
   - Decisiones de diseño
   - Patrones usados
3. 📂 Estructura del proyecto
4. 🧠 Dominio
5. ⚙ Funcionalidades principales
6. 🧪 Tests automáticos
7. 📊 Exportación de Excel y proceso asíncrono
8. ⚡ Escalabilidad horizontal
9. ▶️ Cómo ejecutar el proyecto
10. 📬 Endpoints y documentación

---

# 🎯 Objetivo del proyecto

El objetivo es implementar una API REST robusta para:

- Registro de candidatos
- Validación extensible mediante reglas
- Gestión de evaluadores
- Asignación evaluador → candidato
- Listado consolidado con SQL avanzado
- Resumen extendido de cada candidato
- Exportación de datos a Excel mediante colas
- Envío de email notificando la exportación

El foco principal es:

- Arquitectura limpia
- Desacoplamiento del framework
- Uso de patrones avanzados
- Testing completo
- Escalabilidad real

---

# 🏗 Arquitectura y diseño

Se adopta una **Arquitectura Limpia/Hexagonal**, separando claramente:

- **Domain** → reglas de negocio puras (sin Laravel)
- **Application** → casos de uso
- **Infrastructure** → Eloquent, Jobs, controladores
- **Interfaces** → API pública

## Capas del sistema

```
app/
 ├── Domain/
 │   ├── Candidate/
 │   ├── Evaluator/
 │   └── Assignment/
 ├── Application/
 │   ├── UseCases/
 │   ├── DTO/
 │   └── Contracts/
 └── Infrastructure/
     ├── Persistence/Eloquent/
     ├── Excel/
     ├── Http/Controllers/
     └── Jobs/
```

---

## Decisiones de diseño

### ✔ Desacoplamiento del framework
Toda la lógica de negocio depende de **interfaces**, nunca de Eloquent.

### ✔ Dominio rico
Las invariantes se protegen mediante Value Objects y entidades con reglas internas.

### ✔ Validación flexible
El sistema usa **Chain of Responsibility**, permitiendo agregar reglas sin romper las anteriores.

### ✔ SQL optimizado
El consolidado usa:

- Subconsultas
- DISTINCT + COUNT
- GROUP_CONCAT
- Orden dinámico
- Filtros arbitrarios
- Paginación eficiente

### ✔ Procesos pesados en segundo plano
La exportación Excel no bloquea la API.

---

## Patrones usados

| Patrón | Uso |
|--------|-----|
| **Value Object** | Email, experiencia |
| **Entity** | Candidate, Evaluator, Assignment |
| **Repository Pattern** | Interfaces + implementación Eloquent |
| **Chain of Responsibility** | Validación |
| **Use Case / Interactor** | Lógica de aplicación |
| **DTO** | Respuestas tipadas |
| **Strategy (implícito)** | Orden y filtros |
| **Job / Queue Worker** | Exportaciones pesadas |

---

# 📂 Estructura del proyecto

```
app/
 ├── Domain/
 │   ├── Candidate/
 │   │   ├── Entities/
 │   │   ├── ValueObjects/
 │   │   └── ValidationRules/
 │   ├── Evaluator/
 │   └── Assignment/
 │
 ├── Application/
 │   ├── UseCases/
 │   ├── DTO/
 │   └── Contracts/
 │
 └── Infrastructure/
     ├── Persistence/Eloquent/Models
     ├── Persistence/Eloquent/Repositories
     ├── Excel/
     ├── Http/Controllers
     └── Jobs/
```

---

# 🧠 Dominio

### Entidades
- Candidate  
- Evaluator  
- Assignment  

### Value Objects
- CandidateEmail  
- YearsOfExperience  

### Reglas de Validación Encadenadas
- `HasCvRule`
- `ValidEmailRule`
- `MinExperienceRule`

Sistema extensible sin modificar reglas existentes.

---

# ⚙ Funcionalidades principales

✔ Registro de candidatos  
✔ Validación mediante cadena de reglas  
✔ Gestión de evaluadores  
✔ Asignación evaluador → candidato  
✔ Listado consolidado con métricas:  
   - total candidatos por evaluador  
   - emails concatenados  
   - orden + filtros  
   - paginación  
✔ Resumen completo de candidatura  
✔ Exportación Excel (50 por hoja)  
✔ Worker + email de notificación  -> (el mail aun no se está enviando)

---

# 🧪 Tests automáticos

Incluye:

### Tests unitarios
- Reglas de validación  
- CandidateValidator  
- AssignEvaluatorHandler  

### Test de integración
- SQL consolidado con BD real  

### Test feature
- Resumen de candidatura  

Ejecutar:

```
php artisan test
```

---

# 📊 Exportación de Excel y proceso asíncrono

### Flujo:

1. Cliente llama:
```
POST /api/candidates/consolidated/export/async
```
2. Se encola `ExportConsolidatedCandidatesJob`
3. El worker genera:
```
storage/app/private/exports/*.xlsx
```
4. Email notificando resultado

Worker:

```
php artisan queue:work
```

---

# ⚡ Escalabilidad horizontal

- Colas distribuidas  
- Cache opcional para queries pesadas  
- Jobs idempotentes  
- Dominio desacoplado del framework  
- Posibilidad de múltiples workers concurrentes  

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

# 📬 Endpoints y documentación

### Ping
```
GET /api/ping
```

### Candidatos
```
POST /api/candidates
GET /api/candidates/{id}
GET /api/candidates/{id}/summary
POST /api/candidates/{id}/validate
```

### Asignación
```
POST /api/candidates/{id}/assign-evaluator
```

### Consolidado
```
GET /api/candidates/consolidated
GET /api/candidates/consolidated/export
POST /api/candidates/consolidated/export/async
```

---

# ✔ Estado del proyecto

El proyecto cumple con:

- Arquitectura limpia real  
- Dominio desacoplado  
- SQL complejo  
- Testing completo  
- Procesos asíncronos  
- Exportación Excel avanzada  
- Diseño escalable y mantenible  