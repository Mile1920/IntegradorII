<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mina Porco - Sistema de Gestión Integral Minera</title>
    
    <!-- Tu CSS original (ajústalo según dónde lo tengas) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Font Awesome para el ícono de montaña y futuros íconos -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>
<body>

    <!-- Navigation -->
    <header class="nav-glass">
        <nav>
            <div class="logo">
                <img src="{{ asset('img/Logo.png') }}" class="feature-img" alt="Logo Mina Porco">
                <i class="fas fa-mountain"></i>
            </div>
            <h1>Centro Minero Porco</h1>
            <div class="nav-links">
                <a href="#inicio">Inicio</a>
                <a href="#caracteristicas">Características</a>
                <a href="#como-funciona">Cómo Funciona</a>
                <a href="#contacto">Contacto</a>
                <a href="{{ route('login') }}" class="btn-primary">Iniciar Sesion</a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="inicio" class="hero">
        <div class="hero-content">
            <h2>Sistema Integral de Gestión Minera</h2>
            <p>Optimiza operaciones, mejora la seguridad y maximiza la eficiencia en tu mina con tecnología de última generación adaptada a las necesidades de la industria minera boliviana</p>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stat-card">   
            <img src="{{ asset('img/Minero.png') }}" class="feature-img" alt="Trabajadores">
            <div class="stat-number">500+</div>
            <div class="stat-label">Trabajadores Activos</div>
        </div>
        <div class="stat-card">
            <img src="{{ asset('img/SeguridadOperativa.png') }}" class="feature-img" alt="Eficiencia">
            <div class="stat-number">98%</div>
            <div class="stat-label">Eficiencia Operativa</div>
        </div>
        <div class="stat-card">
            <img src="{{ asset('img/Seguridad.png') }}" class="feature-img" alt="Seguridad">
            <div class="stat-number">100%</div>
            <div class="stat-label">Cumplimiento de Seguridad</div>
        </div>
        <div class="stat-card">
            <img src="{{ asset('img/Monitoreo.png') }}" class="feature-img" alt="Monitoreo">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Monitoreo en Tiempo Real</div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="caracteristicas">
        <h2 class="section-title">Características Principales</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Gestión de Personal</h3>
                <p>Control completo de asistencia, horarios, turnos, capacitaciones y evaluación de desempeño de todos los trabajadores mineros.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3>Control de Inventario</h3>
                <p>Administración inteligente de herramientas, equipos de protección personal, materiales explosivos y suministros en tiempo real.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Seguridad Avanzada</h3>
                <p>Monitoreo continuo de condiciones peligrosas, gases tóxicos, alertas automáticas y protocolos de emergencia minera.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <h3>Análisis de Producción</h3>
                <p>Reportes detallados de tonelaje, métricas de rendimiento, análisis predictivo y optimización de procesos extractivos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚨</div>
                <h3>Alertas Inteligentes</h3>
                <p>Notificaciones instantáneas sobre incidentes de seguridad, mantenimiento de equipos y eventos críticos en la operación.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>Acceso Móvil</h3>
                <p>Gestión completa desde cualquier dispositivo móvil o tablet con aplicación responsive y sincronización en tiempo real.</p>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process" id="como-funciona">
        <h2 class="section-title">Cómo Funciona</h2>
       <div class="process-steps">
            <div class="step">
                <div class="step-number">1</div>
                <h4>Registro e Instalación</h4>
                <p>Crea tu cuenta empresarial y configura tu perfil de operación minera en menos de 10 minutos con nuestro asistente inteligente.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h4>Configuración Personalizada</h4>
                <p>Personaliza módulos según tus necesidades, define permisos de usuarios, roles y parámetros operativos específicos de tu mina.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h4>Integración de Datos</h4>
                <p>Conecta tus sistemas existentes, importa datos históricos y comienza a digitalizar tus procesos mineros de forma gradual.</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h4>Operación Continua</h4>
                <p>Gestiona operaciones diarias, monitorea en tiempo real y toma decisiones basadas en datos con dashboards intuitivos.</p>
            </div>
        </div>
    </section>

    <!-- About Section 
    <section class="features" id="about">
        <h2 class="section-title">¿Por Qué Elegir Mina Porco?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🇧🇴</div>
                <h3>Adaptado a Bolivia</h3>
                <p>Sistema diseñado específicamente para cumplir con normativas mineras bolivianas y adaptado a las condiciones locales.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💎</div>
                <h3>Experiencia Minera</h3>
                <p>Desarrollado por expertos con más de 15 años de experiencia en gestión de operaciones mineras en Bolivia.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔧</div>
                <h3>Soporte Técnico Local</h3>
                <p>Equipo técnico disponible en español, capacitación presencial y soporte remoto 24/7 para resolver cualquier inconveniente.</p>
            </div>
        </div>
    </section>-->

    <!-- Footer -->
    <footer class="footer" id="contacto">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Mina Porco</h3>
                <p>Sistema integral de gestión minera diseñado para optimizar operaciones y mejorar la seguridad en la industria minera boliviana.</p>
            </div>
            <div class="footer-section">
                <h3>Contacto</h3>
                <ul class="footer-links">
                    <li>📞 +591 1234 5678</li>
                    <li>✉️ info@minaporco.com</li>
                    <li>📍 Potosí, Bolivia</li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Enlaces Rápidos</h3>
                <ul class="footer-links">
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#caracteristicas">Características</a></li>
                    <li><a href="#como-funciona">Cómo Funciona</a></li>
                    <li><a href="{{ route('login') }}">Ingresar al Sistema</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Mina Porco. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Smooth Scroll (funciona perfecto) -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>