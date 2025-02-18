<?php
/**
 * Template Name: Innovation
 */

get_header(); ?>

<style>
.page-innovation .hero-section {
    background-color: white !important;
    padding: 60px 0;
    text-align: center;
    margin-top: 50px;
    border-bottom: 1px solid #eee;
}

.page-innovation .hero-section h1 {
    color: #006400;
    font-size: 2.5em;
    margin-bottom: 20px;
    font-weight: bold;
}

.page-innovation .hero-description {
    color: #333;
    font-size: 1.2em;
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.6;
}
</style>

<div class="page-innovation">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Innovation à l'ESP</h1>
            <p class="hero-description">Découvrez nos projets innovants et nos initiatives de recherche</p>
        </div>
    </section>

    <!-- Innovation Projects -->
    <section class="innovation-projects">
        <div class="container">
            <h2>Nos Projets Innovants</h2>
            <div class="projects-grid">
                <div class="project-card">
                    <div class="project-image">
                        <img src="<?php echo home_url('/wp-content/themes/espbot/assets/images/innovation-1.jpg'); ?>" alt="Innovation Project 1">
                    </div>
                    <div class="project-content">
                        <h3>Recherche et Développement</h3>
                        <p>Nos laboratoires de recherche travaillent sur des projets innovants dans divers domaines technologiques.</p>
                    </div>
                </div>

                <div class="project-card">
                    <div class="project-image">
                        <img src="<?php echo home_url('/wp-content/themes/espbot/assets/images/innovation-2.jpg'); ?>" alt="Innovation Project 2">
                    </div>
                    <div class="project-content">
                        <h3>Partenariats Industriels</h3>
                        <p>Collaboration étroite avec l'industrie pour développer des solutions innovantes.</p>
                    </div>
                </div>

                <div class="project-card">
                    <div class="project-image">
                        <img src="<?php echo home_url('/wp-content/themes/espbot/assets/images/innovation-3.jpg'); ?>" alt="Innovation Project 3">
                    </div>
                    <div class="project-content">
                        <h3>Incubateur de Startups</h3>
                        <p>Support aux projets entrepreneuriaux des étudiants et des chercheurs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Research Centers -->
    <section class="research-centers">
        <div class="container">
            <h2>Nos Centres de Recherche</h2>
            <div class="centers-grid">
                <div class="center-item">
                    <h3>Centre de Recherche en Génie Électrique</h3>
                    <p>Recherche avancée en électronique, automatique et télécommunications.</p>
                </div>
                <div class="center-item">
                    <h3>Laboratoire de Génie Mécanique</h3>
                    <p>Innovation en conception mécanique et énergétique.</p>
                </div>
                <div class="center-item">
                    <h3>Centre d'Innovation Numérique</h3>
                    <p>Développement de solutions numériques et intelligence artificielle.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Innovation Events -->
    <section class="innovation-events">
        <div class="container">
            <h2>Événements et Actualités</h2>
            <div class="events-list">
                <?php
                $args = array(
                    'category_name' => 'innovation',
                    'posts_per_page' => 3
                );
                $query = new WP_Query($args);
                
                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post();
                        ?>
                        <div class="event-item">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="event-image">
                                    <?php the_post_thumbnail(); ?>
                                </div>
                            <?php endif; ?>
                            <div class="event-content">
                                <h3><?php the_title(); ?></h3>
                                <div class="event-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary">Lire la suite</a>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>
