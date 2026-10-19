# Guia Passo a Passo - Implementação de Paginação em driverList.php

## ⚡ **Implementação Rápida (2-3 horas)**

---

## **PASSO 1: Adicionar Métodos na Repository**

Arquivo: `repository/driverRepository.php`

Adicione estes dois métodos à classe `driverRepository`:

```php
// Após o método findAll() existente:

/**
 * Buscar drivers com paginação
 * @param int $page Número da página (começa em 1)
 * @param int $limit Quantidade de registros por página
 * @param string $search Termo de busca (opcional)
 * @param string $orderBy Campo para ordenação
 */
public function findPaginated($page = 1, $limit = 50, $search = '', $orderBy = 'name'){
    try {
        $page = max(1, (int)$page);
        $limit = max(1, min((int)$limit, 100)); // Max 100 por página
        $offset = ($page - 1) * $limit;
        
        $whereClause = '';
        if(!empty($search)){
            $searchTerm = $this->mySql->real_escape_string(trim($search));
            $whereClause = " WHERE (name LIKE '%$searchTerm%' 
                            OR cpf LIKE '%$searchTerm%' 
                            OR phone LIKE '%$searchTerm%' 
                            OR shipping_company LIKE '%$searchTerm%')";
        }
        
        // Validar orderBy
        $validOrderBy = ['name', 'cpf', 'phone', 'status', 'created_date'];
        $orderBy = in_array($orderBy, $validOrderBy) ? $orderBy : 'name';
        
        // Query com LIMIT
        $sql = $this->standardQuery . $whereClause . 
               " ORDER BY $orderBy LIMIT $offset, $limit";
        
        $result = $this->mySql->query($sql);
        
        return new ErrorHandler([
            'data' => $result,
            'query' => $sql
        ], false, null);
        
    } catch(Exception $ex){
        return new ErrorHandler('Erro ao buscar motoristas - ', true, $ex->getMessage());
    }
}

/**
 * Contar total de drivers (com filtro opcional)
 */
public function countDrivers($search = ''){
    try {
        $whereClause = '';
        if(!empty($search)){
            $searchTerm = $this->mySql->real_escape_string(trim($search));
            $whereClause = " WHERE (name LIKE '%$searchTerm%' 
                            OR cpf LIKE '%$searchTerm%' 
                            OR phone LIKE '%$searchTerm%' 
                            OR shipping_company LIKE '%$searchTerm%')";
        }
        
        $sql = "SELECT COUNT(*) as total FROM driver" . $whereClause;
        $result = $this->mySql->query($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
        
    } catch(Exception $ex){
        return 0;
    }
}
```

---

## **PASSO 2: Adicionar Método no Controller**

Arquivo: `controller/driverController.php`

Adicione este método:

```php
// Após método findAll() existente:

/**
 * Buscar drivers com paginação
 */
public function findPaginated($page = 1, $limit = 50, $search = '', $orderBy = 'name'){
    try {
        $result = $this->driverRepository->findPaginated($page, $limit, $search, $orderBy);
        
        if($result->hasError) return $result;
        
        // Carregar dados
        $data = $this->loadData($result->result['data'], false);
        
        // Contar total
        $total = $this->driverRepository->countDrivers($search);
        
        return new ErrorHandler([
            'data' => $data,
            'total' => $total,
            'page' => max(1, (int)$page),
            'limit' => max(1, min((int)$limit, 100)),
            'pages' => ceil($total / (int)$limit),
            'search' => $search,
            'orderBy' => $orderBy
        ], false, null);
        
    } catch(Exception $ex){
        return new ErrorHandler('Erro ao buscar motoristas - ', true, $ex->getMessage());
    }
}
```

---

## **PASSO 3: Reescrever View (driverList.php)**

Arquivo: `view/driverList.php`

Substitua o conteúdo inteiro pelo código abaixo:

