<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Prueba Tecnica</title>
    <link rel="shortcut icon" href="skydash/images/sinapsis.png" />

    <style>
        html, body {
        height: 100%;
        overflow: hidden; /* evita desplazamiento al mostrar SweetAlert */
        }

        body.swal2-shown {
            padding-right: 0 !important; /* elimina el desplazamiento causado por el scrollbar oculto */
        }

        .container {
            overflow: hidden;
            position: relative;
        }

        .swal2-container {
            z-index: 9999; /* asegura que esté por encima de todo sin alterar layout */
        }
    </style>

</head>

<body>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form>
                <h1>Crear Cuenta</h1>
                <div class="social-icons">
                    <a href="#" class="icon">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>o utiliza tu correo electrónico para registrarte</span>
                <input type="text" placeholder="Nombre">
                <input type="email" placeholder="Correo Electrónico">
                <input type="password" placeholder="Contraseña">
                <button>Registrarse</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="login.php" method="POST">
                <h1>Iniciar sesión</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>Ingresa tu nombre de usuario</span>
                <input type="text" name="username" placeholder="Nombre de usuario" required>
                <button type="submit">Iniciar sesión</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>¡Bienvenido de nuevo!</h1>
                    <p>Introduce tus datos personales para usar todas las funciones del sitio</p>
                    <button class="hidden" id="login">Iniciar Sesión</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>¡Bienvenidos Sinapsis!</h1>
                    <p>Inicien sesión con la credencial brindada y active el node.js para que el sistema funcione correctamente.</p>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>

<script>
    // Leer el parámetro "mensaje" de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const mensaje = urlParams.get("mensaje");

    if (mensaje === "bienvenido") {
        let timerInterval;
        Swal.fire({
            title: "¡Bienvenido!",
            html: "Redirigiendo en <b></b> milisegundos...",
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                    timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                window.location.href = "skydash/index.php"; // Redirigir al dashboard
            }
        });
    } else if (mensaje === "incorrecta") {
        Swal.fire({
            title: "Error",
            text: "Contraseña incorrecta",
            icon: "error",
        });
    } else if (mensaje === "noregistrado") {
        Swal.fire({
            title: "Usuario no encontrado",
            text: "No estás registrado en el sistema",
            icon: "error",
        });
    } else if (mensaje === "inactivo") {
        Swal.fire({
            title: "Usuario se ha puesto inactivo",
            text: "Porfavor contactarse con la empresa.",
            icon: "error",
        });
    }
</script>
</html>
