<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/img/reloj.png"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">    
    <title>Clock On- Inicio de sesión</title>
</head>
<body>
    <header class ="header">
        <h1 class="title">Clock on</h1>
        <nav class="navContainer">
            <a href="./fichaje.php">Fichaje</a>
            <a href="./computoHoras.php">Cómputo de horas</a>
        </nav>
        <div>
            <a href="/index.php"><svg height="50px" width="50px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 60.671 60.671" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <ellipse style="fill:#010002;" cx="30.336" cy="12.097" rx="11.997" ry="12.097"></ellipse> <path style="fill:#010002;" d="M35.64,30.079H25.031c-7.021,0-12.714,5.739-12.714,12.821v17.771h36.037V42.9 C48.354,35.818,42.661,30.079,35.64,30.079z"></path> </g> </g> </g></svg></a>
        </div>
    </header>
    <section>
        <div>
            <h2>Bienvenido a Clock On</h2>
            <p>La aplicación de fichaje para trabajadores de:</p>
            <img src="./img/image.png" alt="logoActivo" class="logoActivo">
        </div>
        <div class="logInContainer">
            <div class="logInWorkers">
                <form action="logInProcess.php" class="formContainer" method="post">
                    <h3>Iniciar sesión:</h3>
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" name="email">
                    </div>
                    <div>
                        <label for="password">Contraseña:</label>
                        <input type="password" name="password">
                    </div>
                    <a href="#">¿Has olvidado la contraseña?</a>
                    <input type="submit" value="Iniciar sesión">
                </form>
            </div>
            <div class="logInWithoutRegister">
                <h3>Fichar sin registrarse</h3>
                <form action="fichajeSinRegistro.php" class="formContainer" method="post">
                    <div>
                        <label for="workerCode">Código trabajador@:</label>
                        <input type="text" id="workerCode" name="workerCode">
                    </div>
                    <input type="submit" value="Acceder">
                </form>
            </div>
        </div>
        
    </section>
    <footer></footer>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="./flatpickr.js"></script>
    <script src="./script.js"></script>
</body>
</html>