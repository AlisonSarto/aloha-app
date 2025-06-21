<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  // Busca todos os clientes
  $clientes_sql = "
    SELECT *
    FROM clientes
    ORDER BY id DESC
  ";
  $clientes_result = $conn_crm->query($clientes_sql);

  $crm = [];

  while ($cliente = $clientes_result->fetch_assoc()) {
    $cliente_id = $cliente['id'];

    // Status atual do cliente
    $status_sql = "
      SELECT status FROM status_cliente
      WHERE cliente_id = $cliente_id
      ORDER BY criado_em DESC, id DESC
      LIMIT 1
    ";
    $status_result = $conn_crm->query($status_sql);
    $status = $status_result->fetch_assoc()['status'] ?? null;

    // Mensagens agrupadas por data
    $mensagens_sql = "
      SELECT id, direcao, conteudo, datahora
      FROM mensagens
      WHERE cliente_id = $cliente_id
      ORDER BY datahora ASC
    ";
    $mensagens_result = $conn_crm->query($mensagens_sql);

    $conversations = [];
    $msgs_by_date = [];

    while ($msg = $mensagens_result->fetch_assoc()) {
      $date = date('Y-m-d', strtotime($msg['datahora']));
      $msgs_by_date[$date][] = [
        'type' => $msg['direcao'] === 'entrada' ? 'client' : 'company',
        'time' => date('H:i', strtotime($msg['datahora'])),
        'text' => $msg['conteudo']
      ];
    }
    foreach ($msgs_by_date as $date => $msgs) {
      $conversations[] = [
        'date' => $date,
        'messages' => $msgs
      ];
    }

    // Última análise/contexto IA
    $contexto_sql = "
      SELECT contexto, recomendacoes, sentimento_porcentagem
      FROM contextos
      WHERE cliente_id = $cliente_id
      ORDER BY criado_em DESC, id DESC
      LIMIT 1
    ";
    $contexto_result = $conn_crm->query($contexto_sql);
    $contexto = $contexto_result->fetch_assoc();

    $analysis = $contexto ? [
      'summary' => $contexto['contexto'],
      'sentiment' => (int)$contexto['sentimento_porcentagem'],
      'recommendations' => $contexto['recomendacoes'] ? explode("\n", $contexto['recomendacoes']) : []
    ] : null;

    // Tarefas
    $tarefas_sql = "
      SELECT id, titulo, criado_em, status
      FROM tarefas
      WHERE cliente_id = $cliente_id
      ORDER BY criado_em DESC
    ";
    $tarefas_result = $conn_crm->query($tarefas_sql);
    $tasks = [];
    while ($tarefa = $tarefas_result->fetch_assoc()) {
      $tasks[] = [
        'id' => (int)$tarefa['id'],
        'text' => $tarefa['titulo'],
        'dueDate' => date('Y-m-d', strtotime($tarefa['criado_em'])),
        'completed' => $tarefa['status'] === 'concluida'
      ];
    }

    $crm[] = [
      'id' => (int)$cliente['id'],
      'name' => $cliente['nome'],
      'business' => $cliente['nome_comercio'],
      'document' => $cliente['documento'],
      'phone' => $cliente['numero'],
      'status' => $status,
      'conversations' => $conversations,
      'analysis' => $analysis,
      'tasks' => $tasks
    ];
  }

  send([
    'status' => 200,
    'crm' => $crm
  ]);

?>