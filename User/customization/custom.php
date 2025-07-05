<!DOCTYPE html>
<html>
<head>
  <title>PrintCity - Courses</title>
  <style>
    .course { padding: 10px; border: 1px solid #ccc; margin-bottom: 10px; }
  </style>
</head>
<body>
  <h2>All Canvas Courses</h2>

  <div id="courseList">Loading...</div>

  <script>
    fetch('api_proxy.php?endpoint=courses')
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('courseList');
        container.innerHTML = '';
        data.forEach(course => {
          const div = document.createElement('div');
          div.className = 'course';
          div.innerHTML = `<strong>${course.name}</strong><br>
                           ID: ${course.id}<br>
                           <a href="course.php?id=${course.id}">View Details</a>`;
          container.appendChild(div);
        });
      })
      .catch(err => {
        document.getElementById('courseList').innerHTML = 'Error loading courses.';
        console.error(err);
      });
  </script>
</body>
</html>
