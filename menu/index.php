<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contador de Votos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            color: #333;
        }
        .container {
            margin-top: 50px;
            padding: 20px;
            max-width: 600px;
            background: white;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        h1 {
            font-size: 2em;
            color: #444;
        }
        .button-vote {
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            background-color: #4caf50;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .button-vote:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
        .button-vote:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .message {
            margin-top: 20px;
            font-size: 18px;
            color: #333;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        async function sendVote() {
            const button = document.getElementById('voteButton');
            button.disabled = true;

            const response = await fetch('votar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
            });
            const result = await response.json();

            const messageDiv = document.getElementById('message');
            const votesCount = document.getElementById('votesCount');
            const timeLeft = document.getElementById('timeLeft');
            const state = document.getElementById('state');

            if (result.success) {
                messageDiv.className = 'message success';
                votesCount.textContent = `${result.votes}/5`;
            } else {
                messageDiv.className = 'message error';
            }
            messageDiv.textContent = result.message;
            state.textContent = result.state;
            timeLeft.textContent = result.timeLeft > 0 ? `${result.timeLeft} segundos` : 'N/A';

            button.disabled = !result.success && result.state !== 'Inactivo';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>¡Vota ahora!</h1>
        <button id="voteButton" class="button-vote" onclick="sendVote()">Votar</button>
        <div id="message" class="message"></div>
        <h2>Estado del Sistema</h2>
        <table>
            <tr>
                <th>Número de Votos</th>
                <th>Estado</th>
                <th>Tiempo Restante</th>
            </tr>
            <tr>
                <td id="votesCount">0/5</td>
                <td id="
