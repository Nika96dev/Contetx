document.addEventListener('DOMContentLoaded', () => {
    const editor = document.getElementById('editor');
    const charCount = document.getElementById('charCount');
    
    const excludedWords = new Set([
        'il', 'lo', 'la', 'i', 'gli', 'le', 'un', 'uno', 'una', 'di', 'a', 'da', 'in', 
        'con', 'su', 'per', 'tra', 'fra', 'e', 'o', 'ma', 'se', 'che', 'perché', 'come', 
        'quando', 'dove', 'io', 'tu', 'egli', 'ella', 'noi', 'voi', 'loro', 'mi', 'ti', 
        'ci', 'vi', 'li', 'lo', 'ne', 'questo', 'quello', 'chi', 'che', 'cui', 'quale'
    ]);

    let isComposing = false;

    const highlightRepeatedWords = (text) => {
        const tokenRegex = /(\p{L}+[\p{L}'-]*)|([.,!?;:])|(\s+)|(.)/giu;
        const tokens = [...text.matchAll(tokenRegex)].map(match => match[0]);
        
        const wordCount = new Map();
        const cleanWords = [];

        // Prima passata: conteggio parole
        tokens.forEach(token => {
            if (/\p{L}/giu.test(token) && !/\s/.test(token)) {
                const cleanWord = token.toLowerCase().replace(/[^a-zàèéìòù']/g, '');
                if (cleanWord.length > 0 && !excludedWords.has(cleanWord)) {
                    cleanWords.push(cleanWord);
                    wordCount.set(cleanWord, (wordCount.get(cleanWord) || 0) + 1);
                }
            }
        });

        // Seconda passata: evidenziazione
        return tokens.map(token => {
            if (/\s/.test(token) || !/\p{L}/giu.test(token)) {
                return token;
            }
            
            const cleanWord = token.toLowerCase().replace(/[^a-zàèéìòù']/g, '');
            const count = wordCount.get(cleanWord) || 0;
            
            return (count > 1 && cleanWord.length > 2 && !excludedWords.has(cleanWord)) 
                ? `<span class="highlight">${token}</span>` 
                : token;
        }).join('');
    };

    const updateText = (force = false) => {
        if (isComposing && !force) return;
        
        const text = editor.textContent;
        charCount.textContent = text.length;
        
        // Salva la posizione del cursore
        const selection = window.getSelection();
        const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
        const cursorPosition = range ? range.startOffset : 0;
        
        // Aggiorna contenuto
        editor.innerHTML = highlightRepeatedWords(text);
        
        // Ripristino semplificato della posizione del cursore
        const newRange = document.createRange();
        const textNodes = [...editor.childNodes].filter(n => n.nodeType === Node.TEXT_NODE);
        
        if (textNodes.length > 0) {
            // Posiziona il cursore alla fine del testo
            const lastTextNode = textNodes[textNodes.length - 1];
            newRange.setStart(lastTextNode, lastTextNode.length);
        } else {
            // Fallback: posiziona alla fine dell'editor
            newRange.selectNodeContents(editor);
            newRange.collapse(false);
        }
        
        selection.removeAllRanges();
        selection.addRange(newRange);
    };

    // Gestione eventi
    editor.addEventListener('input', () => updateText(true));
    editor.addEventListener('compositionstart', () => isComposing = true);
    editor.addEventListener('compositionend', () => {
        isComposing = false;
        updateText(true);
    });
});