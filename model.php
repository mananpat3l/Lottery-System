<?php
session_start();

function db_object() {
  try {
    $db = new PDO("mysql:host=localhost;dbname=lottery_system;charset=utf8","root","");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    $_SESSION['error'] = $e;
    return false;
  }
  return $db;
}

function db_authentication() {
  $conn = db_object();
  if($conn == false) {
    return false;
  }

  try {
    $res = $conn->prepare($sql);
    $res->execute();
  } catch (PDOException $e) {
    $_SESSION['error'] = $e;
    return false;
  }
}

function test_user_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function insertData($table, $data) {
  $conn = db_object();
  if(!empty($data) && is_array($data)) {
    $columns = '';
    $values = '';
    $i = 0;
    $columnString = implode(',', array_keys($data));
    $valueString = ":".implode(',:', array_keys($data));
    $sql = "INSERT INTO ".$table." (".$columnString.") VALUES (".$valueString.")";
    $query = $conn->prepare($sql);
    print_r($data);
    foreach ($data as $key => $val) {
      $query->bindValue(':'.$key, $val);
    }
    $insert = $query->execute();
  }
  return $insert;
}

function updateMember($data) {
  $conn = db_object();
  if(!empty($data) && is_array($data)) {
    $_SESSION["user"] = $data['first_name'];
    $_SESSION["lname"] = $data['last_name'];
    $sql = "UPDATE members SET first_name='".$data['first_name']."', last_name='".$data['last_name']."' WHERE member_id=".$data['member_id'];
    $query = $conn->prepare($sql);
    print_r($data);
    $update = $query->execute();
  }
  return $update;
}

function deleteMember($id) {
  $conn = db_object();
  $sql = "DELETE FROM members WHERE member_id=".$id;
  $query = $conn->prepare($sql);
  $delete = $query->execute();
  return $delete;
}

function db_get_winner() {
  $conn = db_object();
  if($conn == false) {
    return false;
  }
  $sql = "SELECT lotteries.lotto_id, members.member_id, members.first_name, members.last_name, tickets.num_1, tickets.num_2, tickets.num_3, tickets.num_4, tickets.num_5, tickets.num_6,  tickets.ticket_id FROM members, lotteries, tickets WHERE num_1 = win_1 AND num_2 = win_2 AND num_3 = win_3 AND num_4 = win_4 AND num_5 = win_5 AND num_6 = win_6 AND members.member_id = tickets.user_id AND lotteries.lotto_id = tickets.lotto_id";

  try {
    $res = $conn->prepare($sql);
    $res->execute();
  } catch (PDOException $e) {
    $_SESSION['error'] = $e;
    return false;
  }
  return $res->fetchAll(PDO::FETCH_ASSOC);
}

function db_get_users_tickets($member) {
  $conn = db_object();
  if($conn == false) {
    return false;
  }
  $sql = "SELECT * FROM tickets WHERE user_id = ".$member." ORDER BY lotto_id, num_1, num_2, num_3, num_4, num_5, num_6";

  try {
    $res = $conn->prepare($sql);
    $res->execute();
  } catch (PDOException $e) {
    $_SESSION['error'] = $e;
    return false;
  }
  return $res->fetchAll(PDO::FETCH_ASSOC);
}

function db_get_users_lotteries($member) {
  $conn = db_object();
  if($conn == false) {
    return false;
  }
  $sql = "SELECT DISTINCT tickets.user_id, tickets.lotto_id, lotteries.* FROM tickets, lotteries WHERE tickets.lotto_id = lotteries.lotto_id AND user_id = ".$member." ORDER BY lotteries.date";

  try {
    $res = $conn->prepare($sql);
    $res->execute();
  } catch (PDOException $e) {
    $_SESSION['error'] = $e;
    return false;
  }
  return $res->fetchAll(PDO::FETCH_ASSOC);
}

