<?php
session_start();

$maxIntentos = 3;
$bloqueoDuracion = 300;
$claveArchivo = "clave.txt";
$intentosArchivo = "intentos.txt";

if (!file_exists($claveArchivo)) {
    die("Error: El archivo de clave no existe.");
}
$claveCorrecta = trim(file_get_contents($claveArchivo));

$userIP = $_SERVER['REMOTE_ADDR'];

$intentos = [];
if (file_exists($intentosArchivo)) {
    $contenido = file_get_contents($intentosArchivo);
    $intentos = json_decode($contenido, true) ?: [];
}

if (!isset($intentos[$userIP])) {
    $intentos[$userIP] = ['fallidos' => 0, 'bloqueoHasta' => 0];
}

$tiempoBloqueoRestante = max(0, $intentos[$userIP]['bloqueoHasta'] - time());
$estaBloqueado = $tiempoBloqueoRestante > 0;

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$estaBloqueado) {
    $claveUnica = $_POST['clave_unica'] ?? '';

    if ($claveUnica === $claveCorrecta) {
        unset($intentos[$userIP]);
        file_put_contents($intentosArchivo, json_encode($intentos));
        echo "<p>¡Inicio de sesión exitoso!</p>";
        header("location: /menu/menu1.php");
	exit;
    } else {
        $intentos[$userIP]['fallidos']++;

        if ($intentos[$userIP]['fallidos'] >= $maxIntentos) {
            $intentos[$userIP]['bloqueoHasta'] = time() + $bloqueoDuracion;
            $estaBloqueado = true;
            $tiempoBloqueoRestante = $bloqueoDuracion;
            $error = "Has superado el límite de intentos. Tu IP está bloqueada.";
        } else {
            $intentosRestantes = $maxIntentos - $intentos[$userIP]['fallidos'];
            $error = "Clave incorrecta. Te quedan $intentosRestantes intentos.";
        }

        // Guardar intentos actualizados en intentos.txt
        file_put_contents($intentosArchivo, json_encode($intentos));
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .bubbles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .bubble {
            position: absolute;
            bottom: -150px;
            width: calc(30px + 70px * var(--scale));
            height: calc(30px + 70px * var(--scale));
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            animation: rise calc(8s + 5s * var(--scale)) infinite ease-in-out;
            animation-delay: calc(-5s * var(--delay));
            left: calc(var(--x-pos) * 100%);
        }

        @keyframes rise {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translateY(-200vh) scale(0.7);
                opacity: 0;
            }
        }

        form {
            background: rgba(255, 255, 255, 0.8);
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0px 4px 30px rgba(0, 0, 0, 0.4);
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            z-index: 1;
        }

        h1 {
            font-size: 3rem;
            color: #333;
            margin-bottom: 30px;
        }

        input[type="text"] {
            width: 100%;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ccc;
            border-radius: 15px;
            font-size: 1.5rem;
            box-shadow: inset 1px 1px 5px rgba(0, 0, 0, 0.1);
        }

        input[type="text"]:focus {
            border-color: #9face6;
            outline: none;
            box-shadow: inset 0px 0px 8px rgba(0, 128, 255, 0.4);
        }

        input[type="submit"] {
            background: #9face6;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        input[type="submit"]:hover {
            background: #74ebd5;
            transform: scale(1.1);
        }

        .error {
            color: red;
            margin-top: 20px;
            font-size: 1.2rem;
            animation: slideIn 0.5s ease-in-out;
        }

        .error.fade-out {
            animation: fadeOut 0.5s ease-in-out;
            opacity: 0;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        #bloqueo-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #bloqueo-overlay.active {
            display: flex;
        }

        .candado {
            text-align: center;
            color: white;
            animation: fadeIn 1s ease-in-out;
        }

        .candado i {
            font-size: 100px;
        }

        .candado span {
            font-size: 1.8rem;
            display: block;
            margin-top: 30px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Bubble Animation -->
    <div class="bubbles">
        <script>
            const bubbleContainer = document.querySelector('.bubbles');
            const numBubbles = 15;
            for (let i = 0; i < numBubbles; i++) {
                const bubble = document.createElement('div');
                bubble.classList.add('bubble');
                bubble.style.setProperty('--x-pos', Math.random());
                bubble.style.setProperty('--scale', Math.random());
                bubble.style.setProperty('--delay', Math.random());
                bubbleContainer.appendChild(bubble);
            }
        </script>
    </div>

    <?php if ($estaBloqueado): ?>
        <div id="bloqueo-overlay" class="active">
            <div class="candado">
                <i>🔒</i>
                <span>Tu IP está bloqueada. Tiempo restante: <span id="tiempo-bloqueo"><?= $tiempoBloqueoRestante ?></span> segundos.</span>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <h1>Inicio de Sesión</h1>
        <input type="text" name="clave_unica" id="clave_unica" placeholder="Clave Única" required>
        <br>
        <input type="submit" value="Iniciar Sesión">
        <?php if ($error): ?>
            <p class="error" id="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </form>

    <script>
        const tiempoBloqueoElem = document.getElementById("tiempo-bloqueo");
        if (tiempoBloqueoElem) {
            let tiempoRestante = parseInt(tiempoBloqueoElem.textContent);
            setInterval(() => {
                if (tiempoRestante > 0) {
                    tiempoRestante--;
                    tiempoBloqueoElem.textContent = tiempoRestante;
                }
            }, 1000);
        }

        const errorElem = document.getElementById("error-message");
        if (errorElem) {
            setTimeout(() => {
                errorElem.classList.add("fade-out");
            }, 3000);
        }
    </script>
</body>
</html>
