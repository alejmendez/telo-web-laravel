# 0. Consideraciones
Todos los cambios deben ser realizados en el modulo de Cmr, esto es el directorio Modules/Cmr.

# 1. Modelo de dominio

## Lista final de entidades y decisiones
- Customers: personas/empresas que crean requests; email, phone, dni por país; tipo de cliente; historial de direcciones.
- CustomerContacts: contactos adicionales del cliente, tipificados.
- ContactTypes: catálogo de tipos de contacto.
- Professionals: ejecutores de servicios; email, phone, dni por país; tipo de profesional; average_rating almacenado; historial de direcciones; especialización por subcategorías.
- ProfessionalTypes: catálogo.
- Categories: categorías de servicio.
- Subcategories: subcategorías pertenecientes a una categoría.
- ProfessionalSubcategory (pivot): N–N entre professionals y subcategories.
- Requests: solicitudes de servicio; cliente, subcategoría, urgencia, estado; asignación manual a un profesional; restricción: un cliente solo una request activa (pending|active) a la vez.
- Services: servicio creado al aceptar una request; estado; fechas.
- ServiceRatings: calificación por servicio (1–5); una por servicio por cliente; average_rating del professional se persiste.
- Subscriptions: suscripciones de profesionales; referencia a plan; estado; única activa por profesional.
- SubscriptionPlans: catálogo de planes (incluye “trial” de 3 meses).
- Payments: historial de pagos; vincula profesional y opcionalmente suscripción.
- CustomerTypes: catálogo.
- UrgencyTypes: catálogo con impacto en prioridad y SLA.
- Countries, Cities, Communes: localización normalizada; direcciones apuntan a commune.
- CustomerAddresses: historial direcciones del cliente (rango efectivo).
- ProfessionalAddresses: historial direcciones del profesional (rango efectivo).

Justificaciones breves
- Separación Customers/Professionals: cumple regla de tablas distintas; facilita políticas y validaciones específicas.
- Status como string con check constraint: evita sobreacoplar a un catálogo; simple y robusto.
- Pivot ProfessionalSubcategory: profesionales se especializan por subcategoría; normalizado.
- Direcciones históricas separadas: preserva cambios con rango temporal y sin sobrecargar entidad principal.
- SubscriptionPlans + Subscriptions + Payments: monetización normalizada; plan “trial” parametrizable.

## Relaciones
- Customer 1–N Requests
- Request 1–1 Service
- Request N–1 Customer; N–1 Subcategory; N–1 UrgencyType; N–1 (opcional) Professional (assigned_professional)
- Service N–1 Customer; N–1 Professional; N–1 Request
- Service 1–1 ServiceRating (por cliente)
- Professional 1–N Services; 1–N Subscriptions; N–N Subcategories (via pivot)
- Professional 1–N Payments
- Customer 1–N CustomerContacts; N–1 CustomerType
- Customers/Professionals 1–N Addresses → N–1 Commune → N–1 City → N–1 Country
- Subscriptions N–1 Professional; N–1 SubscriptionPlan

# 2. Migraciones

Convenciones
- Todas las tablas: timestamps, soft deletes (deleted_at), auditoría created_by, updated_by (FK opcional a users.id, indexadas).
- Nombres snake_case; claves foráneas con on update cascade, on delete restrict o set null según corresponda.
- PostgreSQL: partial unique indexes para reglas de unicidad condicional.

Archivos y estructuras

1) create_countries_table
- id, name (unique), iso2 (unique)

2) create_cities_table
- id, country_id FK, name, unique(country_id, name)

3) create_communes_table
- id, city_id FK, name, unique(city_id, name)

4) create_customer_types_table
- id, code (unique), name

5) create_professional_types_table
- id, code (unique), name

6) create_contact_types_table
- id, code (unique), name

7) create_categories_table
- id, name (unique), slug (unique)

8) create_subcategories_table
- id, category_id FK, name, slug, unique(category_id, slug)

9) create_urgency_types_table
- id, code (unique), name, priority_weight (int), sla_hours (int)

10) create_subscription_plans_table
- id, code (unique), name, duration_months (int), price_cents (bigint), currency (string), is_trial (bool)

11) create_customers_table
- id, customer_type_id FK
- first_name, last_name
- email (unique, lowercase)
- phone_e164 (unique)
- dni_country_id FK, dni, unique(dni_country_id, dni)
- notes (text nullable)
- indexes: customer_type_id, dni_country_id

