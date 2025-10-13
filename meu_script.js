// 1. IMPORTAÇÕES - Traz as ferramentas do Firebase que vamos usar
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js";
import { getFirestore, collection, addDoc, getDocs } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-firestore.js";

// 2. CONFIGURAÇÃO DO FIREBASE - Suas chaves secretas que conectam ao seu projeto
const firebaseConfig = {
   apiKey: "AIzaSyBEZwzN3Z37EVoIX8WbcUg03PltWIpFof0",
    authDomain: "sistemaftp3.firebaseapp.com",
    projectId: "sistemaftp3",
    storageBucket: "sistemaftp3.firebasestorage.app",
    messagingSenderId: "706827479169",
    appId: "1:706827479169:web:ebd0bab92fd7216d978580"
};

// 3. INICIALIZAÇÃO - Conecta ao Firebase
const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

// 4. FUNÇÃO PARA SALVAR DADOS (Você já tinha essa parte)
async function salvarproduto(produtoParaSalvar) {
  try {
    // 'nomes' é o nome da nossa coleção no Firestore
    const docRef = await addDoc(collection(db, "produto"), {
      nome: nomeParaSalvar,
      criadoEm: new Date()
    });
    console.log("Produto salvo com sucesso! ID:", docRef.id);
    // Limpa o campo de input e recarrega a lista para mostrar o novo nome
    document.getElementById('campoproduto').value = '';
    carregarNomes(); 
  } catch (error) {
    console.error("Erro ao salvar produto:", error);
  }
}

// 5. FUNÇÃO PARA CARREGAR DADOS (A PARTE QUE FALTAVA!)
async function carregarprodutos() {
  const listaElemento = document.getElementById('listaDeprodutos');
  listaElemento.innerHTML = "Carregando..."; // Mostra uma mensagem enquanto busca

  try {
    // Busca todos os documentos da coleção 'nomes'
    const querySnapshot = await getDocs(collection(db, "produtos"));
    
    listaElemento.innerHTML = ""; // Limpa a lista antes de adicionar os novos itens

    if (querySnapshot.empty) {
        listaElemento.innerHTML = "<li>Nenhum produto cadastrado.</li>";
        return;
    }

    // Para cada documento encontrado, cria um item na lista do HTML
    querySnapshot.forEach((doc) => {
      const dados = doc.data();
      const itemLista = document.createElement('li');
      itemLista.textContent = dados.produto;
      listaElemento.appendChild(itemLista);
    });

  } catch (error) {
      console.error("Erro ao carregar produtos:", error);
      listaElemento.innerHTML = "<li>Erro ao carregar os dados.</li>";
  }
}

// 6. FAZENDO A MÁGICA ACONTECER
// Adiciona um "ouvinte" ao botão para que ele chame a função salvarNome quando clicado
document.getElementById('botaoSalvar').addEventListener('click', () => {
    const produto = document.getElementById('campoproduto').value;
    if (produto) {
        salvarproduto(produto);
    } else {
        alert("Por favor, digite um nome.");
    }
});

// **Chama a função para carregar os nomes ASSIM QUE A PÁGINA ABRIR**
window.onload = carregarprodutos;
  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
</script>
// --- AGORA VOCÊ PODE USAR O BANCO DE DADOS! ---

// Exemplo: Função para salvar os dados de um formulário
async function salvarDados(nomedoproduto, referenciadoproduto, caracteristicadoproduto, imagemdopruto, materiadoproduto) {
  try {
    // 'usuarios' é o nome da sua coleção (como se fosse uma tabela)
    // O Firebase criará a coleção se ela não existir.
    const docRef = await addDoc(collection(db, "produtos"), {
      nome: nomedoproduto,
      referencia: referenciadoproduto,
      caracteristica: caracteristicadoproduto
      imagem: imagemdopruto
      Materia prima: materiadoproduto
      dataCadastro: new Date()
    });

    console.log("produto salvo com sucesso! ID:", docRef.id);
    alert("Obrigado por cadastrar um novo produto!");

  } catch (error) {
    console.error("Erro ao salvar os dados:", error);
    alert("Ocorreu um erro. Tente novamente.");
  }