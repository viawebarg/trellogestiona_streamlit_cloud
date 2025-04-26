/**
 * Archivo JavaScript para el tema VIAWEB
 * Este script agrega funcionalidades de interfaz de usuario específicas para el tema
 */

document.addEventListener('DOMContentLoaded', function() {
    // Aplicar estilos específicos al módulo TrelloGestiona si está presente
    applyTrelloGestionaStyles();
    
    // Mejorar interfaz para dispositivos móviles
    improveMobileInterface();
    
    // Agregar logo VIAWEB en el footer si no existe
    addViawebFooterLogo();
    
    // Aplicar estilos adicionales a elementos dinámicos
    enhanceUIElements();
});

/**
 * Aplica estilos específicos al módulo TrelloGestiona
 */
function applyTrelloGestionaStyles() {
    // Verificar si estamos en una página del módulo TrelloGestiona
    if (window.location.href.includes('trellogestiona')) {
        // Mejorar estilos de iframe si existe
        var iframes = document.querySelectorAll('iframe');
        iframes.forEach(function(iframe) {
            iframe.classList.add('trello-iframe');
            
            // Agregar estilo directamente para evitar problemas CSS
            iframe.style.border = 'none';
            iframe.style.borderRadius = '8px';
            iframe.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.05)';
            
            // Asegurarse de que el iframe se ajuste correctamente
            iframe.style.width = '100%';
            iframe.style.minHeight = '700px';
        });
        
        // Mejorar estilos de tablas de tareas
        var tables = document.querySelectorAll('.trello-tasks table');
        tables.forEach(function(table) {
            table.classList.add('trello-table');
        });
    }
}

/**
 * Mejora la interfaz para dispositivos móviles
 */
function improveMobileInterface() {
    // Detectar si es un dispositivo móvil
    if (window.innerWidth <= 768) {
        // Ajustar estilos para mejor visualización en móviles
        document.body.classList.add('mobile-view');
        
        // Mejorar tamaño de botones para tacto
        var buttons = document.querySelectorAll('.button, .buttonDelete, input[type="submit"], input[type="button"]');
        buttons.forEach(function(button) {
            button.style.padding = '10px 20px';
            button.style.fontSize = '16px';
        });
    }
}

/**
 * Agrega el logo de VIAWEB en el footer si no existe
 */
function addViawebFooterLogo() {
    var footer = document.querySelector('#dolbarr_footer');
    
    if (footer && !document.querySelector('.viaweb-footer-logo')) {
        var logoDiv = document.createElement('div');
        logoDiv.className = 'viaweb-footer-text';
        logoDiv.innerHTML = 'Tema desarrollado por VIAWEB S.A.S';
        logoDiv.style.fontSize = '11px';
        logoDiv.style.margin = '3px 0';
        logoDiv.style.color = '#536AAF';
        
        footer.appendChild(logoDiv);
    }
}

/**
 * Mejora elementos de la interfaz de usuario con estilos adicionales
 */
function enhanceUIElements() {
    // Mejorar cards y paneles
    var cards = document.querySelectorAll('.fichehalfright, .fichehalfleft, .fichecenter, .fiche');
    cards.forEach(function(card) {
        card.style.borderRadius = '8px';
        card.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.05)';
        card.style.padding = '15px';
        card.style.marginBottom = '20px';
    });
    
    // Mejorar tablas de datos
    var dataTables = document.querySelectorAll('.tagtable, .liste');
    dataTables.forEach(function(table) {
        table.style.borderCollapse = 'collapse';
        table.style.width = '100%';
        
        // Aplicar estilos a encabezados
        var headers = table.querySelectorAll('th');
        headers.forEach(function(header) {
            header.style.backgroundColor = '#0E4895';
            header.style.color = 'white';
            header.style.padding = '10px';
            header.style.textAlign = 'left';
        });
        
        // Aplicar estilos a celdas
        var cells = table.querySelectorAll('td');
        cells.forEach(function(cell) {
            cell.style.padding = '8px';
            cell.style.borderBottom = '1px solid #B3BDD7';
        });
        
        // Aplicar estilos alternantes a filas
        var rows = table.querySelectorAll('tr.oddeven');
        rows.forEach(function(row, index) {
            if (index % 2 === 0) {
                row.style.backgroundColor = 'rgba(179, 189, 215, 0.1)';
            }
        });
    });
}