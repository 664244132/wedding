<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Sarabun:wght@200;300;400;500&display=swap');

    :root {
        --gold-primary: #c5a059;
        --gold-light: #e2c285;
        --dark-wedding: #4a3b2b;
        --soft-cream: #fdfaf5;
    }

    .wedding-nav-container {
        width: 100%;
        background: rgba(255, 255, 255, 0.85); 
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 2px solid rgba(197, 160, 89, 0.2);
        padding: 15px 0;
        position: fixed;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        transition: all 0.4s ease;
    }

    .wedding-nav-container.scrolled {
        padding: 10px 0;
        background: rgba(253, 250, 245, 0.98);
    }

    .wedding-nav {
        display: flex;
        justify-content: center;
        list-style: none;
        margin: 0;
        padding: 0;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .wedding-nav li { margin: 0 15px; position: relative; }

    .wedding-nav a {
        text-decoration: none;
        color: var(--dark-wedding);
        font-family: 'Sarabun', sans-serif;
        font-size: 14px;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        padding: 8px 5px;
        font-weight: 400;
        text-transform: uppercase;
    }

    .wedding-nav a:hover { 
        color: var(--gold-primary); 
        text-shadow: 0 0 1px rgba(197, 160, 89, 0.3);
    }

    .wedding-nav a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 1.5px;
        bottom: 0;
        left: 50%;
        background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateX(-50%);
    }

    .wedding-nav a:hover::after { width: 80%; }

    .nav-btn {
        border: 1.5px solid var(--gold-primary);
        padding: 8px 25px !important;
        border-radius: 30px;
        color: var(--gold-primary) !important;
        margin-left: 15px;
        font-weight: 500 !important;
        background: transparent;
        transition: all 0.4s ease !important;
    }

    .nav-btn:hover {
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-light));
        color: white !important;
        border-color: transparent;
        box-shadow: 0 5px 15px rgba(197, 160, 89, 0.3);
        transform: translateY(-2px);
    }

    .user-info {
        display: flex;
        align-items: center;
        padding: 5px 15px;
        border-right: 1px solid rgba(197, 160, 89, 0.3);
        margin-right: 5px;
    }

    .user-name-display {
        font-family: 'Sarabun', sans-serif;
        font-size: 13px;
        color: var(--dark-wedding);
        font-weight: 500;
    }

    .user-name-display i {
        color: var(--gold-primary);
        font-size: 16px;
    }

    .nav-logo {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--gold-primary);
        margin-right: 30px;
        text-decoration: none;
        letter-spacing: 2px;
    }
</style>

<nav class="wedding-nav-container" id="mainNav">
    <ul class="wedding-nav">
        <li><a href="index.php" class="nav-logo">ETERNAL LOVE</a></li>
        
        <li><a href="index.php">หน้าหลัก</a></li>
        <li><a href="services.php">บริการ</a></li> 
        <li><a href="packages.php">แพ็กเกจ</a></li>
        <li><a href="booking.php">จองคิว</a></li>
        <li><a href="gallery.php">แกลเลอรี</a></li>
        <li><a href="reviews.php">รีวิว</a></li>
        <li><a href="contact.php">ติดต่อเรา</a></li>

        <?php if (isset($_SESSION['username'])): ?>
            <li class="user-info">
                <span class="user-name-display">
                    <i class="fa-solid fa-crown me-2"></i> คุณ<?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
            </li>
            <li><a href="logout.php" class="nav-btn">ออกจากระบบ</a></li>
        <?php else: ?>
            <li><a href="login.php" class="nav-btn"><i class="fa-solid fa-lock me-2"></i>เข้าสู่ระบบ</a></li>
        <?php endif; ?>
    </ul>
</nav>

<script>
    window.onscroll = function() {
        const nav = document.getElementById('mainNav');
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    };
</script>