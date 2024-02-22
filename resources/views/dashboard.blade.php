<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Montior - {{ config('app.name') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/split.js@1.6.2/dist/split.min.js"></script>
    <style>
        #split-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #top-pane {
            max-height: 70px;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
<div id="split-container">
    <div id="top-pane">
        <iframe src="{{ route('monitor.services') }}"></iframe>
    </div>
    <div id="middle-pane">
        <iframe src="{{ url(config('websockets.path')) }}"></iframe>
    </div>
    <div id="bottom-pane">
        <iframe src="{{ url(config('horizon.path')) }}"></iframe>
    </div>
</div>

<script>
  Split(['#top-pane', '#middle-pane', '#bottom-pane'], {
    direction: 'vertical',
    gutterSize: 8,
    cursor: 'row-resize'
  });
</script>
</body>
</html>
