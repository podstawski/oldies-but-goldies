--
-- PostgreSQL database dump
--


SET search_path = public, pg_catalog;

--
-- TOC entry 2 (OID 3563382)
-- Name: towar; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE towar (
    to_id serial NOT NULL,
    to_indeks character(64),
    to_nazwa character(128),
    to_jm character(20),
    to_ka_c integer DEFAULT 0,
    to_foto_m character(48),
    to_foto_d character(48),
    to_opis_m_i text,
    to_opis_d_i text,
    to_pr_id integer,
    to_jp character(20),
    to_klucze character(256),
    to_vat double precision,
    to_cena double precision,
    to_foto_s character(48),
    to_att character(128),
    to_ws character(32),
    to_ws_update integer,
    to_ean character(25)
);


--
-- TOC entry 3 (OID 3563391)
-- Name: towar_parametry; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE towar_parametry (
    tp_id serial NOT NULL,
    tp_to_id integer NOT NULL,
    tp_a double precision,
    tp_b double precision,
    tp_c double precision,
    tp_d double precision,
    tp_l double precision,
    tp_r1 double precision,
    tp_r2 double precision,
    tp_o double precision,
    tp_gatunek character(16),
    tp_stan character(16),
    tp_m_m double precision,
    tp_m_m2 double precision,
    tp_m_szt double precision,
    tp_m_jm double precision
);


--
-- TOC entry 4 (OID 3563396)
-- Name: kategorie; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE kategorie (
    ka_id serial NOT NULL,
    ka_parent integer,
    ka_nazwa character(128),
    ka_kod character(64),
    ka_foto_m character(48),
    ka_foto_d character(48),
    ka_opis_m_i text,
    ka_opis_d_i text,
    ka_to_c integer DEFAULT 0,
    ka_ws character(32)
);


--
-- TOC entry 5 (OID 3563405)
-- Name: sklep; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE sklep (
    sk_id serial NOT NULL,
    sk_server integer NOT NULL,
    sk_nazwa character(64)
);


--
-- TOC entry 6 (OID 3563410)
-- Name: towar_sklep; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE towar_sklep (
    ts_id serial NOT NULL,
    ts_to_id integer NOT NULL,
    ts_sk_id integer NOT NULL,
    ts_kwant_zam double precision,
    ts_czas_koszyk integer,
    ts_cena double precision,
    ts_magazyn smallint DEFAULT 1,
    ts_aktywny smallint DEFAULT 1,
    ts_pri_old smallint,
    ts_pri2_old smallint,
    ts_pri integer,
    ts_pri2 integer
);


--
-- TOC entry 7 (OID 3563417)
-- Name: magazyn; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE magazyn (
    ma_id serial NOT NULL,
    ma_nazwa character(64),
    ma_adres text,
    ma_glowny smallint
);


--
-- TOC entry 8 (OID 3563425)
-- Name: stany_magazynowe; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE stany_magazynowe (
    sm_id serial NOT NULL,
    sm_ilosc double precision DEFAULT 0,
    sm_stan_min double precision,
    sm_stan_opt double precision,
    sm_to_id integer NOT NULL,
    sm_ma_id integer NOT NULL
);


--
-- TOC entry 9 (OID 3563431)
-- Name: kontrahent_sklep; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE kontrahent_sklep (
    ks_id serial NOT NULL,
    ks_sk_id integer NOT NULL,
    ks_su_id integer NOT NULL
);


--
-- TOC entry 10 (OID 3563436)
-- Name: rabat_kontrahenta; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE rabat_kontrahenta (
    rk_id serial NOT NULL,
    rk_ka_id integer NOT NULL,
    rk_ks_id integer NOT NULL,
    rk_procent double precision
);


--
-- TOC entry 11 (OID 3563441)
-- Name: rabat_ilosciowy; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE rabat_ilosciowy (
    ri_id serial NOT NULL,
    ri_sk_id integer NOT NULL,
    ri_ka_id integer NOT NULL,
    ri_minmum double precision,
    ri_procent double precision
);


--
-- TOC entry 12 (OID 3563446)
-- Name: system_grupa; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_grupa (
    sg_id serial NOT NULL,
    sg_server integer,
    sg_nazwa character(255),
    sg_admin smallint DEFAULT 0
);


--
-- TOC entry 13 (OID 3563452)
-- Name: system_obiekt; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_obiekt (
    so_id serial NOT NULL,
    so_server integer NOT NULL,
    so_klucz character(128) NOT NULL,
    so_nazwa character(255),
    so_parent integer
);


--
-- TOC entry 14 (OID 3563457)
-- Name: system_acl_obiekt; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_acl_obiekt (
    sao_id serial NOT NULL,
    sao_grupa_id integer NOT NULL,
    sao_server integer NOT NULL,
    sao_klucz character(128) NOT NULL
);


--
-- TOC entry 15 (OID 3563462)
-- Name: system_action; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_action (
    sa_id serial NOT NULL,
    sa_server integer,
    sa_page_id integer,
    sa_action character(255)
);


--
-- TOC entry 16 (OID 3563467)
-- Name: system_log; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_log (
    sl_id serial NOT NULL,
    sl_user_id integer NOT NULL,
    sl_server integer,
    sl_tin integer,
    sl_tout integer,
    sl_session character(64),
    sl_ip character(15),
    sl_lastpage integer
);


--
-- TOC entry 17 (OID 3563472)
-- Name: system_acl_grupa; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_acl_grupa (
    sag_id serial NOT NULL,
    sag_grupa_id integer NOT NULL,
    sag_user_id integer NOT NULL,
    sag_server integer
);


--
-- TOC entry 18 (OID 3563477)
-- Name: system_user; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_user (
    su_id serial NOT NULL,
    su_parent integer,
    su_server integer,
    su_login character(64),
    su_pass character(64),
    su_data_dodania integer DEFAULT date_part('epoch'::text, ('now'::text)::timestamp(6) with time zone),
    su_data_modyfikacji integer,
    su_pesel character varying(11),
    su_imiona character varying(50),
    su_nazwisko character varying(80),
    su_ulica character varying(80),
    su_kod_pocztowy character varying(6),
    su_miasto character varying(80),
    su_telefon character varying(30),
    su_gsm character varying(30),
    su_email character varying(100),
    su_nip character(20),
    su_xml text,
    su_ip text,
    su_adres1 text,
    su_adres2 text,
    su_adres3 text,
    su_dostawa character(32),
    su_platnosc character(32),
    su_opiekun integer,
    su_wyroznik1 character(20),
    su_wyroznik2 character(20),
    su_wyroznik3 character(20),
    su_blokady text,
    su_ws character(32),
    su_ws_update integer,
    su_saldo double precision,
    su_regon character(20),
    su_termin_platnosci character(20)
);


--
-- TOC entry 19 (OID 3563486)
-- Name: system_update; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_update (
    su_id serial NOT NULL,
    su_klucz character(32),
    su_data integer,
    su_sql text
);


--
-- TOC entry 20 (OID 3563494)
-- Name: system_action_log; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_action_log (
    sal_id serial NOT NULL,
    sal_user_id integer NOT NULL,
    sal_action character(255),
    sal_data integer,
    sal_opis text,
    sal_klucz character(32)
);