function show_tickets($lotteries, $tickets) {
  ?>
  <link href="css/styled.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <div class="container">
    <h1><?php echo $_SESSION['member']; ?></h1>
    <div class="lottery">
      <?php
      if (empty($lotteries)) {
        echo '<h3>You have no tickets.</h3>';
      }
      foreach ($lotteries as $lottery) {
      ?>
        <div class="lottery">
          <h2><?php echo $lottery['name'] ?></h2>
          <div class="row info">
            <h3>Prize:
              <?php
              $price = strrev($lottery['prize']);
              $eh = str_split($price, 3);
              $price = '';
              foreach ($eh as $key => $n) {
                if($key == 0) {
                  $price .= $n;
                } else {
                  $price .= ",".$n;
                }
              }
              $prize = strrev($price);
              echo '$'.$prize;
              ?>
            </h3>
            <h3>Date:
              <?php
              $date = DateTime::createFromFormat('Y-m-d', $lottery['date']);
              $d = $date->format('D-d-M-Y');
              $day1 = '';
              $day2 = '';
              $month = '';
              $year = '';
              list($day1, $day2, $month, $year) = preg_split('[-]', $d);

              switch ($day1) {
                case 'Mon':
                  $day1 = 'Monday';
                  break;
                case 'Tue':
                  $day1 = 'Tuesday';
                  break;
                case 'Wed':
                  $day1 = 'Wednesdau';
                  break;
                case 'Thu':
                  $day1 = 'Thursday';
                  break;
                case 'Fri':
                  $day1 = 'Friday';
                  break;
                case 'Sat':
                  $day1 = 'Saturday';
                  break;
                case 'Sun':
                  $day1 = 'Sunday';
                  break;
              }

              $arr = preg_split("[]",$day2);
              $mend = 'th';
              switch ($arr[strlen($day2)]) {
                case '1':
                  if($arr[strlen($day2)-1] != '1' && strlen($day2) != 1) {
                    $mend = 'st';
                  }
                  break;
                case '2':
                  $mend = 'nd';
                  break;
              }
              if($arr[strlen($day2)-1] == '0') {
                $arr[strlen($day2)-1] = '';
              }
              $day2 = $arr[strlen($day2)-1].''.$arr[strlen($day2)].''.$mend;

              switch ($month) {
                case 'Jan':
                  $month = 'January';
                  break;
                case 'Feb':
                  $month = 'February';
                  break;
                case 'Mar':
                  $month = 'March';
                  break;
                case 'Apr':
                  $month = 'April';
                  break;
                case 'Jun':
                  $month = 'June';
                  break;
                case 'Jul':
                  $month = 'July';
                  break;
                case 'Aug':
                  $month = 'August';
                  break;
                case 'Sep':
                  $month = 'September';
                  break;
                case 'Oct':
                  $month = 'October';
                  break;
                case 'Nov':
                  $month = 'November';
                  break;
                case 'Dec':
                  $month = 'December';
                  break;
              }
              echo $day1.' '.$day2.' '.$month.' '.$year;
              ?>
            </h3>
          </div>
          <h3>Tickets you own</h3>
          <?php
          foreach ($tickets as $ticket) {
            if($lottery['lotto_id'] == $ticket['lotto_id']) {
              ?>
              <div class="ticket">
                <div class="row">
                  <div class="col s1 numbers"><?php echo $ticket['num_1'] ?></div>
                  <div class="col s1 numbers"><?php echo $ticket['num_2'] ?></div>
                  <div class="col s1 numbers"><?php echo $ticket['num_3'] ?></div>
                  <div class="col s1 numbers"><?php echo $ticket['num_4'] ?></div>
                  <div class="col s1 numbers"><?php echo $ticket['num_5'] ?></div>
                  <div class="col s1 numbers"><?php echo $ticket['num_6'] ?></div>
                </div>
              </div>
              <?php
              if($ticket['num_1'] == $lottery['win_1'] && $ticket['num_2'] == $lottery['win_2'] && $ticket['num_3'] == $lottery['win_3'] && $ticket['num_4'] == $lottery['win_4'] && $ticket['num_5'] == $lottery['win_5'] && $ticket['num_6'] == $lottery['win_6']) {
                echo '<h3>You won!</h3>';
              }
            }
          }
          if(!isset($lottery['win_1'])) {
          ?>
            <form action="controller.php" method="get">
              <input type="hidden" name="lotto_id" value=<?php echo $lottery['lotto_id']; ?>>
              <input type="hidden" name="lotto_name" value=<?php echo $lottery['name']; ?>>
              <input type="hidden" name="lotto_date" value='<?php echo $day1." ".$day2." ".$month." ".$year; ?>'>
              <input type="hidden" name="lotto_prize" value=<?php echo '$'.$prize; ?>>
              <button class="btn waves-effect waves-light" type="submit">Buy More
                <i class="material-icons right">send</i>
              </button>
            </form>
          </div>
          <?php
        } else {
          ?>
          <h3>Lottery has ended.</h3>
          <?php
        }
      }
      ?>
    </div>
  </div>
<?php
}

function db_get_lotteries() {
  $conn = db_object();
  if($conn == false) {
    return false;
  }
  $sql = "SELECT * FROM lotteries WHERE date > CURRENT_DATE ORDER BY date";
  try {
    $res = $conn->prepare($sql);
    $res->execute();
  } catch (PDOException $e) {
    $_SESSION['error'] = $e;
    return false;
  }
  return $res->fetchAll(PDO::FETCH_ASSOC);
}

