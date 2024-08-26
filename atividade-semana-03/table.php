<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Página de Exemplo</h1>
<?php

  /* Conecta ao MySQL e seleciona o banco de dados. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) {
    echo "Falha ao conectar ao MySQL: " . mysqli_connect_error();
    exit();
  }

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Garante que as tabelas EMPLOYEES e DEPARTMENTS existem. */
  VerificaTabelaEmployees($connection, DB_DATABASE);
  VerificaTabelaDepartments($connection, DB_DATABASE);

  /* Se os campos de entrada estiverem preenchidos, adiciona uma linha à tabela EMPLOYEES ou DEPARTMENTS. */
  $employee_name = isset($_POST['NAME']) ? htmlentities($_POST['NAME']) : '';
  $employee_address = isset($_POST['ADDRESS']) ? htmlentities($_POST['ADDRESS']) : '';
  $department_name = isset($_POST['DEPARTMENT_NAME']) ? htmlentities($_POST['DEPARTMENT_NAME']) : '';
  $department_location = isset($_POST['DEPARTMENT_LOCATION']) ? htmlentities($_POST['DEPARTMENT_LOCATION']) : '';
  $department_creation_date = isset($_POST['DEPARTMENT_CREATION_DATE']) ? htmlentities($_POST['DEPARTMENT_CREATION_DATE']) : '';

  if (!empty($employee_name) && !empty($employee_address)) {
    AdicionaEmployee($connection, $employee_name, $employee_address);
  }

  if (!empty($department_name) && !empty($department_location) && !empty($department_creation_date)) {
    AdicionaDepartment($connection, $department_name, $department_location, $department_creation_date);
  }
?>

<!-- Formulário de entrada para Employees -->
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NOME</td>
      <td>ENDEREÇO</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="ADDRESS" maxlength="90" size="60" />
      </td>
      <td>
        <input type="submit" value="Adicionar Employee" />
      </td>
    </tr>
  </table>
</form>

<!-- Formulário de entrada para Departments -->
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NOME DO DEPARTAMENTO</td>
      <td>LOCALIZAÇÃO DO DEPARTAMENTO</td>
      <td>DATA DE CRIAÇÃO</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="DEPARTMENT_NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="DEPARTMENT_LOCATION" maxlength="60" size="30" />
      </td>
      <td>
        <input type="date" name="DEPARTMENT_CREATION_DATE" />
      </td>
      <td>
        <input type="submit" value="Adicionar Department" />
      </td>
    </tr>
  </table>
</form>

<!-- Exibe os dados da tabela Employees. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NOME</td>
    <td>ENDEREÇO</td>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");

if ($result) {
  while($query_data = mysqli_fetch_row($result)) {
    echo "<tr>";
    echo "<td>", $query_data[0], "</td>",
         "<td>", $query_data[1], "</td>",
         "<td>", $query_data[2], "</td>";
    echo "</tr>";
  }
  mysqli_free_result($result);
}
?>

</table>

<!-- Exibe os dados da tabela Departments. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NOME DO DEPARTAMENTO</td>
    <td>LOCALIZAÇÃO</td>
    <td>DATA DE CRIAÇÃO</td>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM DEPARTMENTS");

if ($result) {
  while($query_data = mysqli_fetch_row($result)) {
    echo "<tr>";
    echo "<td>", $query_data[0], "</td>",
         "<td>", $query_data[1], "</td>",
         "<td>", $query_data[2], "</td>",
         "<td>", $query_data[3], "</td>";
    echo "</tr>";
  }
  mysqli_free_result($result);
}
?>

</table>

<!-- Limpeza. -->
<?php
  mysqli_close($connection);
?>

</body>
</html>

<?php

/* Adiciona um funcionário na tabela. */
function AdicionaEmployee($connection, $name, $address) {
   $n = mysqli_real_escape_string($connection, $name);
   $a = mysqli_real_escape_string($connection, $address);

   $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES ('$n', '$a');";

   if(!mysqli_query($connection, $query)) echo("<p>Erro ao adicionar dados do funcionário.</p>");
}

/* Adiciona um departamento na tabela. */
function AdicionaDepartment($connection, $name, $location, $creation_date) {
   $n = mysqli_real_escape_string($connection, $name);
   $l = mysqli_real_escape_string($connection, $location);
   $d = mysqli_real_escape_string($connection, $creation_date);

   $query = "INSERT INTO DEPARTMENTS (NAME, LOCATION, CREATION_DATE) VALUES ('$n', '$l', '$d');";

   if(!mysqli_query($connection, $query)) echo("<p>Erro ao adicionar dados do departamento.</p>");
}

/* Verifica se a tabela EMPLOYEES existe e, se não, a cria. */
function VerificaTabelaEmployees($connection, $dbName) {
  if(!TabelaExiste("EMPLOYEES", $connection, $dbName))
  {
     $query = "CREATE TABLE EMPLOYEES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Erro ao criar a tabela.</p>");
  }
}

/* Verifica se a tabela DEPARTMENTS existe e, se não, a cria. */
function VerificaTabelaDepartments($connection, $dbName) {
  if(!TabelaExiste("DEPARTMENTS", $connection, $dbName))
  {
     $query = "CREATE TABLE DEPARTMENTS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         LOCATION VARCHAR(60),
         CREATION_DATE DATE
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Erro ao criar a tabela.</p>");
  }
}

/* Verifica a existência de uma tabela. */
function TabelaExiste($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
