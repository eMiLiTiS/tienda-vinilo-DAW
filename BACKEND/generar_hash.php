<?php
echo "<h2>Generador de Hashes</h2>";
echo "<p><strong>Hash para 'iker' (password: 123):</strong><br>";
echo password_hash('123', PASSWORD_DEFAULT);
echo "</p>";

echo "<p><strong>Hash para 'admin' (password: admin123):</strong><br>";
echo password_hash('admin123', PASSWORD_DEFAULT);
echo "</p>";
?>