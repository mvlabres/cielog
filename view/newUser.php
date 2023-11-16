<?php

require_once('../utils.php');
require_once('../model/user.php');
require_once('../model/client.php');
require_once('../controller/clientController.php');
require_once('../controller/userController.php');

$userController = new UserController($MySQLi);
$clientController = new ClientController($MySQLi);
$user = new User();

$action = 'save';
$title = 'Criar';
$disabledField = 'disabled';
$disabledButtonSave = 'disabled';

if(isset($_GET['action']) && $_GET['action'] != null){
    $action = $_GET['action'];
}

if(isset($_GET['userId']) && $_GET['userId'] != null){

    $userId = $_GET['userId'];
    $title = 'Editar';
    $disabledButtonSave = '';

    $result = $userController->findById($userId);
    
    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    
    $user = $result->result;

    if($user->getType() == 'client') $disabledField = '';
}

if(isset($_POST['name']) && $_POST['name'] != null){

    $action = $_POST['action'];
    $result = $userController->save($_POST, $action);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else echo "<script>window.location='index.php?content=userList.php&action=save'</script>";
}

$clientsResult = $clientController->findAll();

if($clientsResult->hasError) errorAlert($clientsResult->result.$clientsResult->errorMessage);

?>

<div class="row">
    <div class="col-lg-12">
    <h3 class="page-header" >Usuário - <span id="title"><?=$title ?></span></h3>
    </div>                
</div>
<div class="row">
    <div class="col-lg-12">
         <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form-new-user" method="post" action="#">
                        <input  type="hidden" name="id" value="<?=$user->getId() ?>" >
                        <input  type="hidden" name="action" value="<?=$action ?>" >
                            <div class="form-group">
                                <label>Nome</label>
                                <input class="form-control" name="name" placeholder="Nome" maxlength="100" value="<?=$user->getName()  ?>" required>
                                <p class="help-block">Insira o nome completo do novo usuário.</p>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input class="form-control" name="username" maxlength="50" placeholder="Username" value="<?php echo $user->getUsername() ?>" required>
                                <p class="help-block">Informe o username que usuário irá usar para acessar o sistema.</p>
                            </div>
                            <div class="form-group">
                                <label>Tipo de acesso</label>
                                <select name="type" class="form-control placeholder" aria-label="Default select example" onchange="checkUserType(this)" required>
                                    <option value="">Selecione...</option>
                                    <?php

                                    foreach ($GLOBAL_USER_TYPES as $key => $value) {

                                        $selected = null;
                                        if($user->getType() == $key) $selected = 'selected';

                                        echo '<option value="'.$key.'" '.$selected.' >'.$value.'</option>';
                                    }
                                    ?>
                                  </select>
                            </div>
                            <div class="form-group">
                                <label>Empresa</label>
                                <select name="business" id="business" class="form-control" <?=$disabledField ?>>
                                    <option value="">Selecione...</option>
                                    <?php

                                    if(!$clientsResult->hasError){
                                        foreach ($clientsResult->result as $client) {
                                            $selected = null;
                                            if($user->getClientId() == $client->getId()) $selected = 'selected';

                                            echo '<option value="'.$client->getId().'" '.$selected.' >'.$client->getName().'</option>';

                                        }
                                    }
                                        
                                    ?>
                                </select>
                                <p class="help-block">Informe qual a empresa esse usuário pertence (Ele terá acesso as informações relacionadas a essa empresa)</p>
                            </div>
                            <div class="form-group">
                                <label>Senha</label>
                                <input class="form-control" id="password" type="password" minlength="6" maxlength="20" placeholder="Senha" name="password" value="<?=$user->getPassword() ?>" required>
                                <p class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <div class="form-group">
                                <label>Repita a senha</label>
                                <input class="form-control" onkeyup="checkPassword(this)" type="password" minlength="6" maxlength="20" placeholder="Senha"  value="<?=$user->getPassword() ?>" >
                                <p id="passwordFeedback" class="help-block">Mínimo de 6 caracteres.</p>
                            </div>
                            <button type="submit" class="btn btn-primary" id="user-save-btn" <?=$disabledButtonSave ?>>Salvar</button>
                            <button type="reset" class="btn btn-danger">Cancelar</button>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</div>

       