function show_lotteries($lotteries) {
  ?>
  <link href="css/styled.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <div class="container">
    <div class="lottery">
      <?php
      foreach ($lotteries as $lottery) {
      ?>
        <div class="lottery">
          <aside style="float:left;margin-top:20px;margin-right:20px;" class="image">
            <img width="150px" height="150px" src="images/<?php echo $lottery['image'] ?>" alt="<?php echo $lottery['image'] ?>">
          </aside>
          <h2><?php echo $lottery['name'] ?></h2>
          <div class="info">
            <h3>Prize:
              <?php
              $price = strrev($lottery['prize']);
              $eh = str_split($price, 3);
              $price = '';
              foreach ($eh as $key => $n) {
                if($key == 0) {
                  $price .= $n;
                } else {
                  $price .= ",".$n;
                }
              }
              $prize = strrev($price);
              echo '$'.$prize;
              ?>
            </h3>
            <h3>Date:
              <?php
              $date = DateTime::createFromFormat('Y-m-d', $lottery['date']);
              $d = $date->format('D-d-M-Y');
              $day1 = '';
              $day2 = '';
              $month = '';
              $year = '';
              list($day1, $day2, $month, $year) = preg_split('[-]', $d);

              switch ($day1) {
                case 'Mon':
                  $day1 = 'Monday';
                  break;
                case 'Tue':
                  $day1 = 'Tuesday';
                  break;
                case 'Wed':
                  $day1 = 'Wednesdau';
                  break;
                case 'Thu':
                  $day1 = 'Thursday';
                  break;
                case 'Fri':
                  $day1 = 'Friday';
                  break;
                case 'Sat':
                  $day1 = 'Saturday';
                  break;
                case 'Sun':
                  $day1 = 'Sunday';
                  break;
              }

              $arr = preg_split("[]",$day2);
              $mend = 'th';
              switch ($arr[strlen($day2)]) {
                case '1':
                  if($arr[strlen($day2)-1] != '1' && strlen($day2) != 1) {
                    $mend = 'st';
                  }
                  break;
                case '2':
                  $mend = 'nd';
                  break;
              }
              if($arr[strlen($day2)-1] == '0') {
                $arr[strlen($day2)-1] = '';
              }
              $day2 = $arr[strlen($day2)-1].''.$arr[strlen($day2)].''.$mend;

              switch ($month) {
                case 'Jan':
                  $month = 'January';
                  break;
                case 'Feb':
                  $month = 'February';
                  break;
                case 'Mar':
                  $month = 'March';
                  break;
                case 'Apr':
                  $month = 'April';
                  break;
                case 'Jun':
                  $month = 'June';
                  break;
                case 'Jul':
                  $month = 'July';
                  break;
                case 'Aug':
                  $month = 'August';
                  break;
                case 'Sep':
                  $month = 'September';
                  break;
                case 'Oct':
                  $month = 'October';
                  break;
                case 'Nov':
                  $month = 'November';
                  break;
                case 'Dec':
                  $month = 'December';
                  break;
              }
              echo $day1.' '.$day2.' '.$month.' '.$year;
              ?>
            </h3>
            <?php
            if(isset($_SESSION['login']) && $_SESSION['login']) {
              if(!isset($lottery['win_1'])) {
                ?>
                <form action="controller.php" method="get">
                  <input type="hidden" name="lotto_id" value=<?php echo $lottery['lotto_id']; ?>>
                  <input type="hidden" name="lotto_name" value=<?php echo $lottery['name']; ?>>
                  <input type="hidden" name="lotto_date" value='<?php echo $day1." ".$day2." ".$month." ".$year; ?>'>
                  <input type="hidden" name="lotto_prize" value=<?php echo '$'.$prize; ?>>
                  <button class="btn waves-effect waves-light" type="submit">Buy Ticket
                    <i class="material-icons right">send</i>
                  </button>
                </form>
                <?php
              } else {
                ?>
                <h3>A winner has already been drawn.</h3>
                <h3>Numbers</h3>
                <div class="row">
                  <div class="col s1 numbers"><?php echo $lottery['win_1'] ?></div>
                  <div class="col s1 numbers"><?php echo $lottery['win_2'] ?></div>
                  <div class="col s1 numbers"><?php echo $lottery['win_3'] ?></div>
                  <div class="col s1 numbers"><?php echo $lottery['win_4'] ?></div>
                  <div class="col s1 numbers"><?php echo $lottery['win_5'] ?></div>
                  <div class="col s1 numbers"><?php echo $lottery['win_6'] ?></div>
              </div>
                  <form action="controller.php" method="get">
                  <input type="hidden" name="lotto_id" value=<?php echo $lottery['lotto_id']; ?>>
                  <input type="hidden" name="lotto_name" value=<?php echo $lottery['name']; ?>>
                  <input type="hidden" name="lotto_date" value='<?php echo $day1." ".$day2." ".$month." ".$year; ?>'>
                  <input type="hidden" name="lotto_prize" value=<?php echo '$'.$prize; ?>>
                  <button class="btn waves-effect waves-light" type="submit">Buy Ticket
                    <i class="material-icons right">send</i>
                  </button>
                </form>
                
                <?php
              }
            }
            ?>
          </div>
        </div>
        <?php
      }
      ?>
    </div>
  </div>
<?php
}

