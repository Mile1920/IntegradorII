<div class="card">
    <div class="card-header">
        <h4 class="card-title">Incidentes recientes</h4>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Área</th>
                    <th>Descripción</th>
                    <th>Gravedad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentIncidentes ?? [] as $inc)
                    <tr>
                        <td>{{ $inc->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $inc->area->nombre ?? '-' }}</td>
                        <td>{{ Illuminate\Support\Str::limit($inc->descripcion, 60) }}</td>
                        <td>{{ ucfirst($inc->gravedad) }}</td>
                        <td>
                            <button class="btn btn-link btn-sm detail-btn" data-type="incidente" data-id="{{ $inc->id }}" data-desc="{{ e($inc->descripcion) }}" data-area="{{ $inc->area->nombre ?? '' }}" data-gravedad="{{ $inc->gravedad }}">Ver</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Sin incidentes recientes</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
