-- BLUE LIGHT - MIGRAÇÃO: recuperação de senha por código de 6 dígitos
-- Use este arquivo APENAS se seu banco blt já existe e você não vai importar o blt.sql do zero.
-- Faça backup do banco antes.

DROP TABLE IF EXISTS tokens_senha;

CREATE TABLE tokens_senha (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    codigo_hash VARCHAR(255) NOT NULL,
    expira_em DATETIME NOT NULL,
    usado TINYINT(1) NOT NULL DEFAULT 0,
    tentativas TINYINT UNSIGNED NOT NULL DEFAULT 0,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_tokens_usuario (usuario_id),
    INDEX idx_tokens_expiracao (expira_em),
    CONSTRAINT fk_tokens_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
