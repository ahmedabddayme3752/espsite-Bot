<?php
/**
 * Template Name: Page Présentation
 */

get_header(); ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="section-title">Présentation de l'ESP</h1>
            
            <div class="card mb-5">
                <h2>L'École Supérieure Polytechnique (ESP)</h2>
                <p class="lead">L'École Supérieure Polytechnique (ESP) est un établissement prestigieux du Groupe Polytechnique (GP), placé sous la double tutelle du Ministère de la Défense Nationale et du Ministère en charge de l'Enseignement supérieur.</p>
                
                <div class="school-image mb-4">
                    <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('large', array('class' => 'img-fluid rounded')); ?>
                    <?php endif; ?>
                </div>
                
                <div class="structure-info">
                    <h3>Structure du Groupe Polytechnique</h3>
                    <p>Le groupe est composé de plusieurs établissements d'excellence :</p>
                    <ul>
                        <li><strong>L'École Supérieure Polytechnique (ESP)</strong> - Le cœur de la formation d'ingénieurs</li>
                        <li><strong>L'Institut Préparatoire aux Grandes Écoles d'Ingénieurs (IPGEI)</strong> - Préparation aux études d'ingénieur</li>
                        <li><strong>4 Instituts Supérieurs des Métiers (ISM)</strong> - Formation technique spécialisée</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-5">
                <h2>Notre Mission</h2>
                <div class="grid">
                    <div class="card">
                        <h3>Excellence Académique</h3>
                        <p>Former des ingénieurs et techniciens hautement qualifiés capables de répondre aux défis technologiques modernes.</p>
                    </div>
                    
                    <div class="card">
                        <h3>Innovation & Recherche</h3>
                        <p>Promouvoir la recherche et l'innovation technologique pour contribuer au développement national.</p>
                    </div>
                    
                    <div class="card">
                        <h3>Partenariats Stratégiques</h3>
                        <p>Développer des collaborations avec le secteur industriel et les institutions académiques internationales.</p>
                    </div>
                </div>
            </div>

            <div class="card mb-5">
                <h2>Nos Départements</h2>
                <p class="lead">L'ESP abrite 7 départements spécialisés offrant une formation complète et diversifiée :</p>
                <div class="grid">
                    <div class="card">
                        <h3>GC-HE</h3>
                        <p>Génie Civil, Hydraulique et Environnement</p>
                    </div>
                    
                    <div class="card">
                        <h3>GE</h3>
                        <p>Génie Electrique</p>
                    </div>
                    
                    <div class="card">
                        <h3>GI</h3>
                        <p>Génie Industriel</p>
                    </div>
                    
                    <div class="card">
                        <h3>GM</h3>
                        <p>Génie Mécanique</p>
                    </div>
                    
                    <div class="card">
                        <h3>IRT</h3>
                        <p>Informatique, Réseaux et Télécommunications</p>
                    </div>
                    
                    <div class="card">
                        <h3>MPG</h3>
                        <p>Mines, Pétrole et Gaz</p>
                    </div>
                    
                    <div class="card">
                        <h3>SID</h3>
                        <p>Statistique et Ingénierie de Données</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2>Nos Valeurs</h2>
                <div class="grid">
                    <div class="card">
                        <h3>Excellence</h3>
                        <p>Viser les plus hauts standards de qualité dans l'enseignement et la recherche</p>
                    </div>
                    
                    <div class="card">
                        <h3>Innovation</h3>
                        <p>Encourager la créativité et l'innovation dans tous nos programmes</p>
                    </div>
                    
                    <div class="card">
                        <h3>Intégrité</h3>
                        <p>Maintenir une éthique professionnelle forte et des valeurs morales élevées</p>
                    </div>
                    
                    <div class="card">
                        <h3>Collaboration</h3>
                        <p>Favoriser le travail d'équipe et les partenariats productifs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