12) create_customer_contacts_table
- id, customer_id FK, contact_type_id FK
- name, email nullable, phone_e164 nullable
- unique(customer_id, email) where email not null
- unique(customer_id, phone_e164) where phone_e164 not null

13) create_professionals_table
- id, professional_type_id FK
- first_name, last_name
- email (unique, lowercase)
- phone_e164 (unique)
- dni_country_id FK, dni, unique(dni_country_id, dni)
- average_rating (decimal(3,2) default 0)
- bio (text nullable)
- indexes: professional_type_id, dni_country_id

14) create_professional_subcategory_table (pivot)
- id, professional_id FK, subcategory_id FK
- unique(professional_id, subcategory_id)

15) create_customer_addresses_table
- id, customer_id FK, commune_id FK
- line1, line2 nullable, postal_code nullable
- effective_from (date), effective_to (date nullable)
- is_primary (bool default false)
- index(customer_id, effective_from)

16) create_professional_addresses_table
- id, professional_id FK, commune_id FK
- line1, line2 nullable, postal_code nullable
- effective_from (date), effective_to (date nullable)
- is_primary (bool default false)
- index(professional_id, effective_from)

17) create_requests_table
- id
- customer_id FK, subcategory_id FK, urgency_type_id FK
- assigned_professional_id FK nullable
- title, description text
- status (string) check in ('pending','active','rejected')
- priority (int) derivado de urgency_type
- sla_due_at (timestamp) derivado de urgency_type
- accepted_at (timestamp nullable)
- índices: status, customer_id, assigned_professional_id
- partial unique index: unique(customer_id) WHERE status IN ('pending','active')

18) create_services_table
- id
- request_id FK unique
- customer_id FK, professional_id FK
- status (string) check in ('pending','active','rejected')
- started_at (timestamp nullable), completed_at (timestamp nullable)
- índices: professional_id, customer_id, status

19) create_service_ratings_table
- id
- service_id FK unique
- customer_id FK
- csat (tinyint) check between 1 and 5
- comment (text nullable)
- unique(service_id, customer_id)

20) create_subscriptions_table
- id
- professional_id FK, subscription_plan_id FK
- status (string) check in ('pending','active','rejected')
- active_at (timestamp nullable), expires_at (timestamp nullable)
- índices: professional_id, status
- partial unique index: unique(professional_id) WHERE status = 'active'

21) create_payments_table
- id
- professional_id FK, subscription_id FK nullable
- amount_cents (bigint), currency (string), status (string), paid_at (timestamp)
- external_ref (string nullable)
- índices: professional_id, subscription_id, paid_at

Auditoría y soft deletes en todas las tablas
- created_by, updated_by (FK users.id, nullable), deleted_at
- índices sobre created_by, updated_by

# 3. Modelos Eloquent

Convenciones comunes
- protected $fillable: campos de entrada; proteger FKs calculadas.
- protected $casts: fechas, decimales, booleanos.
- Scopes: scopeActive, scopePending, scopeRejected; adicionales relevantes.

Modelos y relaciones

Customer
- belongsTo CustomerType; belongsTo Country (dni_country)
- hasMany Requests; hasMany CustomerContacts; hasMany CustomerAddresses; hasMany ServiceRatings
- casts: email to lower (mutator), deleted_at, timestamps
- scopes: withActiveRequest(), withoutActiveRequest()

Professional
- belongsTo ProfessionalType; belongsTo Country (dni_country)
- hasMany Services; hasMany Subscriptions; hasMany Payments; hasMany ProfessionalAddresses
- belongsToMany Subcategories (pivot)
- casts: average_rating decimal; timestamps
- scopes: withActiveSubscription(), withoutActiveSubscription(), prioritized() (orden por subscription activa y rating)

Category
- hasMany Subcategories

Subcategory
- belongsTo Category; belongsToMany Professionals; hasMany Requests

CustomerContact
- belongsTo Customer; belongsTo ContactType

CustomerAddress
- belongsTo Customer; belongsTo Commune

ProfessionalAddress
- belongsTo Professional; belongsTo Commune

City
- belongsTo Country; hasMany Communes

Commune
- belongsTo City

UrgencyType
- attributes: priority_weight, sla_hours

Request
- belongsTo Customer; belongsTo Subcategory; belongsTo UrgencyType; belongsTo Professional (assigned)
- hasOne Service
- scopes: active(), pending(), rejected(), unassigned(), forCustomer($id)

Service
- belongsTo Request; belongsTo Customer; belongsTo Professional
- hasOne ServiceRating
- scopes: active(), pending(), rejected()

