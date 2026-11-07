# Specimen Repository REST API — Laravel + MongoDB

A production-grade **Specimen Repository REST API** built with **Laravel 12**, **MongoDB 7**, **Laravel Sanctum**, and **OpenAPI/Swagger (L5-Swagger)**. 

Designed for managing biological specimens, taxonomic hierarchies, natural history collections, scientific researchers, field research projects, geographic sampling locations, high-resolution voucher imagery, scientific documentation, and specimen loan management with automated state transitions and audit logging.

---

## 🏛️ System Architecture

The API follows strict **Layered Laravel MVC Architecture**:
* **Controllers**: Thin controllers handling request validation routing and returning standardized JSON responses.
* **Form Requests**: Dedicated request validation classes with MongoDB-aware uniqueness rules (`unique:mongodb.<collection>,<field>`).
* **Services**: Encapsulated business domain rules (specimen lifecycle validations, loan approval/return transitions, file uploads, administrative audit logging).
* **Repositories**: Data access layer isolating query construction, full-text and compound filtering, and native MongoDB aggregation pipelines (`$group`, `$match`, `$project`, `$sort`, `$limit`).
* **API Resources**: Eloquent API Resources transforming models into consistent, snake_case JSON schemas with formatted ISO-8601 timestamps and relational nesting.
* **Token Authentication & RBAC**: Laravel Sanctum with custom `PersonalAccessToken` model extending MongoDB DocumentModel, accompanied by role-based authorization middleware (`super_admin`, `admin`, `researcher`).
* **MongoDB Indexing Command**: Custom Artisan command (`php artisan db:mongo-indexes`) applying unique constraints, compound indexes, text indexes, and foreign key indexes.

---

## 🧰 Technology Stack

| Component | Technology | Version | Purpose |
| :--- | :--- | :--- | :--- |
| **Language** | PHP | 8.2+ / 8.4+ | Core Backend Runtime |
| **Framework** | Laravel | 12.x / 13.x | Application Foundation & MVC Engine |
| **Database** | MongoDB | 7.0+ | Primary NoSQL Document Database |
| **DB Driver** | `mongodb/laravel-mongodb` | ^5.11 | Eloquent MongoDB Provider & Aggregations |
| **Authentication** | Laravel Sanctum | ^4.3 | Stateful & Bearer Token Authentication |
| **API Documentation** | L5-Swagger / Swagger-PHP | ^11.1 / ^6.1 | Interactive OpenAPI 3.0 Documentation & UI |
| **Testing** | Pest PHP & PHPUnit | ^5.1 | Feature & Integration Test Suite |

---

## 📦 Prerequisites

* **PHP** >= 8.2 with `ext-mongodb`, `ext-curl`, `ext-mbstring`, `ext-openssl`, `ext-pdo`, `ext-json`
* **Composer** >= 2.x
* **Docker & Docker Compose** (or MongoDB >= 6.0 running on port `27018`)

---

## 🚀 Installation & Setup

### 1. Start the Dedicated MongoDB Container
Run the dedicated project MongoDB container via Docker Compose:
```bash
docker compose up -d
```
*(Starts the `specimen-repo-db` container running MongoDB 7.0 on host port `27018`).*

### 2. Clone & Install Dependencies
```bash
cd backend
composer install
```

### 3. Configure Environment Variables
Copy `.env.example` to `.env` and configure your MongoDB connection:
```bash
cp .env.example .env
php artisan key:generate
```

