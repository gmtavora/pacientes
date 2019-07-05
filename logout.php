<?php
  session_start();
  session_destroy();

  header("refresh:3;url=index.php");
  header('Content-Type: text/html; charset=iso-8859-1');
 ?>

 <!doctype html>
<html lang="pt">
  <head>
    <meta charset="ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="">

    <title>Redirecionando...</title>

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
        <a class="nav-link ml-auto disabled" href="#">Home</a>
        <a class="nav-link disabled" href="#">Funcion�rios</a>
        <a class="nav-link disabled" href="#">Fichas</a>
        <a class="nav-link disabled" href="#">Ajuda</a>
        <a class="nav-link disabled" href="#">Op��es</a>
        <a class="nav-link mr-auto disabled" href="#">Sair</a>
      </nav>
    </div>

    <main role="main" class="container">
      <div class="d-flex align-items-center p-3 ml-3 my-3 text-white-50 bg-blue rounded shadow-sm">
        <div class="lh-100">
          <h6 class="mb-0 text-white lh-100">Logout</h6>
        </div>
      </div>

      <div class="d-flex align-items-center p-3 ml-3 my-3 text-white-50 bg-white rounded shadow-sm">
        <div class="text-black">
          <small>
            Sa�da realizada. Voc� est� sendo redirecionado...
          </small>
        </div>
      </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  </body>
</html>