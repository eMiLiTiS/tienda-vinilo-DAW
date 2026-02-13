# 🎵 Vinyl Lab

Sistema de gestión de catálogo de vinilos con panel de administración y sistema de reseñas.

## 🚀 Stack Tecnológico

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 8.2, MySQL
- **Hosting**: 
  - Frontend en Vercel
  - Backend en Railway

## 📁 Estructura del Proyecto

```
tienda-iker/
├── BACKEND/              # Código PHP y lógica del servidor
│   ├── uploads/          # Imágenes de vinilos
│   ├── *.php            # Archivos PHP
│   └── styles.css       # Estilos del panel admin
│
├── FRONTEND/            # Archivos estáticos
│   ├── imagenes/        # Imágenes del sitio
│   ├── index.html       # Página principal
│   ├── login.html       # Formulario de login
│   ├── formulario.html  # Formulario de reseñas
│   └── styles.css       # Estilos principales
│
└── sql/                 # Scripts de base de datos
    └── vinyl_lab.sql
```

## 🔧 Configuración Local

### Requisitos
- XAMPP (Apache + MySQL + PHP 8.2)
- Navegador moderno

### Instalación

1. Clona el repositorio en `C:\xampp\htdocs\`
2. Importa `sql/vinyl_lab.sql` en phpMyAdmin
3. Accede a `http://localhost/tienda%20iker/BACKEND/index.php`

### Credenciales de prueba
- Usuario: `iker`
- Contraseña: `123`

## 🌐 Despliegue en Producción

### Railway (Backend)
1. Crear nuevo proyecto en Railway
2. Agregar MySQL database
3. Conectar repositorio GitHub
4. Railway detectará automáticamente PHP
5. Configurar variables de entorno (se generan automáticamente)

### Vercel (Frontend)
1. Crear nuevo proyecto en Vercel
2. Conectar repositorio GitHub
3. Configurar:
   - Root Directory: `FRONTEND`
   - Framework Preset: `Other`

## ✨ Funcionalidades

### Públicas
- ✅ Catálogo de vinilos
- ✅ Sistema de reseñas
- ✅ Carrusel de opiniones
- ✅ Diseño responsive

### Administración (requiere login)
- ✅ Gestión de vinilos (CRUD completo)
- ✅ Gestión de reseñas
- ✅ Subida de imágenes
- ✅ Control de visibilidad
- ✅ Búsqueda en tiempo real

## 🔒 Seguridad

- ✅ Prepared statements (SQL injection protection)
- ✅ Sanitización de inputs
- ✅ Validación de archivos subidos
- ✅ Sesiones seguras
- ✅ HTTPS only en producción

## 📝 Base de Datos

### Tablas
- `usuarios` - Cuentas de administrador
- `vinilos` - Catálogo de productos
- `resenas` - Opiniones de usuarios

## 🛠️ Desarrollo

### Archivos importantes
- `BACKEND/conexion.php` - Local
- `BACKEND/conexion_railway.php` - Producción

## 📄 Licencia

Proyecto educativo - 2026