--
-- TOC entry 21 (OID 3563502)
-- Name: koszyk; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE koszyk (
    ko_id serial NOT NULL,
    ko_su_id integer NOT NULL,
    ko_ts_id integer NOT NULL,
    ko_ilosc double precision,
    ko_deadline integer,
    ko_rez_nr character(64),
    ko_rez_uwagi text,
    ko_rez_data integer,
    ko_opcje text
);


--
-- TOC entry 22 (OID 3563510)
-- Name: zamowienia; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE zamowienia (
    za_id serial NOT NULL,
    za_su_id integer NOT NULL,
    za_numer integer,
    za_numer_obcy character(64),
    za_uwagi text,
    za_status integer,
    za_data integer,
    za_data_przyjecia integer,
    za_data_realizacji integer,
    za_adres text,
    za_parametry text,
    za_sk_id integer,
    za_ws character(32),
    za_ws_update integer,
    za_uwagi_przyjecia text,
    za_uwagi_realizacji text,
    za_osoba_przyjecia integer,
    za_osoba_realizacji integer,
    za_wart_nt double precision,
    za_wart_br double precision,
    za_osoba integer,
    za_poczta_nt double precision,
    za_poczta_br double precision,
    za_poczta character varying(80)
);


--
-- TOC entry 23 (OID 3563518)
-- Name: zampoz; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE zampoz (
    zp_id serial NOT NULL,
    zp_za_id integer NOT NULL,
    zp_ts_id integer NOT NULL,
    zp_ilosc double precision,
    zp_cena double precision,
    zp_to_indeks character(64),
    zp_opcje text,
    zp_rabat double precision,
    zp_cena_ws double precision,
    zp_ilosc_ws double precision
);


--
-- TOC entry 24 (OID 3563526)
-- Name: messages; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE messages (
    msg_id serial NOT NULL,
    msg_label character(128),
    msg_group character(16),
    msg_lang character(2),
    msg_msg text
);


--
-- TOC entry 25 (OID 3563534)
-- Name: magazyn_sklep; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE magazyn_sklep (
    ms_id serial NOT NULL,
    ms_ma_id integer NOT NULL,
    ms_sk_id integer NOT NULL
);


--
-- TOC entry 26 (OID 3563539)
-- Name: towar_kategoria; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE towar_kategoria (
    tk_id serial NOT NULL,
    tk_to_id integer NOT NULL,
    tk_ka_id integer NOT NULL
);


--
-- TOC entry 27 (OID 3563544)
-- Name: nieudane_zakupy; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE nieudane_zakupy (
    nz_id serial NOT NULL,
    nz_ts_id integer NOT NULL,
    nz_su_id integer NOT NULL,
    nz_data integer,
    nz_proba double precision,
    nz_dostepne double precision
);


--
-- TOC entry 28 (OID 3563549)
-- Name: ulubione; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE ulubione (
    ul_id serial NOT NULL,
    ul_nazwa character(32),
    ul_su_id integer NOT NULL,
    ul_to_id integer NOT NULL,
    ul_ilosc double precision
);


--
-- TOC entry 29 (OID 3563554)
-- Name: ruchy; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE ruchy (
    ru_id serial NOT NULL,
    ru_su_id integer NOT NULL,
    ru_ts_id integer NOT NULL,
    ru_ma_id integer NOT NULL,
    ru_zmiana double precision,
    ru_stan double precision,
    ru_data integer,
    ru_uwagi text
);


--
-- TOC entry 30 (OID 3563562)
-- Name: system_opcje; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE system_opcje (
    so_id serial NOT NULL,
    so_nazwa2 character(20),
    so_nazwa text,
    so_lista text,
    so_wart character(20)
);


--
-- TOC entry 31 (OID 3563570)
-- Name: grupy_towarow; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE grupy_towarow (
    gt_id serial NOT NULL,
    gt_to_id1 integer NOT NULL,
    gt_to_id2 integer NOT NULL,
    gt_grupa character(20)
);


--
-- TOC entry 32 (OID 3563575)
-- Name: producent; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE producent (
    pr_id serial NOT NULL,
    pr_nazwa character(64),
    pr_www character(64),
    pr_logo_m character(48),
    pr_logo_d character(48),
    pr_opis text,
    pr_kraj character(32),
    pr_ws character(32)
);


--
-- TOC entry 33 (OID 3563583)
-- Name: opcje_towaru; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE opcje_towaru (
    ot_id serial NOT NULL,
    ot_to_id integer NOT NULL,
    ot_opcje text,
    ot_ilosc smallint
);


--
-- TOC entry 34 (OID 3563591)
-- Name: promocja; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE promocja (
    pm_id serial NOT NULL,
    pm_symbol character(32),
    pm_poczatek integer,
    pm_koniec integer,
    pm_rabat_domyslny double precision
);


--
-- TOC entry 35 (OID 3563596)
-- Name: promocja_towaru; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE promocja_towaru (
    pt_id serial NOT NULL,
    pt_ts_id integer NOT NULL,
    pt_pm_id integer NOT NULL,
    pt_cena double precision,
    pt_poczatek integer,
    pt_koniec integer
);


--
-- TOC entry 117 (OID 3563599)
-- Name: iletowwkat (integer); Type: FUNCTION; Schema: public; Owner: magazyn
--

CREATE FUNCTION iletowwkat (integer) RETURNS bigint
    AS 'SELECT count(tk_id) FROM towar_kategoria WHERE tk_ka_id = $1'
    LANGUAGE sql;


--
-- TOC entry 118 (OID 3563600)
-- Name: wilukattow (integer); Type: FUNCTION; Schema: public; Owner: magazyn
--

CREATE FUNCTION wilukattow (integer) RETURNS bigint
    AS 'SELECT count(tk_id) FROM towar_kategoria WHERE tk_to_id = $1'
    LANGUAGE sql;


--
-- TOC entry 36 (OID 3563603)
-- Name: zapytania; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE zapytania (
    za_id serial NOT NULL,
    za_ts_id integer NOT NULL,
    za_pyt_su_id integer,
    za_odp_su_id integer NOT NULL,
    za_email character(96),
    za_telefon character(48),
    za_pyt text,
    za_odp text,
    za_pyt_data integer,
    za_odp_data integer,
    za_cena double precision
);


--
-- TOC entry 37 (OID 3563613)
-- Name: temp; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE "temp" (
    te_id serial NOT NULL,
    te_indeks character(48),
    te_fk1 integer,
    te_fk2 integer,
    te_fk3 integer,
    te_deadline integer,
    te_wart text
);


--
-- TOC entry 38 (OID 3563621)
-- Name: adresy; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE adresy (
    ad_id serial NOT NULL,
    ad_su_id integer NOT NULL,
    ad_adres text,
    ad_ws character(32)
);


--
-- TOC entry 39 (OID 14187958)
-- Name: poczta; Type: TABLE; Schema: public; Owner: magazyn
--

CREATE TABLE poczta (
    po_id serial NOT NULL,
    po_nazwa character varying(80),
    po_cena_nt double precision,
    po_cena_br double precision,
    po_darmo_powyzej double precision
);


--
-- TOC entry 119 (OID 55826170)
-- Name: ts_pri_seq (); Type: FUNCTION; Schema: public; Owner: magazyn
--

