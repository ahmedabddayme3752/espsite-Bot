<?php
/**
 * Template Name: Page Formations
 */

get_header(); ?>

<style>
.hero {
    position: relative;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    padding: 100px 0;
    color: white;
    text-align: center;
    margin-top: 30px;
}

.hero .container {
    position: relative;
    z-index: 2;
}

.hero h1 {
    font-size: 3em;
    margin-bottom: 20px;
    color: white;
}

.hero p {
    font-size: 1.2em;
    max-width: 800px;
    margin: 0 auto;
    color: white;
}
</style>

<div class="hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?php echo get_template_directory_uri(); ?>/espimaeg.png');">
    <div class="container">
        <h1>Nos Formations</h1>
        <p>Découvrez l'excellence académique à l'ESP à travers nos programmes de formation innovants et diversifiés</p>
    </div>
</div>

<div class="container py-5">
    <!-- Introduction -->
    <section class="section">
        <div class="card">
            <h1 class="section-title">Nos Formations</h1>
            <p class="lead">L'ESP offre un large éventail de formations d'excellence, structurées en trois grands pôles : le Cycle Ingénieur, l'Institut Préparatoire, et les Instituts Supérieurs des Métiers.</p>
        </div>
    </section>

    <!-- Pôle Scientifique et Technique -->
    <section class="section" id="pole-scientifique">
        <div class="card">
            <h2 class="section-title">Pôle Scientifique et Technique</h2>
            <p class="lead">Formation scientifique et technique de haut niveau pour les futurs ingénieurs.</p>
            
            <div class="grid">
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/esp_0.png'); ?>" alt="Sciences Fondamentales" class="card-img-top">
                    <div class="card-body">
                        <h3>Sciences Fondamentales</h3>
                        <ul>
                            <li>Mathématiques Appliquées</li>
                            <li>Physique pour l'Ingénieur</li>
                            <li>Chimie Industrielle</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/fp.PNG'); ?>" alt="Sciences de l'Ingénieur" class="card-img-top">
                    <div class="card-body">
                        <h3>Sciences de l'Ingénieur</h3>
                        <ul>
                            <li>Mécanique et Énergétique</li>
                            <li>Électronique et Automatique</li>
                            <li>Informatique Industrielle</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Départements du Cycle Ingénieur -->
    <section class="section" id="cycle-ingenieur">
        <div class="card">
            <h2 class="section-title">Départements du Cycle Ingénieur</h2>
            <p class="lead">Sept départements spécialisés offrant une formation complète d'ingénieur sur 3 ans.</p>
            
            <div class="grid">
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('Formation – Ecole superieure polytechnique_files/77b46739e1_50148266_ingenieur-genie-civil2.jpg'); ?>" alt="Génie Civil" class="card-img-top">
                    <div class="card-body">
                        <h3>Génie Civil (GC)</h3>
                        <h4>Domaines d'expertise</h4>
                        <ul>
                            <li>Construction et Ouvrages d'Art</li>
                            <li>Hydraulique et Ressources en Eau</li>
                            <li>Géotechnique et Fondations</li>
                            <li>Environnement et Développement Durable</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('Formation – Ecole superieure polytechnique_files/Genie-elec-1.jpg'); ?>" alt="Génie Electrique" class="card-img-top">
                    <div class="card-body">
                        <h3>Génie Electrique (GE)</h3>
                        <h4>Spécialisations</h4>
                        <ul>
                            <li>Électrotechnique et Haute Tension</li>
                            <li>Électronique de Puissance</li>
                            <li>Automatismes Industriels</li>
                            <li>Énergies Renouvelables</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/gp12.jpeg'); ?>" alt="Génie Industriel" class="card-img-top">
                    <div class="card-body">
                        <h3>Génie Industriel (GI)</h3>
                        <h4>Compétences Clés</h4>
                        <ul>
                            <li>Gestion de Production</li>
                            <li>Logistique Industrielle</li>
                            <li>Qualité et Maintenance</li>
                            <li>Management de Projets</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/1 (2).jpeg'); ?>" alt="Génie Mécanique" class="card-img-top">
                    <div class="card-body">
                        <h3>Génie Mécanique (GM)</h3>
                        <h4>Axes de Formation</h4>
                        <ul>
                            <li>Conception Mécanique</li>
                            <li>Fabrication et Production</li>
                            <li>Maintenance Industrielle</li>
                            <li>Mécatronique</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/1 (3).jpeg'); ?>" alt="IRT" class="card-img-top">
                    <div class="card-body">
                        <h3>Informatique, Réseaux et Télécommunications (IRT)</h3>
                        <h4>Technologies</h4>
                        <ul>
                            <li>Développement Logiciel</li>
                            <li>Réseaux et Sécurité</li>
                            <li>Systèmes Embarqués</li>
                            <li>Intelligence Artificielle</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('Formation – Ecole superieure polytechnique_files/IMG_8191-1.jpg'); ?>" alt="MPG" class="card-img-top">
                    <div class="card-body">
                        <h3>Mines, Pétrole et Gaz (MPG)</h3>
                        <h4>Secteurs</h4>
                        <ul>
                            <li>Exploration Minière</li>
                            <li>Exploitation Pétrolière</li>
                            <li>Traitement des Minerais</li>
                            <li>Géologie Appliquée</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('Formation – Ecole superieure polytechnique_files/Screenshot-2024-04-17-093412-1-1.jpg'); ?>" alt="SID" class="card-img-top">
                    <div class="card-body">
                        <h3>Statistique et Ingénierie de Données (SID)</h3>
                        <h4>Applications</h4>
                        <ul>
                            <li>Analyse de Données</li>
                            <li>Science des Données</li>
                            <li>Modélisation Statistique</li>
                            <li>Big Data et Analytics</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Institut Préparatoire -->
    <section class="section" id="ipgei">
        <div class="card">
            <h2 class="section-title">Institut Préparatoire (IPGEI)</h2>
            <p class="lead">Formation intensive préparant aux études d'ingénieur</p>
            
            <div class="grid">
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/1 (4).jpeg'); ?>" alt="IPGEI" class="card-img-top">
                    <div class="card-body">
                        <h3>Première Année</h3>
                        <h4>MPSI</h4>
                        <ul>
                            <li>Mathématiques Approfondies</li>
                            <li>Physique Fondamentale</li>
                            <li>Sciences de l'Ingénieur</li>
                            <li>Informatique</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/ensi2.PNG'); ?>" alt="IPGEI" class="card-img-top">
                    <div class="card-body">
                        <h3>Deuxième Année</h3>
                        <h4>MP/PSI</h4>
                        <ul>
                            <li>Mathématiques</li>
                            <li>Physique</li>
                            <li>Sciences Industrielles</li>
                            <li>Chimie (PSI)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Instituts Supérieurs des Métiers -->
    <section class="section" id="ism">
        <div class="card">
            <h2 class="section-title">Instituts Supérieurs des Métiers (ISM)</h2>
            <p class="lead">Formation professionnalisante de niveau BAC+3</p>
            
            <div class="grid">
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/esp05(1).jpg'); ?>" alt="ISM-BTPU" class="card-img-top">
                    <div class="card-body">
                        <h3>ISM-BTPU</h3>
                        <h4>Bâtiment, Travaux Publics et Urbanisme</h4>
                        <ul>
                            <li>Construction Bâtiment</li>
                            <li>Infrastructures</li>
                            <li>Urbanisme</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/SNIM.jpg'); ?>" alt="IS2M" class="card-img-top">
                    <div class="card-body">
                        <h3>IS2M</h3>
                        <h4>Métiers de la Mine</h4>
                        <ul>
                            <li>Exploitation Minière</li>
                            <li>Géologie Minière</li>
                            <li>Traitement du Minerai</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/insa_0.png'); ?>" alt="ISMS" class="card-img-top">
                    <div class="card-body">
                        <h3>ISMS</h3>
                        <h4>Métiers de la Statistique</h4>
                        <ul>
                            <li>Statistique Appliquée</li>
                            <li>Analyse de Données</li>
                            <li>Études Statistiques</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <img src="<?php echo get_theme_file_uri('esp_files/Kinross_logo_0.png'); ?>" alt="ISME" class="card-img-top">
                    <div class="card-body">
                        <h3>ISME</h3>
                        <h4>Métiers de l'Energie</h4>
                        <ul>
                            <li>Production d'Énergie</li>
                            <li>Distribution Électrique</li>
                            <li>Énergies Renouvelables</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>