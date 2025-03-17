// Função para mudar a aba ativa
function mudarTab(tabId) {
    // Remover a classe 'active' de todas as tabs e conteúdo
    document.querySelectorAll('.tab-title').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.remove('active'));

    // Adicionar a classe 'active' na aba e conteúdo selecionados
    document.querySelector(`.tab-title[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById(tabId).classList.add('active');
}
 

// Função para fechar o status-container
function fecharStatus() {
    let statusDiv = document.querySelector(".status-container");
    let overlay = document.querySelector(".overlay");

    if (statusDiv) statusDiv.style.display = "none";
    if (overlay) overlay.style.display = "none";
}
