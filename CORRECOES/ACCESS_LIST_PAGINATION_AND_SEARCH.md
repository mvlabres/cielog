# Paginação e Busca Global - driverAccessList.php e employeeAccessList.php

## Resumo
- Removido o limite de 1000 registros (`if($counter > 1000) break;`) e a exportação automática acima desse limite.
- Paginação server-side (50 registros/página) com `LIMIT/OFFSET` + `COUNT(*)`.
- Novo campo **Buscar Acesso**, no mesmo padrão do `driverList.php`, que pesquisa em várias colunas ao mesmo tempo.
- Formulário convertido de POST para GET: todos os filtros (busca, datas, empresa, empresa cliente, "com acessos em aberto") são preservados nos links de paginação e no redirect após exclusão.

## Colunas pesquisadas
| Página | Colunas |
|---|---|
| driverAccessList.php | nome e CPF do motorista, placas 1/2/3, empresa visitada, empresa cliente |
| employeeAccessList.php | nome, CPF e matrícula do colaborador, placa e veículo do acesso, empresa visitada |

O termo é escapado com `real_escape_string` e aplicado com `LIKE '%termo%'`.

## Arquivos alterados
- `repository/driverAccessRepository.php`: métodos `...Paginated`/`count...` e variações `...WithSearch...` (todas as combinações de empresa, empresa cliente e acessos abertos/fechados).
- `repository/employeeAccessRepository.php`: `findPaginated()` e `countFiltered()` unificados (parâmetros: datas, empresa, somente fechados, busca, página, limite). O COUNT agora faz join por `employee` (`em.business_id`), pois `employee_access` não possui `business_id`.
- `controller/driverAccessController.php` e `controller/employeeAccessController.php`: `findByStartDateEndDateAndBusinessPaginated(..., $openAccess, $search, $page, $limit)` retorna `['data','total','page','limit','pages']`.
- `view/driverAccessList.php` e `view/employeeAccessList.php`: novo layout de filtros, tabela com scroll horizontal e cabeçalhos em uma linha, barra de paginação.

## Layout dos filtros
Painel "Filtros" em grid Bootstrap:
1. Linha 1: campo de busca em largura total.
2. Linha 2: Data inicial, Data final, Empresa visitada, (Empresa cliente - só motoristas) e checkbox "Com acessos em aberto", alinhados à mesma altura.
3. Linha 3: botões **Buscar** e **Limpar Filtros** à esquerda, **Exportar** à direita.

## Correção incluída
- `with-open` estava sempre `true` (`? true : true`). Agora o padrão é marcado apenas quando o formulário ainda não foi enviado; desmarcar o checkbox passa a funcionar.

## Observações
- O botão **Exportar** usa os filtros de data/empresa/em aberto, mas não o texto de busca.
- Índice recomendado (executar manualmente): `CREATE INDEX idx_driver_access_start ON driver_access (start_datetime); CREATE INDEX idx_employee_access_start ON employee_access (start_datetime);`
- A busca com `LIKE '%termo%'` não usa índice; o filtro por data reduz o conjunto antes da busca.
