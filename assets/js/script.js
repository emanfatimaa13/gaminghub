// ============================================
// GAMING STORE - MAIN JAVASCRIPT
// ============================================

// ===== 1. LOADING SCREEN - FAST =====
(function() {
    var preloader = document.getElementById('preloader');
    if (preloader) {
        window.addEventListener('load', function() {
            setTimeout(function() {
                preloader.classList.add('hide');
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 400);
            }, 400);
        });
    }
})();

// ===== 2. DARK/LIGHT MODE - WORKING =====
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) {
        console.warn('Theme toggle button not found');
        return;
    }

    // Get saved theme or default to dark
    const savedTheme = localStorage.getItem('theme') || 'dark';
    
    // Apply theme immediately
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    // Toggle theme on click
    themeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
        
        console.log('Theme changed to:', newTheme);
    });
});

function updateThemeIcon(theme) {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) return;
    
    const icon = themeToggle.querySelector('i');
    if (!icon) return;
    
    if (theme === 'dark') {
        icon.className = 'fas fa-moon';
        themeToggle.title = 'Switch to Light Mode';
    } else {
        icon.className = 'fas fa-sun';
        themeToggle.title = 'Switch to Dark Mode';
    }
}

// ===== 3. AOS ANIMATION - FIXED =====
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 600,
            easing: 'ease-out',
            once: true,
            offset: 80,
            disable: function() {
                return window.innerWidth < 768;
            }
        });
        console.log('AOS initialized successfully');
    } else {
        console.warn('AOS not loaded');
    }
});

// ===== 4. ADD TO CART =====
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.product;
            if (!productId) return;
            
            showToast('Adding to cart...', 'info');
            
            fetch('ajax/add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.cartCount);
                    showToast('Added to cart! 🎮', 'success');
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    showToast(data.message || 'Failed to add', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Something went wrong', 'error');
            });
        });
    });
});

// ===== 5. UPDATE CART COUNT =====
function updateCartCount(count) {
    const cartLink = document.querySelector('.fa-shopping-cart')?.parentElement;
    if (!cartLink) return;
    
    let badge = cartLink.querySelector('.badge');
    if (count > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
            cartLink.appendChild(badge);
        }
        badge.textContent = count;
        badge.style.display = 'inline';
    } else if (badge) {
        badge.style.display = 'none';
    }
}

// ===== 6. TOAST NOTIFICATIONS =====
function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.custom-toast').forEach(t => t.remove());
    
    const colors = {
        success: '#00d47e',
        error: '#ff4757',
        warning: '#ffb800',
        info: '#00d2ff'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-circle',
        info: 'fa-info-circle'
    };
    
    const toast = document.createElement('div');
    toast.className = 'custom-toast';
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a3a' : '#ffffff'};
        color: ${document.documentElement.getAttribute('data-theme') === 'dark' ? '#ffffff' : '#1a1a2e'};
        padding: 14px 22px;
        border-radius: 12px;
        border-left: 4px solid ${colors[type] || colors.success};
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        z-index: 99999;
        font-size: 0.9rem;
        max-width: 350px;
        animation: slideInRight 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    `;
    
    toast.innerHTML = `
        <i class="fas ${icons[type] || icons.success}" style="color: ${colors[type] || colors.success}; font-size: 1.1rem;"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add toast animation styles
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(toastStyles);

// ===== 7. SCROLL TO TOP =====
document.addEventListener('DOMContentLoaded', function() {
    const scrollBtn = document.createElement('button');
    scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollBtn.className = 'scroll-top-btn';
    scrollBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #6c5ce7, #00d2ff);
        color: white;
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        font-size: 18px;
        cursor: pointer;
        display: none;
        z-index: 999;
        box-shadow: 0 4px 20px rgba(108,92,231,0.4);
        transition: all 0.3s ease;
    `;
    
    document.body.appendChild(scrollBtn);
    
    window.addEventListener('scroll', function() {
        scrollBtn.style.display = window.scrollY > 400 ? 'block' : 'none';
    });
    
    scrollBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

console.log('✅ Gaming Store JS loaded successfully!');
console.log('Theme:', document.documentElement.getAttribute('data-theme'));
// ===== FAQ ACCORDION =====
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.faq-question').forEach(item => {
        item.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const icon = this.querySelector('i');
            
            // Close all other FAQs
            document.querySelectorAll('.faq-answer').forEach(a => {
                if (a !== answer) {
                    a.classList.remove('open');
                    a.previousElementSibling.classList.remove('active');
                }
            });
            
            // Toggle current FAQ
            answer.classList.toggle('open');
            this.classList.toggle('active');
        });
    });
});
// ===== THEME TOGGLE - SIMPLE & WORKING =====
function toggleTheme() {
    var html = document.documentElement;
    var current = html.getAttribute('data-theme');
    var next = current === 'dark' ? 'light' : 'dark';
    
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    
    // Update icon
    var btn = document.getElementById('themeToggle');
    if (btn) {
        var icon = btn.querySelector('i');
        if (icon) {
            icon.className = next === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        }
    }
    
    console.log('🌓 Theme changed to:', next);
}

// Apply saved theme on load
(function() {
    var saved = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
    
    var btn = document.getElementById('themeToggle');
    if (btn) {
        var icon = btn.querySelector('i');
        if (icon) {
            icon.className = saved === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        }
    }
})();