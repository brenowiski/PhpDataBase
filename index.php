<?php
require('db/conexao.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserindo Dados</title>
    <style>
        table{
            border-collapse:collapse;
            width:100%;
        }
        th,td{
             padding:10px;
             text-align:center;
             border:1px solid #ccc;
        }
        p{
            padding:20px;
            border:1px solid #ccc;            
        }
        .oculto{
            display:none;
        }
    </style>
</head>
<body>
    <h1> Aula Inserindo Dados</h1>
        <form id="form_salva" method="post">
            <input type="text" name="nome" placeholder="Digite seu nome" required>
            <input type="email" name="email" placeholder="Digite seu email" required>
            <button type="submit" name="salvar">Salvar</button>
        </form>
        
        <form class="oculto" id="form_atualiza" method="post">
            <input type="hidden" name="id_editado" id="id_editado" placeholder="ID" required>
            <input type="text" name="nome_editado" id="nome_editado" placeholder="Editar nome" required>
            <input type="email" name="email_editado" id="email_editado" placeholder="Editar email" required>
            <button type="submit" name="atualizar">Atualizar</button>
            <button type="button" id="cancelar" name="cancelar">Cancelar</button>
        </form>

        <form class="oculto" id="form_deleta" method="post">
            <input type="hidden" name="nome_deleta" id="nome_deleta" placeholder="Digite seu nome" required>
            <input type="hidden" name="email_deleta" id="email_deleta" placeholder="Digite seu email" required>
            <input type="hidden" name="id_deleta" id="id_deleta" placeholder="ID" required>
            <b>Tem certeza que deseja deletar o cliente, <span id=cliente></span>?</b>
            <button type="submit" name="deletar">Confirmar</button>
            <button type="button" id="cancelar_delete" name="cancelar_delete">Cancelar</button>
        </form>
        

        <?php
        //INSERIR DADO NO BANCO (MODO SIMPLES)

        // $sql = $pdo->prepare("INSERT INTO clientes VALUES (null,'Saitama','one@punchman.com','12-05-2022') ");
        // $sql->execute();

        //  MODO CORRETO ANTI SQL INJECTION | qwery preparada

        if (isset($_POST['salvar']) && isset($_POST['nome']) && isset($_POST['email'])){

        $nome = limparPost($_POST['nome']);
        $email = limparPost($_POST['email']);
        $data = date('d-m-Y');

        // VALIDAR QUE SEJA PREENCHIDO

        if ($nome=="" || $nome==null){
            echo "Nome não pode ser vazio";
            exit();
        }

        if ($email=="" || $email==null){
            echo "Email não pode ser vazio";
            exit();
        }

        // VALIDAR NOME E EMAIL

        if (!preg_match("/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ'\s]+$/",$nome)) {
            echo "Somente permitido letras e espaços em branco!";
     }

        //VERIFICAR SE EMAIL VALIDOS

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Email inválido!";
            exit();
        }


        $sql = $pdo->prepare("INSERT INTO clientes VALUES (null,?,?,?)");
        $sql->execute(array($nome,$email,$data));

        echo "<b style='color:green'>Cliente inserido com sucesso!</b>";
    }  
    ?>

    <?php

        if(isset($_POST['atualizar']) && isset($_POST['id_editado']) && isset($_POST['nome_editado']) && isset($_POST['email_editado'])){
            $id=limparPost($_POST['id_editado']);
            $nome=limparPost($_POST['nome_editado']);
            $email=limparPost($_POST['email_editado']);

            if ($nome=="" || $nome==null){
                echo "Nome não pode ser vazio";
                exit();
            }
    
            if ($email=="" || $email==null){
                echo "Email não pode ser vazio";
                exit();
            }
    
            // VALIDAR NOME E EMAIL
    
            if (!preg_match("/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ'\s]+$/",$nome)) {
                echo "Somente permitido letras e espaços em branco!";
         }
    
            //VERIFICAR SE EMAIL VALIDOS
    
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Email inválido!";
                exit();
            }

            
            $sql = $pdo->prepare("UPDATE clientes SET nome=?, email=? WHERE id=?");
            $sql->execute(array($nome,$email,$id));

            echo "<b style='color:green'>Atualizado ".$sql->rowCount()." registros!</b>";
    
        }
    ?>
    <?php
        //DELETAR DADOS

        if(isset($_POST['deletar']) && isset($_POST['id_deleta']) && isset($_POST['email_deleta']) && isset($_POST['nome_deleta'])){
            $id=limparPost($_POST['id_deleta']);
            $nome=limparPost($_POST['nome_deleta']);
            $email=limparPost($_POST['email_deleta']);

            //COMANDO PARA ATUALIZAR
            $sql = $pdo->prepare("DELETE FROM clientes WHERE id=? AND nome=? AND email=?");
            $sql->execute(array($id, $nome, $email));

            echo "Deletado com sucesso";
        }

    ?>



    <?php 
        //SELECIONAR DADOS DA TABELA - EXEMPLO SIMPLES
        $sql = $pdo->prepare("SELECT * FROM clientes");
        $sql ->execute();
        $dados = $sql->fetchAll();

        //EXEMPLO COM FILTRAGEM
        // $sql = $pdo->prepare("SELECT * FROM clientes WHERE email= ?");
        // $email = 'barba_branca@onepiece.comm';
        // $sql ->execute(array($email));
        // $dados = $sql->fetchAll();

        // echo "<pre>";
        // print_r($dados);
        // echo "</pre>";
    ?>



    <?php

        if(count($dados) > 0){
            echo "<br><br><table>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                    </tr>";

            foreach($dados as $chave => $valor){
                    echo "<tr>
                        <td>".$valor['id']."</td>
                        <td>".$valor['nome']."</td>
                        <td>".$valor['email']."</td>
                        <td><a href='#' class='btn-atualizar' data-id='".$valor['id']."' data-nome='".$valor['nome']."' data-email='".$valor['email']."'>Atualizar</a> | 
                        <a href='#' class='btn-deletar' data-id='".$valor['id']."' data-nome='".$valor['nome']."' data-email='".$valor['email']."'>Deletar</a></td>
                        </tr>";
                }
            echo "</table>";    
        }else{
            echo "<p>Nenhum cliente cadastrado</p>";
        }

    ?> 

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(".btn-atualizar").click(function(){
            var id = $(this).attr('data-id');
            var nome = $(this).attr('data-nome');
            var email = $(this).attr('data-email');

            $('#form_salva').addClass('oculto');
            $('#form_atualiza').removeClass('oculto');
            $('#form_deleta').addClass('oculto');

            $("#id_editado").val(id);
            $("#nome_editado").val(nome);
            $("#email_editado").val(email);
        });

            $(".btn-deletar").click(function(){
            var id = $(this).attr('data-id');
            var nome = $(this).attr('data-nome');
            var email = $(this).attr('data-email');

            $("#id_deleta").val(id);
            $("#nome_deleta").val(nome);
            $("#email_deleta").val(email);
            $("#cliente ").html(nome);

            $('#form_salva').addClass('oculto');
            $('#form_atualiza').addClass('oculto');
            $('#form_deleta').removeClass('oculto');

            
            // alert('O ID: '+id+' | Nome: '+nome+' | Email: '+email);
        });

        $('#cancelar').click(function(){
            $('#form_salva').removeClass('oculto');
            $('#form_atualiza').addClass('oculto');
            $('#form_deleta').addClass('oculto');
        });

        $('#cancelar_delete').click(function(){
            $('#form_salva').removeClass('oculto');
            $('#form_atualiza').addClass('oculto');
            $('#form_deleta').addClass('oculto');        
        });
    </script>           
</body>
</html>