<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestore Skill</title>
  <style>
    body {
      font-family: sans-serif;
      padding: 20px;
      background-color: #f5f5f5;
    }
    textarea {
      width: 100%;
      height: 100px;
      font-size: 1rem;
      margin-bottom: 10px;
    }
    .highlight {
      color: red;
      font-weight: bold;
    }
    .tag {
      color: blue;
      font-weight: bold;
    }
    .output {
      white-space: pre-wrap;
      padding: 10px;
      background: white;
      border: 1px solid #ccc;
      min-height: 50px;
      margin-bottom: 10px;
    }
    .saved-box {
      background: #e0ffe0;
      padding: 10px;
      border: 1px dashed green;
    }
    section {
      margin-bottom: 40px;
    }
  </style>
</head>
<body>

  <h1>Editor Skill</h1>

  <!-- Skill Passive -->
  <section>
    <h2>1) Skill Passive</h2>
    <textarea id="passiveInput" placeholder="Scrivi le skill passive..."></textarea>
    <div class="output" id="passiveOutput"></div>
    <button onclick="saveText('passive')">Salva Skill Passive</button>
    <h4>Skill salvate:</h4>
    <div class="saved-box" id="passiveSaved"></div>
  </section>

  <!-- Skill Ibride -->
  <section>
    <h2>2) Skill Ibride</h2>
    <textarea id="hybridInput" placeholder="Scrivi le skill ibride..."></textarea>
    <div class="output" id="hybridOutput"></div>
    <button onclick="saveText('hybrid')">Salva Skill Ibride</button>
    <h4>Skill salvate:</h4>
    <div class="saved-box" id="hybridSaved"></div>
  </section>

  <!-- Skill Attive -->
  <section>
    <h2>3) Skill Attive</h2>
    <textarea id="activeInput" placeholder="Scrivi le skill attive..."></textarea>
    <div class="output" id="activeOutput"></div>
    <button onclick="saveText('active')">Salva Skill Attive</button>
    <h4>Skill salvate:</h4>
    <div class="saved-box" id="activeSaved"></div>
  </section>

  <script>
    const sections = ['passive', 'hybrid', 'active'];

    sections.forEach(section => {
      const input = document.getElementById(`${section}Input`);
      const output = document.getElementById(`${section}Output`);
      const savedBox = document.getElementById(`${section}Saved`);

      input.addEventListener('input', () => updateOutput(input.value, output));
      showSavedText(section, savedBox);
    });

    function updateOutput(text, outputElement) {
      const charMap = {};
      let isInTag = false;
      let i = 0;

      // Conta i caratteri fuori dai tag
      while (i < text.length) {
        if (text[i] === "<") {
          while (i < text.length && text[i] !== ">") i++;
          i++;
        } else {
          charMap[text[i]] = (charMap[text[i]] || 0) + 1;
          i++;
        }
      }

      // Costruisci output con evidenziazione
      let highlighted = "";
      isInTag = false;

      for (let j = 0; j < text.length; j++) {
        const char = text[j];

        if (char === "<") {
          isInTag = true;
          highlighted += `<span class="tag">&lt;`;
        } else if (char === ">" && isInTag) {
          isInTag = false;
          highlighted += `&gt;</span>`;
        } else if (isInTag) {
          highlighted += char;
        } else {
          if (charMap[char] > 1) {
            highlighted += `<span class="highlight">${char}</span>`;
          } else {
            highlighted += char;
          }
        }
      }

      outputElement.innerHTML = highlighted;
    }

    function saveText(section) {
      const value = document.getElementById(`${section}Input`).value;
      localStorage.setItem(`saved_${section}`, value);
      showSavedText(section, document.getElementById(`${section}Saved`));
    }

    function showSavedText(section, target) {
      const saved = localStorage.getItem(`saved_${section}`) || "";
      target.textContent = saved;
    }
  </script>
</body>
</html>
