var canvas = document.getElementById('canvas');
var ctx = canvas.getContext('2d');

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

var texts = '01'.split('');
var fontSize = 14;
var columns = Math.floor(canvas.width / fontSize);

// Inicializando o array drops com posições aleatórias
var drops = [];
for (var i = 0; i < columns; i++) {
    drops[i] = Math.floor(Math.random() * canvas.height); // Posição aleatória para cada coluna
}

function draw() {
    // Fundo quase transparente para evitar acúmulo e manter a suavidade
    ctx.fillStyle = 'rgba(0, 0, 0, 0.07)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    ctx.fillStyle = 'rgba(237, 20, 91, 0.2)'; // Letras bem suaves
    ctx.font = fontSize + 'px Arial';
    ctx.shadowColor = 'rgba(237, 20, 91, 0.3)';
    ctx.shadowBlur = 5; // Menos blur para evitar brilho exagerado

    for (let i = 0; i < drops.length; i++) {
        let text = texts[Math.floor(Math.random() * texts.length)];
        ctx.fillText(text, i * fontSize, drops[i] * fontSize);

        // Quando a gota atinge o final ou aleatoriamente, ela retorna para uma nova posição aleatória
        if (drops[i] * fontSize > canvas.height || Math.random() > 0.975) {
            drops[i] = Math.floor(Math.random() * canvas.height); // Posição aleatória ao resetar
        }

        drops[i]++; // Atualiza a posição da gota
    }
}

setInterval(draw, 60); // Movimento mais lento e fluído
