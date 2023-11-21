<?php
// Archivo: edita_producto.php
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

// Variable para almacenar mensajes de éxito o error
$message = "";

// Función para obtener la lista de productos
function getProductsList($conn)
{
    $sql = "SELECT id, nombre FROM productos";
    $result = $conn->query($sql);

    if ($result === false) {
        die("Error en la consulta de productos: " . $conn->error);
    }

    return $result;
}

// Función para obtener los datos de un producto por su ID
function getProductById($conn, $productId)
{
    $sql = "SELECT * FROM productos WHERE id = $productId";
    $result = $conn->query($sql);

    if ($result === false) {
        die("Error en la consulta del producto: " . $conn->error);
    }

    return $result->fetch_assoc();
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $id = $_POST["id"];
    $nombre = $_POST["nombre"];
    $precio = $_POST["precio"];
    $categoria = $_POST["categoría"];

    // Validación básica
    if (empty($nombre) || !is_numeric($precio) || $precio <= 0) {
        $message = "Por favor, completa todos los campos correctamente.";
    } else {
        // Actualizar los datos en la tabla de productos
        $sql = "UPDATE productos SET nombre='$nombre', precio=$precio, categoría=$categoria WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            $message = "Producto actualizado con éxito.";
        } else {
            $message = "Error al actualizar el producto: " . $conn->error;
        }
    }
}

// Obtener la lista de productos si no se recibe el ID por GET
if (!isset($_GET["id"])) {
    $productList = getProductsList($conn);
}

// Obtener los datos del producto si se recibe el ID por GET
if (isset($_GET["id"])) {
    $productId = $_GET["id"];
    $productData = getProductById($conn, $productId);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
</head>
<body>
    <h2>Editar Producto</h2>

    <?php
    // Mostrar mensajes de éxito o error
    if (!empty($message)) {
        echo "<p>$message</p>";
    }
    ?>

    <?php if (!isset($_GET["id"])) : ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
            <label for="id">Selecciona un producto:</label>
            <select name="id" required>
                <?php
                while ($row = $productList->fetch_assoc()) {
                    echo "<option value='{$row["id"]}'>{$row["nombre"]}</option>";
                }
                ?>
            </select>
            <input type="submit" value="Editar Producto">
        </form>
    <?php endif; ?>

    <?php if (isset($_GET["id"]) && !empty($productData)) : ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $productData["id"] ?? ''; ?>">

        <label for="nombre">Nombre del Producto:</label>
        <input type="text" name="nombre" value="<?php echo $productData["nombre"] ?? ''; ?>" required>

        <label for="precio">Precio:</label>
        <input type="number" name="precio" value="<?php echo $productData["precio"] ?? ''; ?>" min="0.01" step="0.01" required>

        <label for="categoría">Categoría:</label>
        <input type="number" name="categoría" value="<?php echo $productData["categoría"] ?? ''; ?>" required>

        <input type="submit" value="Actualizar Producto">
    </form>
<?php endif; ?>