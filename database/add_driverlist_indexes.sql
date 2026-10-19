-- Performance Optimization Indexes for driverList
-- Execute este script para adicionar índices que irão melhorar significativamente a performance

-- Índices simples para colunas de filtro/busca
CREATE INDEX IF NOT EXISTS idx_driver_name ON driver(name);
CREATE INDEX IF NOT EXISTS idx_driver_cpf ON driver(cpf);
CREATE INDEX IF NOT EXISTS idx_driver_phone ON driver(phone);
CREATE INDEX IF NOT EXISTS idx_driver_status ON driver(status);
CREATE INDEX IF NOT EXISTS idx_driver_record_type ON driver(record_type);

-- Índice composto para buscas comuns (SEARCH)
-- Otimiza queries com WHERE que usam name, cpf, ou phone
CREATE INDEX IF NOT EXISTS idx_driver_search_composite ON driver(name, cpf, phone);

-- Índice para ORDER BY (já que a query usa ORDER BY name)
CREATE INDEX IF NOT EXISTS idx_driver_name_order ON driver(name);

-- Estatísticas de tabela (ajuda o otimizador)
ANALYZE TABLE driver;

-- Verificar índices criados
-- SHOW INDEXES FROM driver;

-- Deletar índices se necessário (comentado):
-- DROP INDEX idx_driver_name ON driver;
-- DROP INDEX idx_driver_cpf ON driver;
-- DROP INDEX idx_driver_phone ON driver;
-- DROP INDEX idx_driver_status ON driver;
-- DROP INDEX idx_driver_record_type ON driver;
-- DROP INDEX idx_driver_search_composite ON driver;
-- DROP INDEX idx_driver_name_order ON driver;
