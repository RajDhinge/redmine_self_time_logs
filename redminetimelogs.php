<!DOCTYPE html>
<?php
//Current date and start date
date_default_timezone_set("Asia/Kolkata");
$now = date("d"); // or your date as well
$your_date = strtotime(date("Y") . "-" . date("m") . "-1"); //first dat of current month
$currentdate = date("Y") . "-" . date("m") . "-$now";
$startdate = date("Y") . "-" . date("m") . "-01";
//Redmine specific URL setup
$domain = "YOUR_DOMAIN";
$key = "YOUR_KEY";

//Current user
if (isset($_GET["userid"])) {
    $userid = $_GET["userid"];
}

if (!isset($userid)) {
    // fallback
    $user = "$domain/users/current.json?key=$key";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $user);
    $res = curl_exec($ch);
    $data = json_decode($res);
    $userid = $data->user->id;
}
$user = "$domain/users/$userid.json?key=$key";
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $user);
$res = curl_exec($ch);
$data = json_decode($res);
if (!isset($data->user->id)) {
    // fallback
    $user = "$domain/users/current.json?key=$key";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $user);
    $res = curl_exec($ch);
    $data = json_decode($res);
    $userid = $data->user->id;
}
$name = $data->user->firstname . " " . $data->user->lastname;
$query = "/time_entries.json?key=$key&user_id=$userid&from=$startdate&to=$currentdate&limit=100000";
?>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Redmine - Time logs</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<style>
  @charset "UTF-8";
@import url("https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css");
.pcs:after {
  content: " pcs";
}

.cur:before {
  content: "$";
}

.per:after {
  content: "%";
}

* {
  box-sizing: border-box;
}

body {
  padding: 0.2em 2em;
}

table {
  width: 100%;
}
table th {
  text-align: left;
  border-bottom: 1px solid #ccc;
}
table th, table td {
  padding: 0.4em;
}

table.fold-table > tbody > tr.view td, table.fold-table > tbody > tr.view th {
  cursor: pointer;
}
table.fold-table > tbody > tr.view td:first-child,
table.fold-table > tbody > tr.view th:first-child {
  position: relative;
  padding-left: 20px;
}
table.fold-table > tbody > tr.view td:first-child:before,
table.fold-table > tbody > tr.view th:first-child:before {
  position: absolute;
  top: 50%;
  left: 5px;
  width: 9px;
  height: 16px;
  margin-top: -8px;
  font: 16px fontawesome;
  color: #999;
  content: "";
  transition: all 0.3s ease;
}
table.fold-table > tbody > tr.view:nth-child(4n-1) {
  background: #eee;
}
table.fold-table > tbody > tr.view:hover {
  background: rgb(248, 245, 245);
}
table.fold-table > tbody > tr.view.open {
  background: tomato;
  color: white;
}
table.fold-table > tbody > tr.view.open td:first-child:before, table.fold-table > tbody > tr.view.open th:first-child:before {
  transform: rotate(-180deg);
  color: #333;
}
table.fold-table > tbody > tr.fold {
  display: none;
}
table.fold-table > tbody > tr.fold.open {
  display: table-row;
}

