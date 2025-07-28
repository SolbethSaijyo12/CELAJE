// Banner carousel functionality
let currentBannerIndex = 0;
const banners = document.querySelectorAll('.banner-slide');
const dots = document.querySelectorAll('.dot');
const totalBanners = banners.length;

function showBanner(index) {
    // Hide all banners
    banners.forEach(banner => banner.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    // Show current banner
    if (banners[index]) {
        banners[index].classList.add('active');
    }
    if (dots[index]) {
        dots[index].classList.add('active');
    }
}

function changeBanner(direction) {
    currentBannerIndex += direction;
    
    if (currentBannerIndex >= totalBanners) {
        currentBannerIndex = 0;
    } else if (currentBannerIndex < 0) {
        currentBannerIndex = totalBanners - 1;
    }
    
    showBanner(currentBannerIndex);
}

function currentBanner(index) {
    currentBannerIndex = index - 1;
    showBanner(currentBannerIndex);
}

// Auto-advance banner every 5 seconds
setInterval(() => {
    changeBanner(1);
}, 5000);

// Initialize first banner
document.addEventListener('DOMContentLoaded', () => {
    if (banners.length > 0) {
        showBanner(0);
    }
});