<?php
include((phpversion()>='5.0' ? 'php5/' : 'php4/').'class.phpmailer.php');

class mailClass {

var $vars = array();
var $smtp_host;
var $smtp_port;
var $smtp_user;
var $smtp_pass;
var $smtp = false;
var $html = false; // Set to true to use HTML in e-mail templates

// Cleans slashes..
function cleanData($data) {
  return (get_magic_quotes_gpc() ? stripslashes($data) : $data);
}

// Converts entities..
function convertChar($data) {
  $find    = array('&#039;','&quot;','&amp;','&lt;','&gt;');
  $replace = array('\'','"','&','<','>');

  return str_replace($find,$replace,$data);
}

// Loads tags into array..
function addTag($placeholder,$data) {
  $this->vars[$placeholder] = $data;
}

// Clears data vars..
function clearVars() {
  $this->vars = array();
}

// Converts tags..
function convertTags($data) {
  if (!empty($this->vars)) {
    foreach ($this->vars AS $tags => $value) {
      $data = str_replace($tags,$value,$data);
    }
  }

  return $data;
}

// Mail headers..
function mailHeaders($name,$email) {
  global $mail_charset;
  if ($this->html) {
    $headers  = "Content-type: text/html; charset=".$mail_charset."\r\n";
    $headers .= "From: \"".$this->injectionCleaner($name)."\" <".$email.">\n";
  } else {
    $headers  = "Content-type: text/plain; charset=".$mail_charset."\r\n";
    $headers .= "From: \"".$this->injectionCleaner($name)."\" <".$email.">\n";
  }
  
  $headers .= "X-Sender: \"".$this->injectionCleaner($name)."\" <".$email.">\n";
  $headers .= "X-Mailer: PHP\n";
  $headers .= "X-Priority: 3\n";
  $headers .= "X-Sender-IP: ".$_SERVER['REMOTE_ADDR']."\n";
  $headers .= "Date: ".date("r")."\n";
  $headers .= "Return-Path: \"".$this->injectionCleaner($name)."\" <".$email.">\n";
  $headers .= "Reply-To: \"".$this->injectionCleaner($name)."\" <".$email.">\n";

  return $headers;
}

// Cleans spam/form injection input..
function injectionCleaner($data) {
  $find = array(
   "\r",
   "\n",
   "%0a",
   "%0d",
   "content-type:",
   "Content-Type:",
   "BCC:",
   "CC:",
   "boundary=",
   "TO:",
   "bcc:",
   "to:",
   "cc:"
   );
  $replace = array();
  return str_replace($find,$replace,$data);
}

// Loads e-mail template..
function template($file) {
  if (!function_exists('file_get_contents')) {
    echo '<b>Error!! PHPv4.3 or higher is required for processing to function correctly!</b><br><br>';
    echo 'Your version is: v'.phpversion();
    exit;
  }
  $email_string = trim(file_get_contents($file));
  return ($email_string ? trim($this->convertTags($email_string)) : die('An error occured opening the <b>'.$file.'</b> file. Check that this file exists in the correct "templates/email/" folder!'));
}

// Sends mail..
// If you are testing the script on localhost and you don`t have mail 
//  capabilities, set the $email var to false to disable mail sending.
function sendMail($to_name,$to_email,$from_name,
                  $from_email,$subject,$msg,$email=true) {
  if ($email) {
    // Additional stripping of too many chars..ie: spammers
    $to_name     = substr($to_name,0,250);
    $to_email    = substr($to_email,0,250);
    $from_name   = substr($from_name,0,250);
    $from_email  = substr($from_email,0,250);
    if ($this->smtp=='yes') {
      $MAILER = new PHPMailer();
      $MAILER->IsSMTP();
      $MAILER->IsHTML($this->html);
      $MAILER->Port       = $this->smtp_port;
      $MAILER->Host       = $this->smtp_host;
      $MAILER->SMTPAuth   = ($this->smtp_user && $this->smtp_pass ? true : false);
      $MAILER->Username   = $this->smtp_user;
      $MAILER->Password   = $this->smtp_pass;
      $MAILER->From       = $from_email;
      $MAILER->FromName   = $this->convertChar($this->cleanData($this->injectionCleaner($from_name)));
      $MAILER->AddAddress($to_email, $this->convertChar($this->cleanData($this->injectionCleaner($to_name))));
      $MAILER->WordWrap   = 1000;
      $MAILER->Subject    = $this->convertChar($this->cleanData($subject));
      $MAILER->Body       = $this->convertChar($this->cleanData($msg));
      $MAILER->Send();
    } else {
      mail($to_email, 
           $this->convertChar($this->cleanData($subject)), 
           $this->convertChar($this->cleanData($msg)), 
           $this->mailHeaders($this->cleanData($this->convertChar($from_name)),$from_email)
           );
    }
  }
}

}

?>
