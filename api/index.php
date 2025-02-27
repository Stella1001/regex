<?php
$resultados = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["regexInput"])) {
    $texto = $_POST["regexInput"];
    $resultados = analizarTexto($texto);
}

function analizarTexto($texto) {


    $numero_real = '(?<![\d,])\d+(?:,\d{3})*\.\d+(?!\d)';

    $porcentaje = '\b\d{1,3}(?:,\d{3})*(?:\.\d+)?%(?!\w)';

    $numero_natural = '(?:\d{1,3}(?:,\d{3})+|\d+)';

    $monetario = '(?<![\d,])\$\s*\d+(?:,\d{3})*(?:\.\d+)?(?!\d)';


    $patron_valido = "/($monetario)|($porcentaje)|($numero_real)|($numero_natural)/";
    $patron_invalido = "/\b\d+[a-zA-Z]+\b/";

    $lineas = explode("\n", $texto);
    $datos = [];

    foreach ($lineas as $num_linea => $linea) {
        if (preg_match_all($patron_valido, $linea, $matches)) {
            foreach ($matches[0] as $match) {
                $tipo = "Número válido";
                if (preg_match("/$monetario/", $match)) $tipo = "Monto monetario";
                elseif (preg_match("/$porcentaje/", $match)) $tipo = "Porcentaje";
                elseif (preg_match("/$numero_real/", $match)) $tipo = "Número real";
                elseif (preg_match("/$numero_natural/", $match)) $tipo = "Número natural";

                $datos[] = ["linea" => $num_linea + 1, "cadena" => $match, "tipo" => $tipo];
            }
        }
        if (preg_match_all($patron_invalido, $linea, $matches)) {
            foreach ($matches[0] as $match) {
                $datos[] = ["linea" => $num_linea + 1, "cadena" => $match, "tipo" => "Número inválido"];
            }
        }
    }
    return $datos;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css"/>
    <title>Regex</title>
    <script>
        function resetForm() {
            document.getElementById("regexInput").value = "";
            document.getElementById("resultadoTabla").innerHTML = '<tr><td colspan="4">No hay resultados aún</td></tr>';
        }
    </script>
</head>
<body>
<div class="container">
    <div class="moduloEntrada">
        <h1 class="titulo">Identificador de Cadenas Numéricas</h1>
        <form method="post">
            <label for="regexInput">Ingrese una cadena:</label>
            <div class="textarea-container">
                <textarea id="regexInput" name="regexInput" required></textarea>
                <button class="boton" type="submit">Enviar</button>
                <button class="boton" type="button" onclick="resetForm()">Reset</button>
            </div>
        </form>
    </div>
    <div class="moduloResultado">
        <table class="table">
            <thead>
            <tr>
                <th>No.</th>
                <th>No. Línea</th>
                <th>Cadena</th>
                <th>Tipo</th>
            </tr>
            </thead>
            <tbody id="resultadoTabla">
            <?php if (!empty($resultados)) : ?>
                <?php foreach ($resultados as $index => $dato) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $dato["linea"] ?></td>
                        <td><?= htmlspecialchars($dato["cadena"]) ?></td>
                        <td><?= $dato["tipo"] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4">No hay resultados aún</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
