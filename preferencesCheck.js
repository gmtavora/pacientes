document.addEventListener("DOMContentLoaded", function () {
  let submitChangeLabels = document.getElementById('submitButton');
  let submitButtonEssentials = document.getElementById('submitButtonEssentials');
  let submitButtonFilters = document.getElementById('submitButtonFilters');

  if (submitButtonEssentials)
  {
    submitButtonEssentials.onclick = function () {
      let essentialsCheckboxes = document.getElementsByName('essentials[]');
      let anyEssential = false;

      essentialsCheckboxes.forEach(function (element) {
        if (element.checked)
          anyEssential = true;
      })

      if (essentialsCheckboxes.length === 0)
        anyEssential = true;

      if (anyEssential)
        essentialsForm.submit();
      else
        alert("É necessário escolher pelo menos um atributo!");
    }
  }

  if (submitButtonFilters)
  {
    submitButtonFilters.onclick = function () {
      let filtersCheckboxes = document.getElementsByName('filters[]');
      let anyFilter = false;

      filtersCheckboxes.forEach(function (element) {
        if (element.checked)
          anyFilter = true;
      })

      if (filtersCheckboxes.length === 0)
        anyFilter = true;

      if (anyFilter)
        filtersForm.submit();
      else
        alert("É necessário escolher pelo menos um atributo para filtro!");
    }
  }

  if (submitChangeLabels)
  {
    submitChangeLabels.onclick = function () {
      let inputText = document.querySelectorAll("input[type=text]");
      let anyBlank = false;

      inputText.forEach(function (element) {
        if (element.value === "")
        {
          anyBlank = true;
          alert("Atributo " + element.name + " está em branco.");
        }
      });

      if (!anyBlank)
        preferences.submit();
    }
  }
});