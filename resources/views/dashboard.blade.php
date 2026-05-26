@extends('layouts.app')
@section('title', 'Dashboard - ' . ucfirst(auth()->user()->getRoleNames()->first()))
@section('content')

@php
    $user = auth()->user();
    $role = $user->getRoleNames()->first();
@endphp

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="card-title mb-0" style="font-size: 2rem; color: #fff;">
                        <img src="{{ asset('img/Minero.png') }}" alt="Bienvenido" class="icon-img">
                        ¡Bienvenido {{ $user->name }}!
                    </h2>
                    <small style="font-size: 1.1rem; color: rgba(255,255,255,0.95);">Rol: <strong>{{ ucfirst($role) }}</strong></small>
                </div>
                <div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user"></i> Mi Perfil
                    </a>
                </div>
            </div>
            <div class="card-body" style="font-size: 1.1rem;">
                <div class="mb-3">
                    <p class="mb-0">Panel de control personalizado para tu rol en el sistema.</p>
                </div>

                <!-- Dashboard según rol -->
                @if($role === 'administrador-principal')
                    @include('dashboard.roles.admin-principal')
                @elseif($role === 'administrador-area')
                    @include('dashboard.roles.admin-area')
                @elseif($role === 'tecnico')
                    @include('dashboard.roles.tecnico')
                @elseif($role === 'trabajador')
                    @include('dashboard.roles.trabajador')
                @else
                    @include('dashboard.partials.stats')
                @endif
            </div>
        </div>
    </div>
</div>

@include('dashboard.partials.detail_modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos para el script externo (no usar Vite, cargar desde public/js)
        window.DASHBOARD_DATA = {
            labels: @json($labels ?? []),
            counts: @json($counts ?? [])
        };
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
@endsection