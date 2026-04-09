// --- Aperçu d'un ticket (API GET) ---
window.viewTicketDetails = async function(ticketId) {
    try {
        const response = await fetch(`/api/tickets/${ticketId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const ticket = await response.json();
        alert(`🔍 DÉTAILS DU TICKET #${ticket.id}\n\nSujet : ${ticket.titre}\nStatut : ${ticket.statut}\nType : ${ticket.type}`);
    } catch (error) {
        alert("Impossible de charger les détails du ticket.");
    }
};

document.addEventListener('DOMContentLoaded', function() {
    
    // --- Gestion du menu mobile ---
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    
    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
    }

    // --- Ajout rapide d'un ticket (API POST) ---
    const ticketForm = document.getElementById('quick-ticket-form');

    if (ticketForm) {
        ticketForm.addEventListener('submit', async function(event) {
            event.preventDefault();

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
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (!response.ok) throw new Error(result.message || "Erreur serveur");

                if (result.success) {
                    messageDiv.className = 'api-msg api-msg-success';
                    messageDiv.innerText = result.message;

                    const tableBody = document.querySelector('table tbody');
                    const newRow = `
                        <tr class="table-row-new">
                            <td style="padding: 12px;">#${result.ticket.id}</td>
                            <td style="padding: 12px;"><strong>${result.ticket.titre}</strong></td>
                            <td style="padding: 12px;"><span class="badge badge-gray">${result.ticket.statut}</span></td>
                            <td style="padding: 12px; text-align: right;">
                                <button onclick="viewTicketDetails(${result.ticket.id})" class="btn btn-sm btn-outline mr-1">👀 Aperçu API</button>
                                <a href="/tickets/${result.ticket.id}" class="btn btn-sm btn-light">Voir</a>
                            </td>
                        </tr>
                    `;
                    
                    tableBody.insertAdjacentHTML('afterbegin', newRow);
                    ticketForm.reset();
                    
                    setTimeout(() => { messageDiv.className = 'api-msg'; }, 3000);
                }
            } catch (error) {
                messageDiv.className = 'api-msg api-msg-error';
                messageDiv.innerText = "Erreur : " + error.message;
            }
        });
    }
});