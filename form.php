<?php
$servername = "localhost";
$username = "mitiendaonline";
$password = "mitiendaonline";
$dbname = "mitiendaonline";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, nombre, correo_electronico, contrasena_hash FROM usuarios2 WHERE correo_electronico = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['contrasena_hash'];


      
        // Verificar si la contraseña ingresada coincide con la almacenada en la base de datos
        if ($password=$stored_password) {
            session_start();
            $_SESSION['loggedin'] = true; // Marca al usuario como autenticado
            $_SESSION['email'] = $email; // Puedes almacenar otros datos de usuario si es necesario
            echo "¡Inicio de sesión exitoso!";
           
        } else {
            echo "La contraseña es incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }
}

$conn->close();
?>