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

// Prevenir la propagación del scroll en el submenú
function initSubmenuScroll() {
    const submenus = document.querySelectorAll('.submenu');
    
    submenus.forEach(submenu => {
        // Prevenir que el scroll se propague al fondo
        submenu.addEventListener('wheel', function(e) {
            // Si el scroll llega al tope o al fondo, permitir que el evento continúe
            const isScrollingDown = e.deltaY > 0;
            const isScrollingUp = e.deltaY < 0;
            
            const isAtTop = this.scrollTop === 0;
            const isAtBottom = this.scrollTop + this.clientHeight >= this.scrollHeight - 1;
            
            if ((isScrollingUp && isAtTop) || (isScrollingDown && isAtBottom)) {
                // Permitir que el scroll continúe al fondo
                return;
            }
            
            // Prevenir el comportamiento por defecto y detener la propagación
            e.preventDefault();
            e.stopPropagation();
        }, { passive: false });
        
        // También prevenir touchmove en dispositivos táctiles
        submenu.addEventListener('touchmove', function(e) {
            e.stopPropagation();
        }, { passive: false });
    });
}

// Popup del sorteo para asociados (VERSIÓN COMPLETA CON ESCAPE)
function initPopupSorteo() {
    const popup = document.getElementById('popupSorteo');
    if (!popup) return;
    
    const closeButtons = document.querySelectorAll('.popup-close, .btn-popup-close');
    const secondaryButton = document.querySelector('.btn-popup-secondary');
    
    // Función para cerrar el popup
    function closePopup() {
        popup.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Función para abrir el popup
    function openPopup() {
        popup.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    // Mostrar popup después de 2 segundos
    setTimeout(function() {
        openPopup();
    }, 2000);
    
    // Cerrar popup con botones
    closeButtons.forEach(button => {
        button.addEventListener('click', closePopup);
    });
    
    // Botón secundario
    if (secondaryButton) {
        secondaryButton.addEventListener('click', function() {
            window.location.href = '../pages/bases-condiciones.html';
        });
    }
    
    // Cerrar al hacer click fuera
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            closePopup();
        }
    });
    
    // Cerrar con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && popup.style.display === 'flex') {
            closePopup();
        }
    });
}

// MENÚ MÓVIL COMPLETO (reemplaza todas las versiones anteriores)
function initMobileMenu() {
    // Elementos del menú móvil
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    const overlay = document.querySelector('.overlay');
    
    if (!mobileMenuBtn || !mobileMenu || !mobileMenuClose || !overlay) {
        console.error('Elementos del menú móvil no encontrados');
        return;
    }
    
    // Abrir menú móvil
    mobileMenuBtn.addEventListener('click', function() {
        mobileMenu.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevenir scroll
    });
    
    // Cerrar menú móvil
    mobileMenuClose.addEventListener('click', function() {
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = ''; // Restaurar scroll
    });
    
    // Cerrar menú al hacer clic en overlay
    overlay.addEventListener('click', function() {
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });
    
    // Toggle submenús en móvil
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    
    if (submenuToggles.length > 0) {
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const parentLi = this.closest('li');
                const submenu = parentLi.querySelector('.mobile-submenu');
                const icon = this.querySelector('i');
                
                if (!submenu) return;
                
                if (submenu.classList.contains('active')) {
                    submenu.classList.remove('active');
                    if (icon) {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                } else {
                    // Cerrar otros submenús abiertos
                    document.querySelectorAll('.mobile-submenu.active').forEach(activeSubmenu => {
                        if (activeSubmenu !== submenu) {
                            activeSubmenu.classList.remove('active');
                            const activeIcon = activeSubmenu.parentElement.querySelector('.submenu-toggle i');
                            if (activeIcon) {
                                activeIcon.classList.remove('fa-chevron-up');
                                activeIcon.classList.add('fa-chevron-down');
                            }
                        }
                    });
                    
                    submenu.classList.add('active');
                    if (icon) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    }
                }
            });
        });
    }
    
    // Cerrar menú al hacer clic en enlace móvil
    const mobileLinks = document.querySelectorAll('.mobile-nav a');
    if (mobileLinks.length > 0) {
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }
    
    // Evitar que se cierren los submenús al hacer clic dentro de ellos
    document.querySelectorAll('.cnz-sub-menu').forEach(submenu => {
        submenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
}

// Smooth scroll para todos los enlaces internos
function initSmoothScroll() {
    const allInternalLinks = document.querySelectorAll('a[href^="#"]');
    
    if (allInternalLinks.length > 0) {
        allInternalLinks.forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Cerrar menú móvil si está abierto
                    const mobileMenu = document.querySelector('.mobile-menu');
                    const overlay = document.querySelector('.overlay');
                    
                    if (mobileMenu && mobileMenu.classList.contains('active')) {
                        mobileMenu.classList.remove('active');
                        if (overlay) overlay.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }
            });
        });
    }
    
    console.log('✅ Smooth scroll inicializado');
}

// Inicializar todas las funciones cuando el DOM esté listo
function initAllScripts() {
    console.log('🚀 DOM completamente cargado, inicializando scripts...');
    
    initHeaderScroll();
    initMobileMenu();
    initHeroSlider();
    initNovedadesSlider();
    initSmoothScroll();
    initServiciosAnimation();
    initCounters();
    initSubmenuScroll();
    initPopupSorteo();
    
    console.log('🎉 Todos los scripts inicializados correctamente');
}

// Evento principal para cargar todos los scripts
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllScripts);
} else {
    // Si el DOM ya está listo, ejecutar inmediatamente
    console.log('⚡ DOM ya está listo, ejecutando scripts inmediatamente');
    initAllScripts();
}