<!-- Modal de Confirmación Personalizado -->
<div id="confirmModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i class="fas fa-check-circle"></i> Confirmar
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-3">
                <p id="confirmMessage" class="mb-0 font-weight-bold small text-center"></p>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="confirmOkBtn">
                    Sí
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Sistema de confirmación personalizado que retorna Promise
window.systemConfirm = function(message) {
    return new Promise((resolve) => {
        const modal = document.getElementById('confirmModal');
        const messageEl = document.getElementById('confirmMessage');
        const okBtn = document.getElementById('confirmOkBtn');
        
        messageEl.textContent = message;
        
        // Limpiar eventos anteriores
        $(modal).off('hidden.bs.modal');
        $(modal).find('.close, .btn-secondary').off('click');
        
        // Variable para controlar si ya se resolvió
        let resolved = false;
        
        // Evento de confirmación
        const confirmHandler = function() {
            if (resolved) return;
            resolved = true;
            $(modal).modal('hide');
            setTimeout(() => resolve(true), 300);
        };
        
        okBtn.onclick = confirmHandler;
        
        // Evento de cancelación al cerrar modal
        $(modal).on('hidden.bs.modal', function() {
            if (!resolved) {
                resolved = true;
                resolve(false);
            }
        });
        
        // Evento al cerrar con X o botón cancelar
        $(modal).find('.close, .btn-secondary').on('click', function() {
            if (resolved) return;
            resolved = true;
            resolve(false);
        });
        
        // Mostrar modal
        $(modal).modal('show');
    });
};

// Función helper para usar con onclick que retorna true/false
window.confirmAction = function(message, callback) {
    window.systemConfirm(message).then(confirmed => {
        if (confirmed && callback) callback();
    });
    return false; // Prevenir submit/action por defecto
};
</script>
