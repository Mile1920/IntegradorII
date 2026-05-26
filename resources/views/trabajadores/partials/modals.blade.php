@component('components.modal', ['id' => 'ingresoModal', 'label' => 'ingresoModalLabel'])
    <form id="ingresoForm" method="POST" action="">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="ingresoModalLabel">Registrar Ingreso — <span id="ingresoModalName" class="font-weight-bold"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Área</label>
                <select name="area_id" id="ingreso_area_id" class="form-control">
                    <option value="">-- Seleccione (opcional) --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Observación (opcional)</label>
                <input type="text" name="observacion" id="ingreso_observacion" class="form-control" placeholder="Nota adicional sobre el ingreso">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Registrar Ingreso</button>
        </div>
    </form>
@endcomponent

@component('components.modal', ['id' => 'salidaModal', 'label' => 'salidaModalLabel'])
    <form id="salidaForm" method="POST" action="">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="salidaModalLabel">Registrar Salida — <span id="salidaModalName" class="font-weight-bold"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Área</label>
                <select name="area_id" id="salida_area_id" class="form-control">
                    <option value="">-- Seleccione (opcional) --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Observación (opcional)</label>
                <input type="text" name="observacion" id="salida_observacion" class="form-control" placeholder="Nota adicional sobre la salida">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Registrar Salida</button>
        </div>
    </form>
@endcomponent

@component('components.modal', ['id' => 'reportModal', 'label' => 'reportModalLabel'])
    <form id="reportForm" method="POST" action="">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="reportModalLabel">Reportar Condición — <span id="reportModalName" class="font-weight-bold"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Área (opcional)</label>
                <select name="area_id" id="report_area_id" class="form-control">
                    <option value="">-- Seleccione (opcional) --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" id="report_descripcion" class="form-control" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label>Gravedad</label>
                <select name="gravedad" id="report_gravedad" class="form-control">
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="critica">Crítica</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning">Enviar Reporte</button>
        </div>
    </form>
@endcomponent
