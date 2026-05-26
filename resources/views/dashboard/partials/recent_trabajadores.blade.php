<div class="card">
    <div class="card-header">
        <h4 class="card-title">Nuevos trabajadores</h4>
        <p class="card-category">Últimas incorporaciones al personal</p>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($recentTrabajadores ?? [] as $t)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 shadow-sm" style="background: rgba(255,255,255,0.03);">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div style="width:64px;height:64px;flex:0 0 64px">
                                @if(!empty($t->foto))
                                    <img src="{{ asset('storage/' . $t->foto) }}" alt="{{ $t->nombre_completo }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;border:2px solid rgba(255,255,255,0.03)">
                                @else
                                    <div style="width:64px;height:64px;background:#2b2f3a;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#9aa0ab;font-weight:bold">{{ strtoupper(substr($t->nombre ?? '',0,1) . substr($t->ap_paterno ?? '',0,1)) }}</div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-weight:700;font-size:15px;color:#fff">{{ $t->nombre }} {{ $t->ap_paterno }} {{ $t->ap_materno }}</div>
                                <div class="text-muted small">CI: {{ $t->ci }} · {{ $t->cargo->nombre ?? '-' }} · {{ $t->area->nombre ?? '-' }}</div>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-sm btn-outline-light detail-btn" data-type="trabajador" data-id="{{ $t->id }}" data-nombre="{{ e($t->nombre . ' ' . $t->ap_paterno . ' ' . $t->ap_materno) }}" data-ci="{{ $t->ci }}" data-area="{{ $t->area->nombre ?? '' }}">Ver</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">No hay trabajadores recientes</div>
            @endforelse
        </div>
    </div>
</div>
