<div class="table-responsive">
    <table class="table table-hover">
        <thead class="text-primary">
            <tr>
                <th>CI</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Celular</th>
                <th>Edad</th>
                <th>Área</th>
                <th>Cargo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trabajadores as $t)
            <tr>
                <td>{{ $t->ci }}</td>
                <td><strong>{{ $t->nombre_completo }}</strong></td>
                <td>{{ $t->email }}</td>
                <td>{{ $t->celular ?? '-' }}</td>
                <td>{{ $t->edad ?? '-' }}</td>
                <td>{{ $t->area->nombre ?? '-' }}</td>
                <td>{{ $t->cargo->nombre ?? '-' }}</td>
                <td>
                    <span class="badge badge-{{ $t->activo ? 'success' : 'secondary' }}">
                        {{ $t->activo ? 'ACTIVO' : 'INACTIVO' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('trabajadores.edit', $t) }}" class="btn btn-warning btn-sm" 
                        data-toggle="tooltip" data-placement="top" title="Editar" @if(!$t->activo) disabled @endif>
                        <img src="{{ asset('img/Logo.png') }}" alt="Editar" class="icon-img">
                    </a>

                    @hasrole('administrador-principal')
                        <form action="{{ route('trabajadores.destroy', $t) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-{{ $t->activo ? 'danger' : 'success' }} btn-sm" 
                                data-toggle="tooltip" data-placement="top" title="{{ $t->activo ? 'Desactivar' : 'Activar' }}"
                                onclick="event.preventDefault(); systemConfirm('{{ $t->activo ? '¿Desactivar a ' . $t->nombre_completo . '?' : '¿Activar a ' . $t->nombre_completo . '?' }}').then(confirmed => { if(confirmed) this.closest('form').submit(); }); return false;">
                                <img src="{{ asset('img/SeguridadOperativa.png') }}" alt="estado" class="icon-img">
                            </button>
                        </form>
                    @endhasrole

                    <button type="button" class="btn btn-info btn-sm" 
                        data-toggle="tooltip" data-placement="top" title="Registrar ingreso de {{ $t->nombre_completo }}"
                        onclick="openIngresoModal({{ $t->id }}, '{{ addslashes($t->nombre_completo) }}')">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Ingreso" class="icon-img">
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm" 
                        data-toggle="tooltip" data-placement="top" title="Registrar salida de {{ $t->nombre_completo }}"
                        onclick="openSalidaModal({{ $t->id }}, '{{ addslashes($t->nombre_completo) }}')">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Salida" class="icon-img">
                    </button>

                    <form action="{{ route('trabajadores.sos', $t) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                            data-toggle="tooltip" data-placement="top" title="SOS"
                            onclick="event.preventDefault(); systemConfirm('¿Enviar SOS por {{ $t->nombre_completo }}?').then(confirmed => { if(confirmed) this.closest('form').submit(); }); return false;">
                            SOS
                        </button>
                    </form>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No hay trabajadores registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $trabajadores->links() }}
</div>
