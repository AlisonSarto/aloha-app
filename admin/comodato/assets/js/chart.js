$(document).on('click', '.chart', function() {

  const id = $(this).data('id');
  const modal = $('#modal');

  // Função para obter o primeiro dia do mês atual e o dia atual
  function getDefaultDates() {
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    return {
      inicio: firstDayOfMonth.toISOString().split('T')[0],
      fim: today.toISOString().split('T')[0]
    };
  }

  // Definir as datas padrão
  const defaultDates = getDefaultDates();

  modal.find('.modal-title').text('Visualizar compras');
  modal.find('.modal-body').html(`

    <div class="row mb-2">
      <div class="col-6">
        <label for="inicio" class="form-label">Data de início</label>
        <input type="date" class="form-control" id="inicio" value="${defaultDates.inicio}" required>
      </div>
      <div class="col-6">
        <label for="fim" class="form-label">Data de fim</label>
        <input type="date" class="form-control" id="fim" value="${defaultDates.fim}" required>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-4">
        <label for="compras" class="form-label">Comprou</label>
        <input type="text" class="form-control" id="compras" value="" readonly>
      </div>
      <div class="col-4">
        <label for="faltou" class="form-label">Faltou</label>
        <input type="text" class="form-control" id="faltou" value="" readonly>
      </div>
      <div class="col-4">
        <label for="meta" class="form-label">Meta</label>
        <input type="text" class="form-control" id="meta" value="" readonly>
      </div>
    </div>

    <canvas id="chart" style="width: 100%; height: 400px;"></canvas>
  `);
  modal.find('.modal-footer').html(`
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
  `);

  let chartInstance; // Variável global para armazenar a instância do gráfico

  function createChart(inicio, fim) {
    $.ajax({
      url: `/admin/api/clientes/verif_compras`,
      type: 'GET',
      data: {
        cliente_id: id,
        inicio: inicio,
        fim: fim,
      },
      success: function(response) {
        var compra = response.qtd_compras;
        var falta = response.qtd_faltou;
        var meta = response.meta_periodo;

        $('#compras').val(compra + ' pacotes');
        $('#faltou').val(falta + ' pacotes');
        $('#meta').val(meta + ' pacotes');
  
        var ctx = document.getElementById('chart').getContext('2d');
  
        // Destruir o gráfico existente, se houver
        if (chartInstance) {
          chartInstance.destroy();
        }
  
        // Criar um novo gráfico
        chartInstance = new Chart(ctx, {
          type: 'pie',
          data: {
            labels: ['Comprou', 'Faltou'],
            datasets: [{
              label: 'Compras',
              data: [compra, falta],
              backgroundColor: [
                'rgb(98, 192, 75)',
                'rgba(255, 99, 132, 1)'
              ],
              borderColor: [
                'rgb(98, 192, 75)',
                'rgba(255, 99, 132, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                display: false,
                position: 'top',
              },
              title: {
                display: false,
              }
            }
          }
        });
  
        // Adiciona o evento de clique no gráfico
        chartInstance.canvas.onclick = function(event) {
          var activePoints = chartInstance.getElementsAtEventForMode(event, 'nearest', { intersect: true }, false);
          if (activePoints.length) {
            var firstPoint = activePoints[0];
            var label = chartInstance.data.labels[firstPoint.index];
            var value = chartInstance.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
            console.log(label + ': ' + value);
          }
        };
      },
      error: function(xhr, status, error) {
        const message = xhr.responseJSON.message;
        console.error(message);
      }
    });
  }

  // Atualizar o gráfico ao abrir o modal
  createChart(defaultDates.inicio, defaultDates.fim);

  // Atualizar o gráfico ao alterar as datas
  modal.on('change', '#inicio, #fim', function() {
    const inicio = $('#inicio').val();
    const fim = $('#fim').val();

    // Verificar se a data de início não é maior que a data de fim
    if (new Date(inicio) > new Date(fim)) {
      alert('A data de início não pode ser maior que a data de fim.');
      return;
    }

    createChart(inicio, fim);
  });

  // Exibir o modal
  modal.modal('show');

  // destroy the chart when the modal is closed
  modal.on('hidden.bs.modal', function() {
    if (chartInstance) {
      chartInstance.destroy();
    }
  });
});