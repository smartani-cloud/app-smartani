function validateDate(idInputDate,idError)
{
  // Customized from https://www.w3resource.com/javascript/form/javascript-date-validation.php

  var inputDate = $("#"+idInputDate);
  var inputDay = $("#day").val();
  var inputMonth = $("#month").val();
  var inputYear = $("#year").val();

  var dd = parseInt(inputDay);
  var mm = parseInt(inputMonth);
  var yy = parseInt(inputYear);

  // Create list of days of a month [assume there is no leap year by default]
  var ListofDays = [31,28,31,30,31,30,31,31,30,31,30,31];
  if(mm == 1 || mm > 2)
  {
    if(dd > ListofDays[mm-1])
    {
      $("#day").focus();
      if($("#"+idError).length < 1)
      {
        inputDate.append('<span id="'+idError+'" class="text-danger">Format tanggal tidak valid.</span>');
      }
      return false;
    }
  }
  if(mm==2)
  {
    var lyear = false;
    if((!(yy % 4) && yy % 100) || !(yy % 400)) 
    {
      lyear = true;
    }
    if((lyear==false) && (dd>=29))
    {
      $("#day").focus();
      if($("#"+idError).length < 1)
      {
        inputDate.append('<span id="'+idError+'" class="text-danger">Format tanggal tidak valid.</span>');
      }
      return false;
    }
    if((lyear==true) && (dd>29))
    {
      $("#day").focus();
      if($("#"+idError).length < 1)
      {
        inputDate.append('<span id="'+idError+'" class="text-danger">Format tanggal tidak valid.</span>');
      }
      return false;
    }
  }
}