.fold-content {
  padding: 0.5em;
}
.fold-content h3 {
  margin-top: 0;
}
.fold-content > table {
  border: 2px solid #ccc;
}
.fold-content > table > tbody tr:nth-child(even) {
  background: #eee;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>

</head>
<body>
<!-- partial:index.partial.html -->
<form action=# method=get>
  <b>Fetch User logs by ID : </b><input type="text" name="userid" id="userid" />
  <input class="btn btn-small btn-info"  type=submit>
</form>
<table class="fold-table">
  <thead>
    <tr>
      <th>Date</th><th>Total tickets addressed</th><th>Total Time</th>
    </tr>
  </thead>
  <tbody>



  <?php
  //helper function
  function printDistinct($arr, $n)
  {
      // Pick all elements one by one
      for ($i = 0; $i < $n; $i++) {
          // Check if the picked element
          // is already printed
          $j;
          for ($j = 0; $j < $i; $j++) {
              if ($arr[$i] == $arr[$j]) {
                  break;
              }
          }

          // If not printed
          // earlier, then print it
          if ($i == $j) {
              @$dist[] .= $arr[$i];
          }
      }
      return $dist;
  }

  //get data from redmine
  $url = "$domain$query";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  $res = curl_exec($ch);
  $data = json_decode($res);
  $total = 0;
  $abstotal = 0;
  $fold = "";
  $absta = 0;
  $pointer = $currentdate;
  $arr1 = [];
  $absarr1 = [];
  $count = 0;
  $ptrcount = 0;
  // echo $res;
  foreach ($data as $key => $value) {
      if ($key == "time_entries") {

          foreach ($value as $inkey => $invalue) {
              if ($pointer != $invalue->spent_on) {
                  $n = sizeof($arr1);

                  if ($n > 0) {
                      $totalticketsadd = count(printDistinct($arr1, $n));
                  } else {
                      $totalticketsadd = 0;
                  }
                  if ($ptrcount > 0) {
                      //view
                      echo "<tr class='view'>
                       <td>$pointer</td>    
                       <td>$totalticketsadd support tickets</td>    
                       <td>$total</td>    
                   </tr>";
                      //fold
                      ?>
                        <tr class="fold">
                          <td colspan="7">
                            <div class="fold-content">
                              <h3><?php echo $pointer; ?></h3>
                                <table>
                                <thead>
                                  <tr>
                                    <th>Edit</th><th>Product</th><th>Comments</th><th>Created time</th><th>Spent time</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <?php echo $fold; ?>
                                  </tr>
                                </tbody>
                              </table>          
                            </div>
                          </td>
                    </tr>                   
                   <?php $count++;
                  }
                  $pointer = $invalue->spent_on;
                  $total = 0;
                  $fold = "";
                  $arr1 = (array) null;
                  //echo "<br>break";
                  $absta += $totalticketsadd;

                  $ptrcount = 0;
              }
              $ptrcount++;
              $curr = $invalue->spent_on;
              //tickets addressed
              if (str_starts_with($invalue->issue->id, "7")) {
                  @$arr1[] .= $invalue->issue->id;
                  @$absarr1[] .= $invalue->issue->id;
              }
              //Total time spent
              $total += $invalue->hours;
              $abstotal += $invalue->hours;

              //fold
              $fold .=
                  "<tr><td><a href='$domain/issues/" .
                  $invalue->issue->id .
                  "'>#" .
                  $invalue->issue->id .
                  " </a><a href='$domain/time_entries/" .
                  $invalue->id .
                  "/edit'> - [EDIT]</a></td>";
              $fold .= "<td>" . $invalue->project->name . "</td>";
              $fold .= "<td>" . $invalue->comments . "</td>";
              $dt = substr($invalue->created_on, 11, -1);
              $mydate = new DateTime($dt, new DateTimeZone("UTC"));
              //echo $date->format('Y-m-d H:i:sP') . "\n";
              $mydate->setTimezone(new DateTimeZone("Asia/Kolkata"));
              $fold .= "<td>" . $mydate->format("h:i:s a") . "</td>";
              $fold .= "<td>" . $invalue->hours . "</td></tr>";
          }
          $n = sizeof($arr1);
          if ($n > 0) {
              $totalticketsadd = count(printDistinct($arr1, $n));
          } else {
              $totalticketsadd = 0;
          }
          //view
          echo "<tr class='view'>
                 <td>$pointer</td>    
                 <td>$totalticketsadd support tickets</td>    
                 <td>$total</td>    
             </tr>";

          //fold
          //fold
          ?>
               <tr class="fold">
                 <td colspan="7">
                   <div class="fold-content">
                     <h3><?php echo $pointer; ?></h3>
                       <table>
                       <thead>
                         <tr>
                           <th>Edit</th><th>Product</th><th>Comments</th><th>Created time</th><th>Spent time</th>
                         </tr>
                       </thead>
                       <tbody>
                         <tr>
                           <?php echo $fold; ?>
                         </tr>
                       </tbody>
                     </table>          
                   </div>
                 </td>
           </tr>  <?php $absta += $totalticketsadd;
      }
  }
  $n = sizeof($absarr1);
  $count++;
  if ($n > 0) {
      $totalabsticketsadd = count(printDistinct($absarr1, $n));
  }
  //var_dump(printDistinct($absarr1, $n));
  echo "<h1>$name's activity for " .
      date("M") .
      " " .
      date("Y") .
      " till $currentdate</h1>";
  echo "<h2>Total tickets addressed : " .
      $totalabsticketsadd .
      " distinct tickets</h2>";
  echo "<h2>Time spent    &nbsp&nbsp: " .
      $abstotal .
      " hours  (average " .
      number_format((float) $abstotal / $count, 2, ".", "") .
      " hours) </h2>";
  ?>

  </tbody>
</table>
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
  
<script>
$(function(){
  $(".fold-table tr.view").on("click", function(){
    $(this).toggleClass("open").next(".fold").toggleClass("open");
  });
});
</script>

</body>
</html>
