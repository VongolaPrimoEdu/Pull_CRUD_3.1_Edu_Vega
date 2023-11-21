<?php
// Archivo: crear_producto.php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: form_login.php"); // Redirige al formulario de inicio de sesión
    exit;
}
// Conexión a la base de datos (reemplaza con tus propios datos)
$servername = "localhost";
$username = "mitiendaonline";
$password = "mitiendaonline";
$dbname = "mitiendaonline";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables para almacenar mensajes de error y éxito
$errors = [];
$success = "";

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $nombre = $_POST["nombre"];
    $precio = $_POST["precio"];
    $categoría = $_POST["categoria"];
    $nombreArchivo = $_FILES["imagen"]["name"];
    $rutaTemporal = $_FILES["imagen"]["tmp_name"];
    $rutaDestino = "imagenes/" . $nombreArchivo;

    // Validación básica
    if (empty($nombre)) {
        $errors[] = "El nombre del producto es obligatorio.";
    }

    if (!is_numeric($precio) || $precio <= 0) {
        $errors[] = "El precio debe ser un número positivo.";
    }

    if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
        echo "La imagen se ha cargado correctamente.";
    } else {
        $errors[] = "Error al cargar la imagen.";
    }

    // Insertar en la base de datos si no hay errores
    if (empty($errors)) {
        // Insertar en la tabla de productos
        $sql = "INSERT INTO productos (nombre, precio, categoría) VALUES ('$nombre', $precio, $categoría)";

        if ($conn->query($sql) === TRUE) {
            $success = "Producto creado con éxito.";
        } else {
            $errors[] = "Error al insertar el producto: " . $conn->error;
        }
    }
}

// Obtener las categorías de la tabla de categorías
$sql_categorias = "SELECT id, nombre FROM categorías";
$result_categorias = $conn->query($sql_categorias);
if ($result_categorias === false) {
    die("Error en la consulta de categorías: " . $conn->error);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
</head>
<body>
    <h2>Crear Producto</h2>

    <?php
    // Mostrar mensajes de éxito o error
    if (!empty($errors)) {
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    } elseif (!empty($success)) {
        echo "<p>$success</p>";
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <label for="nombre">Nombre del Producto:</label>
        <input type="text" name="nombre" required>

        <label for="precio">Precio:</label>
        <input type="number" name="precio" min="0.01" step="0.01" required>

        <label for="categorías">Categoría:</label>
        <select name="categoria" required>
            <?php
            while ($row = $result_categorias->fetch_assoc()) {
                echo "<option value='{$row["id"]}'>{$row["nombre"]}</option>";
            }
            ?>
        </select>
        <label for="imagen">Imagen del Producto:</label>
    <input type="file" name="imagen" accept="image/*" required>

        <input type="submit" value="Crear Producto">
    </form>

    <p><a href="index.php">Ir al Menú Principal</a></p>
</body>
</html>