Ensure `.env` contains:
```env
APP_NAME="Specimen Repository API"
APP_ENV=local
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mongodb
MONGODB_URI=mongodb://127.0.0.1:27018
MONGODB_DATABASE=specimen_repository

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### 3. Create MongoDB Database Indexes
Run the custom index management command to ensure query performance and data integrity:
```bash
php artisan db:mongo-indexes
```

This creates:
* `specimens`: Unique `specimen_code`, index on `catalog_number`, compound index `{status: 1, created_at: -1}`, foreign keys (`taxon_id`, `collection_id`, `project_id`, `location_id`), and text search index `{scientific_name: "text", common_name: "text", description: "text"}`.
* `taxa`: Unique `scientific_name`, indexes on `genus`, `family`, `order`, `class`, `phylum`, `kingdom`.
* `collections`: Unique `name`, index on `status`.
* `researchers`: Unique `email`, index on `institution`.
* `projects`: Unique `project_code`, index on `status`.
* `loans`: Indexes on `status`, `specimen_id`, `researcher_id`, compound index `{status: 1, expected_return_date: 1}`.
* `audit_logs`: Compound index `{created_at: -1, action: 1}`, index on `admin_id`.
* `admins`: Unique `email`, index on `role`.

### 4. Seed Database with Realistic Biological Data
Seed administrators, taxonomic trees, natural history collections, Philippine researchers, conservation projects, voucher specimens, and loan transactions:
```bash
php artisan db:seed
```

### 5. Generate OpenAPI / Swagger Documentation
```bash
php artisan l5-swagger:generate
```

### 6. Start the API Server & Admin Dashboard
```bash
composer run dev
# or:
php artisan serve --host=127.0.0.1 --port=8000
```
* **Admin Dashboard (Achromatic UI)**: `http://127.0.0.1:8000/admin` (or `http://127.0.0.1:8000/`)
* **API Base URL**: `http://127.0.0.1:8000/api/v1`
* **Swagger UI Documentation**: `http://127.0.0.1:8000/api/documentation`

---

## 🖥️ Achromatic Admin Console

The application includes a **curatorial administration console** designed using a strictly **achromatic color palette** (high-contrast monochrome with pure `#000000` blacks, `#FFFFFF` whites, and curated neutral grays; zero chromatic hues).

### Core Features:
1. **Overview Dashboard**: Real-time KPI summary (specimen counts, active loans, overdue alerts, taxonomic distribution).
2. **Biological Specimens Catalog**: Paginated table with regex search, status and preservation filters, deep specimen inspector modal, and voucher registration modal.
3. **Specimen Loans Workflow**: Track loan periods, review borrower credentials, approve pending requests, record returns with specimen condition assessments, and handle cancellations.
4. **Taxonomic Hierarchy**: Interactive classification cards (Kingdom to Species), author citations, and new taxon creation.
5. **Collection Archives**: Natural history archive catalog with curator details and establishment history.
6. **Georeferenced Localities**: Field sampling coordinates (latitude/longitude), habitats, and island groups.
7. **Researchers Directory**: Scientific investigators catalog with institutional affiliations and research specialties.
8. **Security Audit Trail**: Immutable log of curatorial and administrative operations with IP and timestamp metadata.
9. **Dark & Light Mode**: Pure monochrome toggle for low-light lab or curatorial workstation environments.
10. **1-Click Quick Login**: Instant authentication button pre-configured for Super Admin `rhayan@gmail.com` / `rhynlodo`.

---

## 🔐 Default Authentication Credentials

The database seeder provisions three pre-configured accounts representing different roles:

| Role | Email | Password | Permissions |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `rhayan@gmail.com` | `rhynlodo` | Full system access, admin management, hard deletion, audit logs (Rhayan Lodovice) |
| **Curator / Admin** | `curator@specimenrepo.local` | `CuratorPass123!` | Specimen CRUD, loan approval/return, taxon/collection/project management |
| **Researcher** | `researcher@specimenrepo.local` | `ResearcherPass123!` | Read access to catalog, submit loan requests, view taxonomy |

---

## 📖 Complete API Route Catalog (52 Endpoints)

All API routes are prefixed with `/api/v1`.

### 1. Authentication (`/api/v1/auth`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `POST` | `/auth/login` | Public | Authenticate administrator or researcher and obtain Sanctum Bearer token |
| `POST` | `/auth/logout` | Authenticated | Revoke the current access token |
| `GET` | `/auth/me` | Authenticated | Retrieve profile details of currently authenticated user |

