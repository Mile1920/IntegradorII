document.addEventListener('DOMContentLoaded', function () {
    // Detail modal behavior
    document.querySelectorAll('.detail-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var type = this.dataset.type;
            var modalBody = document.getElementById('detailModalBody');
            if (!modalBody) return;
            if (type === 'incidente'){
                var desc = this.dataset.desc || '';
                var area = this.dataset.area || '';
                var gravedad = this.dataset.gravedad || '';
                modalBody.innerHTML = '<p><strong>Área:</strong> ' + area + '</p><p><strong>Gravedad:</strong> ' + gravedad + '</p><hr><p>' + desc + '</p>';
            } else if (type === 'trabajador'){
                var nombre = this.dataset.nombre || '';
                var ci = this.dataset.ci || '';
                var area = this.dataset.area || '';
                modalBody.innerHTML = '<p><strong>Nombre:</strong> ' + nombre + '</p><p><strong>CI:</strong> ' + ci + '</p><p><strong>Área:</strong> ' + area + '</p>';
            }
            if (window.jQuery && $('#detailModal').modal) {
                $('#detailModal').modal('show');
            }
        });
    });

    // Render sparkline using Chart.js and window.DASHBOARD_DATA
    var ctxEl = document.getElementById('sensorSparkline');
    if (ctxEl && window.Chart && window.DASHBOARD_DATA) {
        try {
            var labels = window.DASHBOARD_DATA.labels || [];
            var data = window.DASHBOARD_DATA.counts || [];
            new Chart(ctxEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0,123,255,0.15)',
                        tension: 0.3,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {legend: {display: false}},
                    scales: {x: {display: false}, y: {display: false}}
                }
            });
        } catch (e) {
            console.error('Error inicializando sparkline:', e);
        }
    }
});