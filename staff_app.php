 <?php

    connectDB();
 
    //connect DB and catch bingo data to all_bingo_data[]
      function connectDB(){
        $dsn = 'mysql:dbname=test_bingo;host=localhost;port=3306';
        $user = 'root';
        $password = '';
      try{ 
        $dbh = new PDO($dsn, $user, $password);
  
        $sql = "select * from bingonowdata where id =";
        $row = "";

        $all_bingo_data = [];

        for($i=1; $i<=4; $i++){
          foreach ($dbh->query($sql.$i) as $row);
          $all_bingo_data[$i-1] = $row['nums'].",$i";
          $row = "";
        }
        
        /*
        for( $i = 1; $i <= 800; $i++){
          foreach ($dbh->query($sql.$i) as $row);
             $all_bingo_data[$i-1] = $row['nums'];
	           $row = "";
        }
        
        for( $i = 1601; $i <= 2000; $i++){
          foreach ($dbh->query($sql.$i) as $row);
             $all_bingo_data[$i-801] = $row['nums'];
             $row = "";
        }
        
        for( $i = 2601; $i <= 3000; $i++){
          foreach ($dbh->query($sql.$i) as $row);
             $all_bingo_data[$i-1401] = $row['nums'];
             $row = "";
        }*/

      $Card = array();

      //split all_bingo_data , into Card[] associate array
      for($l=1; $l<=4; $l++){
        $cutdata = "";
        $cutdata = explode("," ,$all_bingo_data[$l-1]);

        $k = 0;
        for($i=0; $i<5; $i++){
          for($j=0; $j<5; $j++){
            if($i == 2 && $j == 2){
              $Carddata[$i][$j] = 100;
            }
            else{
              $Carddata[$i][$j] = $cutdata[$k];
              $k++;
            }
          }
        }
      
        $Card += array(
          "$cutdata[24]" => $Carddata
        );
      }
      
      //CheckCard($Card);

      if($_POST['callnum'] == TRUE){
        TurnBlack($Card);
      }

      }catch (PDOException $e){
         print('Connection failed:'.$e -> getMessgae() );
         die();
      }
      $dbh = null;
  }

  function renewDB($id,$nums){
      $dsn = 'mysql:dbname=test_bingo;host=localhost;port=3306';
      $user = 'root';
      $password = '';
      $stringnums = "";

      for($i=0; $i<5; $i++){
         for($j=0; $j<5; $j++){
            if($nums[$i][$j] != 100){
              if($i == 4 && $j== 4){
                 $stringnums = $stringnums.$nums[$i][$j];
              }
              else{
                 $stringnums = $stringnums.$nums[$i][$j].",";
              }
            }
         }
      }

      try{ 
        $dbh = new PDO($dsn, $user, $password);
        $sql = "UPDATE bingonowdata SET nums = '$stringnums' WHERE id = '$id' ";
        $dbh->query($sql);

      }catch (PDOException $e){
         print('Connection failed:'.$e -> getMessgae() );
         die();
      }
      $dbh = null;
  }

    /*Turn Bingonum black*/
  function TurnBlack($Card){

     $callnum = htmlspecialchars($_POST['callnum']);
     $error = false;
     
     if((int)$callnum > 75 || (int)$callnum < 1){
        $error = true;
        echo "その番号はカードにはありません。";
     }
     if(!$error){
        foreach( $Card as $key => $value){

            for($j=0; $j<5; $j++){
                for($k=0; $k<5; $k++){
                      if((int)$value[$j][$k] == (int)$callnum){
                          $value[$j][$k] +=100;
                          $Card[$key] = $value;
                          renewDB($key,$value);
                      }
                }
            }
        }
     }
      CheckBingo($Card);
  }

      /*bingo check*/
  function CheckBingo($Card){
        $Yokocount =0;
        $Tatecount =0;
        $Nanamecount1 =0;
        $Nanamecount2 =0;
        foreach( $Card as $key => $value){
          $nanamepreach = 0;
          $pbingo = 0;
          $preach = 0;
          $nanamepreach = 0;

            for($j=0; $j<5; $j++){
              if($value[$j][$j] >= 100){
                $Nanamecount1++;
              }

              for($k=0; $k<5; $k++){
                if($j+$k == 4){
                  if($value[$j][$k] >= 100){
                    $Nanamecount2++;
                  }
                }
              }
            }
    
              if($Nanamecount1 == 5){
                $pbingo++;
                $preach--;
                //echo $key."is Naname1 bingo<br>";
              }
              if($Nanamecount1 == 4){
                 if($nanamepreach == 0){
                  $nanamepreach++;
                  $preach++;
                  //echo $key."is Naname1 reach<br>";
                 }
                 else if($Nanamecount2 == 4){
                  $preach = 2;
                  //echo $key."is Naname1 reach<br>";
                 }
                 else if($Nanamecount2 == 5){
                  $preach = 2;
                  //echo $key."is Naname1 reach<br>";
                } 
              }
     
              if($Nanamecount2 == 5){
                 $pbingo++;
                 $preach--;
                 //echo $key."is Naname2 bingo<br>";
              }
              if($Nanamecount2 == 4){
                if($nanamepreach == 0){
                  $nanamepreach++;
                  $preach++;
                  //echo $key."is Naname2 reach<br>";
                 }
                 else if($Nanamecount1 == 4){
                  $preach = 2;
                  //echo $key."is Naname2 reach<br>";
                 }
                 else if($Nanamecount1 == 5){
                  $preach = 2;
                  //echo $key."is Naname2 reach<br>";
                } 
              }
           
           $Nanamecount1 = 0;
           $Nanamecount2 = 0;

           for($j=0; $j<5; $j++){
                for($k=0; $k<5; $k++){
                  if($value[$j][$k] >= 100){
                    $Yokocount++;
                  }
                }
                if( $Yokocount == 5){
                  $pbingo++;
                   //echo $key."is Yoko bingo<br>";
                }
                if($Yokocount == 4){
                   $preach++;
                   //echo $key."is Yoko reach<br>";
                }
                $Yokocount = 0;
           }
            
           for($k=0; $k<5; $k++){
              for($j=0; $j<5; $j++){
                if($value[$j][$k] >= 100){
                  $Tatecount++;
                }
              }
              if( $Tatecount == 5){
                $pbingo++;
                //echo $key."is Tate bingo<br>";
              }
              if($Tatecount == 4){
                $preach++;
                //echo $key."is Tate reach<br>";
              }
              $Tatecount = 0;
          }
        
        /*present bingo status*/
        if($pbingo){
          echo "No".$key."が".$pbingo."つビンゴ<br>";
        }
        if($preach > 0){
         echo "No".$key."が".$preach."つリーチ<br>";
        }

      }
      
  }


  function CheckCard($Card){
        foreach( $Card as $key => $value ){
          echo $key. "：<br />\n";
          for($i=0; $i<5; $i++){
            for($j=0; $j<5; $j++){
              echo $value[$i][$j]."<br />\n";
            }
          }
        }
   }

  ?>

<html manifest="manifest.appcache">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Staff Moniter</title>
  </head>
  <body>
    <form action="staff_app.php" method="post">
          <p>値：<input type="number" name="callnum"></p>
          <input type="submit" value="Bingo">
    </form>
  </body>
</html>