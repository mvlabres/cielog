<?php

include_once('../model/errorHandler.php');

class DriverAccessRepository{

    private $mySql;
    private $standardQuery = 'SELECT dr_a.id AS access_id,
                                    dr.id AS driver_id,
                                    dr.name AS driver_name,
                                    start_datetime,
                                    end_datetime,
                                    dr.cnh AS driver_cnh,
                                    dr.cnh_expiration AS driver_cnh_expiration,
                                    dr.phone AS driver_phone,
                                    dr.cpf AS driver_cpf,
                                    dr.shipping_company AS driver_shipping_company,
                                    dr_a.vehicle_type AS driver_vehicle_type,
                                    dr_a.vehicle_plate AS driver_vehicle_plate,
                                    dr_a.vehicle_plate2 AS driver_vehicle_plate2,
                                    dr_a.vehicle_plate3 AS driver_vehicle_plate3,
                                    cl.id AS business_id,
                                    cl.name AS business_name,
                                    business_client_id,
                                    bc.name AS business_client_name,
                                    inbound_invoice,
                                    outbound_invoice,
                                    operation_type,
                                    rotation,
                                    dr.status AS driver_access_status,
                                    dr.block_reason AS driver_block_reason,
                                    dr_a.created_date AS access_created_date,
                                    dr_a.modified_date AS access_modified_date,
                                    dr_a.created_by AS access_created_by,
                                    us_2.name AS access_created_by_name,
                                    dr_a.modified_by AS access_modified_by,
                                    us_3.name AS access_modified_name,
                                    us_1.id AS access_outbound_id,
                                    us_1.name AS access_outbound_name
                              FROM driver_access AS dr_a
                              INNER JOIN driver AS dr ON dr.id = driver_id
                              INNER JOIN client AS cl ON cl.id = business_id
                              INNER JOIN user AS us_1 ON us_1.id = user_outbound_id
                              INNER JOIN user AS us_2 ON us_2.id = dr_a.created_by
                              LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
                              LEFT JOIN user AS us_3 ON us_3.id = dr_a.modified_by ';

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
           $sql = $this->standardQuery . ' ORDER BY start_datetime ASC';

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByNullEndDate(){

        try{
            $sql = $this->standardQuery . " WHERE (ISNULL(end_datetime) OR end_datetime LIKE '0000-00%') ORDER BY start_datetime ASC";

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos teste - ', true, $ex->getMessage() . $ex->getTrace());
        }
    }

    public function findByStartDateEndDateAndBusiness($startDate, $endDate, $businessId){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' ORDER BY start_datetime ASC';


            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }
    

