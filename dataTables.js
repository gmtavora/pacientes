/* Tabelas de dados
 * criado por Gabriel Távora
 * folha de estilos de datatables.net
 *
 * Modo de usar: crie a tabela dentro de um container em um row, e chame a função. O argumento deve ser a tabela
 *
 */

function PrintRows(table, rows, first, last)
{
  for(let i = first; i < last; i++)
    if (rows[i])
      table.children[1].appendChild(rows[i]);
}

function ClearTable(table, rows)
{
  table.querySelector("tbody").querySelectorAll("tr").forEach(function (element) {
    if (Array.isArray(rows))
      rows.push(element);
    element.remove();
  });
}

function UpdatePagination (thisPage, numberOfPages, nodeNav, table, rows, maxNumberOfRows)
{
  numberOfPages = Math.ceil(rows.length / maxNumberOfRows);

  while (nodeNav.firstChild)
    nodeNav.removeChild(nodeNav.firstChild);

  let nodeUl = document.createElement("ul");
  nodeUl.classList.add("pagination", "justify-content-end");
  let nodeLi = document.createElement("li");
  nodeLi.classList.add("page-item");

  if (thisPage === 1)
    nodeLi.classList.add("disabled");

  let a = document.createElement("a");
  a.classList.add("page-link");
  let span = document.createElement("span");
  span.setAttribute("aria-hidden", "true");
  span.textContent = '\u00AB';

  a.onclick = function () {
    if (thisPage !== 1)
    {
      ClearTable(table);
      PrintRows(table, rows, (thisPage-1)*maxNumberOfRows, thisPage*maxNumberOfRows);
      thisPage -= 1;
      UpdatePagination(thisPage, numberOfPages, nodeNav, table, rows, maxNumberOfRows);
      document.querySelector("#textNavigation").textContent = "Mostrando " + ((thisPage-1)*maxNumberOfRows+1) + " a " + (thisPage)*maxNumberOfRows + " de " + rows.length + " entradas";
    }
  };

  a.appendChild(span);
  nodeLi.appendChild(a);
  nodeUl.appendChild(nodeLi);
  nodeNav.appendChild(nodeUl);

  for (let i = 1; i <= numberOfPages; i++)
  {
    nodeLi = document.createElement("li");
    nodeLi.classList.add("page-item");

    if (i === thisPage)
      nodeLi.classList.add("active");

    a = document.createElement("a");
    a.classList.add("page-link");
    span = document.createElement("span");
    span.setAttribute("aria-hidden", "true");
    span.textContent = i;

    a.onclick = function () {
      if (thisPage != i)
      {
        ClearTable(table);
        PrintRows(table, rows, (i-1)*maxNumberOfRows, i*maxNumberOfRows);
        thisPage = i;
        UpdatePagination(thisPage, numberOfPages, nodeNav, table, rows, maxNumberOfRows);
        document.querySelector("#textNavigation").textContent = "Mostrando " + ((thisPage-1)*maxNumberOfRows+1) + " a " + (((thisPage)*maxNumberOfRows < rows.length) ? (thisPage)*maxNumberOfRows : rows.length) + " de " + rows.length + " entradas";
      }
    };

    a.appendChild(span);
    nodeLi.appendChild(a);
    nodeUl.appendChild(nodeLi);
  }

  nodeLi = document.createElement("li");
  nodeLi.classList.add("page-item");

  if (thisPage === numberOfPages)
    nodeLi.classList.add("disabled");

  a = document.createElement("a");
  a.classList.add("page-link");
  span = document.createElement("span");
  span.setAttribute("aria-hidden", "true");
  span.textContent = '\u00BB';

  a.onclick = function () {
    if (thisPage !== numberOfPages)
    {
      ClearTable(table);
      PrintRows(table, rows, thisPage*maxNumberOfRows, (thisPage+1)*maxNumberOfRows);
      thisPage += 1;
      UpdatePagination(thisPage, numberOfPages, nodeNav, table, rows, maxNumberOfRows);
      document.querySelector("#textNavigation").textContent = "Mostrando " + ((thisPage-1)*maxNumberOfRows+1) + " a " + (((thisPage)*maxNumberOfRows < rows.length) ? (thisPage)*maxNumberOfRows : rows.length) + " de " + rows.length + " entradas";
    }
  };

  a.appendChild(span);
  nodeLi.appendChild(a);
  nodeUl.appendChild(nodeLi);
}

