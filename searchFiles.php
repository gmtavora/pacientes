<?php
// session_start();

// if(!isset($_SESSION['username']))
// {
//   header("Location: index.html");
//   die();
// }

header('Content-Type: text/html; charset=utf-8');

$configUrl = "config.xml";
$patientsUrl = "files/pacientes.xml";
$patientsXml = simplexml_load_file($patientsUrl) or die ("Não foi possível carregar o arquivo de pacientes.");
$configXml = simplexml_load_file($configUrl) or die ("Não foi possível carregar o arquivo " . $configUrl . ".");

$labelList = $configXml->labels->children();
$infoList = $configXml->essentials->children();
$filters = $configXml->filters->children();
$patientList = $patientsXml->paciente;

$values = array();

if (isset($_GET["max"]))
  {
    if (is_numeric($_GET["max"]))
    {
      $maxResults = intval($_GET["max"]);
      if ($maxResults < 1)
        $maxResults = 10;
    }
  }
  else
    $maxResults = 10;

  if (isset($_GET["page"]))
  {
    if (is_numeric($_GET["page"]))
      $thisPage = intval($_GET["page"]);
  }
  else
    $thisPage = 1;

foreach ($labelList as $tag => $label)
  $values[] = array($tag);

$patients = $patientsXml->paciente;

foreach ($patients as $patient)
{
  $aux = $patient->children();

  foreach ($aux as $tag => $value)
  {
    foreach ($values as $a => $b)
    {
      if ($b[0] == $tag)
      {
        $found = false;

        foreach ($values[$a] as $c => $d)
        {
          if($d == $value->__toString())
          {
            $found = true;
            break;
          }
        }

        if (!$found)
        {
          if (!empty($value))
            $values[$a][] = $value->__toString();
        }

        break;
      }
    }
  }
}

$patientCount = 0;

foreach ($patientList as $patient) // para cada paciente
{
  $isFiltering = false;
  $willPrint = true;
  $partialWillPrint = true;

  foreach ($filters as $tagName => $content) // para cada tag
  {
    if (isset($_POST[$tagName])) // verificando se a tag está definida no post (se está filtrando a tag)
    {
      $isFiltering = true;
      $partialWillPrint = false;

      foreach ($_POST[$tagName] as $k => $value)
      {
        if ($value === ($patient->{$tagName})->__toString())
          $partialWillPrint = true;
      }
    }

    $willPrint = ($willPrint && $partialWillPrint);
  }

  if ($isFiltering && !$willPrint)
    continue;

  $patientCount++;
}

$numberOfPages = intval(ceil($patientCount / $maxResults));

if ($thisPage < 1)
  $thisPage = 1;

if ($thisPage > $numberOfPages)
  $thisPage = $numberOfPages;

?>

<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="">

  <title>Fichas médicas - Sistema de Pacientes</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" type="text/css" href="tables.css">
</head>

