

SET client_encoding = 'LATIN2';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

CREATE TABLE search_ustawienia (
    u_status integer,
    u_msg text,
    old_u_lang character(1),
    u_ver double precision,
    servername character(128),
    u_params text,
    u_lang character(2),
    u_tsearch2 text,
    u_sid integer DEFAULT 0
);

CREATE FUNCTION f_weblink_used(integer, smallint, character, integer, integer) RETURNS SETOF integer
    AS $_$	SELECT menu_id FROM webtd WHERE server = $1 AND ver = $2
		AND lang = $3 and menu_id >= $4 and menu_id <= $5	
    	UNION
	SELECT menu_id FROM weblink WHERE server = $1 AND ver = $2
		AND lang = $3 and menu_id >= $4 and menu_id <= $5	
	UNION
	SELECT submenu_id FROM weblink WHERE server = $1 AND ver = $2
		AND lang = $3 and submenu_id >= $4 and submenu_id <= $5	
	
;$_$
    LANGUAGE sql;

CREATE FUNCTION f_webpage_used(integer, smallint, character, integer, integer) RETURNS SETOF integer
    AS $_$	SELECT id FROM webpage WHERE server = $1 AND ver = $2
		AND lang = $3 and id >= $4 and id <= $5
    	UNION
	SELECT page_id FROM webtd WHERE server = $1 AND ver = $2
		AND lang = $3 and page_id >= $4 and page_id <= $5	
    	UNION
	SELECT next FROM webtd WHERE server = $1 AND ver = $2
		AND lang = $3 and next >= $4 and next <= $5	
    	UNION
	SELECT more FROM webtd WHERE server = $1 AND ver = $2
		AND lang = $3 and more >= $4 and more <= $5	
    	UNION
	SELECT page_target FROM weblink WHERE server = $1 AND ver = $2
		AND lang = $3 and page_target >= $4 and page_target <= $5	
;$_$
    LANGUAGE sql;

CREATE FUNCTION webpage_file_name(integer, integer, smallint, character) RETURNS character
    AS $_$
SELECT file_name FROM webpage WHERE server=$1 AND id=$2 AND ver=$3 AND lang=$4
$_$
    LANGUAGE sql;

CREATE FUNCTION webpage_sid(integer, integer, integer, character) RETURNS integer
    AS $_$
SELECT sid FROM webpage WHERE server=$1 AND id=$2 AND ver=$3 AND lang=$4
$_$
    LANGUAGE sql;

SET default_tablespace = '';

SET default_with_oids = false;

CREATE TABLE api2_baner (
    ab_id integer DEFAULT nextval(('"api2_baner_ab_id_seq"'::text)::regclass) NOT NULL,
    ab_html integer,
    ab_place text,
    ab_server integer,
    ab_href text,
    ab_count integer DEFAULT 0,
    ab_limit integer,
    ab_d_start_old date,
    ab_d_end_old date,
    ab_lastviewed_old timestamp with time zone DEFAULT "timestamp"('now'::text),
    ab_lastvtime_old time without time zone DEFAULT "time"('now'::text),
    ab_click integer DEFAULT 0,
    ab_target text,
    nab_d_start integer,
    nab_d_end integer,
    ab_lastviewed integer DEFAULT date_part('epoch'::text, "timestamp"('now'::text)),
    ab_lastvtime integer DEFAULT date_part('epoch'::text, "time"('now'::text)),
    ab_d_start integer DEFAULT date_part('epoch'::text, date('now'::text)),
    ab_d_end integer DEFAULT date_part('epoch'::text, date('now'::text)),
    ab_textid character varying(128)
);

CREATE SEQUENCE api2_baner_ab_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE api2_questionnaire (
    aq_id integer DEFAULT nextval(('"api2_questionnaire_aq_id_seq"'::text)::regclass) NOT NULL,
    aq_name text,
    aq_server integer,
    aq_xml text,
    old_aq_lang character(1),
    aq_answer text,
    aq_hits integer DEFAULT 0,
    aq_input smallint DEFAULT 0,
    aq_pri smallint,
    aq_lang character(2)
);

CREATE SEQUENCE api2_questionnaire_a_aqa_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE api2_questionnaire_answers (
    aqa_id integer DEFAULT nextval(('"api2_questionnaire_a_aqa_id_seq"'::text)::regclass) NOT NULL,
    aqa_aq_id integer,
    aqa_name text,
    aqa_answer text,
    aqa_remote text,
    aqa_timestamp_old timestamp with time zone DEFAULT "timestamp"('now'::text),
    aqa_timestamp integer DEFAULT date_part('epoch'::text, "timestamp"('now'::text))
);

CREATE SEQUENCE api2_questionnaire_aq_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE "class" (
    server integer,
    nazwa character(50),
    pole character(50),
    wart character(50),
    ver double precision,
    hash character(32)
);

CREATE TABLE classp (
    id integer DEFAULT nextval(('classp_id_seq'::text)::regclass) NOT NULL,
    pole character(50),
    wart text,
    domysl character(50)
);

CREATE SEQUENCE classp_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE counter (
    page integer,
    count integer,
    params text,
    servername character(128)
);

CREATE TABLE crm_customer (
    c_id integer DEFAULT nextval(('"crm_customer_c_id_seq"'::text)::regclass) NOT NULL,
    c_parent integer,
    c_name text,
    c_name2 text,
    c_address text,
    c_address_no character(10),
    c_province text,
    c_zip character(15),
    c_city text,
    c_country character(5) DEFAULT 'PL'::bpchar,
    c_tel text,
    c_postal text,
    c_person text,
    c_email text,
    c_email2 text,
    c_username text,
    c_password text,
    c_server integer NOT NULL,
    c_xml text,
    c_payment text,
    c_credit smallint,
    c_ver smallint,
    old_c_lang character(1),
    c_page_id integer,
    c_nip text,
    c_regon text,
    c_create date,
    c_update date,
    nc_create integer,
    nc_update integer,
    c_lang character(2),
    c_birthday timestamp without time zone
);

