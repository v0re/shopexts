 function checkAllEntities() {
    var allCheckBox = document.getElementById("allkeys");
    var check = allCheckBox.checked;
    for (var i = 1; i <= 9; i++) {
      var box = document.getElementById("key" + i);
      if (box)
        box.checked = check;
    }
    updateDeleteButtonAndCheckbox();
  }

  function updateDeleteButtonAndCheckbox() {
    var button = document.getElementById("delete_button");
    var uncheck = false;
    var disable = true;
    for (var i = 1; i <= 9; i++) {
      var box = document.getElementById("key" + i);
      if (box) {
        if (box.checked) {
          disable = false;
        } else {
          uncheck = true;
        }
      }
    }
    button.disabled = disable;
    if (uncheck)
      document.getElementById("allkeys").checked = false;
  }
  