<body class="bg-light">
  <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
    <img class="ml-auto" src="brand_small.png" alt="">
    <a class="navbar-brand mr-auto" href="#">Pacientes</a>
  </nav>

  <div class="nav-scroller bg-white shadow-sm">
    <nav class="nav nav-underline">
      <a class="nav-link ml-auto" href="userPanel.php">Home</a>
      <a class="nav-link active" href="searchFiles.php">Fichas</a>
      <a class="nav-link" href="preferences.php">Opções</a>
      <a class="nav-link mr-auto" href="logout.php">Sair</a>
    </nav>
  </div>

  <!-- Filters' Modal -->
  <div class="modal fade" id="modalFilters" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Alterar filtros</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="filtersForm" class="w-100" action="changePreferences.php" method="POST">
            <input type="hidden" name="redirectTo" value="<?=basename($_SERVER['REQUEST_URI'])?>">
            <div class="w-100">
              <p>Selecione os possíveis filtros:</p>
              <div style="overflow: auto; max-height: 320px;" class="w-100 mb-3 border">
                <table class="table table-sm table-striped table-hover mb-3">
                  <thead>
                    <tr>
                      <th>Atributo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php

                    $filterName = "";
                    $filters = $configXml->filters->children();

                    foreach ($filters as $tag => $value) {
                      $filterName = "filter" . $tag;
                      $$filterName = true;
                    }

                    foreach ($labelList as $tag => $label)
                    {
                      echo "<tr>";
                      echo "<td>";

                      echo "<input class=\"m-1\" type=\"checkbox\" value=\"$tag\" name=\"filters[]\"";

                      $filter = "filter" . $tag;

                      if (isset($$filter))
                        echo "checked=\"checked\">";
                      else
                        echo ">";

                      echo "$tag";

                      echo "</td>";

                      echo "</tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <input id="submitButtonFilters" type="button" class="btn btn-primary" value="Salvar">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Columns' Modal -->
  <div class="modal fade" id="essentialsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Adicionar colunas</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="essentialsForm" class="w-100" action="changePreferences.php" method="POST">
            <input type="hidden" name="redirectTo" value="<?=basename($_SERVER['REQUEST_URI'])?>">
            <div class="w-100">
              <p>Selecione os atributos essenciais para identificação do paciente:</p>
              <?php
              if (isset($configXml->essentials))
              {
                $essentials = $configXml->essentials->children();

                foreach ($essentials as $tag => $value)
                {
                  $$tag = true;
                }
              }
              ?>
              <table class="table table-sm table-striped table-hover border">
                <tr>
                  <td class="form-check">
                    <input class="form-check-input ml-1" type="checkbox" value="numeroPaciente" name="essentials[]" <?=isset($numeroPaciente) ? "checked=\"checked\"" : ""?>>
                    <label class="form-check-label ml-4" for="numeroPaciente">
                      numeroPaciente
                    </label>
                  </td>
                </tr>
                <tr>
                  <td class="form-check">
                    <input class="form-check-input ml-1" type="checkbox" value="txt_anoNascimento" name="essentials[]" <?=isset($txt_anoNascimento) ? "checked=\"checked\"" : ""?>>
                    <label class="form-check-label ml-4" for="txt_anoNascimento">
                      txt_anoNascimento
                    </label>
                  </td>
                </tr>
                <tr>
                  <td class="form-check">
                    <input class="form-check-input ml-1" type="checkbox" value="sexo" name="essentials[]" <?=isset($sexo) ? "checked=\"checked\"" : ""?>>
                    <label class="form-check-label ml-4" for="sexo">
                      sexo
                    </label>
                  </td>
                </tr>
                <tr>
                  <td class="form-check">
                    <input class="form-check-input ml-1" type="checkbox" value="estadoEndereco" name="essentials[]" <?=isset($estadoEndereco) ? "checked=\"checked\"" : ""?>>
                    <label class="form-check-label ml-4" for="estadoEndereco">
                      estadoEndereco
                    </label>
                  </td>
                </tr>
                <tr>
                  <td class="form-check">
                    <input class="form-check-input ml-1" type="checkbox" value="txt_anoUltima" name="essentials[]" <?=isset($txt_anoUltima) ? "checked=\"checked\"" : ""?>>
                    <label class="form-check-label ml-4" for="txt_anoUltima">
                      txt_anoUltima
                    </label>
                  </td>
                </tr>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <input id="submitButtonEssentials" type="button" class="btn btn-primary" value="Enviar">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <main role="main" class="container w-75">
    <div class="d-flex align-items-center p-3 ml-3 my-3 w-100 text-white-50 bg-blue rounded shadow-sm">
      <div class="lh-100">
        <h6 class="mb-0 text-white lh-100">Buscar fichas</h6>
      </div>
    </div>

    <div class="d-flex align-items-center p-3 ml-3 my-3 text-black bg-white rounded shadow-sm container">
      <div class="row lh-100 w-100 m-0">
        <div class="col-md-3 p-0">
          <div class="card p-0">
            <form name="filterResults" class="w-100" method="POST">
              <?php
              foreach ($values as $i => $array)
              {
                $foundFilter = false;

                foreach ($filters as $tagName => $value)
                  if ($tagName === $array[0])
                    $foundFilter = true;

                  if (!$foundFilter)
                    continue;

                  echo "<article class=\"card-group-item\">";
                  echo "<header class=\"card-header\">";

                  $found = false;
                  $userDefinedLabel = "";

                  foreach ($labelList as $tagName => $value)
                  {
                    if ($tagName === $array[0])
                    {
                      $found = true;
                      $userDefinedLabel = $value;
                      break;
                    }
                  }

                  if ($found)
                    echo "<h6 class=\"title\">$userDefinedLabel</h6>";
                  else
                    echo "<h6 class=\"title\">$array[0]</h6>";

                  echo "</header>";
                  echo "<div class=\"filter-content\">";

                  echo "<div class=\"card-body\" style=\"overflow: auto; max-height: 152px;\">";

                  foreach ($array as $j => $value)
                  {
                    if ($j === 0)
                      continue;

                    $checked = "";

                    if (isset($_POST[$array[0]][$j-1]))
                      $checked = "checked=\"checked\"";

                    echo "<label class=\"form-check\">";
                    echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"$array[0][]\" value=\"$array[$j]\" " . $checked . ">";
                    echo "<span class=\"form-check-label\">$value</span>";
                    echo "</label>"; 
                  }

                  echo "</div>";
                  echo "</div>";
                  echo "</article>";
                }

                echo "<article class=\"card-group-item\">";
                echo "<header class=\"card-header\">";
                echo "<h6 class=\"title\">";
                echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#modalFilters\">+ Mais filtros</a>";
                echo "</h6>";
                echo "</header>";
                echo "</article>";
                ?>

                <div class="m-1">
                  <input id="submitFilters" type="submit" class="btn btn-primary w-100" value="Filtrar">
                </div>
              </form>
            </div>
          </div>

          <div class="col-md-9">
            <div id="tableArea" class="w-100 text-black">
              <table id="tableResults" class="table dataTable table-sm table-striped table-hover w-100">
                <thead>
                  <tr>
                    <th scope="col">#</th>

                    <?php
                    $i = 1;

                    foreach ($infoList as $info => $value)
                    {
                      $found = false;
                      $userDefinedLabel = "";

                      foreach ($labelList as $label => $labelValue)
                      {
                        if ($info == $label)
                        {
                          $found = true;
                          $userDefinedLabel = $labelValue;
                        }
                      }

                      if ($found)
                        print("<th scope=\"col\">$userDefinedLabel</th>");
                      else
                        print("<th scope=\"col\">$info</td>");
                    }
                    ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 0;

                foreach ($patientList as $patient) // para cada paciente
                {
                  $isFiltering = false;
                  $willPrint = true;
                  $partialWillPrint = true;

                  foreach ($filters as $tagName => $content) // para cada tag
                  {
                    if (isset($_POST[$tagName])) // verificando se a tag está definida no post (se está filtrando a tag)
                    {
                      $isFiltering = true;
                      $partialWillPrint = false;

                      foreach ($_POST[$tagName] as $k => $value)
                      {
                        if ($value === ($patient->{$tagName})->__toString())
                          $partialWillPrint = true;
                      }
                    }

                    $willPrint = ($willPrint && $partialWillPrint);
                  }

                  $i++;

                  if ($isFiltering && !$willPrint)
                    continue;

                  if ($patient->numeroPaciente->__toString() === "")
                    print("<tr class=\"table-danger\" onclick=\"alert('Número de paciente inválido!');\">");
                  else
                    print("<tr onclick=\"window.location='detailedFile.php?id=" . $i . "'\">");

                  print("<td>$i</td>");

                  foreach ($infoList as $tag => $value)
                  {
                    print("<td>" . $patient->$tag . "</td>");
                  }
                  
                  print("</tr>");
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script type="text/javascript" src="preferencesCheck.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script src="dataTables.js" type="text/javascript"></script>

<script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function() {
    DataTable(document.querySelector("#tableResults"));
  });
</script>

</body>
</html>