document.addEventListener('DOMContentLoaded', function() {
    // Ajouter le style CSS pour la notification
    const style = document.createElement('style');
    style.textContent = `
        .custom-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 16px;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 9999;
            display: flex;
            align-items: center;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
            max-width: 400px;
        }

        .custom-notification.show {
            opacity: 1;
            transform: translateY(0);
        }

        .custom-notification .icon {
            margin-right: 12px;
            font-size: 24px;
        }

        .custom-notification .message {
            flex-grow: 1;
            font-size: 16px;
        }

        .custom-notification .close-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 0 8px;
            margin-left: 8px;
        }
    `;
    document.head.appendChild(style);

    // Fonction pour afficher une notification élégante
    function showNotification(message, type = 'success') {
        // Supprimer les notifications existantes
        const existingNotifications = document.querySelectorAll('.custom-notification');
        existingNotifications.forEach(notification => {
            notification.remove();
        });

        // Créer la notification
        const notification = document.createElement('div');
        notification.className = 'custom-notification';
        notification.style.backgroundColor = type === 'success' ? '#4CAF50' : '#F44336';

        // Icône
        const icon = document.createElement('span');
        icon.className = 'icon';
        icon.innerHTML = type === 'success' ? '✓' : '✗';
        notification.appendChild(icon);

        // Message
        const messageElement = document.createElement('span');
        messageElement.className = 'message';
        messageElement.textContent = message;
        notification.appendChild(messageElement);

        // Bouton de fermeture
        const closeButton = document.createElement('button');
        closeButton.className = 'close-btn';
        closeButton.innerHTML = '×';
        closeButton.addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
        notification.appendChild(closeButton);

        // Ajouter la notification au document
        document.body.appendChild(notification);

        // Afficher la notification avec animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Masquer automatiquement après 5 secondes
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }

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
                showNotification('Veuillez sélectionner un fichier à scanner.', 'error');
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

                    // Afficher une notification élégante au lieu d'une alerte
                    showNotification('Succès ! Les données de la facture ont été extraites et le formulaire a été rempli automatiquement.');
                } else {
                    showNotification('Erreur: ' + (data.message || 'Impossible d\'extraire les données'), 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                scanButton.disabled = false;
                scanButton.innerHTML = 'Scanner et extraire';
                showNotification('Erreur lors de la communication avec le serveur: ' + error.message, 'error');
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
