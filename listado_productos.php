<?php
// Archivo: listado_productos.php
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

// Obtener listado de productos con información de categorías
$sql = "SELECT productos.id, productos.nombre, productos.precio, productos.imagen, categorías.nombre AS categoria
        FROM productos
        INNER JOIN categorías ON productos.categoría = categorías.id";
$result = $conn->query($sql);

// Verificar si hay resultados
if ($result === false) {
    die("Error en la consulta de productos: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos</title>
</head>
<body>
    <h2>Listado de Productos</h2>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Imagen</th>
            <th>Categoría</th>
            <th>Acciones</th>
        </tr>

        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row["id"]}</td>";
            echo "<td>{$row["nombre"]}</td>";
            echo "<td>{$row["precio"]}</td>";
            echo "<td><img src='imagenes/{$row["imagen"]}' alt='{$row["nombre"]}' style='max-width: 50px; max-height: 50px;'></td>";
            echo "<td>{$row["categoria"]}</td>";
            echo "<td>
                    <a href='edita_producto.php?id={$row["id"]}'>Editar</a> |
                    <a href='elimina_producto.php?id={$row["id"]}'>Eliminar</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <p><a href="index.php">Ir al Menú Principal</a></p>
</body>
</html>