<?php
/**
 * Template Name: Page Accueil
 */

get_header(); ?>

<style>
.hero-section .col-lg-6 img {
    width: 100%;
    height: 400px; /* Fixed height for both images */
    object-fit: cover; /* This will maintain aspect ratio while covering the container */
    margin-bottom: 20px;
}

.hero-section .image-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 20px;
    padding: 20px;
}

.hero-section .image-grid img {
    width: 100%;
    height: 450px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .hero-section .image-grid {
        grid-template-columns: 1fr;
    }
    
    .hero-section .image-grid img {
        height: 350px;
    }
}
</style>

<div class="container py-5">
    <!-- Hero Section -->
    <section class="section hero-section">
        <div class="card">
            <div class="row">
                <div class="col-lg-6">
                    <h1 class="section-title">Bienvenue à l'ESP</h1>
                    <p class="lead">L'école Supérieure Polytechnique est un établissement du Groupe Polytechnique (GP), qui est une structure qui regroupe un ensemble d'établissements de formation relevant de l'enseignement supérieur. Ce groupe est placé sous la double tutelle du Ministère de la Défense Nationale et du Ministère en charge de l'Enseignement supérieur.</p>
                    <p>Le groupe est composé de :</p>
                    <ul>
                        <li>L'École Supérieure Polytechnique (ESP)</li>
                        <li>Un Institut Préparatoire aux Grandes Ecoles d'Ingénieurs (IPGEI)</li>
                        <li>4 Instituts Supérieurs des Métiers (ISM)</li>
                    </ul>
                    
                </div>
                <div class="col-lg-6">
                    <div class="image-grid">
                        <img src="<?php echo get_theme_file_uri('esp_files/esp05(1).jpg'); ?>" alt="ESP Campus" class="img-fluid">
                        <img src="<?php echo get_template_directory_uri(); ?>/espimaeg.png" alt="ESP Image" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section values-section">
        <h2 class="section-title">Nos Valeurs</h2>
        <div class="grid">
            <div class="card">
                <h3>L'Excellence</h3>
                <p>Nous visons l'excellence dans tous nos domaines d'activité, de l'enseignement à la recherche.</p>
            </div>
            <div class="card">
                <h3>L'Innovation</h3>
                <p>Nous encourageons l'innovation et la créativité dans nos approches pédagogiques et nos projets de recherche.</p>
            </div>
            <div class="card">
                <h3>L'Équité</h3>
                <p>Nous promouvons l'équité et l'égalité des chances pour tous nos étudiants.</p>
            </div>
        </div>
    </section>

    <!-- Academic Programs -->
    <section class="section">
        <h2 class="section-title">Nos Formations</h2>
        <div class="grid">
            <div class="card">
                <h3>Institut Préparatoire (IPGEI)</h3>
                <p>Formation préparatoire d'excellence:</p>
                <ul>
                    <li>MPSI - Mathématiques, Physique et Sciences de l'Ingénieur</li>
                    <li>MP/PSI - Classes de deuxième année</li>
                    <li>PCSI - Physique, Chimie et Sciences de l'Ingénieur</li>
                </ul>
            </div>
            
            <div class="card">
                <h3>Cycle Ingénieur</h3>
                <p>Départements spécialisés:</p>
                <ul>
                    <li>Génie Civil (GC)</li>
                    <li>Génie Electrique (GE)</li>
                    <li>Génie Mécanique (GM)</li>
                    <li>Informatique, Réseaux et Télécommunications (IRT)</li>
                    <li>Mines, Pétrole et Gaz (MPG)</li>
                    <li>Statistique et Ingénierie de Données (SID)</li>
                </ul>
            </div>
            
            <div class="card">
                <h3>Instituts Supérieurs des Métiers</h3>
                <ul>
                    <li>ISM-BTPU - Bâtiment, Travaux Publics et Urbanisme</li>
                    <li>IS2M - Institut Supérieur des Métiers de la Mine</li>
                    <li>ISMS - Institut Supérieur des Métiers de la Statistique</li>
                    <li>ISME - Institut Supérieur des Métiers de l'Energie</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="section">
        <h2 class="section-title">Actualités</h2>
        <div class="grid">
            <div class="card">
                <img src="<?php echo get_theme_file_uri('esp_files/ensi2.PNG'); ?>" alt="ENSI Partnership" class="card-img-top">
                <div class="card-body">
                    <h3>Partenariat avec l'ENSI de Tunisie</h3>
                    <p>Signature d'un accord cadre entre l'ESP et l'ENSI de Tunisie pour renforcer la coopération académique.</p>
                </div>
            </div>
            
            <div class="card">
                <img src="<?php echo get_theme_file_uri('esp_files/gp12.jpeg'); ?>" alt="EMGA Visit" class="card-img-top">
                <div class="card-body">
                    <h3>Visite Officielle</h3>
                    <p>Le chef d'EMGA du Sénégal visite le Groupe Polytechnique, renforçant les liens de coopération.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Research and Innovation -->
    <section class="section">
        <h2 class="section-title">Recherche et Innovation</h2>
        <div class="grid">
            <div class="card">
                <img src="<?php echo get_theme_file_uri('esp_files/fp.PNG'); ?>" alt="Research" class="card-img-top">
                <h3>Pôles de Recherche</h3>
                <p>Nos laboratoires de recherche travaillent sur des projets innovants dans divers domaines technologiques.</p>
            </div>
            
            <div class="card">
                <img src="<?php echo get_theme_file_uri('esp_files/SNIM.jpg'); ?>" alt="Industry Partners" class="card-img-top">
                <h3>Partenariats Industriels</h3>
                <p>Collaboration étroite avec les acteurs majeurs de l'industrie pour des projets concrets.</p>
            </div>
        </div>
    </section>

    <!-- International Cooperation -->
    <section class="section">
        <h2 class="section-title">Coopération Internationale</h2>
        <div class="grid">
            <div class="card">
                <img src="<?php echo get_theme_file_uri('esp_files/Kinross_logo_0.png'); ?>" alt="International Partners" class="card-img-top">
                <h3>Partenaires Internationaux</h3>
                <p>Des partenariats académiques avec des institutions prestigieuses à travers le monde.</p>
            </div>
            
            <div class="card">
                <img src="<?php echo get_theme_file_uri('esp_files/mcm_logo(1).jpg'); ?>" alt="Student Exchange" class="card-img-top">
                <h3>Échanges Académiques</h3>
                <p>Programmes d'échange pour les étudiants et les enseignants.</p>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>
