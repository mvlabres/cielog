<?php

require_once('../conn.php');
require_once('../session.php');
require_once('../utils.php'); 

?>
<body>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <h3 class="display-2">Ações rápidas</h3>
            </div>
        </div>
        <div class="panel-home">
                <a href="#" class="quick-action-box box-orange0">
                    <div class="box-home-header">
                        <p>Acessos veículos</p>
                    </div>
                </a>
                <a href="#" class="quick-action-box box-orange1" >
                    <div class="box-home-header">
                        <p>Acesso colaborador</p>
                    </div>
                </a>
                <a href="index.php?content=newDriver.php" class="quick-action-box box-orange2">
                    <div class="box-home-header">
                        <p>Novo motorista</p>
                    </div>
                </a>
                <a href="index.php?content=newEmployee.php" class="quick-action-box box-orange3">
                    <div class="box-home-header">
                        <p>Novo colaborador</p>
                    </div>
                </a>
            </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-title">
                <h3 class="display-2">Acessos de veículos em aberto</h3>
            </div>
            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead>
                    <tr>
                        <th scope="column" class="td-70">Nome</th>
                        <th scope="column" class="td-70">Matrícula</th>
                        <th scope="column" class="td-70">CPF</th>
                        <th scope="column" class="td-70">Empresa</th>
                        <th scope="column" class="td-70">Veículo</th>
                        <th scope="column" class="td-70">Placa Veículo</th>
                        <th scope="column" class="td-30">Editar</th>
                        <th scope="column" class="td-30">Excluir</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
