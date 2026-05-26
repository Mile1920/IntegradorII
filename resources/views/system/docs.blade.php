@extends('layouts.app')
@section('title', 'Documentación Técnica')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h2>Guía técnica resumida</h2>
            <p class="text-muted mb-0">
                Esta sección explica, con lenguaje sencillo, cómo se usan <strong>encriptación y hash</strong>,
                y cómo funciona la arquitectura <strong>MVC</strong> y el <strong>middleware</strong> dentro del proyecto Mina Porco.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title mb-0">Encriptación y Hash</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-2">Hash de contraseñas</h5>
                    <p class="mb-2">
                        Las contraseñas de los usuarios <strong>no se guardan en texto plano</strong>.
                        Laravel aplica automáticamente un <strong>hash unidireccional</strong> usando
                        el algoritmo <code>bcrypt</code> u otro configurado en <code>config/hashing.php</code>.
                    </p>
                    <p class="mb-2">
                        En este proyecto se utiliza:
                    </p>
                    <ul>
                        <li>
                            La clase <code>Illuminate\Support\Facades\Hash</code> para crear contraseñas
                            seguras, por ejemplo en <code>TrabajadorController::store()</code>:
                            <code>Hash::make($password)</code>.
                        </li>
                        <li>
                            El hash es <strong>irreversible</strong>: solo se puede comprobar si una
                            contraseña coincide usando <code>Hash::check($input, $hashGuardado)</code>.
                        </li>
                    </ul>

                    <h5 class="mt-3 mb-2">Encriptación de datos</h5>
                    <p class="mb-2">
                        Laravel también dispone de el helper <code>encrypt()/decrypt()</code> y de
                        <code>Crypt</code> para encriptar datos sensibles (por ejemplo, tokens o
                        configuraciones). En este proyecto se puede usar para:
                    </p>
                    <ul>
                        <li>Proteger tokens de sensores o llaves de integración.</li>
                        <li>Guardar datos temporales cifrados en base de datos o en cookies.</li>
                    </ul>
                    <p class="mb-0">
                        La clave usada para encriptar está en la variable <code>APP_KEY</code> del archivo
                        <code>.env</code>. Si se cambia, los datos encriptados antiguos dejan de poder leerse.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title mb-0">Arquitectura MVC y Middleware</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-2">Modelo – Vista – Controlador (MVC)</h5>
                    <ul class="mb-2">
                        <li>
                            <strong>Modelos</strong> (carpeta <code>app/Models</code>): representan las tablas,
                            por ejemplo <code>Trabajador</code>, <code>Area</code>, <code>Sensor</code>,
                            y encapsulan la lógica de acceso a datos.
                        </li>
                        <li>
                            <strong>Controladores</strong> (carpeta <code>app/Http/Controllers</code>):
                            reciben las peticiones, aplican validaciones y llaman a los modelos.
                            Ejemplos: <code>TrabajadorController</code>, <code>IncidenteController</code>,
                            <code>SensorController</code>.
                        </li>
                        <li>
                            <strong>Vistas</strong> (carpeta <code>resources/views</code>): son las pantallas Blade
                            que ve el usuario, por ejemplo <code>trabajadores/index.blade.php</code> o
                            <code>modules/mine-2d.blade.php</code>.
                        </li>
                    </ul>
                    <p class="mb-2">
                        El flujo típico es:
                        <code>Ruta → Controlador → Modelo → Vista</code>.
                    </p>

                    <h5 class="mt-3 mb-2">Middleware en este proyecto</h5>
                    <p class="mb-2">
                        El <strong>middleware</strong> es una capa intermedia que se ejecuta antes o después
                        de cada petición. En este sistema se utiliza para:
                    </p>
                    <ul class="mb-2">
                        <li>
                            <code>auth</code> y <code>verified</code>: obligan a que el usuario
                            esté autenticado y con correo verificado antes de acceder a módulos internos.
                        </li>
                        <li>
                            <code>role:...</code> (de <code>spatie/laravel-permission</code>):
                            restringe rutas según el rol (por ejemplo,
                            solo <em>administrador-principal</em> puede entrar a
                            <code>/system/status</code>).
                        </li>
                    </ul>
                    <p class="mb-0">
                        Las rutas con middleware se definen en <code>routes/web.php</code>,
                        donde se agrupan con llamadas como
                        <code>Route::middleware(['auth','role:administrador-principal'])-&gt;group(...)</code>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('system.status') }}" class="btn btn-secondary">Volver al estado del sistema</a>
    </div>
</div>
@endsection

