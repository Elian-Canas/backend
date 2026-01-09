# Sistema de Gestión de Licitaciones

Sistema fullstack para la gestión de ofertas y licitaciones, desarrollado conforme a los requisitos de la prueba técnica de Suplos 2025. Implementado con **PHP puro **(sin frameworks) siguiendo el patrón MVC y Vue.js 2.6+ en el frontend. El sistema permite crear, editar, listar y visualizar ofertas con validaciones robustas, gestión de documentos y generación automática de consecutivos. 

## 🚀 Características

- **Creación y edición de ofertas** con validaciones completas (frontend y backend)
- **Gestión de documentos adjuntos** (PDF/ZIP) en edición
- **Listado paginado** con filtros por consecutivo, objeto o descripción
- **Generación automática de consecutivos** en formato O-0001-25
- **Vista detallada** de ofertas con todas sus secciones
- **Arquitectura limpia sin frameworks en backend** (PHP puro + ORM Eloquent standalone)

## 🛠️ Tecnologías

- **PHP 8.2+** (sin frameworks: Laravel, Symfony, etc.)
- **Patrón MVC** (Modelo-Vista-Controlador)
- **ORM Eloquent (standalone)** para comunicación con base de datos
- **MySQL 8.0** como motor de base de datos
- **Validaciones personalizadas** y manejo de errores estructurado

## 📋 Frontend
- Vue.js 2.6+
- Axios y Fetch para consumo de API
- Element UI (o Bootstrap 5.2+ según elección)
- Validaciones en tiempo real y feedback al usuario
- Formularios reactivos con estados de botones condicionales

## 📋 Infraestructura
- Docker y Docker Compose para entorno de desarrollo
- Nginx como servidor web
- MySQL como base de datos

## 🏗️ Estructura del Proyecto
```
suplos_prueba_fullstack/
├── backend/              # Backend en PHP 8.2
│   ├── app/              # Controllers, Models, Helpers, Validations
│   ├── db/               # Migraciones, seeds, backups
│   ├── config/           # Configuración de la aplicación
│   ├── public/           # Archivos estáticos (PHP index para compatibilidad)
│   └── vendor/           # Dependencias con Composer (si aplica)
│
├── frontend/             # Aplicación Vue.js 2.6
│   ├── src/              # Componentes, vistas, store, servicios
│   ├── public/           # Assets públicos
│   ├── package.json      # Dependencias npm
│   ├── .env              # Variables de entorno
│   └── vue.config.js     # Configuración de Vue CLI
│
├── nginx/                # Configuración de servidor web reverse proxy
│   └── default.conf      # Configuración de Nginx
│
├── docker-compose.yml    # Orquestación de contenedores
└── README.md             # Documentación del proyecto
``` 

## 🔧 Instalación y Configuración

### 1. Clonar el repositorio

```bash
git clone --recurse-submodules https://github.com/Elian-Canas/suplos_prueba_fullstack.git
cd suplos_prueba_fullstack
```

### 2. Configurar variables de entorno

Copiar el archivo de ejemplo y configurar las variables *Frontend*:

```bash
cp .env-production .env
Dirigirse al archivo src/config.js e indicar la IP del equipo
```

Dentro del archivo docker-compose.yml en la seccion contenedor *frontend - vue* indicar la IP del equipo: 
```bash
VUE_APP_API_BASE_URL=http://192.168.1.32:8080
```
Dentro del archivo *docker-compose.yml en la seccion contenedor mysql - environment* indicar las credenciales de la base de datos:
  - MYSQL_DATABASE: db
  - MYSQL_USER: xxxxxx
  - MYSQL_PASSWORD: xxxxxx
  - MYSQL_ROOT_PASSWORD: xxxxxx


### 3. Construir e iniciar los contenedores
```bash
docker-compose up --build
```

### 4. Ejecutar en modo desarrollo Frontend

```bash
npm run serve
```

La aplicación estará disponible en `http://localhost:8080`


## 🔌 Integración con Backend

El frontend se comunica con el backend FastAPI a través de los siguientes endpoints:

El frontend se comunica con el backend PHP a través de los siguientes endpoints:

- `GET /ofertas` – Obtener listado paginado de ofertas con filtros opcionales
- `GET /ofertas/{id}` – Obtener los detalles completos de una oferta específica
- `GET /ofertas/export` – Exportar listado de ofertas filtrado a Excel
- `POST /ofertas` – Crear una nueva oferta
- `PUT /ofertas/{id}` – Actualizar una oferta existente
- `POST /documentos` – Subir documentos asociados a una oferta (solo en edición)
- `GET /actividades` – Obtener listado completo de actividades (maestra UNSPSC)
- `GET /actividades/buscar` – Buscar actividades por nombre o código
- `GET /actividades/{id}` – Obtener detalles de una actividad específica
