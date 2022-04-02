

<?php

function utf8_substr_replace($original, $replacement, $position, $length)
{
    $startString = mb_substr($original, 0, $position, "UTF-8");
    $endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");

    $out = $startString . $replacement . $endString;

    return $out;
}

function mb_strtolower_Turkish ($string) {

  while(gettype(mb_strpos($string, 'İ')) != "boolean") {
    $big_i_pos = mb_strpos($string, 'İ');
    $string = utf8_substr_replace($string, 'i', $big_i_pos, 1);
  }

   while(gettype(mb_strpos($string, 'I')) != "boolean") {
    $I_pos = mb_strpos($string, 'I');
    $string = utf8_substr_replace($string, 'ı', $I_pos, 1);
  }

  return mb_strtolower($string, 'UTF-8');
}

function downCase($string, $lang_id) {
  if($lang_id == 7) {
       return mb_strtolower_Turkish($string);
  }
  else return mb_strtolower($string, 'UTF-8');

}


$new_text = '';
if(isset($_POST['new_text'])) {
  $new_text = $_POST['new_text'];

}

//$new_text = addslashes($new_text);


$text_title = '';
if(isset($_POST['text_title'])) {
  $text_title = $_POST['text_title'];

}

$lang_id = '';
if(isset($_POST['langselect'])) {
  $lang_id = $_POST['langselect'];

}


include 'db_details_web.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$dt_start = 0;

$sql = "SET NAMES UTF8";
$res = $conn->query($sql);

$sql = "SELECT MAX(tokno) AS dt_start FROM display_text";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$dt_start = $row["dt_start"];
if(is_null($dt_start)) {
  $dt_start = 0;
}
$dt_start++;

$chunk = '';
$ch_start = $dt_start;

$ch_end = 0;
$ch_length = 0;
$word = strtok($new_text, " ");

$dt_counter = 0;

$regexp = "/[-'$%+=~#@><}{_!”“„?\n\r\t,.&^«»:;–\"\[)\](]/u"; //the 'u' modifier is needed to force UTF-8 encoding and prevent multibyte fuckery where cyrillic characters can consist partly of the hex-value of characters in the regex

