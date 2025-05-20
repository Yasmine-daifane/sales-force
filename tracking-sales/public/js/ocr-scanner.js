document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner les éléments
    const fileInput = document.querySelector('input[type="file"]:not([name="file"])');
    const scanButton = document.querySelector('button.btn-primary');

    if (fileInput && scanButton) {
        console.log('Éléments OCR trouvés');

        // Empêcher la soumission normale du formulaire
        scanButton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Bouton scan cliqué');

            // Vérifier si un fichier est sélectionné
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Veuillez sélectionner un fichier à scanner.');
                return;
            }

            // Créer FormData
            const formData = new FormData();
            formData.append('scan', fileInput.files[0]);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

            // Afficher un indicateur de chargement
            scanButton.disabled = true;
            scanButton.innerHTML = 'Traitement en cours...';

            // Envoyer la requête AJAX
            fetch('/factures/scan', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Réponse OCR:', data);
                scanButton.disabled = false;
                scanButton.innerHTML = 'Scanner et extraire';

                if (data.success) {
                    // Remplir le formulaire
                    if (data.fields.prix) document.querySelector('input[name="prix"]').value = data.fields.prix;
                    if (data.fields.departement) document.querySelector('input[name="departement"]').value = data.fields.departement;
                    if (data.fields.date) document.querySelector('input[name="date"]').value = formatDate(data.fields.date);
                    if (data.fields.societe) document.querySelector('input[name="societe"]').value = data.fields.societe;

                    // Pour le select
                    if (data.fields.type) {
                        const typeSelect = document.querySelector('select[name="type"]');
                        if (typeSelect) {
                            for (let i = 0; i < typeSelect.options.length; i++) {
                                if (typeSelect.options[i].value.toLowerCase() === data.fields.type.toLowerCase()) {
                                    typeSelect.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                    }

                    alert('Formulaire rempli avec les données extraites');
                } else {
                    alert('Erreur: ' + (data.message || 'Impossible d\'extraire les données'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                scanButton.disabled = false;
                scanButton.innerHTML = 'Scanner et extraire';
                alert('Erreur lors de la communication avec le serveur: ' + error.message);
            });
        });
    } else {
        console.error('Éléments OCR non trouvés');
    }

    // Fonction pour formater la date au format YYYY-MM-DD
    function formatDate(dateString) {
        // Essayer différents formats de date
        let date;

        // Format DD/MM/YYYY ou DD-MM-YYYY
        if (/^\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{4}$/.test(dateString)) {
            const parts = dateString.split(/[\/\-\.]/);
            date = new Date(parts[2], parts[1] - 1, parts[0]);
        }
        // Format YYYY/MM/DD ou YYYY-MM-DD
        else if (/^\d{4}[\/\-\.]\d{1,2}[\/\-\.]\d{1,2}$/.test(dateString)) {
            date = new Date(dateString);
        }
        // Autres formats
        else {
            date = new Date(dateString);
        }

        // Vérifier si la date est valide
        if (isNaN(date.getTime())) {
            return '';
        }

        // Formater au format YYYY-MM-DD
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }
});
