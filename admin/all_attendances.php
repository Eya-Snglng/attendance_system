<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_admin'] == 0) {
  header("Location: ../employees/index.php");
}
?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/smoothness/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <style>
    body {
      font-family: "Arial";
    }

    .no-attendance {
      color: #dc3545;
      font-style: italic;
    }
  </style>
  <title>Attendance Records</title>
</head>

<body>
  <?php include 'includes/navbar.php'; ?>
  <div class="container-fluid">
    <div class="col-md-12">
      <?php $getAllDates = getAllDates($pdo); ?>
      <?php foreach ($getAllDates as $row) { ?>
        <div class="card shadow mt-4">
          <div class="card-header">
            <h2><?php echo $row['date_added']; ?></h2>
          </div>
          <div class="card-body">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">First Name</th>
                  <th scope="col">Last Name</th>
                  <th scope="col">Time In</th>
                  <th scope="col">Time Out</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $getAllAttendancesByDate = getAllAttendancesByDate($pdo, $row['date_added']);
                $getUsersWithIncompleteAttendance = getUsersWithIncompleteAttendance($pdo, $row['date_added']);

                // First display users who have at least one attendance record
                foreach ($getAllAttendancesByDate as $innerRow) {
                  $time_in = isset($innerRow['time_in']) ? $innerRow['time_in'] : null;
                  $time_out = isset($innerRow['time_out']) ? $innerRow['time_out'] : null;

                  // Only show if they have at least one record
                  if ($time_in || $time_out) {
                ?>
                    <tr>
                      <td><?php echo $innerRow['first_name']; ?></td>
                      <td><?php echo $innerRow['last_name']; ?></td>
                      <td><?php echo $time_in ?? '<span class="no-attendance">No time in</span>'; ?></td>
                      <td><?php echo $time_out ?? '<span class="no-attendance">No time out</span>'; ?></td>
                      <td>
                        <?php
                        if ($time_in && $time_out) {
                          echo '<span class="badge badge-success">Complete</span>';
                        } else {
                          echo '<span class="badge badge-warning">Incomplete</span>';
                        }
                        ?>
                      </td>
                    </tr>
                  <?php
                  }
                }

                // Then display users with no attendance records at all
                foreach ($getUsersWithIncompleteAttendance as $innerRow) {
                  ?>
                  <tr>
                    <td><?php echo $innerRow['first_name']; ?></td>
                    <td><?php echo $innerRow['last_name']; ?></td>
                    <td colspan="3" class="text-danger">No attendance recorded</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
    </div>
  <?php } ?>
  </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</body>

</html>