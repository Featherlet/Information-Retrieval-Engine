
<?php
    include 'SpellCorrector.php';
    //header('Content-Type: text/html; charset=utf-8');
    $start = 0;
    $radio = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : null;
    $query = isset($_REQUEST['text_query']) ? $_REQUEST['text_query'] : false;
    $limit = 10;

    $results = false;
    if ($query) {
        require_once('Apache/Solr/Service.php');
        $solr = new Apache_Solr_Service('localhost', 8983, '/solr/myexample/');
        if (!get_magic_quotes_gpc()) {
            $query = stripslashes($query);
        }
        try {
            $additionalParameters = array(
                'fl' => 'title,og_url,id,description',
                'sort' => $_REQUEST['sort']
            );
            #$results = $solr->search($query, $start, $limit, $additionalParameters);
            $results = $solr->search($query, 0, $limit, $additionalParameters);
        } catch (Exception $e) {
            die("<html><head><title>My Search</title><body><pre>{$e->__toString()}</pre></body></html>");
        }
    }
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <script
            src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
            crossorigin="anonymous">
        </script>
        <script src="js/script.js"></script>
     </head>

     <body>
     	<form>
     	<div align="center" style="margin-top: 60px">
     		<h1><a href="index.php" style="text-decoration:none; color: black;">Little Search Engine</a></h1>
      </div>
      <div align="center" style="margin-top: 30px">
        <label class="radio-inline">
                <input type="radio" name="sort" style="font-size: 20px;" value="score desc" <?php if (!isset($_REQUEST['text_query']) || $_REQUEST['sort'] == 'score desc' || $_REQUEST['sort'] == null) {echo "checked";}; ?>/> 
                Lucent Default
            </label>
            <label class="radio-inline">
                <input type="radio" name="sort" style="font-size: 20px;" value="pageRankFile desc" <?php if ($_REQUEST['sort'] == 'pageRankFile desc') {echo "checked";}; ?>/>
                PageRank
            </label>
      </div>
     	<div align="center" style="margin-top: 30px; height: 200px">
        <table style="width:500px;">
        <tr style="margin-bottom: 0px">
          <td align="right">
          <input type="text" style="width: 400px; height: 40px; font-size: 20px;" autocomplete="off" name="text_query" id="text_query" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>" onkeyup="autocomplet()">   
          </td>
          <td>
            <button id="button" align="left" style="width: 100px; height: 40px; font-size: 20px; margin: 20px;" type="submit">Search</button>
          </td>
        </tr>
     		<tr>
          <td style="margin-top: 0px;">
            <ul align="left" id="downlist" style="width: 350px; height: 40px; font-size: 20px; list-style-type:none"></ul>
          </td>
        </tr>  
        
        </table>
     	</div>


    <?php

     if($results){
        $total = (int) $results->response->numFound;
        $file = fopen("UrlToHtml_Newday.csv","r");
        while ($data = fgetcsv($file)) {
            $dict[$data[0]] = $data[1];
        }
        fclose($file);

        $isCorrected = false;
        $query = strtolower($query);
        $words = explode(" ", $query);
        $curretQuery = "";
        foreach ($words as $word) {
          $correction = SpellCorrector::correct($word);
          
          if($word != $correction){
            $isCorrected = true;
            $curretQuery = $curretQuery . " " . $correction;
          }else{
            $curretQuery = $curretQuery . " " . $word;
          }
        }
        
        if ($isCorrected){
          $curretQuery = substr($curretQuery, 1);
        ?>

           <h3>
              Are you looking for
               <a href=<?php echo "\"?text_query=".$curretQuery."&sort=".$_REQUEST['sort']."\""; ?>>
                   <u><?php echo $curretQuery; ?></u>
               </a>
           </h3>

        <?php
        }
        ?>

        <form>
            <div align="left"><span style="margin-left: 40px;">Results 1 - 10 in <?php echo $total ?> Results</span></div>
            <div align="center">
               <table style="width:100%; margin-left: 40px; margin-right: 40px; margin-top: 20px" >
                  <?php
                    foreach ($results->response->docs as $doc) {
                      $url = $doc->getField('og_url')["value"];
                      $description = $doc->getField('description')["value"];
                      $id  = $doc->getField('id')["value"];
                      $title = $doc->getField('title')["value"];

                      if(is_null($url)){
                        $file_id = substr($id, 55);
                        $url = $dict[$sid];
                      }

                      if(is_null($description)){
                        $description = "NA";
                      }
                    ?>
                    <tr>
                    <td>
                      <div>
                        <?php echo "<h3 style=\"margin: 0px;\"><a href=\"".$url."\" style=\"text-decoration:none;\">".htmlspecialchars($title, ENT_NOQUOTES, 'utf-8')."</h3>"; ?>
                        <div>
                          <?php echo "<a href=\"".$url."\" style=\"text-decoration:none; color:green;\">".htmlspecialchars($url, ENT_NOQUOTES, 'utf-8')."</a>"; ?>
                        </div>
                        <span>
                          <?php echo "<p style=\"margin: 0px; white-space: normal; width:80%\"> <b>Description: </b>".htmlspecialchars($description, ENT_NOQUOTES, 'utf-8')."</p>"; ?>
                        </span>
                        <?php echo "<span><b>ID</b>: ".htmlspecialchars($id, ENT_NOQUOTES, 'utf-8')."</span>"; ?>
                      </div>
                        <span>
                        <?php 

                          $contentFile = $doc->getField('id')["value"];
                          $contentFile = end(explode("/", $contentFile));
                          
                          $contentFile = str_replace("html", "txt", $contentFile);
                          $contentFile = "./parsed_data/".$contentFile;
                          $content = file_get_contents($contentFile);
                          $content = htmlspecialchars($content, ENT_NOQUOTES, 'utf-8');
                          $sentences = preg_split("/[\.\?!]/", $content);

                          $snippet="NA";

                          $words = explode(" ", $query);

                          $firstIndex = 0;
                          //Match single word
                          if(count($words) == 1){
                            foreach($sentences as $sentence){
                              if(stripos($sentence, $query) > 0){
                                $firstIndex = stripos($sentence, $query);
                                $snippet = $sentence;
                                break;
                              }
                            }
                          }else{
                            $firstMatchPicked = false;
                            $firstMatch;
                            $getResult = false;
                            //Try to find a sentence including all the key words
                            foreach($sentences as $sentence){
                              $count = 0;
                              foreach ($words as $word) {
                                if(stripos($sentence, $word) > 0){
                                  if(!$firstMatchPicked){
                                    $firstMatch = stripos($sentence, $word);
                                    $firstMatch = $sentence;
                                    $firstMatchPicked = true;
                                  }
                                  $count++;
                                }
                              }

                              if($count == count($words)){
                                $snippet = $sentence;
                                $getResult = true;
                                break;
                              }
                              if($getResult){
                                break;
                              }
                            }

                            if(!$getResult && $firstMatchPicked){
                              $snippet = $firstMatch;
                            }

                          }


                          if(strlen($snippet) > 180){
                            $substring = substr($snippet, $firstIndex, 180);
                            if(strlen($substring) == 180)
                              $snippet = "..." . $substring . "...";
                            else
                              $snippet = "..." . $substring;
                          }

                          foreach ($words as $word) {
                            //$snippet = preg_replace('/'.$word.'/i',"<b><em>\$0</em></b>",$snippet);
                            $snippet = preg_replace("/([\\W]+)(".$word.")/i", "$1<mark><u><strong>$2</strong></u></mark>", $snippet);
                          }
                          


                          echo "<p style=\"margin: 0px; white-space: normal; width:80%\"> <b>Snippet: </b>". $snippet ."</p>"; 
                          ?>
                        </span>
                      <div>
                        

                      </div>
                    </td>

                    </tr>

                    <?php
                    }
                  ?>
                </table>
            </div>
        </form>

        <?php

         }
        ?>
     	</form>
     </body>
 </html>
