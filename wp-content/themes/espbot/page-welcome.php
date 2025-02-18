<?php
/*
Template Name: Welcome Page
*/

get_header(); ?>

<div class="welcome-container" dir="auto">
    <div class="welcome-content">
        <div class="language-selector">
            <button class="lang-btn active" data-lang="fr">🇫🇷 Français</button>
            <button class="lang-btn" data-lang="ar">🇲🇷 العربية</button>
            <button class="lang-btn" data-lang="en">🇬🇧 English</button>
        </div>
        <div class="logo-container">
            <div class="robot-avatar">
                <img src="<?php echo get_template_directory_uri(); ?>/images/esplogo.png" alt="EspBot Logo">
            </div>
        </div>
        <h1>
            <span class="translate" data-fr="Bienvenue sur" data-ar="مرحباً بك في" data-en="Welcome to">Bienvenue sur</span>
            <span class="highlight">EspBot</span>
        </h1>
        <p class="subtitle translate" 
           data-fr="Votre Assistant de l'École Supérieure Polytechnique" 
           data-ar="مساعدك في المدرسة العليا متعددة التقنيات" 
           data-en="Your École Supérieure Polytechnique Assistant">Votre Assistant de l'École Supérieure Polytechnique</p>
        <div class="features">
            <div class="feature-item">
                <i class="fas fa-language"></i>
                <span class="translate" 
                      data-fr="Support Multilingue" 
                      data-ar="دعم متعدد اللغات" 
                      data-en="Multilingual Support">Support Multilingue</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-university"></i>
                <span class="translate" 
                      data-fr="Informations de l'ESP" 
                      data-ar="معلومات المدرسة العليا متعددة التقنيات" 
                      data-en="ESP Information">Informations de l'ESP</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-clock"></i>
                <span class="translate" 
                      data-fr="Disponible 24/7" 
                      data-ar="متوفر على مدار الساعة" 
                      data-en="24/7 Available">Disponible 24/7</span>
            </div>
        </div>
        <a href="<?php echo get_permalink(get_page_by_path('chat')); ?>" class="chat-button">
            <i class="fas fa-comments"></i>
            <span class="translate" 
                  data-fr="Commencer la Discussion" 
                  data-ar="ابدأ المحادثة الآن" 
                  data-en="Start Chatting">Commencer la Discussion</span>
        </a>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

:root {
    --esp-green: #006838;
    --esp-yellow: #FDB913;
    --esp-light-green: #008d4c;
    --esp-light-yellow: #ffe066;
}

.welcome-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    background: linear-gradient(135deg, var(--esp-green) 0%, var(--esp-light-green) 100%);
    font-family: 'Poppins', sans-serif;
}

.welcome-content {
    background: rgba(255, 255, 255, 0.95);
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 800px;
    width: 100%;
}

.robot-avatar {
    width: 150px;
    height: 150px;
    margin: 0 auto 2rem;
}

.robot-avatar img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

h1 {
    font-size: 2.5rem;
    color: var(--esp-green);
    margin-bottom: 1rem;
}

.highlight {
    color: var(--esp-yellow);
    font-weight: 700;
}

.subtitle {
    font-size: 1.5rem;
    color: #666;
    margin-bottom: 3rem;
}

.features {
    display: flex;
    justify-content: space-around;
    gap: 2rem;
    margin-bottom: 3rem;
}

.feature-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.feature-item i {
    font-size: 2rem;
    color: var(--esp-green);
}

.feature-item span {
    color: #444;
}

.chat-button {
    display: inline-flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 2rem;
    background: var(--esp-yellow);
    color: var(--esp-green);
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.chat-button:hover {
    background: var(--esp-light-yellow);
    transform: translateY(-2px);
}

.language-selector {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.lang-btn {
    padding: 0.8rem 1.2rem;
    border: 2px solid var(--esp-green);
    border-radius: 10px;
    background: white;
    color: var(--esp-green);
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
}

.lang-btn[data-lang="ar"] {
    font-family: 'Noto Sans Arabic', sans-serif;
}

.lang-btn.active {
    background: var(--esp-green);
    color: white;
}

.lang-btn:hover {
    background: var(--esp-light-green);
    color: white;
}

[dir="rtl"] .welcome-container {
    font-family: 'Noto Sans Arabic', sans-serif;
}

[dir="rtl"] .features {
    flex-direction: row-reverse;
}

@media (max-width: 768px) {
    .features {
        flex-direction: column;
    }
    
    .welcome-content {
        padding: 2rem;
    }
    
    .subtitle {
        font-size: 1.2rem;
    }
    
    .language-selector {
        flex-wrap: wrap;
    }
    
    .lang-btn {
        flex: 1;
        min-width: 120px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.welcome-container');
    const langButtons = document.querySelectorAll('.lang-btn');
    const translateElements = document.querySelectorAll('.translate');
    
    function updateLanguage(lang) {
        container.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
        
        translateElements.forEach(el => {
            if (el.dataset[lang] && !el.classList.contains('highlight')) {
                el.textContent = el.dataset[lang];
            }
        });
        
        langButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.lang === lang);
        });
        
        localStorage.setItem('espbot-language', lang);
        document.documentElement.lang = lang;
        
        // Dispatch event for other components
        window.dispatchEvent(new CustomEvent('languageChange', { detail: { language: lang } }));
    }
    
    langButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.dataset.lang;
            updateLanguage(lang);
        });
    });
    
    const savedLang = localStorage.getItem('espbot-language') || 'fr';
    updateLanguage(savedLang);
});
</script>

<?php get_footer(); ?>
