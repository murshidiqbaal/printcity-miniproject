<?php $courseId = $_GET['id']; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Course #<?= htmlspecialchars($courseId) ?></title>
</head>
<body>
  <h2>Course Details: <?= htmlspecialchars($courseId) ?></h2>
  <div id="details">Loading...</div>

  <script>
    fetch('api_proxy.php?endpoint=courses/<?= $courseId ?>/assignments')
      .then(res => res.json())
      .then(data => {
        const details = document.getElementById('details');
        if (data.length === 0) {
          details.innerHTML = '<p>No assignments found.</p>';
          return;
        }
        data.forEach(a => {
          const div = document.createElement('div');
          div.innerHTML = `<strong>${a.name}</strong> - Due: ${a.due_at || 'N/A'}`;
          details.appendChild(div);
        });
      })
      .catch(err => {
        document.getElementById('details').innerHTML = 'Failed to load assignments.';
        console.error(err);
      });
  </script>
</body>
</html>