function db_get_results() {
  $conn = db_object();
  if($conn == false) {
    return false;
  }
  $sql = "SELECT * FROM lotteries ORDER BY date";
  try {
    $res = $conn->prepare($sql);
    $res->execute();
  } catch (PDOException $e) {
    $_SESSION['error'] = $e;
    return false;
  }
  return $res->fetchAll(PDO::FETCH_ASSOC);
}

function show_results($results) {
  ?>
  <div id="results">
    <?php
    foreach($results as $result) {
      ?>

      <div class="lottery">
        <h2><?php echo $result['name'] ?></h2>
        <div class="info">
          <div>Prize: $<?php echo $result['prize'] ?></div>
          <div>Date: <?php echo $result['date'] ?></div>
        </div>
        <?php
        if(is_null($result['win_1'])) {
          ?>
          <div class="row">
            <h3>Lottery has not been drawn yet</h3>
            <a class="waves-effect waves-light btn red" onclick="gen_winner(<?php echo $result['lotto_id'] ?>)">Generate Numbers</a>
          </div>
        </div>
          <?php
        } else {
        ?>
        <h3>Numbers</h3>
        <div class="row">
          <div class="col s1 numbers"><?php echo $result['win_1'] ?></div>
          <div class="col s1 numbers"><?php echo $result['win_2'] ?></div>
          <div class="col s1 numbers"><?php echo $result['win_3'] ?></div>
          <div class="col s1 numbers"><?php echo $result['win_4'] ?></div>
          <div class="col s1 numbers"><?php echo $result['win_5'] ?></div>
          <div class="col s1 numbers"><?php echo $result['win_6'] ?></div>
        </div>
      </div>
      <?php
      }
    }
    ?>
  </div>
  <?php
}

function create_lottery($data) {
  $conn = db_object();
  if($conn == false) {
    return false;
  }
  if(!empty($data) && is_array($data)) {
    $columns = '';
    $values = '';
    $i = 0;
    $columnString = implode(',', array_keys($data));
    $valueString = ":".implode(',:', array_keys($data));
    $sql = "INSERT INTO lotteries (".$columnString.") VALUES (".$valueString.")";
    $query = $conn->prepare($sql);
    print_r($data);
    foreach ($data as $key => $val) {
      $query->bindValue(':'.$key, $val);
    }
    $insert = $query->execute();
  }
  return $insert;
}

function new_ticket($data) {
  $conn = db_object();
  if($conn == false) {
    return false;
  }
  if(!empty($data) && is_array($data)) {
    $columns = '';
    $values = '';
    $i = 0;
    $columnString = implode(',', array_keys($data));
    $valueString = ":".implode(',:', array_keys($data));
    $sql = "INSERT INTO tickets (".$columnString.") VALUES (".$valueString.")";
    $query = $conn->prepare($sql);
    print_r($data);
    foreach ($data as $key => $val) {
      $query->bindValue(':'.$key, $val);
    }
    $insert = $query->execute();
  }
  return $insert;
}

function generate_winner($data) {
  $conn = db_object();
  if($conn == false) {
    return false;
  }
  if(!empty($data) && is_array($data)) {
    $sql = "UPDATE lotteries SET win_1 = " . $data['win_1'] . ", win_2 = " . $data['win_2'] . ", win_3 = " . $data['win_3'] . ", win_4 = " . $data['win_4'] . ", win_5 = " . $data['win_5'] . ", win_6 = " . $data['win_6'] . " WHERE lotteries.lotto_id = " . $data['lotto_id'];
    $conn->exec($sql);
    echo "Numbers have been generated successfully";
  }
}
 ?>
