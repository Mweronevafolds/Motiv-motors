<?php
// Get the current car ID from the URL if it exists
$currentCarId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if we're on a car details page
$isCarPage = (basename($_SERVER['PHP_SELF']) == 'listing.php' && $currentCarId > 0);

?>

<style>
    .menu {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        justify-content: center;
        background: rgba(15, 12, 41, 0.95);
        backdrop-filter: blur(10px);
        padding: 0.8rem 2rem;
        border-radius: 50px;
        gap: 2rem;
        z-index: 1000;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        width: auto;
        max-width: 90%;
        margin: 0 auto;
    }
    
    .menu.is-hidden {
        transform: translateX(-50%) translateY(-100px);
        opacity: 0;
        pointer-events: none;
    }
    
    .menu .link {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #fff;
        transition: all 0.3s ease;
        padding: 0.5rem;
        border-radius: 12px;
        position: relative;
    }
    
    .menu .link:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .menu .link-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        margin-bottom: 0.25rem;
    }
    
    .menu .link-title {
        font-size: 0.7rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.8);
    }
    
    .menu .link svg {
        width: 20px;
        height: 20px;
    }
    
    @media (max-width: 768px) {
        .menu {
            width: 90%;
            padding: 0.8rem 1rem;
            gap: 0.5rem;
        }
        
        .menu .link {
            padding: 0.5rem 0.3rem;
        }
        
        .menu .link-title {
            font-size: 0.6rem;
        }
    }
</style>

<div class="menu">
    
    <a href="index.php" class="link">
        <span class="link-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="192" height="192" fill="currentColor" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"></rect><path d="M213.3815,109.61945,133.376,36.88436a8,8,0,0,0-10.76339.00036l-79.9945,72.73477A8,8,0,0,0,40,115.53855V208a8,8,0,0,0,8,8H208a8,8,0,0,0,8-8V115.53887A8,8,0,0,0,213.3815,109.61945Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path></svg>
        </span>
        <span class="link-title">Home</span>
    </a>

    <a href="inventory.php" class="link">
        <span class="link-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="192" height="192" fill="currentColor" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"></rect><circle cx="116" cy="116" r="84" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></circle><line x1="175.39356" y1="175.40039" x2="223.99414" y2="224.00098" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></line></svg>
        </span>
        <span class="link-title">Inventory</span>
    </a>

    <?php if ($isCarPage): ?>
    <a href="virtual_showroom.php?id=<?= $currentCarId ?>" class="link" id="showroom-link">
        <span class="link-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="192" height="192" fill="currentColor" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"></rect><path d="M48,104,128,56l80,48v88l-80,48-80-48Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path><path d="M48,104l80,48,80-48" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path><line x1="128" y1="152" x2="128" y2="232" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></line></svg>
        </span>
        <span class="link-title">3D Showroom</span>
    </a>
    <?php endif; ?>

    <a href="login.php" class="link">
        <span class="link-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="192" height="192" fill="currentColor" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"></rect><circle cx="128" cy="96" r="64" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="16"></circle><path d="M30.989,215.99064a112.03731,112.03731,0,0,1,194.02311.002" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path></svg>
        </span>
        <span class="link-title">Staff</span>
    </a>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const menu = document.querySelector('.menu');
    if (!menu) return;

    let lastScrollY = window.scrollY;
    let ticking = false;
    let isScrollingDown = false;
    const scrollThreshold = 50; // Pixels to scroll before hiding
    const scrollDelay = 500; // Delay in ms before hiding
    let hideTimeout;

    function updateNavbar() {
        const currentY = window.scrollY;
        isScrollingDown = currentY > lastScrollY;
        
        clearTimeout(hideTimeout);
        
        if (currentY > scrollThreshold) {
            if (isScrollingDown) {
                menu.classList.add('is-hidden');
            } else {
                menu.classList.remove('is-hidden');
                // Auto-hide after delay if not at top
                hideTimeout = setTimeout(() => {
                    if (window.scrollY > scrollThreshold) {
                        menu.classList.add('is-hidden');
                    }
                }, scrollDelay);
            }
        } else {
            // Always show when near top
            menu.classList.remove('is-hidden');
        }
        
        lastScrollY = currentY <= 0 ? 0 : currentY;
        ticking = false;
    }

    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(updateNavbar);
            ticking = true;
        }
    }, { passive: true });
    
    // Show on mouse move (desktop only)
    if (window.innerWidth > 768) {
        document.addEventListener('mousemove', function() {
            if (window.scrollY > scrollThreshold) {
                menu.classList.remove('is-hidden');
                clearTimeout(hideTimeout);
                hideTimeout = setTimeout(() => {
                    if (window.scrollY > scrollThreshold) {
                        menu.classList.add('is-hidden');
                    }
                }, 2000);
            }
        });
    }
});
</script>