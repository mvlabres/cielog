-- Performance Optimization Indexes for driverList.php
-- Crie estes índices manualmente no seu banco de dados

-- ============================================
-- INSTRUÇÕES:
-- 1. Abra seu cliente MySQL (phpMyAdmin, MySQL Workbench, etc)
-- 2. Selecione o banco de dados: cielog
-- 3. Execute os comandos abaixo um a um ou todos de uma vez
-- ============================================

-- =======================
-- DRIVER INDEXES
-- =======================

-- Índices simples para colunas de filtro/busca
CREATE INDEX IF NOT EXISTS idx_driver_name ON driver(name);

CREATE INDEX IF NOT EXISTS idx_driver_cpf ON driver(cpf);

CREATE INDEX IF NOT EXISTS idx_driver_phone ON driver(phone);

CREATE INDEX IF NOT EXISTS idx_driver_status ON driver(status);

CREATE INDEX IF NOT EXISTS idx_driver_record_type ON driver(record_type);

-- Índice composto para buscas comuns (otimiza WHERE com name, cpf, ou phone)
CREATE INDEX IF NOT EXISTS idx_driver_search_composite ON driver(name, cpf, phone);

-- Índice para ORDER BY
CREATE INDEX IF NOT EXISTS idx_driver_name_order ON driver(name);

-- Índice para melhorar queries com data de criação
CREATE INDEX IF NOT EXISTS idx_driver_created_date ON driver(created_date);

-- =======================
-- EMPLOYEE INDEXES
-- =======================

-- Índices simples para colunas de filtro/busca
CREATE INDEX IF NOT EXISTS idx_employee_name ON employee(name);

CREATE INDEX IF NOT EXISTS idx_employee_cpf ON employee(cpf);

CREATE INDEX IF NOT EXISTS idx_employee_registration ON employee(registration);

CREATE INDEX IF NOT EXISTS idx_employee_business_id ON employee(business_id);

-- Índice composto para buscas comuns (otimiza WHERE com name, cpf, ou registration)
CREATE INDEX IF NOT EXISTS idx_employee_search_composite ON employee(name, cpf, registration);

-- Índice para ORDER BY
CREATE INDEX IF NOT EXISTS idx_employee_name_order ON employee(name);

-- Índice para melhorar queries com data de criação
CREATE INDEX IF NOT EXISTS idx_employee_created_date ON employee(created_date);

-- Índice composto para filtro por business + nome
CREATE INDEX IF NOT EXISTS idx_employee_business_name ON employee(business_id, name);

-- ============================================
-- Após criar os índices, execute este comando para atualizar estatísticas:
-- ============================================

ANALYZE TABLE driver;
ANALYZE TABLE employee;

-- ============================================
-- VERIFICAÇÃO (opcional - para ver os índices criados):
-- ============================================

-- SHOW INDEXES FROM driver;
-- SHOW INDEXES FROM employee;

-- ============================================
-- REMOVER ÍNDICES (se necessário - descomente):
-- ============================================

-- DROP INDEX idx_driver_name ON driver;
-- DROP INDEX idx_driver_cpf ON driver;
-- DROP INDEX idx_driver_phone ON driver;
-- DROP INDEX idx_driver_status ON driver;
-- DROP INDEX idx_driver_record_type ON driver;
-- DROP INDEX idx_driver_search_composite ON driver;
-- DROP INDEX idx_driver_name_order ON driver;
-- DROP INDEX idx_driver_created_date ON driver;

-- DROP INDEX idx_employee_name ON employee;
-- DROP INDEX idx_employee_cpf ON employee;
-- DROP INDEX idx_employee_registration ON employee;
-- DROP INDEX idx_employee_business_id ON employee;
-- DROP INDEX idx_employee_search_composite ON employee;
-- DROP INDEX idx_employee_name_order ON employee;
-- DROP INDEX idx_employee_created_date ON employee;
-- DROP INDEX idx_employee_business_name ON employee;
