# Análise de Performance - quickActions.php

## Resumo de Problemas Identificados

### 🔴 CRÍTICOS

#### 1. **JOIN Problemático na Query SQL (driverAccessRepository.php:45)**
**Arquivo:** `repository/driverAccessRepository.php:45`
**Problema:** JOIN com condição OR inválida

```sql
INNER JOIN business_client AS bc ON bc.id = dr_a.business_client_id || isnull(dr_a.business_client_id)
```

**Impacto:** 
- A condição `|| isnull(dr_a.business_client_id)` faz com que quando `business_client_id` for NULL, a join retorne TODOS os registros de `business_client`
- Causa um produto cartesiano, multiplicando os registros desnecessariamente
- Força o `GROUP BY` a processar muito mais dados

**Solução:**
```sql
LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
```

#### 2. **N+1 Query Problem com File System (Controller loadData)**
**Arquivos:** 
- `controller/driverAccessController.php:198`
- `controller/employeeAccessController.php:158`
- `controller/driverController.php:200-208`
- `controller/employeeController.php:193-201`

**Problema:** 
Para CADA registro retornado do banco, há uma chamada `file_exists()` no sistema de arquivos:

```php
public function setImagePath($driver){
    $filename = $this->defaultImagePath.$driver->getId().'.png';
    if (file_exists($filename)) $driver->setImageProfilePath($filename);  // FILE I/O!
    else $driver->setImageProfilePath('../images/profile.jpg');
    return $driver;
}
```

**Impacto:**
- Se há 100 registros, são 100+ chamadas `file_exists()`
- Operações de arquivo I/O são muito mais lentas que operações em memória
- Em teste local com XAMPP, o acesso ao disco é ainda mais lento

#### 3. **GROUP BY com Muitas Colunas (Performance Killer)**
**Arquivo:** `repository/driverAccessRepository.php:55-56, 66`

**Problema:**
```sql
GROUP BY dr_a.id,dr.name,start_datetime,end_datetime,dr.cnh,dr.cnh_expiration,
dr.phone,dr.cpf,dr.shipping_company,dr_a.vehicle_type,dr_a.vehicle_plate,
dr_a.vehicle_plate2,dr_a.vehicle_plate3,cl.id,cl.name,business_client_id,
bc.name,inbound_invoice,outbound_invoice,operation_type,rotation,
dr.status,dr.block_reason,dr_a.created_date,dr_a.modified_date,dr_a.created_by,
us_2.name,dr_a.modified_by,us_3.name,us_1.id,us_1.name
```

**Impacto:**
- GROUP BY com 30+ colunas força o MySQL a ordenar/agrupar uma quantidade massiva de dados
- Sem índices adequados, torna-se uma operação O(n log n) cara
- O problema é agravado pelo JOIN problemático que duplica registros

#### 4. **Falta de Índices nas Colunas de Relacionamento**
**Colunas críticas sem índices aparentes:**
- `driver_access.end_datetime` (usada na cláusula WHERE)
- `driver_access.start_datetime` (usada na cláusula WHERE)
- `employee_access.end_datetime`
- `employee_access.start_datetime`

### 🟡 IMPORTANTES

#### 5. **removeDuplicates() em Memória (Ineficiente)**
**Arquivo:** `controller/driverAccessController.php:209-225`

**Problema:**
```php
public function removeDuplicates($arrayValues){
    $temp = [];
    $result = [];
    foreach ($arrayValues as $value) {
        $key = $value->getId();
        if (!in_array($key, $temp)) {  // O(n) operation!
            array_push($temp, $key);
            array_push($result, $value);
        }
    }
    return $result;
}
```

**Impacto:**
- `in_array()` é O(n) - com 100 registros, 100*100 = 10.000 operações
- Indica que a query retorna registros duplicados (causa raiz: o JOIN problemático!)
- Deveria ser resolvido na query, não em PHP

#### 6. **Múltiplas JOINs com USERs**
**Arquivo:** `repository/driverAccessRepository.php:40-46`

**Problema:**
```sql
INNER JOIN user AS us_1 ON us_1.id = user_outbound_id  -- 3 JOINs de user!
INNER JOIN user AS us_2 ON us_2.id = dr_a.created_by
INNER JOIN user AS us_3 ON (us_3.id = dr_a.modified_by OR ISNULL(dr_a.modified_by))
```

**Impacto:**
- 3 JOINs de users adicionam complexidade
- Especialmente `us_3` com condição OR pode causar problemas
- Sem índices em `user.id`, cada JOIN vira table scan

---

## Cronograma de Impacto (Tempo de Carregamento)

Com ~100 registros:

| Etapa | Tempo | Problema |
|-------|-------|----------|
| Query driver | +500ms | JOIN cartesiano, GROUP BY pesado |
| removeDuplicates() | +200ms | O(n²) com in_array() |
| setImagePath() (100x file_exists) | +1500ms | 100 I/O calls |
| Query employee | +500ms | Idem anterior |
| removeDuplicates() | +200ms | Idem anterior |
| setImagePath() (100x file_exists) | +1500ms | 100 I/O calls |
| **Total** | **~4.5s** | ❌ |

---

## Soluções Recomendadas (Ordem de Impacto)

### Prioridade 1: Corrigir o JOIN (50% melhoria esperada)
Arquivo: `repository/driverAccessRepository.php`
```sql
-- ANTES:
INNER JOIN business_client AS bc ON bc.id = dr_a.business_client_id || isnull(dr_a.business_client_id)

-- DEPOIS:
LEFT JOIN business_client AS bc ON bc.id = dr_a.business_client_id
```

### Prioridade 2: Adicionar Índices (30% melhoria esperada)
```sql
CREATE INDEX idx_driver_access_end_datetime ON driver_access(end_datetime);
CREATE INDEX idx_driver_access_start_datetime ON driver_access(start_datetime);
CREATE INDEX idx_employee_access_end_datetime ON employee_access(end_datetime);
CREATE INDEX idx_employee_access_start_datetime ON employee_access(start_datetime);
CREATE INDEX idx_user_id ON user(id);
```

### Prioridade 3: Otimizar setImagePath (15% melhoria esperada)
**Opção A:** Cache em memória
**Opção B:** Usar glob/scandir uma vez
**Opção C:** Remover verificação (sempre usar path padrão + fallback em CSS)

### Prioridade 4: Remover duplicatas na Query
```sql
SELECT DISTINCT dr_a.id, ... -- Usar DISTINCT ao invés de GROUP BY
```

### Prioridade 5: Adicionar LIMIT (se aplicável)
```sql
LIMIT 100  -- Paginar resultados
```

---

## Testes Sugeridos

1. **EXPLAIN PLAN:** Executar `EXPLAIN` nas queries para verificar execução
2. **Query Profiler:** Ativar log de queries lentas no MySQL
3. **Benchmark:** Medir tempo antes/depois de cada correção
4. **Load Test:** Testar com diferentes volumes (100, 500, 1000 registros)

