-- Blue Light Gamer: migração para períodos mensal, trimestral e anual
-- 1) Faça backup do banco blt antes de executar.
-- 2) Execute este arquivo uma única vez no phpMyAdmin.
-- Se a coluna periodo já existir em assinaturas, comente a linha ALTER TABLE.
ALTER TABLE assinaturas ADD COLUMN periodo VARCHAR(20) NOT NULL DEFAULT 'mensal' AFTER plano;

-- Converte os códigos antigos em períodos e preserva o histórico de assinaturas.
UPDATE assinaturas
SET periodo = CASE
  WHEN plano = 'basico' THEN 'mensal'
  WHEN plano = 'premium' THEN 'trimestral'
  WHEN plano = 'enterprise' THEN 'anual'
  ELSE COALESCE(NULLIF(periodo, ''), 'mensal')
END,
plano = CASE
  WHEN plano = 'basico' THEN 'mensal'
  WHEN plano = 'premium' THEN 'trimestral'
  WHEN plano = 'enterprise' THEN 'anual'
  ELSE plano
END;

-- Atualiza o catálogo sem apagar usuários nem pagamentos.
INSERT INTO planos (slug,nome,valor,dias,ram,ssd,suporte,descricao,ativo)
VALUES
('mensal','Mensal',19.90,30,'1 GB RAM','10 GB SSD','Suporte Básico','Assinatura mensal da Blue Light.',1),
('trimestral','Trimestral',49.90,90,'4 GB RAM','50 GB SSD','Suporte Prioritário','Assinatura trimestral da Blue Light.',1),
('anual','Anual',99.90,365,'8 GB RAM','100 GB SSD','Suporte 24/7','Assinatura anual da Blue Light.',1)
ON DUPLICATE KEY UPDATE
nome=VALUES(nome), valor=VALUES(valor), dias=VALUES(dias), ram=VALUES(ram),
ssd=VALUES(ssd), suporte=VALUES(suporte), descricao=VALUES(descricao), ativo=1;

UPDATE planos SET ativo=0 WHERE slug IN ('basico','premium','enterprise');
