// Maian Recipe v2.0
// Javascript functions
// Written by David Ian Bennett
// http://www.maianscriptworld.co.uk

// Confirm message..
function confirmMessage(txt) {
  var confirmSub = confirm(txt);
  if (confirmSub) { 
    return true;
  } else {
    return false;
  }
}

// Close div..
function closeThisDiv(div) {
  var e = document.getElementById(div);
  e.style.display = 'none';
}

// For reset options..
function resetOptions(txt) {
  switch(txt) {
    case 'hits': 
    case 'ratings': 
    case 'delcom': 
      document.getElementById('delrec').checked = false; 
    break;
    case 'delrec': 
      document.getElementById('hits').checked = false; 
      document.getElementById('ratings').checked = false; 
      document.getElementById('delcom').checked = false; 
    break;
  }
}

// Check/uncheck array of checkboxes..
function selectAll(form) {
  for (var i=0;i<document.forms['form'].elements.length;i++) {
    var e = document.forms['form'].elements[i];
    if ((e.name != 'log') && (e.type=='checkbox')) {
      e.checked = document.forms['form'].log.checked;
    }
  }
}

// Toggles divs..
function toggle_box(id) {
  var e = document.getElementById(id);
  if(e.style.display == 'none') {
    e.style.display = 'block';
  } else {
    e.style.display = 'none';
  }
}

// Form validation..reset/clear/delete recipes option..
function checkform(form,txt,txt2,txt3,txt4) {
  var message = '';
  if (form.elements['cats[]'].value=='' || form.elements['cats[]'].value==null) {
    message = '- '+txt+'..\n';
  }
  if (document.getElementById('hits').checked == false &&
      document.getElementById('ratings').checked == false &&
      document.getElementById('delcom').checked == false &&
      document.getElementById('delrec').checked == false
    ) {
    message +='- '+txt2+'..\n';
  }
  if (message) {
    alert(txt3+'\n\n'+message);
    return false;
  } else {
    return confirmMessage(txt4);
  }
}

// Form validation..delete selected recipe option..
function checkform2(form,txt,txt2,txt3) {
  var message = '';
  var cbox    = false;
  var count   = 0;
  for (var i=0; i<form.elements['recipe[]'].length; i++) {
    var current = form.elements['recipe[]'][i];
    if (current.type == 'checkbox' && current.checked) {
      count++;
    }
  }
  if (count==0) {
    message = '- '+txt+'..\n';
  }
  if (message) {
    alert(txt3+'\n\n'+message);
    return false;
  } else {
    return confirmMessage(txt2);
  }
}

// Form validation..approve comment option..
function checkform3(form,txt,txt2,txt3) {
  var message = '';
  var cbox    = false;
  var count   = 0;
  for (var i=0; i<form.elements['comment[]'].length; i++) {
    var current = form.elements['comment[]'][i];
    if (current.type == 'checkbox' && current.checked) {
      count++;
    }
  }
  if (count==0) {
    message = '- '+txt+'..\n';
  }
  if (message) {
    alert(txt3+'\n\n'+message);
    return false;
  } else {
    return confirmMessage(txt2);
  }
}

// Shows all recipe info divs or hides them..
function infoBlock(which,count) {
  for (var i=0; i<count; i++) {
    var e = document.getElementById('recipe'+i);
    if (e!=null && e!='undefined') {
      if (which=='show') {
        e.style.display = 'block';
      } else {
        e.style.display = 'none';
      }
    }
  }
}