function DataTable(table)
{
  /* Paginação */
  let numberOfRows = table.querySelectorAll('tr');
  let maxNumberOfRows = 10;
  let thisPage = 1;
  let numberOfPages = 1;

  /* Criação de nós */
  let nodeMaxRows = document.createElement('div');
  let nodeChangeColumns = document.createElement('div');
  let nodeParent = table.parentNode.parentNode;
  let nodeFooter = document.createElement('div');
  let nodeEntries = document.createElement('div');
  let nodePagination = document.createElement('div');
  let nodeFirstRow = document.createElement('div');
  let nodeLastRow = document.createElement('div');
  let nodeInputMaxRows = document.createElement('input');

  nodeFirstRow.classList.add("row");
  nodeLastRow.classList.add("row");

  /* Pré-tabela */
  nodeMaxRows.classList.add("col-sm-6", "d-flex", "align-items-center");
  nodeFirstRow.appendChild(nodeMaxRows);
  nodeMaxRows.appendChild(nodeInputMaxRows);
  nodeInputMaxRows.type = "text";
  nodeInputMaxRows.id = "entriesPerPage";
  nodeInputMaxRows.classList.add("form-control", "form-control-sm", "mt-2", "mb-2", "col-2");
  nodeInputMaxRows.value = "10";

  let auxNode = document.createElement('small');
  auxNode.classList.add("mr-2");
  auxNode.textContent = "Exibir";
  nodeMaxRows.appendChild(auxNode);

  nodeMaxRows.appendChild(nodeInputMaxRows);

  auxNode = document.createElement('small');
  auxNode.classList.add("ml-2");
  auxNode.textContent = "registros por página";

  nodeMaxRows.appendChild(auxNode);

  nodeChangeColumns.classList.add("col-sm-6", "d-flex", "align-items-center", "justify-content-end");
  auxNode = document.createElement('button');
  auxNode.type = "button";
  auxNode.setAttribute("data-toggle", "modal");
  auxNode.setAttribute("data-target", "#essentialsModal");
  auxNode.textContent = "Adicionar/remover colunas";
  auxNode.classList.add("btn", "btn-sm", "btn-primary");
  // auxNode = document.createElement('small');
  // let auxNode2 = document.createElement('a');
  // auxNode2.href = "essentials.php";
  // auxNode2.textContent = "Adicionar/remover colunas";
  // auxNode.appendChild(auxNode2);
  nodeChangeColumns.appendChild(auxNode);
  nodeFirstRow.appendChild(nodeChangeColumns);

  nodeParent.prepend(nodeFirstRow);

  /* Tabela */
  let rows = new Array();

  ClearTable(table, rows);
  PrintRows(table, rows, 0, maxNumberOfRows);

  /* Pós-tabela */
  nodeEntries.classList.add("col-sm-6", "mt-2");
  auxNode = document.createElement("small");
  auxNode.id = "textNavigation";
  auxNode.textContent = "Mostrando " + ((thisPage-1)*maxNumberOfRows+1) + " a " + (thisPage)*maxNumberOfRows + " de " + rows.length + " entradas";
  nodeEntries.appendChild(auxNode);
  nodeLastRow.appendChild(nodeEntries);

  nodePagination.classList.add("col-sm-6", "mt-2");
  let nodeNav = document.createElement("nav");
  nodeNav.setAttribute('aria-label', "Page navigation");
  nodePagination.appendChild(nodeNav);

  UpdatePagination(thisPage, numberOfPages, nodeNav, table, rows, maxNumberOfRows);

  document.querySelector("#entriesPerPage").onchange = function () {
    ClearTable(table);
    let aux = maxNumberOfRows;
    maxNumberOfRows = document.querySelector("#entriesPerPage").value;
    PrintRows(table, rows, (thisPage-1)*aux, maxNumberOfRows);
    UpdatePagination(thisPage, numberOfPages, nodeNav, table, rows, maxNumberOfRows);
    document.querySelector("#textNavigation").textContent = "Mostrando " + ((thisPage-1)*maxNumberOfRows+1) + " a " + (((thisPage)*maxNumberOfRows < rows.length) ? (thisPage)*maxNumberOfRows : rows.length) + " de " + rows.length + " entradas";
  };

  document.querySelector('.dataTable').querySelector('thead').querySelector('tr').querySelector('th').classList.add('sorting_asc');

  document.querySelector('.dataTable').querySelector('thead').querySelector('tr').querySelectorAll('th').forEach(function (element, index, array) {
    element.classList.add("sorting");

    element.onclick = function () {
      if (element.classList.contains("sorting_desc") || element.classList.contains("sorting"))
      {
        rows.sort(function (a, b) {
          if (Number(a.querySelectorAll("td")[index].textContent) && Number(b.querySelectorAll("td")[index].textContent))
            return Number(a.querySelectorAll("td")[index].textContent) - Number(b.querySelectorAll("td")[index].textContent);
          else
          {
            if (a.querySelectorAll("td")[index].textContent < b.querySelectorAll("td")[index].textContent)
              return -1;
            else if (a.querySelectorAll("td")[index].textContent > b.querySelectorAll("td")[index].textContent)
              return 1;
            else
              return 0;
          }
        });

        for (let i = 0; i < array.length; i++)
        {
          if (index !== i)
          {
            array[i].classList.remove("sorting_desc", "sorting_asc");
            array[i].classList.add("sorting");
          }
          else
          {
            element.classList.remove("sorting", "sorting_desc");
            element.classList.add("sorting_asc");
          }
        }
      }
      else
      {
        rows.sort(function (a, b) {
          if (Number(b.querySelectorAll("td")[index].textContent) && Number(a.querySelectorAll("td")[index].textContent))
            return Number(b.querySelectorAll("td")[index].textContent) - Number(a.querySelectorAll("td")[index].textContent);
          else
          {
            if (b.querySelectorAll("td")[index].textContent < a.querySelectorAll("td")[index].textContent)
              return -1;
            else if (b.querySelectorAll("td")[index].textContent > a.querySelectorAll("td")[index].textContent)
              return 1;
            else
              return 0;
          }
        });

        for (let i = 0; i < array.length; i++)
        {
          if (index !== i)
          {
            array[i].classList.remove("sorting_desc", "sorting_asc");
            array[i].classList.add("sorting");
          }
          else
          {
            element.classList.remove("sorting_asc");
            element.classList.add("sorting_desc");
          }
        }
      }
      ClearTable(table);
      PrintRows(table, rows, (thisPage-1)*maxNumberOfRows, maxNumberOfRows);
    }
  });

  nodeLastRow.appendChild(nodePagination);
  nodeParent.appendChild(nodeLastRow);
}