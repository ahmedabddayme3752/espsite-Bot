<?php
/**
 * Template Name: Page Contact
 */

get_header(); ?>

<style>
.contact-page {
    padding: 60px 0;
    background-color: #f5f5f5;
}

.contact-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-top: 40px;
}

.contact-info {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.contact-info h3 {
    color: #006400;
    margin-bottom: 20px;
    font-size: 24px;
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 5px;
}

.info-item i {
    color: #006400;
    font-size: 24px;
    margin-right: 15px;
    width: 24px;
}

.info-item .details {
    flex: 1;
}

.info-item h4 {
    margin: 0 0 5px;
    color: #333;
    font-size: 18px;
}

.info-item p {
    margin: 0;
    color: #666;
}

.contact-form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.form-group textarea {
    height: 150px;
    resize: vertical;
}

.submit-button {
    background-color: #006400;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.submit-button:hover {
    background-color: #004d00;
}

.map-container {
    margin-top: 40px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.map-container iframe {
    width: 100%;
    height: 400px;
    border: 0;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .contact-page {
        padding: 40px 0;
    }
    
    .info-item {
        padding: 12px;
    }
    
    .info-item i {
        font-size: 20px;
    }
    
    .map-container iframe {
        height: 300px;
    }
}
</style>

<div class="contact-page">
    <div class="contact-container">
        <h1 class="text-center">Contactez-nous</h1>
        <p class="text-center">N'hésitez pas à nous contacter pour toute question ou information</p>

        <div class="contact-grid">
            <div class="contact-info">
                <h3>Nos Coordonnées</h3>
                
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="details">
                        <h4>Adresse</h4>
                        <p>BP 4303 Cité Cadres - Sebkha, Nouakchott - Mauritanie</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div class="details">
                        <h4>Téléphone</h4>
                        <p>+222 45 24 31 89</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div class="details">
                        <h4>Email</h4>
                        <p>contact@esp.mr</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div class="details">
                        <h4>Heures d'ouverture</h4>
                        <p>Lundi - Jeudi: 8h00 - 17h00, Vendredi: 8h00 - 12h00</p>
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <h3>Envoyez-nous un message</h3>
                <form action="#" method="post">
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Sujet</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>

                    <button type="submit" class="submit-button">Envoyer le message</button>
                </form>
            </div>
        </div>

        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.8248453394726!2d-15.9539!3d18.0875!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTjCsDA1JzE1LjAiTiAxNcKwNTcnMTQuMCJX!5e0!3m2!1sfr!2s!4v1625764428736!5m2!1sfr!2s" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</div>

<?php get_footer(); ?>