### 2. Biological Specimens (`/api/v1/specimens`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/specimens` | Authenticated | Paginated catalog with multi-field search, status filter, kingdom, family, and preservation method |
| `POST` | `/specimens` | Admin+ | Register a new biological specimen with catalog numbers and taxonomic references |
| `GET` | `/specimens/{id}` | Authenticated | Retrieve comprehensive specimen profile with images, documents, and loans |
| `PUT` | `/specimens/{id}` | Admin+ | Update specimen metadata, collection site, or storage location |
| `DELETE`| `/specimens/{id}` | Super Admin | Remove specimen record (blocked if active loans exist) |
| `POST` | `/specimens/{id}/images` | Admin+ | Upload high-resolution specimen voucher photograph (JPEG, PNG, WebP) |
| `POST` | `/specimens/{id}/documents`| Admin+ | Upload scientific specimen document, field notes, or permits (PDF, DOCX) |

### 3. Taxonomy (`/api/v1/taxa`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/taxa` | Authenticated | Paginated taxonomic hierarchy records with filtering by kingdom/phylum/family |
| `POST` | `/taxa` | Admin+ | Add scientific classification rank (Kingdom to Species) |
| `GET` | `/taxa/{id}` | Authenticated | Retrieve single taxon details |
| `PUT` | `/taxa/{id}` | Admin+ | Update taxonomic scientific name, authority, or classification ranks |
| `DELETE`| `/taxa/{id}` | Super Admin | Delete taxon (protected if associated specimens exist) |
| `GET` | `/taxa/{id}/children` | Authenticated | Retrieve downstream child taxa in hierarchy |

### 4. Natural History Collections (`/api/v1/collections`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/collections` | Authenticated | List all physical and institutional collections |
| `POST` | `/collections` | Admin+ | Create new collection archive |
| `GET` | `/collections/{id}` | Authenticated | Retrieve collection summary and statistics |
| `PUT` | `/collections/{id}` | Admin+ | Update collection details and curator info |
| `DELETE`| `/collections/{id}` | Super Admin | Remove collection archive |
| `GET` | `/collections/{id}/specimens` | Authenticated | Retrieve all specimens belonging to collection |

### 5. Scientific Researchers (`/api/v1/researchers`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/researchers` | Authenticated | List registered researchers and affiliations |
| `POST` | `/researchers` | Admin+ | Register scientific investigator |
| `GET` | `/researchers/{id}` | Authenticated | Retrieve researcher profile |
| `PUT` | `/researchers/{id}` | Admin+ | Update researcher affiliation and contact information |
| `DELETE`| `/researchers/{id}` | Super Admin | De-register researcher |
| `GET` | `/researchers/{id}/loans` | Authenticated | Retrieve loan history for specific researcher |

### 6. Research Projects (`/api/v1/projects`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/projects` | Authenticated | List field expedition and conservation research projects |
| `POST` | `/projects` | Admin+ | Register new research project |
| `GET` | `/projects/{id}` | Authenticated | Retrieve project details |
| `PUT` | `/projects/{id}` | Admin+ | Update project scope and timelines |
| `DELETE`| `/projects/{id}` | Super Admin | Remove project record |
| `GET` | `/projects/{id}/specimens` | Authenticated | List all specimens collected under project |

### 7. Geographic Locations (`/api/v1/locations`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/locations` | Authenticated | List sampling localities and GPS coordinates |
| `POST` | `/locations` | Admin+ | Create sampling location with latitude/longitude |
| `GET` | `/locations/{id}` | Authenticated | Retrieve location details |
| `PUT` | `/locations/{id}` | Admin+ | Update geographic coordinates or habitat description |
| `DELETE`| `/locations/{id}` | Super Admin | Remove sampling location record |

