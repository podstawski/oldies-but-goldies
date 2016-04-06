CREATE TABLE payment (
    id integer NOT NULL PRIMARY KEY ,
    type smallint NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL,
    transaction_id character varying,
    amount numeric(12,2),
    status character varying(256),
    payer_email character varying(256),
    data text
);

CREATE SEQUENCE payment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE payment_id_seq OWNED BY payment.id;