CREATE FUNCTION ts_pri_seq () RETURNS integer
    AS '
	SELECT max(ts_pri)+1 FROM towar_sklep WHERE 1 BETWEEN 0 AND (SELECT count(*) FROM towar_sklep WHERE ts_pri IS NOT NULL)
	UNION
	SELECT 1 FROM towar_sklep WHERE 0 BETWEEN (SELECT count(*) FROM towar_sklep WHERE ts_pri IS NOT NULL) AND 1
'
    LANGUAGE sql;


--
-- TOC entry 120 (OID 55826171)
-- Name: ts_pri2_seq (); Type: FUNCTION; Schema: public; Owner: magazyn
--

CREATE FUNCTION ts_pri2_seq () RETURNS integer
    AS '
	SELECT max(ts_pri2)+1 FROM towar_sklep WHERE 1 BETWEEN 0 AND (SELECT count(*) FROM towar_sklep WHERE ts_pri2 IS NOT NULL)
	UNION
	SELECT 1 FROM towar_sklep WHERE 0 BETWEEN (SELECT count(*) FROM towar_sklep WHERE ts_pri2 IS NOT NULL) AND 1
'
    LANGUAGE sql;


--
-- TOC entry 40 (OID 3638684)
-- Name: towar_indeks_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX towar_indeks_key ON towar USING btree (to_indeks, to_ean, to_ws);


--
-- TOC entry 43 (OID 3638685)
-- Name: towar_producent_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX towar_producent_key ON towar USING btree (to_pr_id);


--
-- TOC entry 44 (OID 3638686)
-- Name: towar_u_indeks_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE UNIQUE INDEX towar_u_indeks_key ON towar USING btree (to_indeks);


--
-- TOC entry 45 (OID 3638687)
-- Name: towar_parametry_fk; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX towar_parametry_fk ON towar_parametry USING btree (tp_to_id);


--
-- TOC entry 48 (OID 3638688)
-- Name: kategoria_parent_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX kategoria_parent_key ON kategorie USING btree (ka_parent);


--
-- TOC entry 54 (OID 3638689)
-- Name: towar_sklep_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX towar_sklep_key ON towar_sklep USING btree (ts_to_id, ts_sk_id);


--
-- TOC entry 57 (OID 3638690)
-- Name: stany_magazynowe_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX stany_magazynowe_key ON stany_magazynowe USING btree (sm_to_id, sm_ma_id);


--
-- TOC entry 59 (OID 3638691)
-- Name: kontrahent_sklep_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX kontrahent_sklep_key ON kontrahent_sklep USING btree (ks_sk_id, ks_su_id);


--
-- TOC entry 61 (OID 3638692)
-- Name: rabat_kontrahenta_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX rabat_kontrahenta_key ON rabat_kontrahenta USING btree (rk_ka_id, rk_ks_id);


--
-- TOC entry 63 (OID 3638693)
-- Name: rabat_ilosciowy_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX rabat_ilosciowy_key ON rabat_ilosciowy USING btree (ri_sk_id, ri_ka_id);


--
-- TOC entry 66 (OID 3638694)
-- Name: system_obiekt_key2; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX system_obiekt_key2 ON system_obiekt USING btree (so_server, so_klucz);


--
-- TOC entry 68 (OID 3638695)
-- Name: system_acl_obiekt_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX system_acl_obiekt_key ON system_acl_obiekt USING btree (sao_grupa_id, sao_server, sao_klucz);


--
-- TOC entry 70 (OID 3638696)
-- Name: system_action_key2; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX system_action_key2 ON system_action USING btree (sa_server, sa_page_id, sa_action);


--
-- TOC entry 72 (OID 3638697)
-- Name: system_log_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX system_log_key ON system_log USING btree (sl_user_id, sl_server, sl_tin, sl_tout);


--
-- TOC entry 75 (OID 3638698)
-- Name: system_acl_grupa_ukey2; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE UNIQUE INDEX system_acl_grupa_ukey2 ON system_acl_grupa USING btree (sag_grupa_id, sag_user_id, sag_server);


--
-- TOC entry 77 (OID 3638699)
-- Name: system_user_login2; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX system_user_login2 ON system_user USING btree (su_login, su_pesel);


--
-- TOC entry 82 (OID 3638700)
-- Name: update_db_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX update_db_key ON system_update USING btree (su_klucz, su_data);


--
-- TOC entry 83 (OID 3638701)
-- Name: system_action_log_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX system_action_log_key ON system_action_log USING btree (sal_user_id, sal_action, sal_data);


--
-- TOC entry 85 (OID 3638702)
-- Name: koszyk_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX koszyk_key ON koszyk USING btree (ko_su_id, ko_ts_id, ko_deadline, ko_rez_data);


--
-- TOC entry 87 (OID 3638703)
-- Name: zamowienia_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX zamowienia_key ON zamowienia USING btree (za_su_id, za_sk_id, za_data);


--
-- TOC entry 90 (OID 3638704)
-- Name: zampoz_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX zampoz_key ON zampoz USING btree (zp_za_id, zp_ts_id);


--
-- TOC entry 92 (OID 3638705)
-- Name: messages_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX messages_key ON messages USING btree (msg_label, msg_lang);


--
-- TOC entry 97 (OID 3638706)
-- Name: nieudane_zakupy_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX nieudane_zakupy_key ON nieudane_zakupy USING btree (nz_ts_id, nz_su_id);


--
-- TOC entry 99 (OID 3638707)
-- Name: ulubione_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX ulubione_key ON ulubione USING btree (ul_su_id, ul_to_id);


--
-- TOC entry 100 (OID 3638708)
-- Name: ruchy_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX ruchy_key ON ruchy USING btree (ru_su_id, ru_ts_id, ru_ma_id);


--
-- TOC entry 101 (OID 3638709)
-- Name: grupy_towarow_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX grupy_towarow_key ON grupy_towarow USING btree (gt_to_id1, gt_to_id2, gt_grupa);


--
-- TOC entry 105 (OID 3638710)
-- Name: towar_opcje_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX towar_opcje_key ON opcje_towaru USING btree (ot_to_id);


--
-- TOC entry 107 (OID 3638711)
-- Name: promocja_towaru_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX promocja_towaru_key ON promocja_towaru USING btree (pt_ts_id, pt_pm_id);


--
-- TOC entry 109 (OID 3638712)
-- Name: zapytania_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX zapytania_key ON zapytania USING btree (za_ts_id, za_odp_su_id);


--
-- TOC entry 49 (OID 3638713)
-- Name: kategoria_ws_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX kategoria_ws_key ON kategorie USING btree (ka_ws);


--
-- TOC entry 47 (OID 3638714)
-- Name: kategoria_kod_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX kategoria_kod_key ON kategorie USING btree (ka_kod);


--
-- TOC entry 103 (OID 3638715)
-- Name: producent_ws_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX producent_ws_key ON producent USING btree (pr_ws);


--
-- TOC entry 89 (OID 3638716)
-- Name: zamowienia_ws_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX zamowienia_ws_key ON zamowienia USING btree (za_ws);


--
-- TOC entry 41 (OID 3638717)
-- Name: towar_nazwa_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX towar_nazwa_key ON towar USING btree (to_nazwa);


--
-- TOC entry 53 (OID 3638718)
-- Name: towar_sklep_cena_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX towar_sklep_cena_key ON towar_sklep USING btree (ts_cena);


