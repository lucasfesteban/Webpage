<?php
header('Content-Type: application/json');

// Ruta del archivo donde se almacenan los datos
$dataFile = 'ipvoto.txt';

// Leer los datos existentes
$votos = 0;
$ips = [];
$startTime = null;

if (file_exists($dataFile)) {
    $lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($ip, $timestamp) = explode('|', $line);
        $ips[$ip] = $timestamp; // Almacenar IP y marca de tiempo
    }
    $votos = count($ips);
    $startTime = file_exists('timer.txt') ? (int)file_get_contents('timer.txt') : null;
}

// Obtener la IP del usuario
$userIp = $_SERVER['REMOTE_ADDR'];
$currentTime = time(); // Tiempo actual en segundos

// Configuración del temporizador
$executionTime = 60; // Tiempo de ejecución en segundos (1 minuto)
$cooldownTime = 30; // Tiempo de cooldown en segundos
$state = 'Inactivo';
$timeLeft = 0;
$canVote = true;

// Determinar el estado y el tiempo restante
if ($startTime) {
    if ($currentTime - $startTime < $executionTime) {
        $state = 'Ejecución';
        $timeLeft = $executionTime - ($currentTime - $startTime);
        $canVote = false;
    } elseif ($currentTime - $startTime < $executionTime + $cooldownTime) {
        $state = 'Cooldown';
        $timeLeft = $executionTime + $cooldownTime - ($currentTime - $startTime);
        $canVote = false;
    } else {
        // Reiniciar el sistema
        $votos = 0;
        $ips = [];
        unlink('timer.txt'); // Eliminar el archivo de temporizador
        $state = 'Inactivo';
        $timeLeft = 0;
    }
}

// Manejo de votos
$response = ['success' => false, 'message' => '', 'state' => $state, 'votes' => $votos, 'timeLeft' => $timeLeft];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canVote) {
    if (array_key_exists($userIp, $ips)) {
        $response['message'] = 'Ya has votado. Solo se permite un voto por usuario.';
    } elseif ($votos >= 5) {
        $startTime = time();
        file_put_contents('timer.txt', $startTime);
        $response['state'] = 'Ejecución';
        $response['timeLeft'] = $executionTime;
        $response['message'] = 'El límite de votos ha sido alcanzado. Temporizador iniciado.';
    } else {
        $ips[$userIp] = $currentTime;
        $dataToSave = '';
        foreach ($ips as $ip => $timestamp) {
            $dataToSave .= "$ip|$timestamp" . PHP_EOL;
        }
        file_put_contents($dataFile, $dataToSave); // Sobrescribe el archivo
        $votos++;
        $response['success'] = true;
        $response['message'] = '¡Gracias por tu voto!';
        $response['votes'] = $votos;
    }
} else {
    $response['message'] = 'No puedes votar en este momento.';
}

echo json_encode($response);