CREATE SEQUENCE crm_customer_c_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE crm_proc (
    p_id integer DEFAULT nextval(('"crm_proc_p_id_seq"'::text)::regclass) NOT NULL,
    p_customer integer,
    p_subcustomer integer,
    p_state integer,
    p_title text,
    p_author character(16),
    p_d_create date,
    p_d_start date,
    p_d_deadline date,
    p_d_end date,
    p_desc text,
    p_server integer,
    old_p_lang character(1),
    p_ver smallint,
    p_page_id integer,
    p_xml text,
    p_type character(16),
    np_d_create integer,
    np_d_start integer,
    np_d_deadline integer,
    np_d_end integer,
    p_lang character(2)
);

CREATE TABLE crm_proc_state (
    ps_id integer DEFAULT nextval(('"crm_proc_state_ps_id_seq"'::text)::regclass) NOT NULL,
    ps_title text,
    ps_server integer,
    old_ps_lang character(1),
    ps_ver smallint,
    ps_page_id integer,
    ps_xml text,
    ps_lang character(2)
);

CREATE TABLE crm_task (
    t_id integer DEFAULT nextval(('"crm_task_t_id_seq"'::text)::regclass) NOT NULL,
    t_author character(16),
    t_executive character(16),
    t_customer integer,
    t_proc integer,
    t_proc_state integer,
    t_title text,
    t_desc text,
    t_d_create date,
    t_d_start date,
    t_d_deadline date,
    t_d_end date,
    t_excuse text,
    t_totaltime double precision,
    t_totalcost double precision,
    t_server integer,
    old_t_lang character(1),
    t_ver smallint,
    t_page_id integer,
    t_xml text,
    nt_d_create integer,
    nt_d_start integer,
    nt_d_deadline integer,
    nt_d_end integer,
    t_lang character(2)
);

CREATE VIEW crm_page AS
    ((SELECT crm_customer.c_id AS id, crm_customer.c_server AS server, crm_customer.c_page_id AS page_id, 'customer_master' AS file_id FROM crm_customer WHERE (crm_customer.c_page_id IS NOT NULL) UNION SELECT crm_proc.p_id AS id, crm_proc.p_server AS server, crm_proc.p_page_id AS page_id, 'proc_master' AS file_id FROM crm_proc WHERE (crm_proc.p_page_id IS NOT NULL)) UNION SELECT crm_proc_state.ps_id AS id, crm_proc_state.ps_server AS server, crm_proc_state.ps_page_id AS page_id, 'state' AS file_id FROM crm_proc_state WHERE (crm_proc_state.ps_page_id IS NOT NULL)) UNION SELECT crm_task.t_id AS id, crm_task.t_server AS server, crm_task.t_page_id AS page_id, 'task_master' AS file_id FROM crm_task WHERE (crm_task.t_page_id IS NOT NULL);

CREATE TABLE crm_proc_hist (
    ph_id integer DEFAULT nextval(('"crm_proc_hist_ph_id_seq"'::text)::regclass) NOT NULL,
    ph_proc integer,
    ph_state integer,
    ph_parent integer,
    ph_title text,
    ph_desc text,
    ph_author character(16),
    ph_executive character(16),
    ph_d_create date,
    ph_d_start date,
    ph_d_deadline date,
    ph_d_end date,
    ph_server integer,
    old_ph_lang character(1),
    ph_ver smallint,
    ph_page_id integer,
    ph_xml text,
    nph_d_create integer,
    nph_d_start integer,
    nph_d_deadline integer,
    nph_d_end integer,
    ph_lang character(2)
);

CREATE SEQUENCE crm_proc_hist_ph_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE crm_proc_p_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE crm_proc_state_ps_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE crm_query (
    q_id integer DEFAULT nextval(('"crm_query_q_id_seq"'::text)::regclass) NOT NULL,
    q_customer integer,
    q_origin text,
    q_subject text,
    q_query text,
    q_timestamp timestamp with time zone,
    q_email text,
    q_name text,
    q_person text,
    q_address text,
    q_tel text,
    q_xml text,
    q_answer text,
    q_answer_timestamp timestamp with time zone,
    q_answer_person text,
    q_server integer,
    q_ver smallint,
    old_q_lang character(1),
    q_query_ip character(15),
    q_answer_ip character(15),
    q_lang character(2)
);

CREATE SEQUENCE crm_query_q_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE crm_recent (
    cr_server integer,
    cr_file_id character(32),
    cr_timestamp timestamp with time zone,
    cr_username character(16),
    cr_id integer
);

CREATE TABLE crm_sendmail_report (
    cs_id integer DEFAULT nextval(('"crm_sendmail_report_cs_id_seq"'::text)::regclass) NOT NULL,
    cs_webtd_sid integer,
    cs_server integer,
    cs_xml text,
    old_cs_lang character(1),
    cs_action text,
    cs_timestamp timestamp with time zone DEFAULT "timestamp"('now'::text),
    cs_from text,
    cs_to text,
    cs_cc text,
    cs_cc_count integer,
    cs_bcc text,
    cs_bcc_count integer,
    cs_subject text,
    cs_msg text,
    cs_att text,
    cs_size integer,
    cs_lang character(2)
);

CREATE SEQUENCE crm_sendmail_report_cs_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE crm_task_t_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE forum (
    forumid integer DEFAULT nextval(('forum_forumid_seq'::text)::regclass) NOT NULL,
    serwisid character(5),
    ojciec integer,
    userid integer,
    data_old date,
    czas time without time zone,
    temat text,
    tresc text,
    servername character(128),
    osoba text,
    data integer
);

