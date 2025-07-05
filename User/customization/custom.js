fetch('https://your-institute.instructure.com/api/v1/courses', {
  headers: {
    'Authorization': 'Bearer YOUR_ACCESS_TOKEN'
  }
})
.then(res => res.json())
.then(data => {
  console.log('Courses:', data);
})
.catch(err => console.error(err));
