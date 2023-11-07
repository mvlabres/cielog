<?php
// require('klabin-agendamentos/pages/class.php');
// require('klabin-agendamentos/functions.php');

// $usuario = new Usuario();
// $result = $usuario->buscarUsuario($_SESSION['id'], $MySQLi);
// while ($dados = $result->fetch_assoc()){ 
//     $usuario->setId($dados['id']);
//     $usuario->setNome($dados['nome']);
//     $usuario->setPassword($dados['password']);
//     $usuario->setTipo($dados['tipo']);
// }
// if(isset($_POST['senha']) && $_POST['senha'] != null){
//     $usuario->setId($_POST['id']);
//     $usuario->setNome($_POST['nome']);
//     $usuario->setPassword($_POST['check_senha']);
//     $usuario->setTipo($_POST['tipo']);
//     if($usuario->alterarSenha($_POST['id'], $MySQLi, $_POST['tipo'], $_POST['nome']) == true){
//         echo messageSuccess('Senha alterada com sucesso!');
//     }
//}

?>

<!-- <div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Alterar senha</h1>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form-new-user" method="post" action="index.php?conteudo=trocar-senha.php&senha=save">
                        <input type="hidden" name="id" value="<?php echo $usuario->getId() ?>">
                        <input type="hidden" name="tipo" value="<?php echo $usuario->getTipo() ?>">
                        <input type="hidden" name="nome" value="<?php echo $usuario->getNome() ?>">
                            <div class="form-group">
                                <label>Senha</label>
                                <input class="form-control" type="password" id="senha" placeholder="Senha" minlength="6" maxlength="20" name="senha" value="<?php echo $usuario->getPassword() ?>" required>
                                <p class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <div class="form-group">
                                <label>Repita a senha</label>
                                <input class="form-control" type="password" placeholder="Senha" name="check_senha" minlength="6" maxlength="20" onkeyup="verificaSenha(this, 'senha')" value="<?php echo $usuario->getPassword() ?> "required>
                                <p id="feedback-senha" class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <button id="btn-salvar" type="submit" class="btn btn-primary">Salvar</button>
                            <button type="reset" class="btn btn-danger">Cancelar</button>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
    </div> 
</div>-->

<!-- <script>
    var label = document.getElementById('feedback-senha');
    function verificaSenha(senha1, el2){
		var senha2 = document.getElementById(el2);
		if(senha1.value == senha2.value){
			label.innerHTML = 'Senhas iguais';
            document.getElementById('btn-salvar').disabled = false;
		}
		else{
			label.innerHTML = 'Senhas não conferem';
            document.getElementById('btn-salvar').disabled = true;
		}
		liberaSalvar()
	} 
</script> -->
       