CREATE SEQUENCE forum_forumid_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE forum_ustawienia (
    email text,
    subject text,
    servername character(128),
    slownik text
);

CREATE TABLE ftp (
    id integer DEFAULT nextval(('ftp_id_seq'::text)::regclass) NOT NULL,
    server integer,
    username character(16),
    t_begin integer,
    t_end integer,
    killed character(1),
    ver smallint,
    pid integer,
    lang character(2)
);

CREATE TABLE ftp_arch (
    id integer,
    server integer,
    username character(16),
    t_begin integer,
    t_end integer,
    old_lang character(1),
    ver smallint,
    pid integer,
    lang character(2)
);

CREATE SEQUENCE ftp_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE ftplog (
    id integer DEFAULT nextval(('ftplog_id_seq'::text)::regclass) NOT NULL,
    ftp_id integer,
    czas time without time zone,
    rozkaz text,
    wynik text,
    nczas integer
);

CREATE TABLE ftplog_arch (
    id integer,
    ftp_id integer,
    czas time without time zone,
    rozkaz text,
    wynik text
);

CREATE SEQUENCE ftplog_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE gemius (
    sid integer DEFAULT nextval(('"gemius_sid_seq"'::text)::regclass) NOT NULL,
    server integer,
    page_id integer,
    ver smallint,
    old_lang character(1),
    pagekey text,
    node integer,
    id integer,
    lang character(2)
);

CREATE SEQUENCE gemius_sid_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE group_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE groups (
    id integer DEFAULT nextval(('group_id_seq'::text)::regclass) NOT NULL,
    groupname character(16)
);

CREATE TABLE kameleon (
    id integer DEFAULT nextval(('"kameleon_id_seq"'::text)::regclass) NOT NULL,
    version double precision,
    sql text,
    opis text,
    d_issue date,
    nd_issue integer
);

CREATE TABLE kameleon_acl (
    ka_server integer,
    ka_oid integer,
    ka_resource_name character(16),
    ka_username text,
    ka_rights character(8)
);

CREATE TABLE kameleon_acl_users (
    kau_server integer,
    kau_username text,
    kau_password character(64),
    kau_inherits text
);

CREATE SEQUENCE kameleon_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE kameleon_performance (
    pe_id integer DEFAULT nextval(('"kameleon_performance_pe_id_seq"'::text)::regclass) NOT NULL,
    pe_parent integer,
    pe_data integer,
    pe_czas double precision,
    pe_sql text,
    pe_limit integer,
    pe_offset integer,
    pe_count integer,
    pe_sess_id character varying(40),
    pe_result text
);

CREATE SEQUENCE kameleon_performance_pe_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE kontakt (
    id integer DEFAULT nextval(('kontakt_id_seq'::text)::regclass) NOT NULL,
    subject text,
    email character(100),
    servername character(128),
    opis text
);

CREATE SEQUENCE kontakt_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE ksiega (
    id integer DEFAULT nextval(('ksiega_id_seq'::text)::regclass) NOT NULL,
    osoba text,
    grupa character(100),
    email character(100),
    opis text,
    wpis date,
    servername character(128),
    nwpis integer
);

CREATE SEQUENCE ksiega_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE ksiega_ustawienia (
    email text,
    subject text,
    servername character(128),
    slownik text
);

CREATE TABLE label (
    id integer DEFAULT nextval(('label_id_seq'::text)::regclass) NOT NULL,
    label character(256),
    value character(256),
    old_lang character(1),
    lang character(2)
);

CREATE SEQUENCE label_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE "login" (
    id integer DEFAULT nextval(('login_id_seq'::text)::regclass) NOT NULL,
    tin integer,
    tout integer,
    server integer,
    username character(16),
    ip character(15),
    groupid integer
);

CREATE TABLE login_arch (
    id integer,
    tin integer,
    tout integer,
    server integer,
    username character(16),
    ip character(16),
    groupid integer
);

CREATE VIEW login_all AS
    SELECT login_arch.id, login_arch.tin, login_arch.tout, login_arch.server, login_arch.username, login_arch.ip, login_arch.groupid FROM login_arch UNION SELECT "login".id, "login".tin, "login".tout, "login".server, "login".username, "login".ip, "login".groupid FROM "login";

CREATE SEQUENCE login_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE ogloszenia (
    id integer DEFAULT nextval(('ogloszenia_id_seq'::text)::regclass) NOT NULL,
    opis text,
    osoba text,
    email character(100),
    deadline date,
    wpis date,
    grupa character(100),
    servername character(128),
    ndeadline integer,
    nwpis integer
);

CREATE SEQUENCE ogloszenia_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE ogloszenia_ustawienia (
    email text,
    subject text,
    servername character(128),
    slownik text
);

CREATE TABLE passwd (
    username character(16),
    "password" character(64),
    groupid integer,
    "admin" smallint,
    fullname text,
    total_time integer,
    limit_time integer,
    email text,
    license_agreement_date date,
    license_agreement_time time without time zone,
    forget_help smallint,
    skin character(64),
    nlicense_agreement_date integer,
    svn_pass character(32),
    oldeditormode smallint,
    ulang character(2)
);

SET default_with_oids = true;

SET default_with_oids = false;

CREATE TABLE plugins (
    pl_name character varying(80) NOT NULL,
    pl_update date,
    pl_version integer,
    pl_subname character varying(32) DEFAULT ''::character varying
);

CREATE TABLE polecam (
    id integer DEFAULT nextval(('"polecam_id_seq"'::text)::regclass) NOT NULL,
    dzieki text,
    od text,
    subject text,
    msg text,
    servername character(128)
);