### 8. Specimen Loans Workflow (`/api/v1/loans`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/loans` | Authenticated | List specimen loan requests with status filtering |
| `POST` | `/loans` | Authenticated | Submit loan request for research examination |
| `GET` | `/loans/{id}` | Authenticated | View loan transaction details |
| `POST` | `/loans/{id}/approve` | Admin+ | Approve loan; transitions specimen status to `loaned` |
| `POST` | `/loans/{id}/return` | Admin+ | Record return of specimen; restores specimen to `active` |
| `POST` | `/loans/{id}/cancel` | Admin+ | Cancel pending loan request |

### 9. MongoDB Analytical Reports (`/api/v1/reports`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/reports/dashboard` | Authenticated | Real-time counts, loan states, and specimen status distribution |
| `GET` | `/reports/specimens` | Authenticated | Aggregation by status, kingdom, preservation method, and monthly intake |
| `GET` | `/reports/taxonomy` | Authenticated | Aggregation of top families, orders, and classes |
| `GET` | `/reports/collections`| Authenticated | Specimen distributions across collection archives |
| `GET` | `/reports/loans` | Authenticated | Loan analytics, overdue tracking, and top borrowers |

### 10. Audit Logs (`/api/v1/audit-logs`)
| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/audit-logs` | Admin+ | Paginated audit trail of all state-changing administrative operations |
| `GET` | `/audit-logs/{id}` | Admin+ | Retrieve individual audit log entry with IP, User-Agent, and payload |

---

## ⚡ Quick Start: cURL Examples

### 1. Authenticate (Obtain Bearer Token)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "rhayan@gmail.com",
    "password": "rhynlodo"
  }'
```

*Save the returned `token` string for subsequent calls:*
```bash
export TOKEN="your_plain_text_token_here"
```

### 2. Fetch Dashboard Analytics
```bash
curl -X GET http://127.0.0.1:8000/api/v1/reports/dashboard \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 3. Query Filtered Specimens with Pagination
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/specimens?status=active&preservation_method=wet&per_page=10" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 4. Create a New Biological Specimen
```bash
curl -X POST http://127.0.0.1:8000/api/v1/specimens \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "specimen_code": "SPEC-2026-HERP-001",
    "catalog_number": "NM-HERP-2026-001",
    "scientific_name": "Varanus bitatawa",
    "common_name": "Northern Sierra Madre Forest Monitor",
    "kingdom": "Animalia",
    "phylum": "Chordata",
    "class": "Reptilia",
    "order": "Squamata",
    "family": "Varanidae",
    "genus": "Varanus",
    "species": "bitatawa",
    "collector": "Sierra Madre Expedition Unit",
    "collection_date": "2026-02-14",
    "preservation_method": "wet",
    "repository_location": "Herpetology Storage - Shelf H2",
    "status": "active"
  }'
```

### 5. Approve a Specimen Loan
```bash
curl -X POST http://127.0.0.1:8000/api/v1/loans/{loan_id}/approve \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

---

## 🧪 Automated Testing

The project includes an automated test suite implemented with **Pest PHP** covering:
* **Authentication**: Login validation, invalid credentials rejection, profile retrieval, logout token revocation.
* **Specimen Management**: Registration, uniqueness validation, search and filtering, unauthorized mutation prevention, deletion constraints.
* **Specimen Loans**: Full lifecycle transitions (`pending` → `approved` → `returned` / `cancelled`), specimen status locking, preventing double loans.
* **Taxonomy**: Taxonomic records CRUD, hierarchy validations, researcher role permission enforcement (403 Forbidden).
* **Analytics Reports**: Native MongoDB aggregation pipelines verification for dashboard, specimens, taxonomy, collections, and loans.

To run the entire automated test suite:
```bash
php artisan test
```

Expected output:
```text
Pass: 118, Fail: 0, Assertions: 444
```

---

## 📄 License

This software is released under the [MIT License](LICENSE).
