<?php

return [

    'core' => [
        'name'     => 'Core',
        'label'    => 'Módulos Principais',
        'required' => true,
        'icon'     => 'fa-cube',
        'color'    => 'green',
        'permissions'  => [
            'stock'      => ['name' => 'Estoque',      'icon' => 'fa-boxes-stacked'],
            'purchasing' => ['name' => 'Compras',      'icon' => 'fa-cart-shopping'],
            'sales'      => ['name' => 'Vendas',       'icon' => 'fa-bag-shopping'],
            'finance'    => ['name' => 'Financeiro',   'icon' => 'fa-coins'],
        ],
    ],

    'factory' => [
        'name'     => 'Fábrica',
        'label'    => 'Módulo Fábrica',
        'required' => false,
        'icon'     => 'fa-industry',
        'color'    => 'orange',
        'permissions'  => [
            'production'   => ['name' => 'Produção',      'icon' => 'fa-industry'],
            'raw_materials' => ['name' => 'Matéria-Prima', 'icon' => 'fa-flask-vial'],
        ],
    ],

];