CREATE SEQUENCE polecam_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE rights (
    username character(16),
    server integer,
    pages text,
    menus text,
    ftp smallint,
    "class" smallint,
    basic smallint,
    expire date,
    proof text,
    acl smallint,
    nexpire integer,
    accesslevel smallint,
    "template" smallint
);

CREATE TABLE search_desc (
    d_page integer,
    d_title text,
    d_desc text,
    servername character(128),
    old_lang character(1),
    ver double precision,
    lang character(2)
);

CREATE TABLE search_index (
    i_id integer,
    i_href character(256),
    servername character(128),
    old_lang character(1),
    ver double precision,
    i_page integer,
    s_tree character(80),
    lang character(2)
);

CREATE TABLE search_slownik (
    s_id integer DEFAULT nextval(('search_slownik_s_id_seq'::text)::regclass) NOT NULL,
    s_slowo character(50),
    servername character(128)
);

CREATE SEQUENCE search_slownik_s_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE servers (
    id integer DEFAULT nextval(('servers_id_seq'::text)::regclass) NOT NULL,
    nazwa character(128),
    ftp_pass character(64),
    ftp_user text,
    szablon character(32),
    ver smallint,
    "header" integer,
    footer integer,
    editbordercolor character(7),
    old_lang character(1),
    ftp_dir text,
    file_ext character(10),
    groupid integer,
    hide_identity smallint,
    ftp_server character(128),
    svn text,
    versions smallint,
    http_url text,
    trans text,
    lang character(2)
);

CREATE SEQUENCE servers_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE services (
    servername character(128),
    service character(128),
    expired date,
    nexpired integer
);

CREATE TABLE shop_article (
    a_id integer DEFAULT nextval(('"shop_article_a_id_seq"'::text)::regclass) NOT NULL,
    a_name text,
    a_server integer,
    old_a_lang character(1),
    a_ver smallint,
    a_page_id integer,
    a_xml text,
    a_lang character(2)
);

CREATE SEQUENCE shop_article_a_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE shop_cart (
    sc_sid text,
    sc_sa_id integer,
    sc_quantity double precision,
    sc_server integer,
    old_sc_lang character(1),
    sc_xml text,
    sc_lang character(2)
);

CREATE TABLE shop_order (
    so_id integer DEFAULT nextval(('"shop_order_so_id_seq"'::text)::regclass) NOT NULL,
    so_c_id integer,
    so_ip character(15),
    so_state integer,
    so_sum double precision,
    so_date date,
    so_time time without time zone,
    so_server integer,
    old_so_lang character(1),
    so_xml text,
    nso_date integer,
    so_lang character(2)
);

CREATE TABLE shop_order_item (
    si_id integer DEFAULT nextval(('"shop_order_item_si_id_seq"'::text)::regclass) NOT NULL,
    si_so_id integer,
    si_sa_id integer,
    si_name text,
    si_quantity double precision,
    si_price double precision,
    si_vat double precision,
    si_state integer,
    si_server integer,
    old_si_lang character(1),
    si_xml text,
    si_lang character(2)
);

CREATE SEQUENCE shop_order_item_si_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE shop_order_so_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE webaktual (
    rok smallint,
    mies smallint,
    pri smallint,
    akt text,
    more text,
    d_akt date,
    kategoria smallint,
    headline text,
    img character(200),
    servername character(128),
    nd_akt integer
);

CREATE TABLE webfav (
    wf_sid integer NOT NULL,
    wf_user character(16),
    wf_server integer,
    wf_page_id integer,
    wf_lang character(2)
);

CREATE SEQUENCE webfav_wf_sid_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE webfav_wf_sid_seq OWNED BY webfav.wf_sid;

CREATE TABLE webfile (
    wf_id integer DEFAULT nextval(('"webfile_wf_id_seq"'::text)::regclass) NOT NULL,
    wf_server integer,
    wf_ver smallint,
    wf_gal smallint,
    wf_accesslevel smallint,
    wf_file character(100),
    wf_autor character(16),
    wf_d_create integer,
    wf_status character(1),
    wf_type character(1),
    wf_page integer
);

CREATE SEQUENCE webfile_wf_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE weblink (
    page_id integer,
    menu_id integer,
    ver double precision,
    old_lang character(1),
    page_target integer,
    href_old character(80),
    pri smallint,
    fgcolor character(6),
    "type" smallint,
    "class" character(50),
    variables text,
    server integer,
    alt text,
    name character(32),
    hidden smallint,
    target text,
    old_lang_target character(1),
    sid integer DEFAULT nextval(('weblink_sid_seq'::text)::regclass),
    submenu_id integer,
    href text,
    menu_sid integer DEFAULT nextval(('weblink_menu_id_seq'::text)::regclass),
    alt_title text,
    accesslevel smallint,
    lang character(2),
    lang_target character(2),
    ufile_target text,
    nd_create integer,
    nd_update integer,
    description text,
    d_xml text,
    img text,
    imga text
);

CREATE SEQUENCE weblink_menu_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE weblink_sid_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE webpage (
    id integer,
    ver double precision,
    old_lang character(1),
    file_name text,
    description text,
    keywords text,
    bgcolor character(6),
    fgcolor character(6),
    tbgcolor character(6),
    tfgcolor character(6),
    "class" character(12),
    background character(80),
    "type" smallint,
    "next" integer,
    prev integer,
    submenu_id integer,
    menu_id integer,
    server integer,
    title text,
    hidden smallint,
    d_create date,
    d_update date,
    d_ftp date,
    tree text,
    pagekey text,
    nositemap smallint,
    noproof smallint,
    sid integer DEFAULT nextval(('webpage_sid_seq'::text)::regclass),
    title_short character(64),
    nd_create integer,
    nd_update integer,
    nd_ftp integer,
    proof_autor character(16),
    proof_date integer,
    unproof_autor character(16),
    unproof_date integer,
    unproof_counter integer DEFAULT 0,
    accesslevel smallint,
    lang character(2),
    unproof_sids text DEFAULT ':'::text,
    unproof_comment text,
    default_file_name text,
    d_xml text,
    langs_related text
);

