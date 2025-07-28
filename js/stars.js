// Animated stars for footer
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('starsCanvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = canvas.offsetHeight;
    }
    
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
    
    const stars = [];
    const starCount = 150;
    const maxSpeed = 2;
    
    // Create stars
    for (let i = 0; i < starCount; i++) {
        stars.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            speed: Math.random() * maxSpeed + 0.5,
            radius: Math.random() * 1.5 + 0.5,
            opacity: Math.random() * 0.8 + 0.2
        });
    }
    
    function animate() {
        // Clear canvas with gradient background
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, '#674e82');
        gradient.addColorStop(1, '#523d68');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Draw and update stars
        stars.forEach(star => {
            ctx.globalAlpha = star.opacity;
            ctx.fillStyle = 'white';
            ctx.beginPath();
            ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
            ctx.fill();
            
            // Move star
            star.y += star.speed;
            
            // Reset star position when it goes off screen
            if (star.y > canvas.height + star.radius) {
                star.y = -star.radius;
                star.x = Math.random() * canvas.width;
            }
        });
        
        ctx.globalAlpha = 1;
        requestAnimationFrame(animate);
    }
    
    animate();
});