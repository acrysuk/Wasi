// DEBUG: Verificar que el script se cargue
console.log('✅ scripts.js cargado correctamente');

// Header scroll effect - VERSIÓN MEJORADA
function initHeaderScroll() {
    const header = document.querySelector('header');
    
    if (!header) {
        console.log('❌ No se encontró el header');
        return;
    }
    
    console.log('✅ Header encontrado, inicializando scroll effect');
    
    function handleScroll() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
    
    // Ejecutar inmediatamente para establecer estado inicial
    handleScroll();
    
    // Agregar event listener
    window.addEventListener('scroll', handleScroll);
}

// Mobile menu toggle
function initMobileMenu() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const nav = document.querySelector('nav');
    
    if (mobileMenuBtn && nav) {
        mobileMenuBtn.addEventListener('click', function() {
            nav.classList.toggle('active');
            console.log('📱 Menú móvil toggled');
        });
    }
}

// Hero slider
function initHeroSlider() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    
    if (slides.length === 0 || dots.length === 0) {
        console.log('❌ No se encontró el hero slider');
        return;
    }
    
    console.log('✅ Hero slider encontrado');
    
    let currentSlide = 0;

    function showSlide(n) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        currentSlide = (n + slides.length) % slides.length;
        
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
        });
    });

    // Auto slide
    setInterval(() => {
        showSlide(currentSlide + 1);
    }, 5000);
}

// Slider de novedades
function initNovedadesSlider() {
    const sliderContainer = document.querySelector('.novedades-container');
    const prevBtn = document.querySelector('.slider-btn.prev');
    const nextBtn = document.querySelector('.slider-btn.next');
    
    if (sliderContainer && prevBtn && nextBtn) {
        const itemWidth = 320;
        
        prevBtn.addEventListener('click', function() {
            sliderContainer.scrollBy({
                left: -itemWidth,
                behavior: 'smooth'
            });
        });
        
        nextBtn.addEventListener('click', function() {
            sliderContainer.scrollBy({
                left: itemWidth,
                behavior: 'smooth'
            });
        });
        
        console.log('✅ Slider de novedades inicializado');
    }
}

// Smooth scroll for navigation links
function initSmoothScroll() {
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const nav = document.querySelector('nav');
                    if (nav) {
                        nav.classList.remove('active');
                    }
                }
            }
        });
    });
    
    console.log('✅ Smooth scroll inicializado');
}

// Animación de aparición para los servicios
function initServiciosAnimation() {
    const servicios = document.querySelectorAll('.servicio-card');
    
    if (servicios.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    servicios.forEach(servicio => {
        servicio.style.opacity = '0';
        servicio.style.transform = 'translateY(20px)';
        servicio.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(servicio);
    });
}

// ANIMACIÓN DE CONTADORES
function initCounters() {
    const counters = document.querySelectorAll('.stat-number');
    let countersAnimated = false;

    if (counters.length === 0) return;

    function animateCounter(counter) {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = Math.ceil(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
                if (target === 5000) {
                    counter.textContent = '5.000';
                }
            }
        };
        
        updateCounter();
    }

    function animateAllCounters() {
        if (countersAnimated) return;
        
        counters.forEach(counter => {
            animateCounter(counter);
        });
        
        countersAnimated = true;
    }

    // Intersection Observer para contadores
    const aboutSection = document.querySelector('.about-section');
    
    if (aboutSection) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !countersAnimated) {
                    setTimeout(animateAllCounters, 100);
                }
            });
        }, { threshold: 0.2 });

        sectionObserver.observe(aboutSection);
    }

    // Fallback para contadores
    function checkCountersVisibility() {
        if (countersAnimated) return;
        
        const statsSection = document.querySelector('.about-stats');
        if (!statsSection) return;
        
        const rect = statsSection.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        
        const isVisible = rect.top <= windowHeight - 100 && rect.bottom >= 100;
        
        if (isVisible) {
            animateAllCounters();
        }
    }

    window.addEventListener('load', function() {
        setTimeout(function() {
            if (!countersAnimated) {
                console.log('⏰ Activando contadores por timeout');
                animateAllCounters();
            }
        }, 4000);
    });

    window.addEventListener('scroll', checkCountersVisibility);
    setTimeout(checkCountersVisibility, 500);
}

// Inicializar todas las funciones cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM completamente cargado, inicializando scripts...');
    
    initHeaderScroll();
    initMobileMenu();
    initHeroSlider();
    initNovedadesSlider();
    initSmoothScroll();
    initServiciosAnimation();
    initCounters();
    
    console.log('🎉 Todos los scripts inicializados correctamente');
});

// Fallback en caso de que DOMContentLoaded ya haya ocurrido
if (document.readyState === 'loading') {
    console.log('📄 DOM aún cargando...');
} else {
    console.log('⚡ DOM ya está listo, ejecutando scripts inmediatamente');
    document.dispatchEvent(new Event('DOMContentLoaded'));
}
