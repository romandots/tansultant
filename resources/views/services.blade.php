<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Monitor</title>
    <style>
        .status-icon {
            height: 20px;
            width: 20px;
        }
    </style>
</head>
<body>
<div id="redis-status"><img class="status-icon" id="redis-icon" src="/icons/green-check.svg" alt="status-icon"> Redis</div>
<div id="db-status"><img class="status-icon" id="db-icon" src="/icons/green-check.svg" alt="status-icon"> Database</div>

<script src="{{ asset('js/statusMonitor.js') }}"></script>
</body>
</html>
