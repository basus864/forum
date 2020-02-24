<html>
  <head>
    <meta charset="UTF-8">
    <title>Diskuzní fórum</title>
  </head>
    <body>
    
<?php
function validateEmail($e){
  $com = 0;
  $e = Str_Replace(
  Array("á","č","ď","é","ě","í","ľ","ň","ó","ř","š","ť","ú","ů","ý","ž","Á","Č","Ď","É","Ě","Í","Ľ","Ň","Ó","Ř","Š","Ť","Ú","Ů","Ý","Ž") ,
  Array("a","c","d","e","e","i","l","n","o","r","s","t","u","u","y","z","A","C","D","E","E","I","L","N","O","R","S","T","U","U","Y","Z") ,
$e);
  $pattern='/^[_a-zA-Z0-9\.\-\=\^\-]+@[_a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,4}$/';
  if(preg_match($pattern, $e)){
    $com = 1;
  } 
  else
    echo 'Spatny mail';
  return $com;
}

  $name = "";
  $email = "";
  $text = "";
  ?>
  <h1>Diskuzní fórum</h1>
      <form method="POST">
          <table border="0"><tr><th>Jméno:</th>
              <tr><td><input type="text" name="name" value="<?php echo $name ?>" size="50"/></td></tr>
              <tr><th>Email:</th>
              <tr><td><input type="text" name="email" value="<?php echo $email ?>" size="50"/></td></tr>
              <tr><th>Text zprávy:</th>
              <tr><td><input type="text" name="text" value="<?php echo $text ?>" size="50"/></td>
             
              </tr>    
          </table>                
          <input type="submit" value="Odeslat"/>
        <input type="HIDDEN" NAME="Sent" VALUE="true"/>  
      </form>
 <?php 
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Pripojeni k datazi
  $mysqli = new mysqli("127.0.0.1", "root", "", "databaze");
  $err = 0; // 0 = chyba, 1 = OK
  if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
  }
  
  if(isset($_POST['Sent'])): 
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $email = strtolower($email);
    $text = htmlspecialchars(trim($_POST['text']));
    
    if($name=="" || $email=="" || $email!="" || $text==""): 
      echo '<b>';
      if($email!=''):                
        $err = validateEmail($email); // zadat formular znovu a koncit else
      endif; 
      if($name==""):
        echo "Nezadali jste Jmeno!<br>";
       $err = 0;
      endif;
      if($email==""):
         echo "Nezadali jste email!<br>";       
        $err = 0;
       endif;
      if($text==""):
        echo "Nezadali jste text zpravy!<br>";                       
        $err = 0;
      endif;
 
      echo '</b>';
    endif;
  endif;

/* 
 * Vytvoreni tabulky v databazi
CREATE DATABASE `databaze` CHARACTER SET utf8 COLLATE utf8_czech_ci;
        
CREATE TABLE forum (
  forum_id int AUTO_INCREMENT,
  name varchar(50),
  email varchar(254),
  text text,
  date datetime,  
  PRIMARY KEY (forum_id)
);                
*/   
  
// Zapis do databaze pri spravnem vstupu dat
  if($err == 1){ 
    $date = date('Y-m-d H:i:s');
    $record = $mysqli->query("INSERT INTO forum(name, email, text, date) VALUES('$name', '$email', '$text', '$date')");  
  }

// Zpracovani dat z databaze pro vypis celeho fora
  $forum = $mysqli->query("SELECT * FROM forum ORDER BY date"); 
  $ii=0;
  echo('<h2>Příspěvky</h2>'); 
  foreach ($forum as $u)
  {     
    $records[0][$u['forum_id']]=$u['name'];
    $records[1][$u['forum_id']]=$u['email'];
    $records[2][$u['forum_id']]=$u['text'];
    $records[3][$u['forum_id']]=$u['date'];
    $ii++;
  }      
      
  for($i=$ii;$i>=1;$i--){
      echo '<table border="1">';
    $sum=0;
    $e = $records[1][$i];    
      for($j=$ii;$j>=1;$j--){     
        if(strcmp($e,$records[1][$j])===0){                     
        $sum++;           
        }
      }    
    echo('<tr><td width="250">Jméno: ' . htmlspecialchars($records[0][$i]));
    echo('</td><td width="250">Email: ' . htmlspecialchars($records[1][$i]));
    echo('</td><td width="250">Vloženo: ' . htmlspecialchars($records[3][$i]));
    echo('</td><td width="250">Počet příspěcků: ' . $sum . '</td>');
    echo('<tr><td colspan="4" width="1000">' . htmlspecialchars($records[2][$i]));        
    echo('</td></tr>');
    echo('</table>');
    echo "<br>";
  }  

  $forum->close();
  $mysqli->close();
    ?>  
    </body>         
</html>