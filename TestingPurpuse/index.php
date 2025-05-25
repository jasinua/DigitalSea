<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Help Feature with F1</title>
  <style>
    /* Modal style */
    #helpModal {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: white;
      padding: 20px;
      border: 1px solid #ccc;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      z-index: 9999;
    }

    #helpModal h2 {
      margin-top: 0;
    }

    #helpModal button {
      margin-top: 10px;
    }
  </style>
</head>
<body>

  <h1>Press F1 for Help</h1>
  
  <!-- Help Modal -->
  <div id="helpModal">
    <h2>Help</h2>
    <p>This is your help section. Add your help content here.</p>
    <button onclick="closeHelp()">Close</button>
  </div>

  <script>
    // Function to open the help modal
    function openHelp() {
      document.getElementById('helpModal').style.display = 'block';
    }

    // Function to close the help modal
    function closeHelp() {
      document.getElementById('helpModal').style.display = 'none';
    }

    // Listen for the F1 key press
    window.addEventListener('keydown', function(event) {
      if (event.key === 'F1') {
        event.preventDefault(); // Prevent the default F1 action
        openHelp(); // Show help modal
      }
    });
  </script>
  
</body>
</html>