CREATE SEQUENCE webpage_sid_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE webtd (
    page_id integer,
    ver double precision,
    old_lang character(1),
    pri smallint,
    img character(50),
    plain text,
    html character(50),
    menu_id integer,
    "class" character(50),
    align character(12),
    valign character(12),
    bgcolor character(6),
    cos smallint,
    width character(10),
    "type" smallint,
    "level" smallint,
    title text,
    more integer,
    "next" integer,
    size integer,
    bgimg text,
    server integer,
    api character(50),
    costxt text,
    hidden smallint,
    staticinclude smallint,
    autor text,
    autor_update text,
    d_create date,
    d_update date,
    d_valid_from date,
    d_valid_to date,
    t_create time without time zone,
    t_update time without time zone,
    sid integer DEFAULT nextval(('webtd_sid_seq'::text)::regclass),
    mod_action text,
    xml text,
    nd_create integer,
    nd_update integer,
    nd_valid_from integer,
    nd_valid_to integer,
    swfstyle smallint,
    ob smallint,
    accesslevel smallint,
    uniqueid character(8),
    lang character(2),
    nohtml text,
    d_xml text,
    web20 text,
    js text
);

CREATE VIEW webpage_used AS
    (((SELECT webpage.server, webpage.old_lang AS lang, webpage.ver, webpage.id FROM webpage UNION SELECT webpage.server, webpage.old_lang AS lang, webpage.ver, webpage."next" AS id FROM webpage WHERE (webpage."next" > 0)) UNION SELECT webtd.server, webtd.old_lang AS lang, webtd.ver, webtd."next" AS id FROM webtd WHERE (webtd."next" > 0)) UNION SELECT webtd.server, webtd.old_lang AS lang, webtd.ver, webtd.more AS id FROM webtd WHERE (webtd.more > 0)) UNION SELECT weblink.server, weblink.old_lang AS lang, weblink.ver, weblink.page_target AS id FROM weblink WHERE (weblink.page_target > 0);

CREATE TABLE webpagetrash (
    id integer DEFAULT nextval(('"webpagetrash_id_seq"'::text)::regclass) NOT NULL,
    server integer,
    page_id integer,
    ver integer,
    lang character(2),
    d_issue date,
    d_complete date,
    status character(1),
    nd_issue integer,
    nd_complete integer,
    file_name text
);

CREATE SEQUENCE webpagetrash_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE webtd_sid_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

CREATE TABLE webtrans (
    wt_sid integer NOT NULL,
    wt_parent integer,
    wt_server integer,
    wt_lang character(2),
    wt_table character(16),
    wt_table_sid integer,
    wt_table_field text,
    wt_o_html text,
    wt_o_plain text,
    wt_t_html text,
    wt_t_plain text,
    wt_translation integer,
    wt_verification integer,
    wt_translator character(16),
    wt_verificator character(16),
    wt_similar text,
    wt_context text,
    wt_path character(150)
);

CREATE SEQUENCE webtrans_wt_sid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE webtrans_wt_sid_seq OWNED BY webtrans.wt_sid;

CREATE TABLE webver (
    wv_id integer DEFAULT nextval(('"webver_wv_id_seq"'::text)::regclass) NOT NULL,
    wv_date integer,
    wv_date_ftp integer,
    wv_autor character(32),
    wv_autor_ftp character(32),
    wv_action character(48),
    wv_table character(32),
    wv_sid integer,
    wv_query text,
    wv_webver text,
    wv_uwagi text,
    wv_noproof smallint
);

CREATE SEQUENCE webver_wv_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;

ALTER TABLE webfav ALTER COLUMN wf_sid SET DEFAULT nextval('webfav_wf_sid_seq'::regclass);

ALTER TABLE webtrans ALTER COLUMN wt_sid SET DEFAULT nextval('webtrans_wt_sid_seq'::regclass);

ALTER TABLE ONLY api2_questionnaire
    ADD CONSTRAINT api2_questionnaire_pkey PRIMARY KEY (aq_id);

ALTER TABLE ONLY crm_customer
    ADD CONSTRAINT crm_customer_pkey PRIMARY KEY (c_id, c_server);

ALTER TABLE ONLY crm_sendmail_report
    ADD CONSTRAINT crm_sendmail_report_pkey PRIMARY KEY (cs_id);

ALTER TABLE ONLY kameleon_performance
    ADD CONSTRAINT kameleon_performance_pkey PRIMARY KEY (pe_id);

ALTER TABLE ONLY shop_order
    ADD CONSTRAINT shop_order_pkey PRIMARY KEY (so_id);

CREATE UNIQUE INDEX api2_baner_ab_id_key ON api2_baner USING btree (ab_id);

CREATE INDEX api2_baner_key ON api2_baner USING btree (ab_place, ab_server, ab_d_start_old, ab_d_end_old);

CREATE INDEX api2_baner_last_key ON api2_baner USING btree (ab_place, ab_server, ab_lastviewed_old, ab_lastvtime_old);

CREATE UNIQUE INDEX api2_questionnaire_a_aqa_id_key ON api2_questionnaire_answers USING btree (aqa_id);

CREATE INDEX api2_questionnaire_key ON api2_questionnaire USING btree (aq_name, aq_server, aq_pri);

