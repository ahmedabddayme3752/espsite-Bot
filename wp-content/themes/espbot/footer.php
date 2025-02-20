</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <ul class="contact-info">
                    <li><i class="fas fa-phone"></i> <a href="tel:+221338246981">+221 33 824 69 81</a></li>
                    <li><i class="fas fa-envelope"></i> <a href="mailto:contact@esp.sn">contact@esp.mr</a></li>
                    <li><i class="fas fa-map-marker-alt"></i> ESP Campus</li>
                </ul>
            </div>
            
            <div class="footer-section">
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

<style>
.site-footer {
    background-color: #006400;
    color: white;
    padding: 20px 0 10px;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.footer-section {
    padding: 0 10px;
}

.footer-section h3 {
    color: white;
    margin-bottom: 10px;
    font-size: 1em;
}

.contact-info {
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 0.9em;
}

.contact-info li {
    margin-bottom: 5px;
    display: inline-block;
    margin-right: 15px;
}

.contact-info a {
    color: white;
    text-decoration: none;
}

.social-links {
    display: flex;
    gap: 10px;
}

.social-links a {
    color: white;
    font-size: 16px;
}

.copyright {
    text-align: center;
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid rgba(255,255,255,0.1);
    font-size: 0.9em;
}

@media (max-width: 768px) {
    .footer-content {
        justify-content: center;
        text-align: center;
    }
    
    .contact-info li {
        display: block;
        margin-right: 0;
    }
    
    .social-links {
        justify-content: center;
    }
}
</style>

</body>
</html>
