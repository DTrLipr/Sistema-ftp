<script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-app.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  const firebaseConfig = {
    apiKey: "AIzaSyBEZwzN3Z37EVoIX8WbcUg03PltWIpFof0",
    authDomain: "sistemaftp3.firebaseapp.com",
    projectId: "sistemaftp3",
    storageBucket: "sistemaftp3.firebasestorage.app",
    messagingSenderId: "706827479169",
    appId: "1:706827479169:web:ebd0bab92fd7216d978580"
  };

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