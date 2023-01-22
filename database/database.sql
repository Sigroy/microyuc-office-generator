CREATE TABLE carta
(
    id                      INT(11)                                 NOT NULL PRIMARY KEY AUTO_INCREMENT,
    fecha_creacion          TIMESTAMP                               NOT NULL,
    fecha_visita            DATE                                         DEFAULT NULL,
    numero_expediente       VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    nombre_cliente          VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    calle                   VARCHAR(254) COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
    cruzamientos            VARCHAR(254) COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
    numero_direccion        VARCHAR(100) COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
    colonia_fraccionamiento VARCHAR(100) COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
    localidad               VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    municipio               VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    fecha_firma             DATE                                    NOT NULL,
    documentacion           TEXT COLLATE utf8mb4_unicode_ci         NULL DEFAULT '',
    comprobacion_monto      DECIMAL(30, 2)                          NOT NULL,
    comprobacion_tipo       VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    pagos_fecha_inicial     DATE                                    NOT NULL,
    pagos_fecha_final       DATE                                    NOT NULL,
    modalidad               VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    tipo_credito            VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    fecha_otorgamiento      DATE                                    NOT NULL,
    monto_inicial           DECIMAL(30, 2)                          NOT NULL,
    mensualidades_vencidas  INT(11)                                 NOT NULL,
    adeudo_total            DECIMAL(30, 2)                          NOT NULL,
    nombre_archivo          VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE bitacora
(
    id                              INT(11)                                 NOT NULL AUTO_INCREMENT PRIMARY KEY,
    fecha_creacion                  TIMESTAMP                               NOT NULL,
    acreditado_nombre               VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    acreditado_folio                VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    acreditado_municipio            VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    acreditado_localidad            VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    tipo_garantia                   VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    acreditado_garantia             VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    acreditado_telefono             VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    acreditado_email                VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    acreditado_direccion_negocio    VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    acreditado_direccion_particular VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    aval_nombre                     VARCHAR(254) COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
    aval_telefono                   VARCHAR(254) COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
    aval_email                      VARCHAR(254) COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
    aval_direccion                  VARCHAR(254) COLLATE utf8mb4_unicode_ci NULL DEFAULT '',
    nombre_archivo                  VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE gestion
(
    id                  INT(11)                                 NOT NULL AUTO_INCREMENT,
    gestion_fecha       VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    gestion_via         VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    gestion_comentarios VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    bitacora_id         INT(11)                                 NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT FK_gestion_bitacora FOREIGN KEY (bitacora_id) REFERENCES bitacora (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE evidencia
(
    id                   INT(11)                                 NOT NULL AUTO_INCREMENT,
    evidencia_fecha      VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    evidencia_fotografia VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    gestion_id           INT(11)                                 NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT FK_evidencia_gestion FOREIGN KEY (gestion_id) REFERENCES gestion (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE usuario
(
    id       INT(11)                                 NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre   VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    rol      VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
    password VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO usuario
VALUES (null, 'Admin', 'admin' ,'123456789@MY');