<?php

require_once('../utils.php');
require_once('../model/user.php');
require_once('../controller/userController.php');

$userController = new UserController($MySQLi);
$user = new User();

$result = $userController->findById($_SESSION['id'] );
$user = $result->result;

if(isset($_POST['password']) && $_POST['password'] != null){

    $result = $userController->save($_POST, 'edit');

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else echo "<script>window.location='index.php?user=success'</script>";
}

?>

<div class="row">
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
                        <form role="form-new-user" method="post" action="index.php?content=userChangePassword.php">
                            <input type="hidden" name="id" value="<?=$user->getId() ?>">
                            <input type="hidden" name="type" value="<?=$user->getType() ?>">
                            <input type="hidden" name="username" value="<?=$user->getUserName() ?>">
                            <input type="hidden" name="business" value="<?=$user->getClientId() ?>">
                            <input type="hidden" name="name" value="<?=$user->getName() ?>">
                            <div class="form-group">
                                <label>Senha</label>
                                <input class="form-control" type="password" id="password" placeholder="Senha" minlength="6" maxlength="20" name="password" value="<?=$user->getPassword() ?>" required>
                                <p class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <div class="form-group">
                                <label>Repita a senha</label>
                                <input class="form-control" type="password" placeholder="Senha" name="check_senha" minlength="6" maxlength="20" onkeyup="checkPassword(this)" value="<?=$user->getPassword() ?> "required>
                                <p id="passwordFeedback" class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <button id="user-save-btn" type="submit" class="btn btn-primary">Salvar</button>
                            <button type="reset" class="btn btn-danger">Cancelar</button>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
    </div> 
</div>
       