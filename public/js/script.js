// ==========================================
// 1. FONCTION GET : APERÇU D'UN TICKET
// ==========================================
window.viewTicketDetails = function(ticketId) {
    fetch(`/api/tickets/${ticketId}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(ticket => {
        alert(`🔍 DÉTAILS DU TICKET #${ticket.id}\n\nSujet : ${ticket.titre}\nStatut : ${ticket.statut}\nType : ${ticket.type}`);
    })
    .catch(error => console.error('Erreur API GET:', error));
};

// ==========================================
// AU CHARGEMENT DE LA PAGE
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    
    // --- A. GESTION DU MENU MOBILE ---
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    
    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }

    // --- B. FONCTION POST : AJOUTER UN TICKET (API) ---
    const ticketForm = document.getElementById('quick-ticket-form');

    if (ticketForm) {
        ticketForm.addEventListener('submit', async function(event) {
            event.preventDefault(); // Bloque le rechargement

            const messageDiv = document.getElementById('api-message');
            
            const data = {
                titre: document.getElementById('api-titre').value,
                type: document.getElementById('api-type').value,
                priorite: document.getElementById('api-priorite').value,
                projet_id: document.getElementById('api-project-id').value,
                description: "Créé via l'ajout rapide API"
            };

            try {
                const response = await fetch('/api/tickets', {
                    method: 'POST',
                    credentials: 'same-origin', // Envoie le cookie de connexion
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                // Si Laravel renvoie une erreur (ex: 500)
                if (!response.ok) {
                    throw new Error(result.message || "Erreur serveur");
                }

                if (result.success) {
                    // Affichage du succès en VERT
                    messageDiv.style.display = 'block';
                    messageDiv.style.color = '#166534';
                    messageDiv.style.background = '#dcfce7';
                    messageDiv.style.padding = '10px';
                    messageDiv.innerText = result.message;

                    // Ajout au tableau
                    const tableBody = document.querySelector('table tbody');
                    const newRow = `
                        <tr style="border-bottom: 1px solid #eee; background: #f0fdf4;">
                            <td style="padding: 12px;">#${result.ticket.id}</td>
                            <td style="padding: 12px;"><strong>${result.ticket.titre}</strong></td>
                            <td style="padding: 12px;"><span class="badge badge-gray">${result.ticket.statut}</span></td>
                            <td style="padding: 12px; text-align: right;">
                                <button onclick="viewTicketDetails(${result.ticket.id})" class="btn btn-sm btn-outline" style="margin-right: 5px;">👀 Aperçu API</button>
                                <a href="/tickets/${result.ticket.id}" class="btn btn-sm btn-light">Voir</a>
                            </td>
                        </tr>
                    `;
                    
                    tableBody.insertAdjacentHTML('afterbegin', newRow);
                    ticketForm.reset();
                    
                    setTimeout(() => { messageDiv.style.display = 'none'; }, 3000);
                }
            } catch (error) {
                // Affichage de l'erreur en ROUGE
                console.error('Erreur:', error);
                messageDiv.style.display = 'block';
                messageDiv.style.color = '#991b1b';
                messageDiv.style.background = '#fee2e2';
                messageDiv.style.padding = '10px';
                messageDiv.innerText = "Erreur : " + error.message;
            }
        });
    }
});