ServiceRating
- belongsTo Service; belongsTo Customer
- casts: csat integer

SubscriptionPlan
- hasMany Subscriptions

Subscription
- belongsTo Professional; belongsTo SubscriptionPlan; hasMany Payments
- scopes: active(), pending(), rejected(), current()

Payment
- belongsTo Professional; belongsTo Subscription (nullable)
- casts: amount_cents integer, paid_at datetime

# 4. Factories

- CustomerFactory: nombres realistas, email lowercased, phone_e164 faker, dni con país válido; relaciona CustomerType.
- ProfessionalFactory: similar a Customer, professional_type; average_rating inicial 0; asignar subcategorías.
- CategoryFactory y SubcategoryFactory: nombres y slugs coherentes.
- UrgencyTypeFactory: códigos estándar (low, medium, high) con priority_weight y sla_hours.
- RequestFactory: cliente, subcategoría, urgencia; status aleatorio controlado; sin violar única activa (usar estado rejected o asignar sólo una active por cliente en seeding).
- ServiceFactory: basado en Request aceptada; status, fechas plausibles.
- ServiceRatingFactory: csat 1–5, comentario opcional; unique por service+customer.
- SubscriptionPlanFactory: planes básicos + trial (3 months, is_trial=true).
- SubscriptionFactory: profesional, plan; estados; para estado active, set active_at y expires_at.
- PaymentFactory: montos razonables en cents, currency 'USD' o local; vincular a suscripción si corresponde.
- AddressFactories: líneas de dirección y periodos efectivos válidos.

# 5. Seeders

- CustomerTypesSeeder: home_owner, business_owner, company.
- ProfessionalTypesSeeder: lista base (ej. plumber, electrician, general_contractor).
- UrgencyTypesSeeder: low (weight 1, sla 72h), medium (2, 48h), high (3, 24h).
- Countries/Cities/CommunesSeeder: mínimo de ejemplos (ej. Chile → Santiago → Providencia/Las Condes; Argentina → CABA → Palermo).
- Categories/SubcategoriesSeeder: catálogos base (ej. Plumbing → Leak Repair, Installation; Electrical → Wiring, Panel Upgrade).
- SubscriptionPlansSeeder: trial (3 meses, price_cents=0, is_trial=true), standard, premium.
- Statuses: no tabla dedicada en core; opcional StatusCatalogSeeder para UI con slugs pending/active/rejected.
- SampleDataSeeder: crea N customers y M professionals; asigna direcciones; crea relaciones profesionales-subcategorías; genera requests válidas, acepta algunas para crear services; crea subscriptions (una active por professional usando partial unique idx); genera payments y ratings.

# 6. Orden de ejecución (comandos)

Generación de modelos con migración, factory y seeder
- php artisan make:model Country -mfs
- php artisan make:model City -mfs
- php artisan make:model Commune -mfs
- php artisan make:model CustomerType -mfs
- php artisan make:model ProfessionalType -mfs
- php artisan make:model ContactType -mfs
- php artisan make:model Category -mfs
- php artisan make:model Subcategory -mfs
- php artisan make:model UrgencyType -mfs
- php artisan make:model SubscriptionPlan -mfs
- php artisan make:model Customer -mfs
- php artisan make:model CustomerContact -mfs
- php artisan make:model Professional -mfs
- php artisan make:model ProfessionalSubcategory -mfs
- php artisan make:model CustomerAddress -mfs
- php artisan make:model ProfessionalAddress -mfs
- php artisan make:model Request -mfs
- php artisan make:model Service -mfs
- php artisan make:model ServiceRating -mfs
- php artisan make:model Subscription -mfs
- php artisan make:model Payment -mfs

Migrar y sembrar
- php artisan migrate
- php artisan db:seed --class=CustomerTypesSeeder
- php artisan db:seed --class=ProfessionalTypesSeeder
- php artisan db:seed --class=UrgencyTypesSeeder
- php artisan db:seed --class=CountriesCitiesCommunesSeeder
- php artisan db:seed --class=CategoriesSubcategoriesSeeder
- php artisan db:seed --class=SubscriptionPlansSeeder
- php artisan db:seed --class=StatusCatalogSeeder
- php artisan db:seed --class=SampleDataSeeder

Notas de buenas prácticas
- Aplicar índices compuestos y parciales vía DB::statement en migraciones para reglas "una activa".
- Mutadores para normalizar email en minúsculas.
- Validaciones de integridad en Requests para impedir crear segunda pending/active.
- Actualizar average_rating del Professional al crear/actualizar ServiceRating (listener).
