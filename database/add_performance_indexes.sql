-- Performance Optimization Indexes
-- Run this script to add indexes that will improve query performance

-- Indexes for driver_access table
CREATE INDEX IF NOT EXISTS idx_driver_access_end_datetime ON driver_access(end_datetime);
CREATE INDEX IF NOT EXISTS idx_driver_access_start_datetime ON driver_access(start_datetime);
CREATE INDEX IF NOT EXISTS idx_driver_access_business_id ON driver_access(business_id);
CREATE INDEX IF NOT EXISTS idx_driver_access_driver_id ON driver_access(driver_id);
CREATE INDEX IF NOT EXISTS idx_driver_access_business_client_id ON driver_access(business_client_id);

-- Indexes for employee_access table
CREATE INDEX IF NOT EXISTS idx_employee_access_end_datetime ON employee_access(end_datetime);
CREATE INDEX IF NOT EXISTS idx_employee_access_start_datetime ON employee_access(start_datetime);
CREATE INDEX IF NOT EXISTS idx_employee_access_employee_id ON employee_access(employee_id);

-- Indexes for related tables (if not already present)
CREATE INDEX IF NOT EXISTS idx_driver_id ON driver(id);
CREATE INDEX IF NOT EXISTS idx_employee_id ON employee(id);
CREATE INDEX IF NOT EXISTS idx_client_id ON client(id);
CREATE INDEX IF NOT EXISTS idx_user_id ON user(id);
CREATE INDEX IF NOT EXISTS idx_business_client_id ON business_client(id);

-- Composite indexes for common WHERE clauses
CREATE INDEX IF NOT EXISTS idx_driver_access_start_end_business ON driver_access(start_datetime, end_datetime, business_id);
CREATE INDEX IF NOT EXISTS idx_employee_access_start_end_business ON employee_access(start_datetime, end_datetime);