--
-- TOC entry 95 (OID 3638719)
-- Name: towar_kategoria_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX towar_kategoria_key ON towar_kategoria USING btree (tk_to_id, tk_ka_id);


--
-- TOC entry 112 (OID 3638720)
-- Name: temp_indeks_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE UNIQUE INDEX temp_indeks_key ON "temp" USING btree (te_indeks);


--
-- TOC entry 111 (OID 3638721)
-- Name: temp_fk_key; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX temp_fk_key ON "temp" USING btree (te_fk1, te_fk2, te_fk3);


--
-- TOC entry 114 (OID 3638722)
-- Name: adrest_fkey; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX adrest_fkey ON adresy USING btree (ad_su_id);


--
-- TOC entry 76 (OID 3638723)
-- Name: system_user_email; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX system_user_email ON system_user USING btree (su_email);


--
-- TOC entry 80 (OID 3638724)
-- Name: system_user_ws; Type: INDEX; Schema: public; Owner: magazyn
--

CREATE INDEX system_user_ws ON system_user USING btree (su_ws);


--
-- TOC entry 42 (OID 3638725)
-- Name: towar_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar
    ADD CONSTRAINT towar_pkey PRIMARY KEY (to_id);


--
-- TOC entry 46 (OID 3638727)
-- Name: towar_parametry_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar_parametry
    ADD CONSTRAINT towar_parametry_pkey PRIMARY KEY (tp_id);


--
-- TOC entry 50 (OID 3638729)
-- Name: kategorie_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY kategorie
    ADD CONSTRAINT kategorie_pkey PRIMARY KEY (ka_id);


--
-- TOC entry 51 (OID 3638731)
-- Name: sklep_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY sklep
    ADD CONSTRAINT sklep_pkey PRIMARY KEY (sk_id);


--
-- TOC entry 52 (OID 3638733)
-- Name: sklep_sk_server_key; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY sklep
    ADD CONSTRAINT sklep_sk_server_key UNIQUE (sk_server);


--
-- TOC entry 55 (OID 3638735)
-- Name: towar_sklep_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar_sklep
    ADD CONSTRAINT towar_sklep_pkey PRIMARY KEY (ts_id);


--
-- TOC entry 56 (OID 3638737)
-- Name: magazyn_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY magazyn
    ADD CONSTRAINT magazyn_pkey PRIMARY KEY (ma_id);


--
-- TOC entry 58 (OID 3638739)
-- Name: stany_magazynowe_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY stany_magazynowe
    ADD CONSTRAINT stany_magazynowe_pkey PRIMARY KEY (sm_id);


--
-- TOC entry 60 (OID 3638741)
-- Name: kontrahent_sklep_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY kontrahent_sklep
    ADD CONSTRAINT kontrahent_sklep_pkey PRIMARY KEY (ks_id);


--
-- TOC entry 62 (OID 3638743)
-- Name: rabat_kontrahenta_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY rabat_kontrahenta
    ADD CONSTRAINT rabat_kontrahenta_pkey PRIMARY KEY (rk_id);


--
-- TOC entry 64 (OID 3638745)
-- Name: rabat_ilosciowy_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY rabat_ilosciowy
    ADD CONSTRAINT rabat_ilosciowy_pkey PRIMARY KEY (ri_id);


--
-- TOC entry 65 (OID 3638747)
-- Name: system_grupa_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_grupa
    ADD CONSTRAINT system_grupa_pkey PRIMARY KEY (sg_id);


--
-- TOC entry 67 (OID 3638749)
-- Name: system_obiekt_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_obiekt
    ADD CONSTRAINT system_obiekt_pkey PRIMARY KEY (so_server, so_klucz);


--
-- TOC entry 69 (OID 3638751)
-- Name: system_acl_obiekt_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_acl_obiekt
    ADD CONSTRAINT system_acl_obiekt_pkey PRIMARY KEY (sao_id, sao_grupa_id, sao_server, sao_klucz);


--
-- TOC entry 71 (OID 3638753)
-- Name: system_action_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_action
    ADD CONSTRAINT system_action_pkey PRIMARY KEY (sa_id);


--
-- TOC entry 73 (OID 3638755)
-- Name: system_log_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_log
    ADD CONSTRAINT system_log_pkey PRIMARY KEY (sl_id);


--
-- TOC entry 74 (OID 3638757)
-- Name: system_acl_grupa_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_acl_grupa
    ADD CONSTRAINT system_acl_grupa_pkey PRIMARY KEY (sag_id, sag_grupa_id, sag_user_id);


--
-- TOC entry 78 (OID 3638759)
-- Name: system_user_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_user
    ADD CONSTRAINT system_user_pkey PRIMARY KEY (su_id);


--
-- TOC entry 79 (OID 3638761)
-- Name: system_user_su_pesel_key; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_user
    ADD CONSTRAINT system_user_su_pesel_key UNIQUE (su_pesel);


--
-- TOC entry 81 (OID 3638763)
-- Name: system_update_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_update
    ADD CONSTRAINT system_update_pkey PRIMARY KEY (su_id);


--
-- TOC entry 84 (OID 3638765)
-- Name: system_action_log_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_action_log
    ADD CONSTRAINT system_action_log_pkey PRIMARY KEY (sal_id);


--
-- TOC entry 86 (OID 3638767)
-- Name: koszyk_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY koszyk
    ADD CONSTRAINT koszyk_pkey PRIMARY KEY (ko_id);


--
-- TOC entry 88 (OID 3638769)
-- Name: zamowienia_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY zamowienia
    ADD CONSTRAINT zamowienia_pkey PRIMARY KEY (za_id);


--
-- TOC entry 91 (OID 3638771)
-- Name: zampoz_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY zampoz
    ADD CONSTRAINT zampoz_pkey PRIMARY KEY (zp_id);


