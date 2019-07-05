<?php
//   session_start();

//   if(!isset($_SESSION['username']))
//   {
//     header("Location: index.html");
//     die();
//   }

  $configUrl = "config.xml";
  $patientsUrl = "files/pacientes.xml";
  $configXml = simplexml_load_file($configUrl) or die ("Não foi possível carregar o arquivo " . $configUrl . ".");
  $patientsXml = simplexml_load_file($patientsUrl) or die("Não foi possível carregar o arquivo pacientes.xml.");
  $patientList = $patientsXml->paciente;

  if (isset($configXml->patientsLastUpdate))
    $patientsLastUpdate = intval($configXml->patientsLastUpdate);
  else
    $patientsLastUpdate = 0;

  $patientsLastModified = filemtime($patientsUrl);

  if ($patientsLastUpdate !== $patientsLastModified)
  {
    $tags = array();
    $labelList = $configXml->labels->children();

    foreach ($patientsXml->paciente as $paciente)
    {
      foreach ($paciente->children() as $nodeName => $value)
      {
        $found = false;

        foreach ($labelList as $tag => $data)
        {
          if ($nodeName == $tag)
          {
            $found = true;
            break;
          }
        }

        if (!$found)
        {
          $pieces = preg_split('/(?=[A-Z])/', $nodeName, -1, PREG_SPLIT_NO_EMPTY);

          $newNode = $configXml->labels->addChild($nodeName, ucwords(implode(" ", $pieces)));
        }
      }
    }

    $configXml->patientsLastUpdate = $patientsLastModified;

    $configXml->saveXML($configUrl);
  }

  $warnings = array();

  foreach ($patientList as $patient)
  {
    if (isset($patient->numeroPaciente))
    {
      if ($patient->numeroPaciente->__toString() === "")
      {
        $warnings[] = "Numero de paciente inválido.";
      }
    }
    else
    {
      $warnings[] = "Numero de paciente inválido.";
    }
  }
?>

<!doctype html>
<html lang="pt">
  <head>
    <meta charset="ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="">

    <title>Painel de controle - Sistema de Pacientes</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  </head>

  <body class="bg-light">
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
      <img class="ml-auto" src="brand_small.png" alt="">
      <a class="navbar-brand mr-auto" href="#">Pacientes</a>
    </nav>

    <div class="nav-scroller bg-white shadow-sm">
      <nav class="nav nav-underline">
        <a class="nav-link ml-auto active" href="userPanel.php">Home</a>
        <a class="nav-link" href="searchFiles.php">Fichas</a>
        <a class="nav-link" href="preferences.php">Opções</a>
        <a class="nav-link mr-auto" href="logout.php">Sair</a>
      </nav>
    </div>

    <main role="main" class="container">
      <div class="d-flex align-items-center p-3 ml-3 my-3 text-white-50 bg-blue rounded shadow-sm">
        <div class="lh-100">
          <h6 class="mb-0 text-white lh-100">Sistema de Fichas Médicas</h6>
        </div>
      </div>

      <div class="d-flex align-items-center p-3 ml-3 my-3 text-white-50 bg-white rounded shadow-sm">
        <div class="w-100">
          <!--<h6 class="text-black">Bem-vindo, <?=$_SESSION['name']?>.</h6>-->
          <h6 class="text-black">Bem-vindo, Administrador.</h6>
          <small>
            <p class="text-black">
              <?php
                setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
                date_default_timezone_set('America/Sao_Paulo');
                
                $today = strtotime(date("Y-m-d"));
                
                echo ucwords(strftime('%A', $today)) . ", " . strftime('%d', $today) . " de " . strftime('%B', $today) . " de " . strftime('%Y', $today);
              ?>
            </p>
            <?php
              if (count($warnings))
              {
                echo "<div class=\"alert alert-warning\" role=\"alert\">";
                echo "Atenção: o  possui dados não normalizados.";
                echo "</div>";
              }
            ?>
          </small>
        </div>
      </div>

      <div class="d-flex align-items-center p-3 ml-3 my-3 text-white-50 bg-white rounded shadow-sm">
        <div>
          <div id="medicalFilesActions">
            <h6 class="text-black">Fichas Médicas</h6>
            <small>
              <ul>
                <li class="text-black"><a href="searchFiles.php"><i class="far fa-file-alt"></i> Listar fichas médicas</a></li>
              </ul>
            </small>
          </div>
        </div>
      </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  </body>
</html>