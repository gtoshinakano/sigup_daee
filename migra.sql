## PARA HOME

ALTER TABLE daee_uddc ADD COLUMN meta INT(11);
ALTER TABLE daee_uddc ADD COLUMN area INT(11);
ALTER TABLE daee_uddc ADD COLUMN pop_fixa INT(11);

## PARA MEDICAO

ALTER TABLE daee_notas ADD COLUMN data_medicao DATE;
ALTER TABLE daee_notas ADD COLUMN medicao INT(11);
ALTER TABLE daee_notas ADD COLUMN pop_flut INT(11);
ALTER TABLE daee_notas ADD COLUMN dias_uteis TINYINT(2);

