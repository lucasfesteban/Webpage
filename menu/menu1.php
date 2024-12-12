<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contador de Votos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            padding: 20px;
            width: 90%;
            max-width: 400px;
            background: white;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        h1 {
            font-size: 2em;
            color: #444;
            margin-bottom: 20px;
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
            margin-top: 10px;
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
            padding: 10px;
            border-radius: 8px;
        }
        .message.success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 100%;
            text-align: left;
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            border: none;
            padding: 15px;
            font-size: 16px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        td {
            background-color: #fafafa;
        }
        tr:nth-child(odd) td {
            background-color: #f7f7f7;
        }
    </style>
    <script>
        async function fetchStatus() {
            const response = await fetch('menu.php', { method: 'GET' }); // Solicitud para obtener el estado actual
            const result = await response.json();

            // Actualizar la interfaz con los datos del servidor
            document.getElementById('votesCount').textContent = `${result.votes}/5`;
            document.getElementById('state').textContent = result.state;
            document.getElementById('timeLeft').textContent = result.timeLeft > 0 ? `${result.timeLeft} segundos` : 'N/A';

            const messageDiv = document.getElementById('message');
            if (result.success) {
                messageDiv.className = 'message success';
                messageDiv.textContent = 'Estado actualizado correctamente.';
            } else {
                messageDiv.className = 'message error';
                messageDiv.textContent = result.message;
            }

            // Habilitar/deshabilitar el botón según el estado
            document.getElementById('voteButton').disabled = result.state !== 'Inactivo';
        }

        async function sendVote() {
            const button = document.getElementById('voteButton');
            button.disabled = true; // Desactivar el botón mientras se procesa la solicitud

            const response = await fetch('menu.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
            });
            const result = await response.json();

            // Actualizar la interfaz con los datos del servidor
            const messageDiv = document.getElementById('message');
            if (result.success) {
                messageDiv.className = 'message success';
                messageDiv.textContent = result.message;
                document.getElementById('votesCount').textContent = `${result.votes}/5`;
            } else {
                messageDiv.className = 'message error';
                messageDiv.textContent = result.message;
            }

            document.getElementById('state').textContent = result.state;
            document.getElementById('timeLeft').textContent = result.timeLeft > 0 ? `${result.timeLeft} segundos` : 'N/A';

            // Habilitar/deshabilitar el botón según el estado
            button.disabled = result.state !== 'Inactivo';
        }

        // Llamar a fetchStatus al cargar la página
        window.onload = fetchStatus;
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
                <th>Estado:</th>
                <td id="state">Cargando...</td>
            </tr>
            <tr>
                <th>Número de Votos:</th>
                <td id="votesCount">0/5</td>
            </tr>
            <tr>
                <th>Tiempo Restante:</th>
                <td id="timeLeft">N/A</td>
            </tr>
        </table>
    </div>
</body>
</html>