    public function findByStartDateEndDateAndBusinessAndClosedAccess($startDate, $endDate, $businessId){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND end_datetime NOT LIKE "0000%" ORDER BY start_datetime ASC';

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByStartDateEndDateAndBusinessAndBusinessClient($startDate, $endDate, $businessId, $businessClientId){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId.' ORDER BY start_datetime ASC';

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }
    

    public function findByStartDateEndDateAndBusinessAndBusinessClientAndClosedAccess($startDate, $endDate, $businessId, $businessClientId){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId.' AND end_datetime NOT LIKE "0000%" ORDER BY start_datetime ASC';

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByStartDateAndEndDateAndClosedAccess($startDate, $endDate){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND end_datetime NOT LIKE "0000%" ORDER BY start_datetime ASC';

            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findByStartDateAndEndDate($startDate, $endDate){

        try{
            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" ORDER BY start_datetime ASC';


            return new ErrorHandler($this->mySql->query($sql), false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Error ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function findById($id){

        try{
            $sql = $this->standardQuery . 'WHERE dr_a.id = '.$id;
            return new ErrorHandler($this->mySql->query($sql), false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acesso - ', true, $ex->getMessage());
        }
    }

    // ===== PAGINATED METHODS =====

    public function findByStartDateAndEndDatePaginated($startDate, $endDate, $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateAndEndDate($startDate, $endDate){
        try{
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'"';
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateAndEndDateAndClosedAccessPaginated($startDate, $endDate, $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND end_datetime NOT LIKE "0000%" ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateAndEndDateAndClosedAccess($startDate, $endDate){
        try{
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND end_datetime NOT LIKE "0000%"';
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateEndDateAndBusinessPaginated($startDate, $endDate, $businessId, $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateEndDateAndBusiness($startDate, $endDate, $businessId){
        try{
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId;
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateEndDateAndBusinessAndClosedAccessPaginated($startDate, $endDate, $businessId, $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND end_datetime NOT LIKE "0000%" ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateEndDateAndBusinessAndClosedAccess($startDate, $endDate, $businessId){
        try{
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND end_datetime NOT LIKE "0000%"';
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateEndDateAndBusinessAndBusinessClientPaginated($startDate, $endDate, $businessId, $businessClientId, $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId.' ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateEndDateAndBusinessAndBusinessClient($startDate, $endDate, $businessId, $businessClientId){
        try{
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId;
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateEndDateAndBusinessAndBusinessClientAndClosedAccessPaginated($startDate, $endDate, $businessId, $businessClientId, $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId.' AND end_datetime NOT LIKE "0000%" ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateEndDateAndBusinessAndBusinessClientAndClosedAccess($startDate, $endDate, $businessId, $businessClientId){
        try{
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId.' AND end_datetime NOT LIKE "0000%"';
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    // ===== SEARCH METHODS WITH TEXT FILTER =====

    private function buildSearchClause($search){
        if(empty($search)) return '';
        $searchTerm = $this->mySql->real_escape_string(trim($search));
        return " AND (dr.name LIKE '%$searchTerm%'
                  OR dr.cpf LIKE '%$searchTerm%'
                  OR dr_a.vehicle_plate LIKE '%$searchTerm%'
                  OR dr_a.vehicle_plate2 LIKE '%$searchTerm%'
                  OR dr_a.vehicle_plate3 LIKE '%$searchTerm%'
                  OR cl.name LIKE '%$searchTerm%'
                  OR bc.name LIKE '%$searchTerm%')";
    }

    public function findByStartDateAndEndDateWithSearchPaginated($startDate, $endDate, $search = '', $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;
            $searchClause = $this->buildSearchClause($search);

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'"'.$searchClause.' ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateAndEndDateWithSearch($startDate, $endDate, $search = ''){
        try{
            $searchClause = $this->buildSearchClause($search);
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN driver AS dr ON dr.id = dr_a.driver_id
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'"'.$searchClause;
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateAndEndDateWithSearchAndClosedAccessPaginated($startDate, $endDate, $search = '', $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;
            $searchClause = $this->buildSearchClause($search);

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND end_datetime NOT LIKE "0000%"'.$searchClause.' ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateAndEndDateWithSearchAndClosedAccess($startDate, $endDate, $search = ''){
        try{
            $searchClause = $this->buildSearchClause($search);
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN driver AS dr ON dr.id = dr_a.driver_id
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND end_datetime NOT LIKE "0000%"'.$searchClause;
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateEndDateAndBusinessWithSearchPaginated($startDate, $endDate, $businessId, $search = '', $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;
            $searchClause = $this->buildSearchClause($search);

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.$searchClause.' ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateEndDateAndBusinessWithSearch($startDate, $endDate, $businessId, $search = ''){
        try{
            $searchClause = $this->buildSearchClause($search);
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN driver AS dr ON dr.id = dr_a.driver_id
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.$searchClause;
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateEndDateAndBusinessWithSearchAndClosedAccessPaginated($startDate, $endDate, $businessId, $search = '', $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;
            $searchClause = $this->buildSearchClause($search);

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND end_datetime NOT LIKE "0000%"'.$searchClause.' ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateEndDateAndBusinessWithSearchAndClosedAccess($startDate, $endDate, $businessId, $search = ''){
        try{
            $searchClause = $this->buildSearchClause($search);
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN driver AS dr ON dr.id = dr_a.driver_id
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND end_datetime NOT LIKE "0000%"'.$searchClause;
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateEndDateAndBusinessAndBusinessClientWithSearchPaginated($startDate, $endDate, $businessId, $businessClientId, $search = '', $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;
            $searchClause = $this->buildSearchClause($search);

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId.$searchClause.' ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateEndDateAndBusinessAndBusinessClientWithSearch($startDate, $endDate, $businessId, $businessClientId, $search = ''){
        try{
            $searchClause = $this->buildSearchClause($search);
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN driver AS dr ON dr.id = dr_a.driver_id
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId.$searchClause;
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function findByStartDateEndDateAndBusinessAndBusinessClientWithSearchAndClosedAccessPaginated($startDate, $endDate, $businessId, $businessClientId, $search = '', $page = 1, $limit = 50){
        try{
            $page = max(1, (int)$page);
            $limit = max(1, min((int)$limit, 100));
            $offset = ($page - 1) * $limit;
            $searchClause = $this->buildSearchClause($search);

            $sql = $this->standardQuery . ' WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId.' AND end_datetime NOT LIKE "0000%"'.$searchClause.' ORDER BY start_datetime ASC LIMIT '.$offset.', '.$limit;

            return new ErrorHandler([
                'data' => $this->mySql->query($sql),
                'query' => $sql
            ], false, null);
        }catch(Exception $ex){
            return new ErrorHandler('Erro ao buscar acessos - ', true, $ex->getMessage());
        }
    }

    public function countByStartDateEndDateAndBusinessAndBusinessClientWithSearchAndClosedAccess($startDate, $endDate, $businessId, $businessClientId, $search = ''){
        try{
            $searchClause = $this->buildSearchClause($search);
            $sql = 'SELECT COUNT(*) as total FROM driver_access AS dr_a
                    INNER JOIN driver AS dr ON dr.id = dr_a.driver_id
                    INNER JOIN client AS cl ON cl.id = dr_a.business_id
                    LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
                    WHERE start_datetime >= "'.$startDate.'" AND start_datetime <= "'.$endDate.'" AND cl.id = '.$businessId.' AND bc.id = '.$businessClientId.' AND end_datetime NOT LIKE "0000%"'.$searchClause;
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }catch(Exception $ex){
            return 0;
        }
    }

    public function save($driverAccess){

        try {
            $sql = 'INSERT INTO driver_access (start_datetime,end_datetime,driver_id,business_id,vehicle_type,vehicle_plate,vehicle_plate2,vehicle_plate3,inbound_invoice,outbound_invoice,operation_type,rotation,business_client_id,user_outbound_id,created_date,created_by)
            VALUES(
                "'.$driverAccess->getStartDatetime().'", 
                "'.$driverAccess->getEndDatetime().'", 
                '.$driverAccess->getDriverId().', 
                '.$driverAccess->getBusinessId().',
                "'.$driverAccess->getVehicleType().'", 
                "'.$driverAccess->getVehiclePlate().'", 
                "'.$driverAccess->getVehiclePlate2().'", 
                "'.$driverAccess->getVehiclePlate3().'", 
                "'.$driverAccess->getInboundInvoice().'", 
                "'.$driverAccess->getOutboundInvoice().'", 
                "'.$driverAccess->getOperationType().'", 
                "'.$driverAccess->getRotation().'",
                '.$driverAccess->getBusinessClientId().',
                '.$_SESSION['id'].', 
                "'.date("Y-m-d").'", 
                '.$_SESSION['id'].' 
            )';
            
            $sql = str_replace('""',"null",$sql);

            $result = $this->mySql->query($sql);
            return new ErrorHandler('Acesso criado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao criar o acesso! - ', true, $ex->getMessage());
        } 
    }

    public function update($driverAccess){
        try {
            $sql = 'UPDATE driver_access 
                        SET start_datetime = "'.$driverAccess->getStartDatetime().'",
                        end_datetime = "'.$driverAccess->getEndDatetime().'", 
                        driver_id = '.$driverAccess->getDriverId().', 
                        business_id = '.$driverAccess->getBusinessId().', 
                        vehicle_type = "'.$driverAccess->getVehicleType().'", 
                        vehicle_plate = "'.$driverAccess->getVehiclePlate().'", 
                        vehicle_plate2 = "'.$driverAccess->getVehiclePlate2().'", 
                        vehicle_plate3 = "'.$driverAccess->getVehiclePlate3().'", 
                        inbound_invoice = "'.$driverAccess->getInboundInvoice().'", 
                        outbound_invoice = "'.$driverAccess->getOutboundInvoice().'", 
                        operation_type = "'.$driverAccess->getOperationType().'",
                        rotation = "'.$driverAccess->getRotation().'",
                        business_client_id = '.$driverAccess->getBusinessClientId().',
                        user_outbound_id = '.$_SESSION['id'].',
                        modified_by = '.$_SESSION['id'].',
                        modified_date = "'.date("Y-m-d").'" 
                         WHERE id = '.$driverAccess->getId();
                         
            $sql = str_replace('""',"null",$sql);
                         
            $result = $this->mySql->query($sql);
            return new ErrorHandler('Acesso atualizado com sucesso!', false, null);

        } catch (Exception $ex) {
            return new ErrorHandler('Erro ao atualizar acesso! - ', true, $ex->getMessage());
        } 
    }

    public function delete($id){

        try{
            $sql = 'DELETE FROM driver_access WHERE id = '.$id;
            $this->mySql->query($sql);
            return new ErrorHandler('Acesso excluido com sucesso!', false, null);

        }catch(Exception $ex){
            return new ErrorHandler('Error ao excluir acesso', true, $ex->getMessage());
        }
    }
}