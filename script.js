document.addEventListener('DOMContentLoaded', function () {
    // Gestion de l'ouverture des modals
    const openModalButtons = document.querySelectorAll('.open-modal');
    openModalButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modalId = this.dataset.modalTarget;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'block';

                // Si c'est un modal d'édition, pré-remplir les champs
                if (modalId === 'editProductModal') {
                    document.getElementById('edit_product_id').value = this.dataset.id;
                    document.getElementById('edit_product_name').value = this.dataset.name;
                    document.getElementById('edit_category_id').value = this.dataset.category_id;
                    document.getElementById('edit_supplier_id').value = this.dataset.supplier_id;
                    document.getElementById('edit_packaging').value = this.dataset.packaging;
                    document.getElementById('edit_max_stock').value = this.dataset.max_stock;
                    document.getElementById('edit_critical_threshold').value = this.dataset.critical_threshold;
                }
                if (modalId === 'editCategoryModal') {
                    document.getElementById('edit_category_id').value = this.dataset.id;
                    document.getElementById('edit_category_name').value = this.dataset.name;
                }
                // Ajoutez ici la logique pour les autres modals d'édition (fournisseurs, etc.)
            }
        });
    });

    // Gestion de la fermeture des modals
    const closeModalButtons = document.querySelectorAll('.close-modal');
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modalId = this.dataset.modalTarget;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Fermer le modal si on clique en dehors de son contenu
    window.addEventListener('click', function (event) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Petite fonction de recherche simple pour les tables (peut être améliorée)
    function setupTableSearch(inputId, tableId) {
        const searchInput = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        if (searchInput && table) {
            const tableRows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                for (let i = 0; i < tableRows.length; i++) {
                    let rowVisible = false;
                    const cells = tableRows[i].getElementsByTagName('td');
                    for (let j = 0; j < cells.length -1; j++) { // -1 pour ne pas chercher dans la colonne Actions
                        if (cells[j]) {
                            if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                                rowVisible = true;
                                break;
                            }
                        }
                    }
                    tableRows[i].style.display = rowVisible ? '' : 'none';
                }
            });
        }
    }
    setupTableSearch('productSearch', 'productsTable');
    setupTableSearch('categorySearch', 'categoriesTable');
    // Ajoutez pour les autres tables (fournisseurs, inventaires)

});