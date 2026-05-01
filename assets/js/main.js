// Fichier JavaScript principal pour Vitrinup

document.addEventListener('DOMContentLoaded', function() {
    console.log('Vitrinup - Le script JS est chargé.');

    // Vous pouvez ajouter ici des fonctionnalités JavaScript interactives
    // Par exemple, pour des formulaires, des animations, etc.

    // Exemple : Ajouter une classe au survol d'un élément
    const ctaButton = document.querySelector('.cta-button');
    if (ctaButton) {
        ctaButton.addEventListener('mouseover', function() {
            this.classList.add('hovered');
        });
        ctaButton.addEventListener('mouseout', function() {
            this.classList.remove('hovered');
        });
    }
});