CREATE INDEX aqa_aq_id_key ON api2_questionnaire_answers USING btree (aqa_aq_id);

CREATE INDEX class_all_key ON "class" USING btree (server, nazwa, ver);

CREATE INDEX class_nazwa_key ON "class" USING btree (nazwa);

CREATE INDEX class_server_key ON "class" USING btree (server);

CREATE INDEX class_ver_key ON "class" USING btree (ver);

CREATE UNIQUE INDEX classp_id_key ON classp USING btree (id);

CREATE INDEX classp_pole_key ON classp USING btree (pole);

CREATE INDEX counter_key_key ON counter USING btree (servername);

CREATE UNIQUE INDEX crm_customer_c_id_key ON crm_customer USING btree (c_id);

CREATE INDEX crm_customer_key ON crm_customer USING btree (c_email, c_email2, c_username);

CREATE INDEX crm_customer_page_key ON crm_customer USING btree (c_page_id);

CREATE INDEX crm_customer_parent_key ON crm_customer USING btree (c_parent);

CREATE INDEX crm_customer_server_key ON crm_customer USING btree (c_server);

CREATE INDEX crm_page_id_key ON crm_task USING btree (t_page_id);

CREATE INDEX crm_proc_customer_key ON crm_proc USING btree (p_customer);

CREATE INDEX crm_proc_hist_author_key ON crm_proc_hist USING btree (ph_author);

CREATE INDEX crm_proc_hist_executive_key ON crm_proc_hist USING btree (ph_executive);

CREATE INDEX crm_proc_hist_page_id_key ON crm_proc_hist USING btree (ph_page_id);

CREATE UNIQUE INDEX crm_proc_hist_ph_id_key ON crm_proc_hist USING btree (ph_id);

CREATE INDEX crm_proc_hist_proc_key ON crm_proc_hist USING btree (ph_proc);

CREATE INDEX crm_proc_hist_server_key ON crm_proc_hist USING btree (ph_server);

CREATE INDEX crm_proc_hist_state_key ON crm_proc_hist USING btree (ph_state);

CREATE UNIQUE INDEX crm_proc_p_id_key ON crm_proc USING btree (p_id);

CREATE INDEX crm_proc_page_id_key ON crm_proc USING btree (p_page_id);

CREATE INDEX crm_proc_server_key ON crm_proc USING btree (p_server);

CREATE INDEX crm_proc_state_page_id_key ON crm_proc_state USING btree (ps_page_id);

CREATE UNIQUE INDEX crm_proc_state_ps_id_key ON crm_proc_state USING btree (ps_id);

CREATE INDEX crm_proc_state_server_key ON crm_proc_state USING btree (ps_server);

CREATE INDEX crm_query_customer_key ON crm_query USING btree (q_customer);

CREATE INDEX crm_query_email_key ON crm_query USING btree (q_email);

CREATE UNIQUE INDEX crm_query_q_id_key ON crm_query USING btree (q_id);

CREATE INDEX crm_query_server_key ON crm_query USING btree (q_server);

CREATE INDEX crm_query_timestamp_key ON crm_query USING btree (q_timestamp);

CREATE UNIQUE INDEX crm_recent_key ON crm_recent USING btree (cr_server, cr_file_id, cr_timestamp, cr_username, cr_id);

CREATE INDEX crm_sendmail_report_key ON crm_sendmail_report USING btree (cs_webtd_sid, cs_server, cs_action, cs_timestamp, cs_from, cs_to);

CREATE INDEX crm_task_author_key ON crm_task USING btree (t_author);

CREATE INDEX crm_task_customer_key ON crm_task USING btree (t_customer);

CREATE INDEX crm_task_executive_key ON crm_task USING btree (t_executive);

CREATE INDEX crm_task_proc_key ON crm_task USING btree (t_proc);

CREATE INDEX crm_task_proc_state_key ON crm_task USING btree (t_proc_state);

CREATE INDEX crm_task_server_key ON crm_task USING btree (t_server);

CREATE UNIQUE INDEX crm_task_t_id_key ON crm_task USING btree (t_id);

CREATE INDEX desc_all_key ON search_desc USING btree (servername, d_page);

CREATE INDEX desc_key ON search_desc USING btree (servername);

CREATE INDEX desc_page ON search_desc USING btree (d_page);

CREATE INDEX forum_data_key ON forum USING btree (data);

CREATE UNIQUE INDEX forum_forumid_key ON forum USING btree (forumid);

CREATE INDEX forum_key_key ON forum USING btree (servername);

CREATE INDEX forum_ojciec_key ON forum USING btree (ojciec);

CREATE INDEX forum_serwisid_key ON forum USING btree (serwisid);

CREATE INDEX forum_userid_key ON forum USING btree (userid);

CREATE INDEX ftp_all_key ON ftp USING btree (server, t_begin, t_end);

CREATE INDEX ftp_id_key ON ftp USING btree (id);

CREATE INDEX ftplog_ftp_id_key ON ftplog USING btree (ftp_id);

CREATE UNIQUE INDEX ftplog_id_key ON ftplog USING btree (id);

CREATE INDEX gemius_all_key ON gemius USING btree (server, ver, old_lang, page_id, node, id);

CREATE UNIQUE INDEX gemius_sid_key ON gemius USING btree (sid);

CREATE UNIQUE INDEX group_id_key ON groups USING btree (id);

CREATE INDEX index_all_key ON search_index USING btree (i_id, servername, lang, ver, i_page);

CREATE INDEX index_i_href ON search_index USING btree (i_href);

CREATE INDEX index_i_id ON search_index USING btree (i_id);

CREATE INDEX kameleon_acl_key ON kameleon_acl USING btree (ka_server, ka_oid, ka_resource_name, ka_username);

