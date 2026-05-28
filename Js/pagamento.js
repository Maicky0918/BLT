function verificarPagamento(event) {
    event.preventDefault();

    // PEGAR EMAILS
    let email = document.getElementById("email")?.value;
    let confirmarEmail = document.getElementById("confirmarEmail")?.value;

    if (!email || !confirmarEmail) {
        alert("Preencha os campos de email!");
        return false;
    }

    if (email !== confirmarEmail) {
        alert("Os emails não coincidem!");
        return false;
    }

    // VERIFICAR MÉTODO DE PAGAMENTO
    let metodoSelecionado = document.querySelector('input[name="pagamento"]:checked');

    if (!metodoSelecionado) {
        alert("Escolha um método de pagamento!");
        return false;
    }

    let metodo = metodoSelecionado.value;

    // PIX
    if (metodo === "pix") {
        let chavePix = "BLT-PAGAMENTO-123";
        let url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + chavePix;

        document.getElementById("imgQR").src = url;
        document.getElementById("qrcode").style.display = "block";
        document.getElementById("cartaoForm").style.display = "none";

    } 
    // CARTÃO
    else {
        document.getElementById("cartaoForm").style.display = "block";
        document.getElementById("qrcode").style.display = "none";
    }
}


// ==========================
// FORMATAÇÃO DO CARTÃO
// ==========================
let numeroCartaoInput = document.getElementById("numeroCartao");

if (numeroCartaoInput) {
    numeroCartaoInput.addEventListener("input", function(e) {
        let valor = e.target.value.replace(/\D/g, "");
        valor = valor.replace(/(.{4})/g, "$1 ").trim();

        e.target.value = valor;
        detectarBandeira(valor.replace(/\s/g, ""));
    });
}


// ==========================
// CVV SOMENTE NÚMEROS
// ==========================
let cvvInput = document.getElementById("cvv");

if (cvvInput) {
    cvvInput.addEventListener("input", function(e) {
        e.target.value = e.target.value.replace(/\D/g, "");
    });
}


// ==========================
// DETECTAR BANDEIRA
// ==========================
function detectarBandeira(numero) {
    let bandeira = document.getElementById("bandeira");

    if (!bandeira) return;

    if (numero.startsWith("4")) {
        bandeira.innerText = "Visa";
    } else if (/^5[1-5]/.test(numero)) {
        bandeira.innerText = "MasterCard";
    } else if (/^3[47]/.test(numero)) {
        bandeira.innerText = "American Express";
    } else {
        bandeira.innerText = "";
    }
}


// ==========================
// VALIDAR CARTÃO (BOTÃO PAGAR)
// ==========================
function validarCartao() {
    let numero = document.getElementById("numeroCartao")?.value.replace(/\s/g, "");

    if (!numero) {
        alert("Digite o número do cartão!");
        return;
    }

    if (!luhnCheck(numero)) {
        alert("Cartão inválido!");
        return;
    }

    alert("Pagamento aprovado! ✅");
    window.location.href = "pagamentoconcluido.html";
}


// ==========================
// ALGORITMO DE LUHN
// ==========================
function luhnCheck(num) {
    let soma = 0;
    let alternar = false;

    for (let i = num.length - 1; i >= 0; i--) {
        let n = parseInt(num[i]);

        if (alternar) {
            n *= 2;
            if (n > 9) n -= 9;
        }

        soma += n;
        alternar = !alternar;
    }

    return (soma % 10 === 0);
}


// ==========================
// FINALIZAR PIX
// ==========================
function finalizar() {
    window.location.href = "pagamentoconcluido.html";
}