<?php
if (!isset($pdo)) {
  require_once __DIR__ . '/../core/dbConfig.php';
  require_once __DIR__ . '/../core/models.php';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: #008080;">
  <a class="navbar-brand" href="#">Employee Panel</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="file_an_attendance.php">File an Attendance</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="file_a_leave.php">File a Leave</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="leaves.php">Leaves</a>
      </li>
      <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Notifications
            <?php
            if (function_exists('getUnreadNotificationsCount')) {
              $unreadCount = getUnreadNotificationsCount($pdo, $_SESSION['user_id']);
              if ($unreadCount > 0): ?>
                <span class="badge badge-danger badge-pill position-absolute" style="top: 0; right: 0;" id="unreadCountBadge"><?= $unreadCount ?></span>
            <?php endif;
            } ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown" style="max-height: 400px; overflow-y: auto;">
            <?php
            if (function_exists('getNotifications')) {
              $notifications = getNotifications($pdo, $_SESSION['user_id']);
              if (empty($notifications)): ?>
                <span class="dropdown-item">No notifications</span>
              <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                  <a class="dropdown-item <?= $notification['is_read'] ? 'text-muted' : 'font-weight-bold text-primary' ?>" href="#">
                    <?= $notification['message'] ?>
                    <small class="d-block"><?= date('M j, Y g:i a', strtotime($notification['created_at'])) ?></small>
                  </a>
                <?php endforeach; ?>
              <?php endif;
            } else { ?>
              <span class="dropdown-item">Notifications not available</span>
            <?php } ?>
          </div>
        </li>
      <?php endif; ?>
      <li class="nav-item">
        <a class="nav-link" href="core/handleForms.php?logoutUserBtn=1">Logout</a>
      </li>
    </ul>
  </div>
  <script>
    $(document).ready(function() {
      // Mark notifications as read when dropdown is opened
      $('#notificationDropdown').on('click', function() {
        $.ajax({
          url: 'core/handleForms.php',
          type: 'POST',
          data: {
            action: 'mark_notifications_read',
            user_id: <?= $_SESSION['user_id'] ?? 0 ?>
          },
          success: function() {
            // Hide the unread count badge
            $('#unreadCountBadge').hide();
            // Update all notification items to appear as read
            $('.dropdown-item').removeClass('font-weight-bold text-primary').addClass('text-muted');
          }
        });
      });

      // Optional: Poll for new notifications every 30 seconds
      setInterval(function() {
        if (typeof getUnreadNotificationsCount !== 'undefined') {
          $.get('core/handleForms.php?get_unread_count=1&user_id=<?= $_SESSION['user_id'] ?? 0 ?>', function(data) {
            if (data.count > 0) {
              $('#unreadCountBadge').text(data.count).show();
            } else {
              $('#unreadCountBadge').hide();
            }
          });
        }
      }, 30000);
    });
  </script>
</nav>