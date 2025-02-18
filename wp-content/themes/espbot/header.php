<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        .site-header {
            background-color: #006400;
            padding: 10px 0;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
            position: relative;
        }
        
        .logo-container img {
            max-height: 50px;
            width: auto;
        }

        .main-navigation {
            display: block;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-menu li {
            margin: 0 10px;
        }
        
        .nav-menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            text-transform: uppercase;
            padding: 5px 10px;
            display: block;
        }
        
        .nav-menu a:hover {
            color: #ffeb3b;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .main-navigation {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #006400;
                padding: 20px;
            }

            .main-navigation.show {
                display: block;
            }

            .nav-menu {
                flex-direction: column;
                width: 100%;
            }

            .nav-menu li {
                margin: 10px 0;
                width: 100%;
                text-align: center;
            }

            .nav-menu a {
                padding: 10px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
        }
    </style>
</head>

<body <?php body_class(); ?>>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <?php if (has_custom_logo()): ?>
                    <div class="logo-container">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php endif; ?>

                <button class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>

                <nav class="main-navigation">
                    <?php
                    if (has_nav_menu('primary')) {
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_class' => 'nav-menu',
                            'container' => false
                        ));
                    } else {
                        echo '<ul class="nav-menu">';
                        echo '<li><a href="' . esc_url(home_url('/')) . '">Accueil</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/ecole')) . '">École</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/formation')) . '">Formation</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/recherche')) . '">Recherche</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/innovation')) . '">Innovation</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/contact')) . '">Contact</a></li>';
                        echo '</ul>';
                    }
                    ?>
                </nav>
            </div>
        </div>
    </header>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var menuToggle = document.querySelector('.menu-toggle');
        var mainNav = document.querySelector('.main-navigation');
        
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('show');
            var icon = this.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    });
    </script>

    <main id="main" class="site-main">
    </main>