CREATE UNIQUE INDEX kameleon_acl_users_key ON kameleon_acl_users USING btree (kau_server, kau_username);

CREATE INDEX kameleon_performance_key ON kameleon_performance USING btree (pe_data, pe_czas);

CREATE INDEX kameleon_performance_parent_key ON kameleon_performance USING btree (pe_parent);

CREATE INDEX kameleon_performance_sessid_key ON kameleon_performance USING btree (pe_sess_id);

CREATE INDEX kameleon_version_key ON kameleon USING btree (version);

CREATE UNIQUE INDEX kontakt_id_key ON kontakt USING btree (id);

CREATE UNIQUE INDEX ksiega_id_key ON ksiega USING btree (id);

CREATE INDEX ksiega_idx_grupa ON ksiega USING btree (grupa);

CREATE INDEX ksiega_idx_key ON ksiega USING btree (servername);

CREATE INDEX label_all_key ON label USING btree (label, lang);

CREATE INDEX label_label_key ON label USING btree (label);

CREATE INDEX label_lang_key ON label USING btree (lang);

CREATE INDEX login_arch_id_key ON login_arch USING btree (id);

CREATE INDEX login_arch_tin_key ON login_arch USING btree (tin);

CREATE INDEX login_arch_tout_key ON login_arch USING btree (tout);

CREATE INDEX login_arch_username_key ON login_arch USING btree (username);

CREATE UNIQUE INDEX login_id_key ON "login" USING btree (id);

CREATE INDEX login_tin_key ON "login" USING btree (tin);

CREATE INDEX login_tout_key ON "login" USING btree (tout);

CREATE INDEX login_username_key ON "login" USING btree (username);

CREATE UNIQUE INDEX ogloszenia_id_key ON ogloszenia USING btree (id);

CREATE INDEX ogloszenia_idx_grupa ON ogloszenia USING btree (grupa);

CREATE INDEX ogloszenia_idx_key ON ogloszenia USING btree (servername);

CREATE INDEX passwd_username_key ON passwd USING btree (username);

CREATE UNIQUE INDEX polecam_id_key ON polecam USING btree (id);

CREATE INDEX rights_all_key ON rights USING btree (server, username, expire);

CREATE INDEX rights_server_key ON rights USING btree (server);

CREATE INDEX rights_username_key ON rights USING btree (username);

CREATE INDEX search_index_tree_key ON search_index USING btree (s_tree);

CREATE INDEX search_slownik_hash_slowo ON search_slownik USING hash (s_slowo);

CREATE INDEX search_slownik_key ON search_slownik USING btree (s_slowo, servername);

CREATE INDEX search_slownik_s_slowo ON search_slownik USING btree (s_slowo);

CREATE INDEX search_slownik_slowo_key ON search_slownik USING btree (s_slowo, servername);

CREATE UNIQUE INDEX servers_id_key ON servers USING btree (id);

CREATE INDEX servers_nazwa_key ON servers USING btree (nazwa);

CREATE UNIQUE INDEX shop_article_a_id_key ON shop_article USING btree (a_id);

CREATE INDEX shop_cart_key ON shop_cart USING btree (sc_sid);

CREATE UNIQUE INDEX shop_order_item_si_id_key ON shop_order_item USING btree (si_id);

CREATE INDEX shop_page_id_key ON shop_article USING btree (a_page_id);

CREATE INDEX shop_task_server_key ON shop_article USING btree (a_server);

CREATE INDEX ustawienia_key ON search_ustawienia USING btree (servername);

CREATE INDEX webaktual_mies_key ON webaktual USING btree (mies);

CREATE INDEX webaktual_pri_key ON webaktual USING btree (pri);

CREATE INDEX webaktual_rok_key ON webaktual USING btree (rok);

CREATE UNIQUE INDEX webfav_all_key ON webfav USING btree (wf_user, wf_server, wf_page_id, wf_lang);

CREATE INDEX webfile_file_key ON webfile USING hash (wf_file);

CREATE INDEX webfile_key ON webfile USING btree (wf_server, wf_ver, wf_gal);

CREATE UNIQUE INDEX webfile_wf_id_key ON webfile USING btree (wf_id);

CREATE INDEX weblink_all2_key ON weblink USING btree (server, ver, lang, menu_id, page_target);

CREATE INDEX weblink_all_key ON weblink USING btree (menu_id, ver, server, lang, pri);

CREATE INDEX weblink_lang_key ON weblink USING btree (lang);

CREATE INDEX weblink_menu_key ON weblink USING btree (menu_id);

CREATE INDEX weblink_page_key ON weblink USING btree (page_id);

CREATE INDEX weblink_pri_key ON weblink USING btree (pri);

CREATE INDEX weblink_server_key ON weblink USING btree (server);

CREATE UNIQUE INDEX weblink_sid_key ON weblink USING btree (sid);

CREATE UNIQUE INDEX weblink_unique_all_key ON webtd USING btree (server, menu_id, sid);

CREATE INDEX weblink_ver_key ON weblink USING btree (ver);

CREATE INDEX webpage_all_key ON webpage USING btree (id, ver, server, lang);

CREATE INDEX webpage_id_key ON webpage USING btree (id);

CREATE INDEX webpage_lang_key ON webpage USING btree (lang);

CREATE INDEX webpage_nd_update_key ON webpage USING btree (nd_update);

CREATE INDEX webpage_prev_key ON webpage USING btree (prev);

CREATE INDEX webpage_server_key ON webpage USING btree (server);

CREATE UNIQUE INDEX webpage_sid_key ON webpage USING btree (sid);

CREATE UNIQUE INDEX webpage_unique_all_key ON webpage USING btree (server, prev, id, sid);

