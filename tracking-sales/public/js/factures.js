// resources/js/factures.js
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si SweetAlert est disponible
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert n\'est pas chargé. Veuillez inclure la bibliothèque SweetAlert.');
        return;
    }

    // Excel export button
    const exportExcelBtn = document.getElementById('export-excel-btn');
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Empêcher le comportement par défaut du lien

            // Show loading indicator
            const loadingAlert = Swal.fire({
                title: 'Exportation en cours...',
                html: 'Veuillez patienter pendant la génération de votre fichier Excel',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Rediriger vers l'URL d'exportation après l'affichage de l'alerte
            setTimeout(() => {
                window.location.href = exportExcelBtn.getAttribute('href');

                // Fermer l'alerte après un court délai pour permettre le téléchargement
                setTimeout(() => {
                    loadingAlert.close();
                }, 1000);
            }, 500);
        });
    }

    // PDF export button
    const exportPdfBtn = document.getElementById('export-pdf-btn');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Empêcher le comportement par défaut du lien

            const loadingAlert = Swal.fire({
                title: 'Exportation en cours...',
                html: 'Veuillez patienter pendant la génération de votre fichier PDF',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Rediriger vers l'URL d'exportation après l'affichage de l'alerte
            setTimeout(() => {
                window.location.href = exportPdfBtn.getAttribute('href');

                // Fermer l'alerte après un court délai pour permettre le téléchargement
                setTimeout(() => {
                    loadingAlert.close();
                }, 1000);
            }, 500);
        });
    }


    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    if (deleteButtons.length > 0) {
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Empêcher le comportement par défaut du bouton

                const form = button.closest('form');

                Swal.fire({
                    title: 'Supprimer cette facture ?',
                    text: "Cette action est irréversible !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Soumettre le formulaire manuellement
                        form.submit();
                    }
                });
            });
        });
    }
});