--
-- TOC entry 122 (OID 3638773)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar_parametry
    ADD CONSTRAINT "$1" FOREIGN KEY (tp_to_id) REFERENCES towar(to_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 124 (OID 3638777)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar_sklep
    ADD CONSTRAINT "$1" FOREIGN KEY (ts_to_id) REFERENCES towar(to_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 126 (OID 3638781)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY stany_magazynowe
    ADD CONSTRAINT "$1" FOREIGN KEY (sm_to_id) REFERENCES towar(to_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 123 (OID 3638785)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY kategorie
    ADD CONSTRAINT "$1" FOREIGN KEY (ka_parent) REFERENCES kategorie(ka_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 130 (OID 3638789)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY rabat_kontrahenta
    ADD CONSTRAINT "$1" FOREIGN KEY (rk_ka_id) REFERENCES kategorie(ka_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 132 (OID 3638793)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY rabat_ilosciowy
    ADD CONSTRAINT "$1" FOREIGN KEY (ri_ka_id) REFERENCES kategorie(ka_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 125 (OID 3638797)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar_sklep
    ADD CONSTRAINT "$2" FOREIGN KEY (ts_sk_id) REFERENCES sklep(sk_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 128 (OID 3638801)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY kontrahent_sklep
    ADD CONSTRAINT "$1" FOREIGN KEY (ks_sk_id) REFERENCES sklep(sk_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 133 (OID 3638805)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY rabat_ilosciowy
    ADD CONSTRAINT "$2" FOREIGN KEY (ri_sk_id) REFERENCES sklep(sk_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 141 (OID 3638809)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY koszyk
    ADD CONSTRAINT "$1" FOREIGN KEY (ko_ts_id) REFERENCES towar_sklep(ts_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 144 (OID 3638813)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY zampoz
    ADD CONSTRAINT "$1" FOREIGN KEY (zp_ts_id) REFERENCES towar_sklep(ts_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 127 (OID 3638817)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY stany_magazynowe
    ADD CONSTRAINT "$2" FOREIGN KEY (sm_ma_id) REFERENCES magazyn(ma_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 131 (OID 3638821)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY rabat_kontrahenta
    ADD CONSTRAINT "$2" FOREIGN KEY (rk_ks_id) REFERENCES kontrahent_sklep(ks_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 137 (OID 3638825)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_acl_grupa
    ADD CONSTRAINT "$1" FOREIGN KEY (sag_grupa_id) REFERENCES system_grupa(sg_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 134 (OID 3638829)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_acl_obiekt
    ADD CONSTRAINT "$1" FOREIGN KEY (sao_grupa_id) REFERENCES system_grupa(sg_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 138 (OID 3638833)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_acl_grupa
    ADD CONSTRAINT "$2" FOREIGN KEY (sag_user_id) REFERENCES system_user(su_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 136 (OID 3638837)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_log
    ADD CONSTRAINT "$1" FOREIGN KEY (sl_user_id) REFERENCES system_user(su_id) ON UPDATE NO ACTION ON DELETE CASCADE;


--
-- TOC entry 140 (OID 3638841)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_action_log
    ADD CONSTRAINT "$1" FOREIGN KEY (sal_user_id) REFERENCES system_user(su_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 139 (OID 3638845)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_user
    ADD CONSTRAINT "$1" FOREIGN KEY (su_parent) REFERENCES system_user(su_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 129 (OID 3638849)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY kontrahent_sklep
    ADD CONSTRAINT "$2" FOREIGN KEY (ks_su_id) REFERENCES system_user(su_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 143 (OID 3638853)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY zamowienia
    ADD CONSTRAINT "$1" FOREIGN KEY (za_su_id) REFERENCES system_user(su_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 145 (OID 3638857)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY zampoz
    ADD CONSTRAINT "$2" FOREIGN KEY (zp_za_id) REFERENCES zamowienia(za_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 93 (OID 3638861)
-- Name: messages_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (msg_id);


--
-- TOC entry 94 (OID 3638863)
-- Name: magazyn_sklep_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY magazyn_sklep
    ADD CONSTRAINT magazyn_sklep_pkey PRIMARY KEY (ms_id);


--
-- TOC entry 146 (OID 3638865)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY magazyn_sklep
    ADD CONSTRAINT "$1" FOREIGN KEY (ms_sk_id) REFERENCES sklep(sk_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 147 (OID 3638869)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY magazyn_sklep
    ADD CONSTRAINT "$2" FOREIGN KEY (ms_ma_id) REFERENCES magazyn(ma_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 96 (OID 3638873)
-- Name: towar_kategoria_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar_kategoria
    ADD CONSTRAINT towar_kategoria_pkey PRIMARY KEY (tk_id);


--
-- TOC entry 148 (OID 3638875)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar_kategoria
    ADD CONSTRAINT "$1" FOREIGN KEY (tk_to_id) REFERENCES towar(to_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 149 (OID 3638879)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar_kategoria
    ADD CONSTRAINT "$2" FOREIGN KEY (tk_ka_id) REFERENCES kategorie(ka_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 98 (OID 3638883)
-- Name: nieudane_zakupy_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY nieudane_zakupy
    ADD CONSTRAINT nieudane_zakupy_pkey PRIMARY KEY (nz_id);


--
-- TOC entry 150 (OID 3638885)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY nieudane_zakupy
    ADD CONSTRAINT "$1" FOREIGN KEY (nz_ts_id) REFERENCES towar_sklep(ts_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 151 (OID 3638889)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY nieudane_zakupy
    ADD CONSTRAINT "$2" FOREIGN KEY (nz_su_id) REFERENCES system_user(su_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 152 (OID 3638893)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY ulubione
    ADD CONSTRAINT "$1" FOREIGN KEY (ul_to_id) REFERENCES towar(to_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 153 (OID 3638897)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY ulubione
    ADD CONSTRAINT "$2" FOREIGN KEY (ul_su_id) REFERENCES system_user(su_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 154 (OID 3638901)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY ruchy
    ADD CONSTRAINT "$1" FOREIGN KEY (ru_ts_id) REFERENCES towar_sklep(ts_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 155 (OID 3638905)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY ruchy
    ADD CONSTRAINT "$2" FOREIGN KEY (ru_ma_id) REFERENCES magazyn(ma_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 156 (OID 3638909)
-- Name: $3; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY ruchy
    ADD CONSTRAINT "$3" FOREIGN KEY (ru_su_id) REFERENCES system_user(su_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 157 (OID 3638913)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY grupy_towarow
    ADD CONSTRAINT "$1" FOREIGN KEY (gt_to_id1) REFERENCES towar(to_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 158 (OID 3638917)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY grupy_towarow
    ADD CONSTRAINT "$2" FOREIGN KEY (gt_to_id2) REFERENCES towar(to_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 102 (OID 3638921)
-- Name: producent_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY producent
    ADD CONSTRAINT producent_pkey PRIMARY KEY (pr_id);


--
-- TOC entry 121 (OID 3638923)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY towar
    ADD CONSTRAINT "$1" FOREIGN KEY (to_pr_id) REFERENCES producent(pr_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 104 (OID 3638927)
-- Name: opcje_towaru_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY opcje_towaru
    ADD CONSTRAINT opcje_towaru_pkey PRIMARY KEY (ot_id);


--
-- TOC entry 159 (OID 3638929)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY opcje_towaru
    ADD CONSTRAINT "$1" FOREIGN KEY (ot_to_id) REFERENCES towar(to_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 106 (OID 3638933)
-- Name: promocja_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY promocja
    ADD CONSTRAINT promocja_pkey PRIMARY KEY (pm_id);


--
-- TOC entry 108 (OID 3638935)
-- Name: promocja_towaru_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY promocja_towaru
    ADD CONSTRAINT promocja_towaru_pkey PRIMARY KEY (pt_id);


--
-- TOC entry 160 (OID 3638937)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY promocja_towaru
    ADD CONSTRAINT "$1" FOREIGN KEY (pt_pm_id) REFERENCES promocja(pm_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 161 (OID 3638941)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY promocja_towaru
    ADD CONSTRAINT "$2" FOREIGN KEY (pt_ts_id) REFERENCES towar_sklep(ts_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 135 (OID 3638945)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY system_acl_obiekt
    ADD CONSTRAINT "$2" FOREIGN KEY (sao_server, sao_klucz) REFERENCES system_obiekt(so_server, so_klucz) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 110 (OID 3638949)
-- Name: zapytania_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY zapytania
    ADD CONSTRAINT zapytania_pkey PRIMARY KEY (za_id);


--
-- TOC entry 162 (OID 3638951)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY zapytania
    ADD CONSTRAINT "$1" FOREIGN KEY (za_ts_id) REFERENCES towar_sklep(ts_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 163 (OID 3638955)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY zapytania
    ADD CONSTRAINT "$2" FOREIGN KEY (za_pyt_su_id) REFERENCES system_user(su_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 164 (OID 3638959)
-- Name: $3; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY zapytania
    ADD CONSTRAINT "$3" FOREIGN KEY (za_odp_su_id) REFERENCES system_user(su_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 113 (OID 3638963)
-- Name: temp_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY "temp"
    ADD CONSTRAINT temp_pkey PRIMARY KEY (te_id);


--
-- TOC entry 115 (OID 3638965)
-- Name: adresy_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY adresy
    ADD CONSTRAINT adresy_pkey PRIMARY KEY (ad_id);


--
-- TOC entry 165 (OID 3638967)
-- Name: $1; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY adresy
    ADD CONSTRAINT "$1" FOREIGN KEY (ad_su_id) REFERENCES system_user(su_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 142 (OID 3638971)
-- Name: $2; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY koszyk
    ADD CONSTRAINT "$2" FOREIGN KEY (ko_su_id) REFERENCES system_user(su_id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- TOC entry 116 (OID 14187961)
-- Name: poczta_pkey; Type: CONSTRAINT; Schema: public; Owner: magazyn
--

ALTER TABLE ONLY poczta
    ADD CONSTRAINT poczta_pkey PRIMARY KEY (po_id);


--
-- PostgreSQL database dump
--


SET search_path = public, pg_catalog;

--
-- Data for TOC entry 2 (OID 3563486)
-- Name: system_update; Type: TABLE DATA; Schema: public; Owner: magazyn
--

INSERT INTO system_update VALUES (3, 'opisy_kat_jpaktow.sql           ', 1098182632, 'ALTER TABLE towar ADD to_jp char(20);
ALTER TABLE kategorie ADD ka_opis_m_i Text;
ALTER TABLE kategorie ADD ka_opis_d_i Text;');
INSERT INTO system_update VALUES (7, 'opcje_towaru.sql                ', 1098708547, 'ALTER TABLE zampoz ADD zp_opcje Text;

Create table "opcje_towaru"
(
	"ot_id" Serial NOT NULL,
	"ot_to_id" integer NOT NULL,
	"ot_opcje" Text,
	"ot_ilosc" Smallint,
 primary key ("ot_id")
);

Alter table "opcje_towaru" add  foreign key ("ot_to_id") references "towar" ("to_id") on update cascade on delete cascade;');
INSERT INTO system_update VALUES (8, 'promocje.sql                    ', 1098708547, 'Create table "promocja"
(
	"pm_id" Serial NOT NULL,
	"pm_symbol" Char(32),
	"pm_poczatek" integer,
	"pm_koniec" integer,
	"pm_rabat_domyslny" Double precision,
 primary key ("pm_id")
);

Create table "promocja_towaru"
(
	"pt_id" Serial NOT NULL,
	"pt_ts_id" integer NOT NULL,
	"pt_pm_id" integer NOT NULL,
	"pt_cena" Double precision,
 primary key ("pt_id")
);


Alter table "promocja_towaru" add  foreign key ("pt_pm_id") references "promocja" ("pm_id") on update cascade on delete cascade;
Alter table "promocja_towaru" add  foreign key ("pt_ts_id") references "towar_sklep" ("ts_id") on update cascade on delete cascade;');
INSERT INTO system_update VALUES (4, 'opcje_systemu.sql               ', 1, NULL);
INSERT INTO system_update VALUES (5, 'blokady.sql                     ', 1, NULL);
INSERT INTO system_update VALUES (6, 'slowa_kluczowe.sql              ', 1098186467, 'ALTER TABLE towar ADD to_klucze char(256);');
INSERT INTO system_update VALUES (9, 'opcje_systemu2.sql              ', 1098709105, 'INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES (''master'',''Gospodarz'',''SELECT su_id,su_nazwisko FROM system_user WHERE su_parent IS NULL ORDER BY su_nazwisko'','''');

INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES (''auth'',''Autoryzacja'',''email,login'',''login'');');
INSERT INTO system_update VALUES (10, 'opcje_towaru2.sql               ', 1099044095, 'ALTER TABLE koszyk ADD ko_opcje Text;');
INSERT INTO system_update VALUES (11, 'towar_sklep_aktywny.sql         ', 1099576708, 'ALTER TABLE towar_sklep ADD ts_aktywny Smallint ;
ALTER TABLE towar_sklep ALTER ts_aktywny SET DEFAULT 1;
UPDATE towar_sklep SET ts_aktywny=1;');
INSERT INTO system_update VALUES (12, 'towar_kategoria_liczenie.sql    ', 1099652146, 'ALTER TABLE towar RENAME to_ka_id TO to_ka_c;
ALTER TABLE towar ALTER to_ka_c SET DEFAULT 0;

ALTER TABLE kategorie ADD ka_to_c Integer;
ALTER TABLE kategorie ALTER ka_to_c SET DEFAULT 0;

CREATE function ileTowWKat (Integer) returns Bigint
AS ''SELECT count(tk_id) FROM towar_kategoria WHERE tk_ka_id = $1''
LANGUAGE ''sql'';

CREATE function wIluKatTow (Integer) returns Bigint
AS ''SELECT count(tk_id) FROM towar_kategoria WHERE tk_to_id = $1''
LANGUAGE ''sql'';');
INSERT INTO system_update VALUES (14, 'webservices.sql                 ', 1100011363, 'ALTER TABLE zamowienia ADD za_ws char(32);
ALTER TABLE towar ADD to_ws char(32);');
INSERT INTO system_update VALUES (15, 'webservices2.sql                ', 1100075965, 'ALTER TABLE zamowienia ADD za_ws_update Int;
ALTER TABLE towar ADD to_ws_update Int;

INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES (''wsu'',''WebServices użytkownik'','''','''');

INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES (''wsp'',''WebServices hasło'','''','''');');
INSERT INTO system_update VALUES (16, 'uwagi_marcina.sql               ', 1100605412, 'ALTER TABLE zamowienia ADD za_uwagi_przyjecia Text;
ALTER TABLE zamowienia ADD za_uwagi_realizacji Text;
ALTER TABLE zamowienia ADD za_osoba_przyjecia Integer;
ALTER TABLE zamowienia ADD za_osoba_realizacji Integer;

ALTER TABLE zampoz ADD zp_rabat Double precision;

ALTER TABLE promocja_towaru ADD pt_poczatek Integer;
ALTER TABLE promocja_towaru ADD pt_koniec Integer;');
INSERT INTO system_update VALUES (17, 'webservices3.sql                ', 1100605925, 'ALTER TABLE zampoz ADD zp_cena_ws Double precision;
ALTER TABLE zampoz ADD zp_ilosc_ws Double precision;');
INSERT INTO system_update VALUES (18, 'zapytania.sql                   ', 1100707755, 'Create table "zapytania"
(
	"za_id" Serial NOT NULL,
	"za_ts_id" integer NOT NULL,
	"za_pyt_su_id" integer,
	"za_odp_su_id" integer NOT NULL,
	"za_email" Char(96),
	"za_telefon" Char(48),
	"za_pyt" Text,
	"za_odp" Text,
	"za_pyt_data" integer,
	"za_odp_data" integer,
 primary key ("za_id")
);


Alter table "zapytania" add  foreign key ("za_ts_id") references "towar_sklep" ("ts_id") on update cascade on delete cascade;
Alter table "zapytania" add  foreign key ("za_pyt_su_id") references "system_user" ("su_id") on update cascade on delete cascade;
Alter table "zapytania" add  foreign key ("za_odp_su_id") references "system_user" ("su_id") on update cascade on delete cascade;');
INSERT INTO system_update VALUES (19, 'opcje_systemu3.sql              ', 1100777458, 'INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES (''prompt'',''Zapytaj o ilość przed dodaniem do koszyka'',''0,1'',''1'');');
INSERT INTO system_update VALUES (20, 'zamowienia_wart_osoba.sql       ', 1100864997, 'ALTER TABLE zamowienia ADD za_wart_nt Double precision;
ALTER TABLE zamowienia ADD za_wart_br Double precision;
ALTER TABLE zamowienia ADD za_osoba Integer;');
INSERT INTO system_update VALUES (21, 'webservices4.sql                ', 1101224795, 'ALTER TABLE kategorie ADD ka_ws Char(32);');
INSERT INTO system_update VALUES (22, 'towar_ean.sql                   ', 1101389228, 'ALTER TABLE towar ADD to_ean Char(25);');
INSERT INTO system_update VALUES (23, 'indeksy.sql                     ', 1101465483, 'Drop index "towar_indeks_key";
Drop index "system_obiekt_key2";
Drop index "system_action_key2";
Drop index "system_acl_grupa_ukey2";
Drop index "system_user_login2";
Drop index "messages_key";
Drop index "grupy_towarow_key";
Drop index "koszyk_key";


Create index "towar_indeks_key" on "towar" using btree ("to_indeks","to_ean","to_ws");
Create index "towar_producent_key" on "towar" using btree ("to_pr_id");
Create unique index "towar_u_indeks_key" on "towar" using btree ("to_indeks");
Create unique index "towar_u_ean_key" on "towar" using btree ("to_ws");
Create index "towar_parametry_fk" on "towar_parametry" using btree ("tp_to_id");
Create index "kategoria_parent_key" on "kategorie" using btree ("ka_parent");
Create index "towar_sklep_key" on "towar_sklep" using btree ("ts_to_id","ts_sk_id");
Create index "stany_magazynowe_key" on "stany_magazynowe" using btree ("sm_to_id","sm_ma_id");
Create index "kontrahent_sklep_key" on "kontrahent_sklep" using btree ("ks_sk_id","ks_su_id");
Create index "rabat_kontrahenta_key" on "rabat_kontrahenta" using btree ("rk_ka_id","rk_ks_id");
Create index "rabat_ilosciowy_key" on "rabat_ilosciowy" using btree ("ri_sk_id","ri_ka_id");
Create index "system_obiekt_key2" on "system_obiekt" using btree ("so_server","so_klucz");
Create index "system_acl_obiekt_key" on "system_acl_obiekt" using btree ("sao_grupa_id","sao_server","sao_klucz");
Create index "system_action_key2" on "system_action" using btree ("sa_server","sa_page_id","sa_action");
Create index "system_log_key" on "system_log" using btree ("sl_user_id","sl_server","sl_tin","sl_tout");
Create unique index "system_acl_grupa_ukey2" on "system_acl_grupa" using btree ("sag_grupa_id","sag_user_id","sag_server");
Create index "system_user_login2" on "system_user" using btree ("su_login","su_pesel");
Create index "update_db_key" on "system_update" using btree ("su_klucz","su_data");
Create index "system_action_log_key" on "system_action_log" using btree ("sal_user_id","sal_action","sal_data");
Create index "koszyk_key" on "koszyk" using btree ("ko_su_id","ko_ts_id","ko_deadline","ko_rez_data");
Create index "zamowienia_key" on "zamowienia" using btree ("za_su_id","za_sk_id","za_data");
Create index "zampoz_key" on "zampoz" using btree ("zp_za_id","zp_ts_id");
Create index "messages_key" on "messages" using btree ("msg_label","msg_lang");
Create index "towar_kategoria_key" on "towar_kategoria" using btree ("tk_to_id","tk_ka_id");
Create index "nieudane_zakupy_key" on "nieudane_zakupy" using btree ("nz_ts_id","nz_su_id");
Create index "ulubione_key" on "ulubione" using btree ("ul_su_id","ul_to_id");
Create index "ruchy_key" on "ruchy" using btree ("ru_su_id","ru_ts_id","ru_ma_id");
Create index "grupy_towarow_key" on "grupy_towarow" using btree ("gt_to_id1","gt_to_id2","gt_grupa");
Create index "towar_opcje_key" on "opcje_towaru" using btree ("ot_to_id");
Create index "promocja_towaru_key" on "promocja_towaru" using btree ("pt_ts_id","pt_pm_id");
Create index "zapytania_key" on "zapytania" using btree ("za_ts_id","za_odp_su_id");');
INSERT INTO system_update VALUES (24, 'webservices5.sql                ', 1101771224, 'ALTER TABLE producent ADD pr_ws Char(32);');
INSERT INTO system_update VALUES (25, 'webservices_idx.sql             ', 1101830931, 'Create index "kategoria_ws_key" on "kategorie" using btree ("ka_ws");
Create index "kategoria_kod_key" on "kategorie" using btree ("ka_kod");
Create index "producent_ws_key" on "producent" using btree ("pr_ws");
Create index "zamowienia_ws_key" on "zamowienia" using btree ("za_ws");');
INSERT INTO system_update VALUES (26, 'temp.sql                        ', 1101977907, 'Create table "temp"
(
	"te_id" Serial NOT NULL,
	"te_indeks" Char(32),
	"te_fk1" integer,
	"te_fk2" integer,
	"te_fk3" integer,
	"te_wart" Char(100),
 primary key ("te_id")
);

Create unique index "temp_indeks_key" on "temp" using btree ("te_indeks");
Create index "temp_fk_key" on "temp" using btree ("te_fk1","te_fk2","te_fk3");');
INSERT INTO system_update VALUES (27, 'opcje_systemu4.sql              ', 1101978424, 'INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES (''autologout'',''Auto logout [min]'','''',''0'');');
INSERT INTO system_update VALUES (28, 'indeksy2.sql                    ', 1101988023, 'Create index "towar_nazwa_key" on "towar" using btree ("to_nazwa");
Create index "towar_sklep_cena_key" on "towar_sklep" using btree ("ts_cena");');
INSERT INTO system_update VALUES (29, 'opcje_systemu5.sql              ', 1102021524, 'INSERT INTO system_opcje (so_nazwa2,so_nazwa,so_lista,so_wart) 
VALUES (''temptime'',''Czas przechowywania danych temporalnych'','''',''3600'');');
INSERT INTO system_update VALUES (30, 'clear_empty_msg.sql             ', 1102065082, 'DELETE FROM messages WHERE msg_label='''';');
INSERT INTO system_update VALUES (33, 'adresy.sql                      ', 1102409695, 'Create table "adresy"
(
	"ad_id" Serial NOT NULL,
	"ad_su_id" integer NOT NULL,
	"ad_adres" Text,
 primary key ("ad_id")
);

Alter table "adresy" add  foreign key ("ad_su_id") references "system_user" ("su_id") on update cascade on delete cascade;

Create index "adrest_fkey" on "adresy" using btree ("ad_su_id");');
INSERT INTO system_update VALUES (32, 'temptext.sql                    ', 1102066944, 'DROP TABLE temp CASCADE;

Create table "temp"
(
	"te_id" Serial NOT NULL,
	"te_indeks" Char(48),
	"te_fk1" integer,
	"te_fk2" integer,
	"te_fk3" integer,
	"te_deadline" integer,
	"te_wart" Text,
 primary key ("te_id")
);

Create unique index "temp_indeks_key" on "temp" using btree ("te_indeks");
Create index "temp_fk_key" on "temp" using btree ("te_fk1","te_fk2","te_fk3");');
INSERT INTO system_update VALUES (34, 'refer_koszyk_cascade.sql        ', 1102715382, 'ALTER TABLE koszyk DROP CONSTRAINT "$2";
ALTER TABLE koszyk ADD CONSTRAINT "$2" FOREIGN KEY (ko_su_id) REFERENCES
	system_user(su_id) ON UPDATE RESTRICT ON DELETE CASCADE');
INSERT INTO system_update VALUES (35, 'su_regon_termin.sql             ', 1103716744, 'ALTER TABLE system_user ADD su_regon Char(20);
ALTER TABLE system_user ADD su_termin_platnosci Char(20);');
INSERT INTO system_update VALUES (36, 'poczta.sql                      ', 1116668742, 'ALTER TABLE zamowienia ADD za_poczta_nt double precision;
ALTER TABLE zamowienia ADD za_poczta_br double precision;
ALTER TABLE zapytania ADD za_cena double precision;

Create table "poczta"
(
        "po_id" Serial NOT NULL,
        "po_nazwa" Varchar(80),
        "po_cena_nt" double precision,
        "po_cena_br" double precision,
 primary key ("po_id")
);');
INSERT INTO system_update VALUES (37, 'ts_pri.sql                      ', 1129328333, 'Alter table "towar_sklep" add  ts_pri int2;');
INSERT INTO system_update VALUES (38, 'ts_pri2.sql                     ', 1133964334, 'Alter table "towar_sklep" add  ts_pri2 int2;');
INSERT INTO system_update VALUES (39, 'ts_pri_int4.sql                 ', 1133964840, 'Alter table "towar_sklep" add  ts_pri4 Integer;
Alter table "towar_sklep" add  ts_pri24 Integer;

UPDATE "towar_sklep" SET ts_pri4=ts_pri;
UPDATE "towar_sklep" SET ts_pri24=ts_pri2;

Alter table "towar_sklep" Rename ts_pri TO ts_pri_old;
Alter table "towar_sklep" Rename ts_pri4 TO ts_pri;

Alter table "towar_sklep" Rename ts_pri2 TO ts_pri2_old;
Alter table "towar_sklep" Rename ts_pri24 TO ts_pri2;');
INSERT INTO system_update VALUES (40, 'ts_pri_seq.sql                  ', 1133964866, 'CREATE OR REPLACE FUNCTION ts_pri_seq() returns integer
AS ''
	SELECT max(ts_pri)+1 FROM towar_sklep WHERE 1 BETWEEN 0 AND (SELECT count(*) FROM towar_sklep WHERE ts_pri IS NOT NULL)
	UNION
	SELECT 1 FROM towar_sklep WHERE 0 BETWEEN (SELECT count(*) FROM towar_sklep WHERE ts_pri IS NOT NULL) AND 1
''
LANGUAGE ''sql'';

CREATE OR REPLACE FUNCTION ts_pri2_seq() returns integer
AS ''
	SELECT max(ts_pri2)+1 FROM towar_sklep WHERE 1 BETWEEN 0 AND (SELECT count(*) FROM towar_sklep WHERE ts_pri2 IS NOT NULL)
	UNION
	SELECT 1 FROM towar_sklep WHERE 0 BETWEEN (SELECT count(*) FROM towar_sklep WHERE ts_pri2 IS NOT NULL) AND 1
''
LANGUAGE ''sql'';


ALTER TABLE towar_sklep ALTER ts_pri DROP DEFAULT ;
ALTER TABLE towar_sklep ALTER ts_pri SET DEFAULT ts_pri_seq();

ALTER TABLE towar_sklep ALTER ts_pri2 DROP DEFAULT ;
ALTER TABLE towar_sklep ALTER ts_pri2 SET DEFAULT ts_pri2_seq();

UPDATE towar_sklep SET ts_pri=ts_id WHERE ts_pri IS NULL;
UPDATE towar_sklep SET ts_pri2=ts_id WHERE ts_pri2 IS NULL;');


--
-- TOC entry 1 (OID 3563484)
-- Name: system_update_su_id_seq; Type: SEQUENCE SET; Schema: public; Owner: magazyn
--

SELECT pg_catalog.setval ('system_update_su_id_seq', 40, true);


--
-- PostgreSQL database dump
--


SET search_path = public, pg_catalog;

--
-- Data for TOC entry 2 (OID 3563562)
-- Name: system_opcje; Type: TABLE DATA; Schema: public; Owner: magazyn
--

INSERT INTO system_opcje VALUES (5, 'auth                ', 'Autoryzacja', 'email,login', 'login               ');
INSERT INTO system_opcje VALUES (10, 'autologout          ', 'Auto logout [min]', '', '45                  ');
INSERT INTO system_opcje VALUES (3, 'czas                ', 'Limit czasu dla produktów w koszyku', '0,1', '                    ');
INSERT INTO system_opcje VALUES (2, 'koszyk              ', 'Nawiguj do koszyka', '0,1', '                    ');
INSERT INTO system_opcje VALUES (1, 'mag                 ', 'Kontrola magazynu', '0,1', '1                   ');
INSERT INTO system_opcje VALUES (8, 'master              ', 'Gospodarz', 'SELECT su_id AS value,su_nazwisko AS option FROM system_user WHERE su_parent IS NULL ORDER BY su_nazwisko', '18                  ');
INSERT INTO system_opcje VALUES (9, 'prompt              ', 'Zapytaj o ilość przed dodaniem do koszyka', '0,1', '1                   ');
INSERT INTO system_opcje VALUES (11, 'temptime            ', 'Czas przechowywania danych temporalnych', '', '36000               ');
INSERT INTO system_opcje VALUES (7, 'wsp                 ', 'WebServices hasło', '', 'robert              ');
INSERT INTO system_opcje VALUES (6, 'wsu                 ', 'WebServices użytkownik', '', 'stronawww           ');


--
-- TOC entry 1 (OID 3563560)
-- Name: system_opcje_so_id_seq; Type: SEQUENCE SET; Schema: public; Owner: magazyn
--

SELECT pg_catalog.setval ('system_opcje_so_id_seq', 11, true);


ALTER TABLE towar_sklep ALTER ts_pri SET DEFAULT ts_pri_seq(); ALTER TABLE towar_sklep ALTER ts_pri2 SET DEFAULT ts_pri2_seq();
