/**
 * Sistema de autocompletado para posts y comentarios
 * 
 * Este script maneja el autocompletado de texto en los campos de contenido
 * basado en las palabras clave del usuario almacenadas en caché.
 */
$(document).ready(function() {
    // Selector para los textarea de contenido
    const contentTextarea = $('#post-content, [name="Posts[contenido]"]');
    
    // Array de palabras clave disponibles (se cargará vía AJAX)
    let keywords = [];
    
    // Cargar las palabras clave vía AJAX
    function loadKeywords() {
        $.ajax({
            url: '/site/get-autocomplete-keywords',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.keywords) {
                    keywords = response.keywords;
                    
                    // Inicializar la funcionalidad de autocompletado
                    initializeAutocomplete();
                }
            }
        });
    }
    
    // Inicializar el autocompletado
    function initializeAutocomplete() {
        if (keywords.length === 0 || contentTextarea.length === 0) {
            return;
        }
        
        // Configurar el autocompletado en los textareas
        contentTextarea.each(function() {
            const textarea = $(this);
            
            // Escuchar eventos de entrada
            textarea.on('input', function() {
                const cursorPos = this.selectionStart;
                const text = this.value.substring(0, cursorPos);
                const wordStart = text.lastIndexOf(' ') + 1;
                const currentWord = text.substring(wordStart);
                
                // Si la palabra actual tiene más de 2 caracteres, buscar coincidencias
                if (currentWord.length >= 2) {
                    const matches = findMatches(currentWord);
                    
                    if (matches.length > 0) {
                        showSuggestions(textarea, matches, wordStart, cursorPos);
                    } else {
                        hideSuggestions();
                    }
                } else {
                    hideSuggestions();
                }
            });
        });
    }
    
    // Buscar coincidencias en las palabras clave
    function findMatches(prefix) {
        prefix = prefix.toLowerCase();
        return keywords.filter(keyword => 
            keyword.toLowerCase().startsWith(prefix)
        ).slice(0, 5); // Mostrar máximo 5 sugerencias
    }
    
    // Mostrar sugerencias
    function showSuggestions(textarea, matches, wordStart, cursorPos) {
        // Ocultar sugerencias anteriores
        hideSuggestions();
        
        // Crear contenedor de sugerencias
        const $suggestions = $('<div class="autocomplete-suggestions"></div>');
        
        // Agregar cada sugerencia
        matches.forEach(match => {
            const $suggestion = $('<div class="autocomplete-suggestion"></div>')
                .text(match)
                .data('word-start', wordStart)
                .data('cursor-pos', cursorPos)
                .data('value', match);
            
            $suggestion.on('click', function() {
                // Insertar la sugerencia en el textarea
                const suggestionText = $(this).data('value');
                const wordStart = $(this).data('word-start');
                const cursorPos = $(this).data('cursor-pos');
                
                const textareaElement = textarea[0];
                const text = textareaElement.value;
                const newText = text.substring(0, wordStart) + suggestionText + text.substring(cursorPos);
                
                textareaElement.value = newText;
                
                // Posicionar el cursor después de la palabra insertada
                const newCursorPos = wordStart + suggestionText.length;
                textareaElement.setSelectionRange(newCursorPos, newCursorPos);
                
                // Enfocar el textarea
                textareaElement.focus();
                
                // Ocultar sugerencias
                hideSuggestions();
            });
            
            $suggestions.append($suggestion);
        });
        
        // Posicionar el contenedor de sugerencias
        const textareaPos = textarea.offset();
        const lineHeight = parseInt(textarea.css('line-height'));
        
        $suggestions.css({
            top: textareaPos.top + textarea.height() - lineHeight,
            left: textareaPos.left
        });
        
        // Agregar al DOM
        $('body').append($suggestions);
    }
    
    // Ocultar sugerencias
    function hideSuggestions() {
        $('.autocomplete-suggestions').remove();
    }
    
    // Ocultar sugerencias al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.autocomplete-suggestions').length) {
            hideSuggestions();
        }
    });
    
    // Estilos para las sugerencias
    $("<style>")
        .html(`
            .autocomplete-suggestions {
                position: absolute;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                max-width: 300px;
                z-index: 1050;
            }
            .autocomplete-suggestion {
                padding: 8px 12px;
                cursor: pointer;
                transition: background 0.2s ease;
            }
            .autocomplete-suggestion:hover {
                background: #f0f0f0;
            }
        `)
        .appendTo("head");
    
    // Cargar palabras clave al iniciar
    loadKeywords();
}); 