```php
<?php

require_once('../model/errorHandler.php');
require_once('../model/driver.php');
require_once('../controller/driverController.php');
require_once('../utils.php');

$driverController = new DriverController($MySQLi);

// Obter parâmetros
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 50; // Registros por página
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'name';

// Ação de delete
if(isset($_POST['action']) && $_POST['action'] == 'delete'){
    $driverId = $_POST['idDelete'];
    $result = $driverController->delete($driverId);
    if(!$result->hasError) {
        successAlert($result->result);
        // Redirect para mesma página
        header("Location: index.php?content=driverList.php&page=$page&search=".urlencode($search));
        exit;
    }else{
        errorAlert($result->result.$result->errorMessage);
    }
}

// Mensagem de sucesso após insert
if(isset($_GET['action']) && $_GET['action'] != null){
    successAlert('Registro salvo com sucesso');
}

// Buscar dados paginados
$driverResult = $driverController->findPaginated($page, $limit, $search, $orderBy);

?>

<div class="row row-space-between">
    <div class="col-lg-12">
        <h3 class="page-header">Motoristas, visitantes e outros</h3>
    </div>  
    <div class="center-box">
        <a href="index.php?content=newDriver.php" type="button" class="btn btn-primary">Novo Motorista</a>
    </div>             
</div>

<!-- FILTRO DE BUSCA -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <form method="GET" action="">
                    <input type="hidden" name="content" value="driverList.php">
                    
                    <div class="form-group">
                        <label>Buscar Motorista</label>
                        <input type="text" name="search" placeholder="Nome, CPF, Telefone ou Transportadora..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="form-control" style="margin-bottom: 10px;">
                    </div>
                    
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <a href="index.php?content=driverList.php" class="btn btn-default">
                            <i class="fa fa-times"></i> Limpar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TABELA DE DADOS -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-body" style="overflow-x: auto;">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <td style="width: 60px;">Acesso</td>
                                <td style="width: 60px;">Ver</td>
                                <td style="width: 60px;">Editar</td>
                                <th>Nome</th>
                                <th style="width: 120px;">CPF</th>
                                <th>Tipo</th>
                                <th>CNH</th>
                                <th>Vencimento CNH</th>
                                <th>Telefone</th>
                                <th>Transportadora</th>
                                <th>Tipo Veículo</th>
                                <th>Placa</th>
                                <th>Placa 2</th>
                                <th>Placa 3</th>
                                <th>Status</th>
                                <th>Motivo Bloqueio</th>
                                <td style="width: 60px;">Excluir</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!$driverResult->hasError && isset($driverResult->result['data'])){
                                $drivers = $driverResult->result['data'];
                                
                                if(count($drivers) > 0){
                                    foreach ($drivers as $driver) {
                                        $driverId = $driver->getId();
                                        echo '<tr class="odd gradeX">';
                                        echo '<td class="text-center"><a href="index.php?content=newDriverAccess.php&driverId='.$driverId.'"><span class="fa fa-hand-o-right text-primary"></span></a></td>';
                                        echo '<td class="text-center"><a href="index.php?content=newDriver.php&driverId='.$driverId.'&action=view"><span class="fa fa-search text-primary"></span></a></td>';
                                        echo '<td class="text-center"><a href="index.php?content=newDriver.php&driverId='.$driverId.'&action=edit"><span class="fa fa-edit text-primary"></span></a></td>';
                                        echo '<td>'.$driver->getName().'</td>';
                                        echo '<td>'.$driver->getCpf().'</td>';
                                        echo '<td>'.$DRIVER_RECORD_TYPES[$driver->getRecordType()].'</td>';
                                        echo '<td>'.$driver->getCnh().'</td>';
                                        echo '<td>'.$driver->getCnhExpiration().'</td>';
                                        echo '<td>'.$driver->getPhone().'</td>';
                                        echo '<td>'.$driver->getShippingCompany().'</td>';
                                        echo '<td>'.$driver->getVehicleType().'</td>';
                                        echo '<td>'.$driver->getVehiclePlate().'</td>';
                                        echo '<td>'.$driver->getVehiclePlate2().'</td>';
                                        echo '<td>'.$driver->getVehiclePlate3().'</td>';
                                        echo '<td>'.$DRIVER_STATUS[$driver->getStatus()].'</td>';
                                        echo '<td>'.$driver->getBlockReason().'</td>';
                                        echo '<td class="text-center" data-toggle="modal" data-target="#delete-modal-'.$driverId.'" style="cursor: pointer;">';
                                        echo '<span class="fa fa-trash text-danger"></span>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                }else{
                                    echo '<tr><td colspan="17" class="text-center" style="padding: 20px;">Nenhum registro encontrado</td></tr>';
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

<!-- PAGINAÇÃO -->
<?php if(!$driverResult->hasError && isset($driverResult->result['pages'])): 
    $total_pages = $driverResult->result['pages'];
    $current_page = $driverResult->result['page'];
    $total = $driverResult->result['total'];
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <!-- Info -->
                <p class="text-muted" style="margin-bottom: 15px;">
                    Mostrando 
                    <strong><?php echo $limit; ?></strong> de 
                    <strong><?php echo $total; ?></strong> 
                    registros (Página <strong><?php echo $current_page; ?></strong> de <strong><?php echo $total_pages; ?></strong>)
                </p>
                
                <!-- Paginação -->
                <nav>
                    <ul class="pagination">
                        <!-- Primeira página -->
                        <?php if($current_page > 1): ?>
                            <li>
                                <a href="index.php?content=driverList.php&page=1&search=<?php echo urlencode($search); ?>">
                                    <span aria-hidden="true">&laquo;</span> Primeira
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Anterior -->
                        <?php if($current_page > 1): ?>
                            <li>
                                <a href="index.php?content=driverList.php&page=<?php echo $current_page-1; ?>&search=<?php echo urlencode($search); ?>">
                                    <span aria-hidden="true">&lsaquo;</span> Anterior
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Números das páginas -->
                        <?php
                        $start = max(1, $current_page - 2);
                        $end = min($total_pages, $current_page + 2);
                        
                        if($start > 1){
                            echo '<li><a href="index.php?content=driverList.php&page=1&search='.urlencode($search).'">1</a></li>';
                            if($start > 2) echo '<li class="disabled"><span>...</span></li>';
                        }
                        
                        for($i = $start; $i <= $end; $i++){
                            if($i == $current_page){
                                echo '<li class="active"><span>'.$i.'</span></li>';
                            }else{
                                echo '<li><a href="index.php?content=driverList.php&page='.$i.'&search='.urlencode($search).'">'.$i.'</a></li>';
                            }
                        }
                        
                        if($end < $total_pages){
                            if($end < $total_pages - 1) echo '<li class="disabled"><span>...</span></li>';
                            echo '<li><a href="index.php?content=driverList.php&page='.$total_pages.'&search='.urlencode($search).'">'.$total_pages.'</a></li>';
                        }
                        ?>
                        
                        <!-- Próxima -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=driverList.php&page=<?php echo $current_page+1; ?>&search=<?php echo urlencode($search); ?>">
                                    Próxima <span aria-hidden="true">&rsaquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Última página -->
                        <?php if($current_page < $total_pages): ?>
                            <li>
                                <a href="index.php?content=driverList.php&page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>">
                                    Última <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- MODAIS DE DELEÇÃO -->
<?php
if(!$driverResult->hasError && isset($driverResult->result['data'])){
    foreach ($driverResult->result['data'] as $driver) {
        $driverId = $driver->getId();
        ?>
        <div class="modal fade" id="delete-modal-<?php echo $driverId; ?>" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Confirmar Exclusão</h4>
                        </div>
                        <div class="modal-body">
                            <p>Tem certeza que deseja deletar <strong><?php echo htmlspecialchars($driver->getName()); ?></strong>?</p>
                            <p class="text-danger"><small>Esta ação não pode ser desfeita!</small></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="idDelete" value="<?php echo $driverId; ?>">
                                Deletar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
```

---

## **PASSO 4: Executar Índices**

Execute o script SQL no seu MySQL:

```bash
mysql -u root -p cielog < database/add_driverlist_indexes.sql
```

---

## **PASSO 5: Testar**

1. Acesse: `http://localhost/cielog/view/index.php?content=driverList.php`
2. Verifique:
   - [ ] Tabela carrega apenas 50 registros
   - [ ] Paginação funciona
   - [ ] Busca filtra resultados
   - [ ] Deletar funciona
   - [ ] Tempo de carregamento < 1 segundo

---

## **Configurações Opcionais**

Para mudar quantidade de registros por página, edite em `driverController.php`:

```php
$limit = 50;  // Mudar este valor
// Opções: 25, 50, 100
```

---

## **Troubleshooting**

Se ocorrerem erros:

1. **"findPaginated not found"** → Verifique se adicionou método em Repository e Controller
2. **Blank page** → Verifique logs de erro em `error_log`
3. **Paginação não funciona** → Limpe cache do navegador (Ctrl+F5)

---

## **Próximos Passos Opcionais**

- [ ] Adicionar filtro por Status
- [ ] Adicionar export para Excel
- [ ] Adicionar importação em massa
- [ ] Implementar soft delete

