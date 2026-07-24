<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Gaming Store</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Loading Screen - FAST -->
<div id="preloader">
    <div class="loader"></div>
    <div class="loader-text">Loading</div>
</div>

<!-- FAST HIDE - 800ms mein hide ho jaye -->
<script>
(function() {
    var preloader = document.getElementById('preloader');
    if (preloader) {
        // Page load hone par hide
        window.addEventListener('load', function() {
            setTimeout(function() {
                preloader.classList.add('hide');
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 400);
            }, 400); // Sirf 400ms wait
        });
        
        // Emergency hide after 1.5 seconds (agar kuch load na ho)
        setTimeout(function() {
            if (!preloader.classList.contains('hide')) {
                preloader.classList.add('hide');
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 400);
            }
        }, 1500);
    }
})();
</script>