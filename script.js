// Récupère tous les boutons de thème et tous les articles
const themeButtons = document.querySelectorAll('.theme-btn');
const articles = document.querySelectorAll('.article');

// Ajoute un événement de clic sur chaque bouton de thème
themeButtons.forEach(button => {
    button.addEventListener('click', function() {
        const selectedTheme = this.getAttribute('data-theme');

        // Retire la classe 'active' de tous les boutons
        themeButtons.forEach(btn => btn.classList.remove('active'));
        // Ajoute la classe 'active' au bouton cliqué
        this.classList.add('active');

        // Filtre les articles
        articles.forEach(article => {
        const articleCategory = article.getAttribute('data-category');
                        
        // Si "Tous" est sélectionné ou la catégorie correspond
        if (selectedTheme === 'all' || articleCategory === selectedTheme) {
            article.classList.remove('hidden');
        } else {
                article.classList.add('hidden');
                }
        });
    });
});