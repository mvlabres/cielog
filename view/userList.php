<?php

require_once('../model/errorHandler.php');
require_once('../model/user.php');
require_once('../controller/userController.php');
require_once('../utils.php');

$userController = new UserController($MySQLi);
$user = new User();
$usersResult;

if(isset($_GET['action']) && $_GET['action'] != null){
    successAlert('Usuário salvo com sucesso'); 
}

if(isset($_POST['action']) && $_POST['action'] == 'delete'){

    $userId = $_POST['idDelete'];
    $result = $userController->delete($userId);

    if($result->hasError) errorAlert($result->result.$result->errorMessage);
    else successAlert($result->result);
}

$usersResult = $userController->findAll();

if($usersResult->hasError) errorAlert($usersResult->result.$usersResult->errorMessage);
?>

<div class="row row-space-between">
    <div class="col-lg-12">
        <h3 class="page-header" >Usuários</h3>
    </div>  
    <div class="center-box">
        <a href="index.php?content=newUser.php" type="button" class="btn btn-primary" id="user-save-btn">Novo</a>
    </div>             
</div>
<div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th scope="column" class="td-70">Nome</th>
                                    <th scope="column" class="td-70">Username</th>
                                    <th scope="column" class="td-70">Tipo</th>
                                    <th scope="column" class="td-70">Empresa</th>
                                    <th scope="column" class="td-30">Editar</th>
                                    <th scope="column" class="td-30">Excluir</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if(!$usersResult->hasError){
                                    foreach ($usersResult->result as $user) {
                                        echo '<tr class="odd gradeX">';
                                        echo '<td>'.$user->getName().'</td>';
                                        echo '<td>'.$user->getUsername().'</td>';
                                        echo '<td>'.$GLOBAL_USER_TYPES[$user->getType()].'</td>';
                                        echo '<td>'.$user->getClientName().'</td>';
                                        echo '<td class="text-center clickble"><a class="cell-action" href="index.php?content=newUser.php&userId='.$user->getId().'&action=edit"><span class="fa fa-edit text-primary cell-action"></span></a></td>';
                                        echo '<td class="text-center clickble" data-toggle="modal" data-target="#'.$user->getId().'"><span class="fa fa-trash text-primary"></span></td>';
                                        echo '</tr>';
                                        echo '<div class="modal fade" id="'.$user->getId().'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form role="form-delete" method="post" action="#">
                                                    <input type="hidden" name="idDelete" value="'.$user->getId().'">
                                                    <input type="hidden" name="action" value="delete" >
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">Excluir</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        Tem certeza que deseja deletar '.$user->getName().'?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
                                                        <button type="submit" class="btn btn-primary" id="confirm">Sim</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
       