// Maian Recipe v2.0
// Javascript/Ajax functions - Add Comments
// Written by David Ian Bennett
// http://www.maianscriptworld.co.uk

var xmlHttp = createXmlHttpRequestObject();

function createXmlHttpRequestObject() {
  var xmlHttp;
  if (window.ActiveXObject) {
    try {
      xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    catch(e) {
      xmlHttp = false;
    }
  } else {
    try {
      xmlHttp = new XMLHttpRequest();
    }
    catch(e) {
      var xmlHttpVersions = new Array('MSXML2.XMLHTTP.6.0',
                                      'MSXML2.XMLHTTP.5.0',
                                      'MSXML2.XMLHTTP.4.0',
                                      'MSXML2.XMLHTTP.3.0',
                                      'MSXML2.XMLHTTP',
                                      'Microsoft.XMLHTTP');
      for (var i=0; i<xmlHttpVersions.length && !xmlHttp; i++) {
        try {
          xmlHttp = new ActiveXObject(xmlHttpVersions[i]);
        }
        catch (e) {}
      }                                
      xmlHttp = false;
    }
  }

  if (!xmlHttp) {
    alert('Error Creating XMLHttpRequest Object');
  } else {
    return xmlHttp;
  }
}

function addRecipeComment(id,txt,code,isibox) {
  if (xmlHttp.readyState==4 || xmlHttp.readyState==0) {
    try {
      message = '';
      mrname  = encodeURIComponent(document.getElementById("name").value);
      mremail = encodeURIComponent(document.getElementById("ctct").value);
      mrcomm  = encodeURIComponent(document.getElementById("comments").value);
      if (code=='yes') {
        mrcode  = encodeURIComponent(document.getElementById("code").value);
      }
      if (mrname=='' || mremail=='' || mrcomm=='') {
        message ='- '+txt+'\n';
      } else {
        if (code=='yes' && mrcode=='') {
          message ='- '+txt+'\n';
        }
      }
      if (message) {
        // See note below about ibox var..
        if (isibox=='yes') {
        }
        alert(message);
        return false;
      } else {
        xmlHttp.open("POST","index.php",true);
        xmlHttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
        xmlHttp.onreadystatechange = handleServerResponse;
        if (code=='yes') {
          xmlHttp.send('r='+id+'&name='+mrname+'&ctct='+mremail+'&comments='+mrcomm+'&code='+mrcode);
        } else {
          xmlHttp.send('r='+id+'&name='+mrname+'&ctct='+mremail+'&comments='+mrcomm);
        }
      }
    }
    catch (e) {
      alert("Can`t connect to server:"+e.toString());
    }
  } else {
    setTimeout('addRecipeComment(id,txt,code)',1000);
  }
}

function xmlResponseHeaders(msg,xml) {
  /*
    The isibox var evaluates to yes or no depending on your setting
    in the 'control/defined.inc.php' file
    
    define ('USE_IBOX_FOR_COMMENTS', 1);
    
    If you prefer to use the ibox for all operations including
    displaying errors, simply adjust the if statement(s) below.
    
    Example:
    if (isibox=='yes') {
      iBox.showURL(url, text, params);
    } else {
      alert(msg);
    }
    
    See the case 'ok' statement..
  */  
  error    = xml.getElementsByTagName('field')[0].firstChild.data;
  isibox   = xml.getElementsByTagName('ibox')[0].firstChild.data;
  msg      = xml.getElementsByTagName('message')[0].firstChild.data;
  capCode  = xml.getElementsByTagName('captcha')[0].firstChild.data;
  switch (error) {
    case 'all-fields':
    case 'invalid-email':
    if (isibox=='yes') {
    }
    alert(msg);
    break;
    case 'invalid-word':
    if (isibox=='yes') {
    }
    document.getElementById('image').src = 'index.php?p=comment-captcha&sid='+Math.random();
    document.getElementById('code').value = '';
    alert(msg);
    break;
    case 'ok':
    if (isibox=='yes') {
      iBox.showURL('#msgCommentsAdded', '','');
    } else {
      alert(msg);
    }
    // Clear form boxes..
    document.getElementById('name').value = '';
    document.getElementById('ctct').value = '';
    document.getElementById('comments').value = '';
    if (capCode=='yes') {
      document.getElementById('image').src  = 'index.php?p=comment-captcha&sid='+Math.random();
      document.getElementById('code').value = '';
    }
    break;
  }
  return false;
}

function handleServerResponse() {
  if (xmlHttp.readyState==4) {
    if (xmlHttp.status==200) {
      try {
        var xmlResponse  = xmlHttp.responseXML;
        var rootNodeName = xmlResponse.documentElement.nodeName;
        if (!xmlResponse || !xmlResponse.documentElement) {
          throw("Invalid XML Structure:\n"+xmlHttp.responseText);
        } else if (rootNodeName=='parsererror') {
          throw("Invalid XML Structure:\n"+xmlHttp.responseText);
        } else {
          xmlDoc      = xmlResponse.documentElement;
          msg         = xmlDoc.getElementsByTagName('message')[0].firstChild.data;
          xmlResponseHeaders(msg,xmlDoc);
        }
      }
      catch(e) {
        alert('Error reading response: '+e.toString());
      }
    } else {
      alert('There was a problem accessing the server:'+xmlHttp.statusText);
    }
  }
}
