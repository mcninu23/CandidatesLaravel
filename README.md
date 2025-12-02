# Candidaturas & Evaluadores – Backend Senior Test (Laravel)

Este proyecto implementa una API modular, escalable y desacoplada en Laravel, siguiendo principios de Clean Architecture, Domain-Driven Design y prácticas profesionales orientadas a manejabilidad, testabilidad y extensibilidad.

---

# 📑 Índice

1. 🎯 [Objetivo del proyecto](#-objetivo-del-proyecto)  
2. 🏗 [Arquitectura y diseño](#-arquitectura-y-diseño)  
   - [Capas del sistema](#capas-del-sistema)  
   - [Decisiones de diseño](#decisiones-de-diseño)  
   - [Patrones usados](#patrones-usados)  
3. 📂 [Estructura del proyecto](#-estructura-del-proyecto)  
4. 🧠 [Dominio](#-dominio)  
5. ⚙ [Funcionalidades principales](#-funcionalidades-principales)  
6. 🧪 [Tests automáticos](#-tests-automáticos)  
7. 📊 [Exportación de Excel asíncrona](#-exportación-de-excel-asíncrona)  
8. ⚡ [Escalabilidad horizontal](#-escalabilidad-horizontal)  
9. ▶️ [Cómo ejecutar el proyecto](#️-cómo-ejecutar-el-proyecto)  
10. 📬 [Endpoints](#-endpoints)

---

# 🎯 Objetivo del proyecto

El objetivo es implementar una API REST completa capaz de gestionar:

- Candidatos  
- Validación extensible de candidaturas  
- Evaluadores  
- Asignaciones  
- Listado consolidado con estadísticas  
- Exportación a Excel en procesos asíncronos  
- Notificación por email al finalizar la exportación

El proyecto pone especial foco en:

- Arquitectura limpia  
- Desacoplamiento del framework  
- Patrones de diseño  
- SQL complejo y eficiente  
- Testing  
- Escalabilidad horizontal

---

# 🏗 Arquitectura y diseño

El proyecto utiliza una **Arquitectura Limpia / Hexagonal** donde cada capa tiene una responsabilidad clara:

- **Domain:** Reglas de negocio puras, entidades y Value Objects  
- **Application:** Casos de uso (orquestación)  
- **Infrastructure:** Base de datos, Eloquent, Jobs, controladores, servicios externos  
- **Interface / Delivery:** API HTTP

---

## Capas del sistema

app/
├── Domain/
│ ├── Candidate/
│ ├── Evaluator/
│ └── Assignment/
│
├── Application/
│ ├── UseCases/
│ └── Contracts/
│
└── Infrastructure/
├── Persistence/
│ └── Eloquent/
├── Http/Controllers/
└── Excel/


---

## Decisiones de diseño

- **Dominio rico:** las invariantes se validan mediante value objects.
- **Validación extensible:** Chain of Responsibility permite añadir reglas sin modificar las existentes.
- **Repositorios basados en interfaces:** evita dependencia con Eloquent.
- **DTOs para respuestas:** evita filtrar entidades del dominio.
- **SQL optimizado:** joins, group_concat, COUNT(DISTINCT), subconsultas, orden dinámico y filtros.
- **Procesamiento pesado en colas:** exportación Excel ejecutada mediante workers.

---

## Patrones usados

| Patrón | Uso |
|-------|-----|
| Value Object | Email, YearsExperience |
| Entity | Candidate, Evaluator, Assignment |
| Repository | Contratos + implementaciones Eloquent |
| Chain of Responsibility | Validación de Candidatos |
| Use Case (Interactor) | Lógica de aplicación |
| DTO | Respuestas de casos de uso |
| Strategy (implícito) | Normalizadores, filtros |

---

# 📂 Estructura del proyecto

app/
├── Domain/
│ ├── Candidate/
│ │ ├── Entities/Candidate.php
│ │ ├── ValueObjects/
│ │ └── ValidationRules/
│ ├── Evaluator/
│ └── Assignment/
│
├── Application/
│ ├── UseCases/
│ └── Contracts/
│
└── Infrastructure/
├── Persistence/Eloquent/
├── Http/Controllers/
├── Excel/
└── Jobs/


---

# 🧠 Dominio

El dominio contiene:

### **Entidades**
- Candidate  
- Evaluator  
- Assignment  

### **Value Objects**
- `CandidateEmail`  
- `YearsOfExperience`  

### **Reglas extensibles de validación**
- `HasCvRule`  
- `ValidEmailRule`  
- `MinExperienceRule`  

Cada regla implementa una interfaz común y se encadena dinámicamente.

---

# ⚙ Funcionalidades principales

### ✔ Registro de candidatos  
### ✔ Validación extensible de candidatos  
### ✔ Gestión de evaluadores  
### ✔ Asignación candidato → evaluador  
### ✔ Listado consolidado con SQL avanzado  
Incluye:
- total de asignaciones por evaluador  
- concatenación de emails  
- orden dinámico  
- filtros  
- paginación  

### ✔ Resumen de candidatura  
### ✔ Exportación Excel con 50 registros por página  
### ✔ Envío de email notificando la exportación  

---

# 🧪 Tests automáticos

Incluye:

### ✔ Tests unitarios
- Reglas de validación  
- CandidateValidator  
- AssignEvaluatorHandler  

### ✔ Test feature
- Resumen de candidatura

### ✔ Test de integración
- SQL del listado consolidado con DB real (SQLite)

Ejecutar:

```bash
php artisan test


📊 Exportación de Excel y proceso asíncrono
Flujo:

Cliente llama:

POST /api/candidates/consolidated/export/async


Se encola ExportConsolidatedCandidatesJob

El worker genera un Excel:

storage/app/private/exports/*.xlsx


(Opcional) Se envía email al usuario notificando que ya está disponible

Worker:

php artisan queue:work

⚡ Escalabilidad horizontal

El sistema soporta:

Ejecución distribuida de trabajos en cola

Exportaciones pesadas sin bloquear el servidor HTTP

Capa de dominio desacoplada → permite cambiar DB o framework

Posibilidad de cachear agregaciones mediante Redis

Idempotencia en asignaciones (evita duplicados)

▶️ Cómo ejecutar el proyecto
1. Instalar dependencias
composer install

2. Copiar configuración base
cp .env.example .env
php artisan key:generate

3. Migrar base de datos
php artisan migrate --seed

4. Arrancar servidor
php artisan serve

5. Arrancar worker de colas
php artisan queue:work

6. Ejecutar tests
php artisan test

📬 Endpoints y documentación
Ping
GET /api/ping

Candidatos
POST /api/candidates
GET /api/candidates/{id}
GET /api/candidates/{id}/summary

Evaluadores
POST /api/evaluators

Asignación
POST /api/candidates/{id}/assign

Consolidado
GET /api/candidates/consolidated
POST /api/candidates/consolidated/export/async