CREATE INDEX webpage_ver_key ON webpage USING btree (ver);

CREATE UNIQUE INDEX webpagetrash_id_key ON webpagetrash USING btree (id);

CREATE INDEX webpagetrash_lang_key ON webpagetrash USING btree (lang);

CREATE INDEX webpagetrash_page_key ON webpagetrash USING btree (page_id);

CREATE INDEX webpagetrash_server_key ON webpagetrash USING btree (server);

CREATE INDEX webpagetrash_status_key ON webpagetrash USING btree (status);

CREATE INDEX webpagetrash_ver_key ON webpagetrash USING btree (ver);

CREATE INDEX webtd_all2_key ON webtd USING btree (server, ver, lang, page_id, menu_id, "next", more, "level");

CREATE UNIQUE INDEX webtd_all_key ON webtd USING btree (page_id, ver, server, lang, pri, "level", sid);

CREATE INDEX webtd_lang_key ON webtd USING btree (lang);

CREATE INDEX webtd_level_key ON webtd USING btree ("level");

CREATE INDEX webtd_menu_key ON webtd USING btree (menu_id);

CREATE INDEX webtd_mod_action_key ON webtd USING btree (server, ver, lang, mod_action);

CREATE INDEX webtd_page_key ON webtd USING btree (page_id);

CREATE INDEX webtd_pri_key ON webtd USING btree (pri);

CREATE INDEX webtd_server_key ON webtd USING btree (server);

CREATE UNIQUE INDEX webtd_sid_key ON webtd USING btree (sid);

CREATE INDEX webtd_type_key ON webtd USING btree ("type");

CREATE UNIQUE INDEX webtd_unique_all_key ON webtd USING btree (server, page_id, "level", sid);

CREATE INDEX webtd_uniqueid_key ON webtd USING btree (uniqueid);

CREATE INDEX webtd_valid_from_key ON webtd USING btree (d_valid_from);

CREATE INDEX webtd_valid_to_key ON webtd USING btree (d_valid_to);

CREATE INDEX webtd_ver_key ON webtd USING btree (ver);

CREATE INDEX webtrans_key ON webtrans USING btree (wt_server, wt_translation, wt_verification);

CREATE INDEX webtrans_parent_hkey ON webtrans USING hash (wt_parent);

CREATE INDEX webtrans_path_hkey ON webtrans USING hash (wt_path);

CREATE INDEX webtrans_sid_hkey ON webtrans USING hash (wt_sid);

CREATE INDEX webver_sid_key ON webver USING hash (wv_sid);

CREATE UNIQUE INDEX webver_wv_id_key ON webver USING btree (wv_id);

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER INSERT OR UPDATE ON crm_customer
    FROM crm_customer
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_check_ins"('<unnamed>', 'crm_customer', 'crm_customer', 'UNSPECIFIED', 'c_parent', 'c_id', 'c_server', 'c_server');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER DELETE ON crm_customer
    FROM crm_customer
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_cascade_del"('<unnamed>', 'crm_customer', 'crm_customer', 'UNSPECIFIED', 'c_parent', 'c_id', 'c_server', 'c_server');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER UPDATE ON crm_customer
    FROM crm_customer
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_noaction_upd"('<unnamed>', 'crm_customer', 'crm_customer', 'UNSPECIFIED', 'c_parent', 'c_id', 'c_server', 'c_server');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER INSERT OR UPDATE ON api2_questionnaire_answers
    FROM api2_questionnaire
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_check_ins"('<unnamed>', 'api2_questionnaire_answers', 'api2_questionnaire', 'UNSPECIFIED', 'aqa_aq_id', 'aq_id');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER DELETE ON api2_questionnaire
    FROM api2_questionnaire_answers
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_cascade_del"('<unnamed>', 'api2_questionnaire_answers', 'api2_questionnaire', 'UNSPECIFIED', 'aqa_aq_id', 'aq_id');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER UPDATE ON api2_questionnaire
    FROM api2_questionnaire_answers
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_noaction_upd"('<unnamed>', 'api2_questionnaire_answers', 'api2_questionnaire', 'UNSPECIFIED', 'aqa_aq_id', 'aq_id');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER INSERT OR UPDATE ON shop_order_item
    FROM shop_order
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_check_ins"('<unnamed>', 'shop_order_item', 'shop_order', 'UNSPECIFIED', 'si_sa_id', 'so_id');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER DELETE ON shop_order
    FROM shop_order_item
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_cascade_del"('<unnamed>', 'shop_order_item', 'shop_order', 'UNSPECIFIED', 'si_sa_id', 'so_id');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER UPDATE ON shop_order
    FROM shop_order_item
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_noaction_upd"('<unnamed>', 'shop_order_item', 'shop_order', 'UNSPECIFIED', 'si_sa_id', 'so_id');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER INSERT OR UPDATE ON kameleon_performance
    FROM kameleon_performance
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_check_ins"('<unnamed>', 'kameleon_performance', 'kameleon_performance', 'UNSPECIFIED', 'pe_parent', 'pe_id');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER DELETE ON kameleon_performance
    FROM kameleon_performance
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_cascade_del"('<unnamed>', 'kameleon_performance', 'kameleon_performance', 'UNSPECIFIED', 'pe_parent', 'pe_id');

CREATE CONSTRAINT TRIGGER "<unnamed>"
    AFTER UPDATE ON kameleon_performance
    FROM kameleon_performance
    NOT DEFERRABLE INITIALLY IMMEDIATE
    FOR EACH ROW
    EXECUTE PROCEDURE "RI_FKey_cascade_upd"('<unnamed>', 'kameleon_performance', 'kameleon_performance', 'UNSPECIFIED', 'pe_parent', 'pe_id');

