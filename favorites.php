<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Garage | Motiv Motors</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <h1 style="text-align: center; margin-bottom: 10px;">MY GARAGE</h1>
        <p style="text-align: center; color: #aaa; margin-bottom: 50px;">Your curated collection of favorites.</p>

        <div id="favorites-container" class="car-grid">
            <p style="text-align: center; width: 100%; color: #666;">Loading your collection...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('favorites-container');
            const savedKeys = Object.keys(localStorage);
            const favoriteIds = [];

            savedKeys.forEach(key => {
                if (key.startsWith('fav-')) {
                    favoriteIds.push(key.replace('fav-', ''));
                }
            });

            if (favoriteIds.length === 0) {
                container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 50px; background: rgba(255,255,255,0.05); border-radius: 16px;"><h3>Your garage is empty.</h3><a href="inventory.php" class="btn btn-accent" style="margin-top:20px;">Browse Showroom</a></div>';
                return;
            }

            window.location.href = "view_favorites.php?ids=" + favoriteIds.join(',');
        });
    </script>
</body>
</html>