while($word != false) {
  
  
       //checks the token for punctuation or linebreaks, puts the characters in the first row of multi-dimensional array $arr_punct; returns 'false' if the word is clean
  if( preg_match_all($regexp, $word, $arr_punct) ) { 

    $arr =  preg_split($regexp, $word); //returns an array of the non-punctuation components (words) of the token
    $arr_size = count($arr);
    $arr_size_minus_1 = $arr_size - 1;

    $line_break = 1; //$line_break refers to the nature of the space immediately preceding the word, so at the start of the token (which was defined as space-separated in the first place) there is always a preceding space, hence = 1
    
    for($c = 0; $c < $arr_size_minus_1; $c++) {  //we have to start $c at 0 because it's needed as an array index
      
      $text_word = $arr[$c];
      $punct = $arr_punct[0][$c]; //other rows of this 2d array are used for some intricacies of regexes which idgaf about
      
      //$text_word could be nothing if the punctuation is at the beginning of the token, in which case we do nothing (so no else)
      if($text_word != "") {

        $engine_word = downCase($text_word, $lang_id);

        $sql = "INSERT IGNORE INTO word_engine (word, lang_id) VALUES ('$engine_word', '$lang_id')";
        $result = $conn->query($sql);

        $sql = "SELECT word_engine_id FROM word_engine WHERE word = '$engine_word'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $word_engine_id = $row["word_engine_id"];

        if($dt_counter != 0){

          $ch_end = $ch_start + $ch_length;

          if($line_break > 0){
            $sql = "INSERT INTO chunks (chunk, dt_start, dt_end) VALUES ('$chunk', $ch_start, $ch_end)";
            $result = $conn->query($sql);
            $chunk = $text_word;
            $ch_length = 0;

            $ch_start = $ch_end + 1;
          }
          else {
            $chunk = $chunk.$text_word;
            $ch_length++;
          }
          //$ch_end = $ch_start + $ch_length;
          //$ch_start = $ch_end + 1;
        }

        $sql = "INSERT INTO display_text (text_word, line_break, word_engine_id) VALUES ('$text_word', $line_break, $word_engine_id)";
        $result = $conn->query($sql);
        $dt_counter++;
      }

      $line_break = 0; //line_break set to zero in anticipation of this being word-medial punctuation; if it is word-initial punctuation (i.e. if($arr[0] == "")) we set line_break to 1 below

      if($punct == "\n" || $punct == "\r") {
        $line_break = 2;
      }
      if($punct == "\t") {
        $line_break = 3;
      }
            //if word-initial punctuation lb = 1 because preceded by a space
      if($arr[0] == "" && $c == 0) {
        $line_break = 1;
      }  
      
      if($punct == "'") {$punct = "\'";}

      if($dt_counter != 0){

        $ch_end = $ch_start + $ch_length;

        if($line_break > 0) {
          $sql = "INSERT INTO chunks (chunk, dt_start, dt_end) VALUES ('$chunk', $ch_start, $ch_end)";
          $result = $conn->query($sql);
          $chunk = $punct;
          $ch_length = 0;

          $ch_start = $ch_end + 1;
        }
        else{
          $chunk = $chunk.$punct;
          $ch_length++;
        }

      }
      
      
      $sql = "INSERT INTO display_text (text_word, line_break) VALUES ('$punct', $line_break)";
      $result = $conn->query($sql);
      $dt_counter++;


          //if we have just added the second-to-last component of the token and the final component is not zero (i.e. the token is not punctuation-ended), we manually increment $c and add the final component to the table right now, otherwise $c will increment beyond maximum index of the punctuation array and try to access the punctuation array with it in the next loop and break. If the token was punctuation-ended then we don't need to do this because $c incrementing normally will take it to the maximum index of the punctuation-array and not above it.
      if($c == $arr_size_minus_1 - 1 && $arr[$arr_size_minus_1] != "") {
        $c++;
        $text_word = $arr[$c];
        $engine_word = downCase($text_word, $lang_id);
        
        $sql = "INSERT IGNORE INTO word_engine (word, lang_id) VALUES ('$engine_word', '$lang_id')";
        $result = $conn->query($sql);

        $sql = "SELECT word_engine_id FROM word_engine WHERE word = '$engine_word'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $word_engine_id = $row["word_engine_id"];

        $chunk = $chunk.$text_word;
        $ch_length++;

        $sql = "INSERT INTO display_text (text_word, line_break, word_engine_id) VALUES ('$text_word', 0, $word_engine_id)";
        $result = $conn->query($sql);  
        $dt_counter++;
        
      }

      $line_break = 0; //line-break set to zero for the next component of the punctuation-separated token, because obviously not preceded by space

    }

  }
      //if no punctuation is found in the token (if(preg_match_all == false)) we can just stick the word whole in the database with a '1' for 'preceding space' in the lb column
  else {

    $engine_word = downCase($word, $lang_id);

    $sql = "INSERT IGNORE INTO word_engine (word, lang_id) VALUES ('$engine_word', '$lang_id')";
    $result = $conn->query($sql);

    $sql = "SELECT word_engine_id FROM word_engine WHERE word = '$engine_word'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $word_engine_id = $row["word_engine_id"];
   

    if($dt_counter != 0){

      $ch_end = $ch_start + $ch_length;

      $sql = "INSERT INTO chunks (chunk, dt_start, dt_end) VALUES ('$chunk', $ch_start, $ch_end)";
      $result = $conn->query($sql);

      $chunk = $word;
      $ch_start = $ch_end + 1;
      $ch_length = 0;
      //$ch_end = $ch_start + $ch_length;
      //$ch_start = $ch_end + 1;
    } 
/*
    if($dt_counter != 0){

      if($line_break > 0) {
        $sql = "INSERT INTO chunks (chunk, dt_start, dt_end) VALUES ('$chunk', $ch_start, $ch_end)";
        $result = $conn->query($sql);
        $chunk = $word;
      }
      else{
        $chunk = $chunk.$word;
        $ch_length++;
      }

    } */
    
    
    $sql = "INSERT INTO display_text (text_word, line_break, word_engine_id) VALUES ('$word', 1, $word_engine_id)";
    $result = $conn->query($sql);
    $dt_counter++;

  }
  
  $word = strtok(" ");
 
}

$ch_end = $ch_start + $ch_length;
$sql = "INSERT INTO chunks (chunk, dt_start, dt_end) VALUES ('$chunk', $ch_start, $ch_end)";
$result = $conn->query($sql);

$sql = "SELECT MAX(tokno) AS dt_end FROM display_text";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$dt_end = $row["dt_end"];

$sql = "INSERT INTO texts (text_title, dt_start, dt_end, lang_id) VALUES ('$text_title', '$dt_start', '$dt_end', '$lang_id')";
$res = $conn->query($sql);

$conn->close(); 

?>
