document.addEventListener('DOMContentLoaded', function() {
  const updateStatus = (elementId, status) => {
    const iconPath = status === 'ok' ? '/icons/green-check.svg' : '/icons/red-cross.svg';
    document.getElementById(elementId).src = iconPath;
  };

  fetch('/redischeck')
    .then(response => response.json())
    .then(data => updateStatus('redis-icon', data.status))
    .catch(error => console.error('Error fetching Redis status:', error));

  fetch('/dbcheck')
    .then(response => response.json())
    .then(data => updateStatus('db-icon', data.status))
    .catch(error => console.error('Error fetching Database status:', error));
});
