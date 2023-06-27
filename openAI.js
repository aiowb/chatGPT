// Die typeWriter-Funktion fügt den Text Zeichen für Zeichen in das angegebene Element ein.
// element: Das HTML-Element, in das der Text eingefügt wird
// text: Der Text, der eingefügt werden soll
// index: Der Startindex des Textes
// delay: Die Zeitverzögerung in Millisekunden zwischen den eingefügten Zeichen
function typeWriter(element, text, index, delay) {
  if (index < text.length) {
    element.textContent += text.charAt(index);
    index++;
    setTimeout(() => typeWriter(element, text, index, delay), delay);
  }
}

// Überprüft, ob die URL-Parameter "response" und "product_info" enthalten
if (window.location.search.includes("response=")) {
  // Extrahiert die URL-Parameter
  const urlParams = new URLSearchParams(window.location.search);
  const openai_response = urlParams.get("response");
  const product_info = urlParams.get("product_info");

  // Ruft das chat_messages HTML-Element ab, um die Nachrichten hinzuzufügen
  const chat_messages = document.getElementById("chat_messages");

  // Erstellt und fügt die Benutzernachricht zur Liste hinzu
  const user_message = document.createElement("li");
  user_message.className = "user_message";
  user_message.innerHTML = `<strong>Produktinformationen:</strong> ${decodeURIComponent(product_info)}`;
  chat_messages.appendChild(user_message);

  // Erstellt und fügt ein Trennzeichen zur Liste hinzu
  const separator = document.createElement("hr");
  chat_messages.appendChild(separator);

  // Erstellt und fügt die OpenAI-Nachricht zur Liste hinzu
  const openai_message = document.createElement("li");
  openai_message.className = "openai_message";
  chat_messages.appendChild(openai_message);

  // Erstellt ein <strong>-Element für den "Generierte Produktbeschreibung:"-Text
  const openai_description_label = document.createElement("strong");
  openai_description_label.textContent = "Generierte Produktbeschreibung: ";
  openai_message.appendChild(openai_description_label);

  // Erstellt ein <span>-Element für den generierten Text
  const openai_description_text = document.createElement("span");
  openai_message.appendChild(openai_description_text);

  // Die Zeitverzögerung in Millisekunden zwischen den eingefügten Zeichen
  const delay = 50;

  // Fügt den generierten Text mithilfe der typeWriter-Funktion in das openai_description_text-Element ein
  typeWriter(openai_description_text, decodeURIComponent(openai_response), 0, delay);
}
  // Beschränkt die OpenAI-Antwort auf maximal 30 Wörter
  //const limited_response = decodeURIComponent(openai_response).split(' ').slice(0, 50).join(' ');
  //typeWriter(openai_description_text, limited_response, 0, delay);