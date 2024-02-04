<?php
require_once __DIR__ . '/../../Model/constants.php';

$formPage['validationJS'] = 'problemValidation.js';
$formPage['title'] = 'Crear problema';
$formPage['fields'] = [
    array('type' => 'number', 'id' => 'max_execution_time', 'placeholder' => "Temps d'execució (s)",
        'required' => 'required', 'min' => 0, 'max' => 120),
    array('type' => 'number', 'id' => 'max_memory_usage', 'placeholder' => "Memòria a utilitzar (MB)",
        'required' => 'required', 'min' => 0, 'max' => 1200),
    array('type' => 'selector', 'id' => 'visibility', 'name' => 'visibility', 'placeholder' => 'Visibilitat',
        'options' => [ array('id' => 'Public', 'title' => 'Públic'), array('id' => 'Private', 'title' => 'Privat')],
        'required' => 'required'),
    array('type' => 'selector', 'id' => 'language', 'name' => 'language', 'placeholder' => 'Llenguatge de programació',
        'options' => [ array('id' => 'C++', 'title' => 'C++'), array('id' => 'Python', 'title' => 'Python'),
            array('id' => 'Notebook', 'title' => 'Notebook')],
        'required' => 'required'),
    array('type'=>'checkbox', 'id' => 'entregable' ),
];
$formPage['submitText'] = 'Crear';
