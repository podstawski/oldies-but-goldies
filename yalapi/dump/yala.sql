--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: yala
--

CREATE PROCEDURAL LANGUAGE plpgsql;


ALTER PROCEDURAL LANGUAGE plpgsql OWNER TO yala;

SET search_path = public, pg_catalog;

--
-- Name: acl_delete_cascade(); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION acl_delete_cascade() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
	EXECUTE ('
		DELETE FROM ' || TG_ARGV[0] || '_acl WHERE object_id=' || OLD.id || ';
	');
RETURN OLD;
END;
$$;


ALTER FUNCTION public.acl_delete_cascade() OWNER TO yala;

--
-- Name: acl_has_right(name, integer, character varying, name); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION acl_has_right(name, integer, character varying, name) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$
DECLARE
	result Boolean;
BEGIN
	SELECT INTO result usesuper FROM pg_user WHERE usename IN ($4);

	IF result THEN
		RETURN true;
	END IF;

	FOR result IN EXECUTE 'SELECT _' || $3 || ' FROM ' || $1 || '_acl WHERE object_id IN (0, ' || $2 || ') AND username IN (''*'', ''' || CURRENT_DATABASE() || '_' || $4 || ''') ORDER BY _' || $3 || ' DESC LIMIT 1' LOOP END LOOP;


	RETURN COALESCE(result, FALSE);
END;
$_$;


ALTER FUNCTION public.acl_has_right(name, integer, character varying, name) OWNER TO yala;

--
-- Name: acl_has_right(name, integer, character varying); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION acl_has_right(name, integer, character varying) RETURNS boolean
    LANGUAGE sql
    AS $_$
	SELECT acl_has_right($1, $2, $3, replace(CURRENT_USER, CURRENT_DATABASE() || '_', ''));
$_$;


ALTER FUNCTION public.acl_has_right(name, integer, character varying) OWNER TO yala;

--
-- Name: acl_insert_cascade(); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION acl_insert_cascade() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
	EXECUTE ('
		INSERT INTO ' || TG_ARGV[0] || '_acl (object_id)
                SELECT ' || NEW.id || '
                WHERE NOT acl_has_right(''' || TG_ARGV[0] || ''',' || NEW.id || ',''update'');
	');
RETURN OLD;
END;
$$;


ALTER FUNCTION public.acl_insert_cascade() OWNER TO yala;

--
-- Name: acl_table_change(); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION acl_table_change() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
NEW.updated=CURRENT_TIMESTAMP;
RETURN NEW;
END;
$$;


ALTER FUNCTION public.acl_table_change() OWNER TO yala;

--
-- Name: acl_table_delete(); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION acl_table_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
UPDATE acl SET updated=CURRENT_TIMESTAMP WHERE id IN
    (SELECT id FROM acl WHERE id<>OLD.id AND table_name=OLD.table_name LIMIT 1);
RETURN OLD;
END;
$$;


ALTER FUNCTION public.acl_table_delete() OWNER TO yala;

--
-- Name: create_acl_table(name); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION create_acl_table(name) RETURNS name
    LANGUAGE plpgsql
    AS $_$
BEGIN
	EXECUTE('
		DROP TABLE IF EXISTS ' || $1 || '_acl CASCADE; 
		CREATE TABLE ' || $1 || '_acl (object_id Integer DEFAULT 0, username Name DEFAULT CURRENT_USER, _select boolean DEFAULT true, _update boolean DEFAULT true, _insert boolean DEFAULT true, _delete boolean DEFAULT true);
		CREATE UNIQUE INDEX ' || $1 || '_acl_key ON ' || $1 || '_acl (object_id, username);
		GRANT SELECT ON ' || $1 || '_acl TO public;
                GRANT ALL ON ' || $1 || ' TO public;
                GRANT ALL ON ' || $1 || '_id_seq TO public;

		DROP RULE IF EXISTS ' || $1 || '_delete ON ' || $1 || ';
		CREATE RULE ' || $1 || '_delete AS ON DELETE TO ' || $1 || ' WHERE NOT acl_has_right(''' || $1 || ''' , id, ''delete'') DO INSTEAD NOTHING;		

		DROP RULE IF EXISTS ' || $1 || '_update ON ' || $1 || ';
		CREATE RULE ' || $1 || '_update AS ON UPDATE TO ' || $1 || ' WHERE NOT acl_has_right(''' || $1 || ''' , NEW.id, ''update'') DO INSTEAD NOTHING;

		DROP RULE IF EXISTS ' || $1 || '_insert ON ' || $1 || ';
		CREATE RULE ' || $1 || '_insert AS ON INSERT TO ' || $1 || ' WHERE NOT acl_has_right(''' || $1 || ''' , 0, ''insert'') DO INSTEAD NOTHING;

		DROP TRIGGER IF EXISTS ' || $1 || '_acl_delete ON ' || $1 || ';

		DROP TRIGGER IF EXISTS ' || $1 || '_acl_insert ON ' || $1 || ';

	');
        
        PERFORM create_acl_view( $1 );

        EXECUTE ('INSERT INTO ' || $1 || '_acl ("object_id","username") VALUES (0,''*'');');
	RETURN $1 || '_acl';
END;
$_$;


ALTER FUNCTION public.create_acl_table(name) OWNER TO yala;

--
-- Name: create_acl_view(name); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION create_acl_view(name) RETURNS name
    LANGUAGE plpgsql
    AS $_$
BEGIN
       

	EXECUTE ('
                DROP VIEW IF EXISTS ' || $1 || '_view;
		CREATE VIEW ' || $1 || '_view AS 
		SELECT ' || $1 || '.* FROM ' || $1 || ' 
		INNER JOIN ' || $1 || '_acl 
			ON (' || $1 || '_acl.object_id IN (0,' || $1 || '.id) AND ' || $1 || '_acl.username IN (CURRENT_USER,''*'') AND _select) OR (SELECT usesuper FROM pg_user WHERE usename=CURRENT_USER) ;
		GRANT ALL ON ' || $1 || '_view TO public;

	');

	RETURN $1;
END;
$_$;


ALTER FUNCTION public.create_acl_view(name) OWNER TO yala;

--
-- Name: create_user(character varying, character varying, character varying, character varying, boolean, integer); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION create_user(character varying, character varying, character varying, character varying, boolean, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
	result integer;
BEGIN
	EXECUTE E'CREATE USER "' || CURRENT_DATABASE() || '_' || $1 || '" ENCRYPTED PASSWORD ''' || $2 || '''';
	INSERT INTO users (username, first_name, last_name) VALUES ($1, $3, $4);

	SELECT INTO result update_password($1, $2, $5);
    UPDATE users SET role_id = $6 WHERE id = result;

	RETURN result;
END;
$_$;


ALTER FUNCTION public.create_user(character varying, character varying, character varying, character varying, boolean, integer) OWNER TO yala;

--
-- Name: create_user(character varying, character varying, boolean, integer); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION create_user(character varying, character varying, boolean, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
	result Integer;
BEGIN
	EXECUTE E'CREATE USER "' || CURRENT_DATABASE() || '_' || $1 || '" ENCRYPTED PASSWORD ''' || $2 || '''';
	INSERT INTO users (username) VALUES ($1);

	SELECT INTO result update_password($1, $2, $3);
    UPDATE users SET role_id = $4 WHERE id = result;

	RETURN result;
END;
$_$;


ALTER FUNCTION public.create_user(character varying, character varying, boolean, integer) OWNER TO yala;

--
-- Name: delete_user(character varying); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION delete_user(character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
	result Integer;
BEGIN
	SELECT INTO result id FROM users WHERE username = $1;

	EXECUTE E'DROP USER "' || CURRENT_DATABASE() || '_' || $1 || '"';
	DELETE FROM users WHERE username = $1;

	RETURN result;
END;
$_$;


ALTER FUNCTION public.delete_user(character varying) OWNER TO yala;

--
-- Name: drop_acl_table(name); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION drop_acl_table(name) RETURNS name
    LANGUAGE plpgsql
    AS $_$
BEGIN
	EXECUTE('
		DROP TABLE IF EXISTS ' || $1 || '_acl CASCADE; 
		DROP RULE IF EXISTS ' || $1 || '_delete ON ' || $1 || ';	
		DROP RULE IF EXISTS ' || $1 || '_update ON ' || $1 || ';
		DROP RULE IF EXISTS ' || $1 || '_insert ON ' || $1 || ';
		DROP TRIGGER IF EXISTS ' || $1 || '_acl_delete ON ' || $1 || ';
		DROP TRIGGER IF EXISTS ' || $1 || '_acl_insert ON ' || $1 || ';

	');
        PERFORM drop_acl_view( $1 );
	RETURN $1 || '_acl';
END;
$_$;


ALTER FUNCTION public.drop_acl_table(name) OWNER TO yala;

--
-- Name: drop_acl_view(name); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION drop_acl_view(name) RETURNS name
    LANGUAGE plpgsql
    AS $_$
BEGIN
	EXECUTE ('
		DROP VIEW IF EXISTS ' || $1 || '_view;
	');

	RETURN $1;
END;
$_$;


ALTER FUNCTION public.drop_acl_view(name) OWNER TO yala;

--
-- Name: update_password(character varying, character varying, boolean); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION update_password(character varying, character varying, boolean) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
	show_plain_password ALIAS FOR $3;
	result Integer;
	pass Varchar;
BEGIN
	EXECUTE E'ALTER USER "' || CURRENT_DATABASE() || '_' || $1 || '" ENCRYPTED PASSWORD ''' || $2 || '''';

	pass = NULL;
	IF show_plain_password THEN
		pass = $2;
	END IF;

	UPDATE users SET plain_password = pass WHERE username = $1;
	SELECT INTO result id FROM users WHERE username = $1;

	RETURN result;

END;
$_$;


ALTER FUNCTION public.update_password(character varying, character varying, boolean) OWNER TO yala;

--
-- Name: user_profile_printed(integer); Type: FUNCTION; Schema: public; Owner: yala
--

CREATE FUNCTION user_profile_printed(integer) RETURNS integer
    LANGUAGE sql
    AS $_$
    UPDATE user_profile SET printed = 1 WHERE user_id = $1;
    SELECT 1;
$_$;


ALTER FUNCTION public.user_profile_printed(integer) OWNER TO yala;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE acl (
    id integer NOT NULL,
    table_name character varying(64),
    username name,
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true,
    updated timestamp without time zone DEFAULT now(),
    object_id integer DEFAULT 0
);


ALTER TABLE public.acl OWNER TO yala;

--
-- Name: acl_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE acl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.acl_id_seq OWNER TO yala;

--
-- Name: acl_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE acl_id_seq OWNED BY acl.id;


--
-- Name: acl_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('acl_id_seq', 5441, true);


--
-- Name: apps; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE apps (
    id integer NOT NULL,
    domain character varying(256) NOT NULL,
    token text NOT NULL
);


ALTER TABLE public.apps OWNER TO yala;

--
-- Name: apps_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE apps_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.apps_acl OWNER TO yala;

--
-- Name: apps_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE apps_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.apps_id_seq OWNER TO yala;

--
-- Name: apps_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE apps_id_seq OWNED BY apps.id;


--
-- Name: apps_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('apps_id_seq', 1, true);


--
-- Name: apps_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW apps_view AS
    SELECT apps.id, apps.domain, apps.token FROM (apps JOIN apps_acl ON ((((((apps_acl.object_id = 0) OR (apps_acl.object_id = apps.id)) AND (apps_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND apps_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.apps_view OWNER TO yala;

--
-- Name: course_schedule; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE course_schedule (
    id integer NOT NULL,
    course_unit_id integer NOT NULL,
    lesson_date date NOT NULL,
    schedule text NOT NULL,
    subject character varying(256) NOT NULL
);


ALTER TABLE public.course_schedule OWNER TO yala;

--
-- Name: course_schedule_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE course_schedule_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.course_schedule_acl OWNER TO yala;

--
-- Name: course_schedule_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE course_schedule_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.course_schedule_id_seq OWNER TO yala;

--
-- Name: course_schedule_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE course_schedule_id_seq OWNED BY course_schedule.id;


--
-- Name: course_schedule_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('course_schedule_id_seq', 1, true);


--
-- Name: course_schedule_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW course_schedule_view AS
    SELECT course_schedule.id, course_schedule.course_unit_id, course_schedule.lesson_date, course_schedule.schedule, course_schedule.subject FROM (course_schedule JOIN course_schedule_acl ON ((((((course_schedule_acl.object_id = 0) OR (course_schedule_acl.object_id = course_schedule.id)) AND (course_schedule_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND course_schedule_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.course_schedule_view OWNER TO yala;

--
-- Name: course_units; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE course_units (
    id integer NOT NULL,
    name character varying(256) NOT NULL,
    hour_amount integer NOT NULL,
    course_id integer NOT NULL,
    user_id integer NOT NULL
);


ALTER TABLE public.course_units OWNER TO yala;

--
-- Name: course_units_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE course_units_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.course_units_acl OWNER TO yala;

--
-- Name: course_units_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE course_units_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.course_units_id_seq OWNER TO yala;

--
-- Name: course_units_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE course_units_id_seq OWNED BY course_units.id;


--
-- Name: course_units_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('course_units_id_seq', 7, true);


--
-- Name: course_units_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW course_units_view AS
    SELECT course_units.id, course_units.name, course_units.hour_amount, course_units.course_id, course_units.user_id FROM (course_units JOIN course_units_acl ON ((((((course_units_acl.object_id = 0) OR (course_units_acl.object_id = course_units.id)) AND (course_units_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND course_units_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.course_units_view OWNER TO yala;

--
-- Name: courses; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE courses (
    id integer NOT NULL,
    training_center_id integer NOT NULL,
    name character varying(256) NOT NULL,
    code character varying(256) NOT NULL,
    level smallint,
    price integer NOT NULL,
    description text,
    created_date date DEFAULT now() NOT NULL,
    project_id integer NOT NULL,
    group_id integer,
    color character varying(256),
    status integer DEFAULT 1 NOT NULL,
    start_date timestamp without time zone,
    end_date timestamp without time zone,
    show_on_www smallint DEFAULT 1 NOT NULL,
    hash character varying(256) DEFAULT 'empty'::character varying NOT NULL
);


ALTER TABLE public.courses OWNER TO yala;

--
-- Name: courses_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE courses_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.courses_acl OWNER TO yala;

--
-- Name: courses_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE courses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.courses_id_seq OWNER TO yala;

--
-- Name: courses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE courses_id_seq OWNED BY courses.id;


--
-- Name: courses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('courses_id_seq', 7, true);


--
-- Name: courses_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW courses_view AS
    SELECT courses.id, courses.training_center_id, courses.name, courses.code, courses.level, courses.price, courses.description, courses.created_date, courses.project_id, courses.group_id, courses.color, courses.status, courses.start_date, courses.end_date, courses.show_on_www, courses.hash FROM (courses JOIN courses_acl ON ((((((courses_acl.object_id = 0) OR (courses_acl.object_id = courses.id)) AND (courses_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND courses_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.courses_view OWNER TO yala;

--
-- Name: doctrine_migration_version; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE doctrine_migration_version (
    version integer
);


ALTER TABLE public.doctrine_migration_version OWNER TO yala;

--
-- Name: exam_grades; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE exam_grades (
    id integer NOT NULL,
    exam_id integer NOT NULL,
    user_id integer NOT NULL,
    grade numeric(4,2) NOT NULL
);


ALTER TABLE public.exam_grades OWNER TO yala;

--
-- Name: exam_grades_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE exam_grades_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.exam_grades_acl OWNER TO yala;

--
-- Name: exam_grades_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE exam_grades_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.exam_grades_id_seq OWNER TO yala;

--
-- Name: exam_grades_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE exam_grades_id_seq OWNED BY exam_grades.id;


--
-- Name: exam_grades_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('exam_grades_id_seq', 2, true);


--
-- Name: exam_grades_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW exam_grades_view AS
    SELECT exam_grades.id, exam_grades.exam_id, exam_grades.user_id, exam_grades.grade FROM (exam_grades JOIN exam_grades_acl ON ((((((exam_grades_acl.object_id = 0) OR (exam_grades_acl.object_id = exam_grades.id)) AND (exam_grades_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND exam_grades_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.exam_grades_view OWNER TO yala;

--
-- Name: exams; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE exams (
    id integer NOT NULL,
    course_unit_id integer NOT NULL,
    name character varying(256) NOT NULL,
    type character varying(256),
    created_date date NOT NULL
);


ALTER TABLE public.exams OWNER TO yala;

--
-- Name: exams_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE exams_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.exams_acl OWNER TO yala;

--
-- Name: exams_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE exams_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.exams_id_seq OWNER TO yala;

--
-- Name: exams_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE exams_id_seq OWNED BY exams.id;


--
-- Name: exams_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('exams_id_seq', 2, true);


--
-- Name: exams_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW exams_view AS
    SELECT exams.id, exams.course_unit_id, exams.name, exams.type, exams.created_date FROM (exams JOIN exams_acl ON ((((((exams_acl.object_id = 0) OR (exams_acl.object_id = exams.id)) AND (exams_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND exams_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.exams_view OWNER TO yala;

--
-- Name: files; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE files (
    id integer NOT NULL,
    hash character varying(256) NOT NULL,
    size integer NOT NULL,
    created_date timestamp without time zone DEFAULT now() NOT NULL,
    downloads integer DEFAULT 0 NOT NULL,
    filename character varying(256) NOT NULL,
    user_id integer
);


ALTER TABLE public.files OWNER TO yala;

--
-- Name: files_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE files_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.files_acl OWNER TO yala;

--
-- Name: files_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE files_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.files_id_seq OWNER TO yala;

--
-- Name: files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE files_id_seq OWNED BY files.id;


--
-- Name: files_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('files_id_seq', 1, false);


--
-- Name: files_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW files_view AS
    SELECT files.id, files.hash, files.size, files.created_date, files.downloads, files.filename, files.user_id FROM (files JOIN files_acl ON ((((((files_acl.object_id = 0) OR (files_acl.object_id = files.id)) AND (files_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND files_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.files_view OWNER TO yala;

--
-- Name: google_tokens; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE google_tokens (
    id integer NOT NULL,
    user_id integer NOT NULL,
    scope character varying(256) NOT NULL,
    token character varying(256) NOT NULL
);


ALTER TABLE public.google_tokens OWNER TO yala;

--
-- Name: google_tokens_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE google_tokens_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.google_tokens_acl OWNER TO yala;

--
-- Name: google_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE google_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.google_tokens_id_seq OWNER TO yala;

--
-- Name: google_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE google_tokens_id_seq OWNED BY google_tokens.id;


--
-- Name: google_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('google_tokens_id_seq', 1, true);


--
-- Name: google_tokens_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW google_tokens_view AS
    SELECT google_tokens.id, google_tokens.user_id, google_tokens.scope, google_tokens.token FROM (google_tokens JOIN google_tokens_acl ON ((((((google_tokens_acl.object_id = 0) OR (google_tokens_acl.object_id = google_tokens.id)) AND (google_tokens_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND google_tokens_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.google_tokens_view OWNER TO yala;

--
-- Name: group_users; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE group_users (
    id integer NOT NULL,
    group_id integer,
    user_id integer,
    status smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.group_users OWNER TO yala;

--
-- Name: group_users_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE group_users_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.group_users_acl OWNER TO yala;

--
-- Name: group_users_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE group_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.group_users_id_seq OWNER TO yala;

--
-- Name: group_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE group_users_id_seq OWNED BY group_users.id;


--
-- Name: group_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('group_users_id_seq', 3, true);


--
-- Name: group_users_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW group_users_view AS
    SELECT group_users.id, group_users.group_id, group_users.user_id, group_users.status FROM (group_users JOIN group_users_acl ON ((((((group_users_acl.object_id = 0) OR (group_users_acl.object_id = group_users.id)) AND (group_users_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND group_users_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.group_users_view OWNER TO yala;

--
-- Name: groups; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE groups (
    id integer NOT NULL,
    name character varying(256),
    advance_level character varying(256)
);


ALTER TABLE public.groups OWNER TO yala;

--
-- Name: groups_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE groups_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.groups_acl OWNER TO yala;

--
-- Name: groups_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.groups_id_seq OWNER TO yala;

--
-- Name: groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE groups_id_seq OWNED BY groups.id;


--
-- Name: groups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('groups_id_seq', 3, true);


--
-- Name: groups_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW groups_view AS
    SELECT groups.id, groups.name, groups.advance_level FROM (groups JOIN groups_acl ON ((((((groups_acl.object_id = 0) OR (groups_acl.object_id = groups.id)) AND (groups_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND groups_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.groups_view OWNER TO yala;

--
-- Name: lesson_presence; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE lesson_presence (
    id integer NOT NULL,
    lesson_id integer NOT NULL,
    user_id integer NOT NULL
);


ALTER TABLE public.lesson_presence OWNER TO yala;

--
-- Name: lesson_presence_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE lesson_presence_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.lesson_presence_acl OWNER TO yala;

--
-- Name: lesson_presence_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE lesson_presence_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.lesson_presence_id_seq OWNER TO yala;

--
-- Name: lesson_presence_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE lesson_presence_id_seq OWNED BY lesson_presence.id;


--
-- Name: lesson_presence_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('lesson_presence_id_seq', 5, true);


--
-- Name: lesson_presence_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW lesson_presence_view AS
    SELECT lesson_presence.id, lesson_presence.lesson_id, lesson_presence.user_id FROM (lesson_presence JOIN lesson_presence_acl ON ((((((lesson_presence_acl.object_id = 0) OR (lesson_presence_acl.object_id = lesson_presence.id)) AND (lesson_presence_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND lesson_presence_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.lesson_presence_view OWNER TO yala;

--
-- Name: lessons; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE lessons (
    id integer NOT NULL,
    course_unit_id integer NOT NULL,
    room_id integer NOT NULL,
    user_id integer,
    start_date timestamp without time zone NOT NULL,
    end_date timestamp without time zone NOT NULL,
    cycle_id integer,
    sequence bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.lessons OWNER TO yala;

--
-- Name: lessons_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE lessons_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.lessons_acl OWNER TO yala;

--
-- Name: lessons_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE lessons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.lessons_id_seq OWNER TO yala;

--
-- Name: lessons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE lessons_id_seq OWNED BY lessons.id;


--
-- Name: lessons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('lessons_id_seq', 55, true);


--
-- Name: lessons_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW lessons_view AS
    SELECT lessons.id, lessons.course_unit_id, lessons.room_id, lessons.user_id, lessons.start_date, lessons.end_date, lessons.cycle_id, lessons.sequence FROM (lessons JOIN lessons_acl ON ((((((lessons_acl.object_id = 0) OR (lessons_acl.object_id = lessons.id)) AND (lessons_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND lessons_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.lessons_view OWNER TO yala;

--
-- Name: message_attachments; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE message_attachments (
    id integer NOT NULL,
    message_id integer NOT NULL,
    file_id integer NOT NULL
);


ALTER TABLE public.message_attachments OWNER TO yala;

--
-- Name: message_attachments_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE message_attachments_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.message_attachments_acl OWNER TO yala;

--
-- Name: message_attachments_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE message_attachments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.message_attachments_id_seq OWNER TO yala;

--
-- Name: message_attachments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE message_attachments_id_seq OWNED BY message_attachments.id;


--
-- Name: message_attachments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('message_attachments_id_seq', 1, false);


--
-- Name: message_attachments_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW message_attachments_view AS
    SELECT message_attachments.id, message_attachments.message_id, message_attachments.file_id FROM (message_attachments JOIN message_attachments_acl ON ((((((message_attachments_acl.object_id = 0) OR (message_attachments_acl.object_id = message_attachments.id)) AND (message_attachments_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND message_attachments_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.message_attachments_view OWNER TO yala;

--
-- Name: message_users; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE message_users (
    id integer NOT NULL,
    message_id integer NOT NULL,
    user_id integer NOT NULL,
    read_date timestamp without time zone,
    folder smallint DEFAULT 1 NOT NULL
);


ALTER TABLE public.message_users OWNER TO yala;

--
-- Name: message_users_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE message_users_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.message_users_acl OWNER TO yala;

--
-- Name: message_users_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE message_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.message_users_id_seq OWNER TO yala;

--
-- Name: message_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE message_users_id_seq OWNED BY message_users.id;


--
-- Name: message_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('message_users_id_seq', 6, true);


--
-- Name: message_users_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW message_users_view AS
    SELECT message_users.id, message_users.message_id, message_users.user_id, message_users.read_date, message_users.folder FROM (message_users JOIN message_users_acl ON ((((((message_users_acl.object_id = 0) OR (message_users_acl.object_id = message_users.id)) AND (message_users_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND message_users_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.message_users_view OWNER TO yala;

--
-- Name: messages; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE messages (
    id integer NOT NULL,
    subject character varying(256) NOT NULL,
    body text NOT NULL,
    send_date timestamp without time zone DEFAULT now() NOT NULL,
    sender_id integer NOT NULL,
    recipient_list text NOT NULL
);


ALTER TABLE public.messages OWNER TO yala;

--
-- Name: messages_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE messages_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.messages_acl OWNER TO yala;

--
-- Name: messages_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.messages_id_seq OWNER TO yala;

--
-- Name: messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE messages_id_seq OWNED BY messages.id;


--
-- Name: messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('messages_id_seq', 3, true);


--
-- Name: messages_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW messages_view AS
    SELECT messages.id, messages.subject, messages.body, messages.send_date, messages.sender_id, messages.recipient_list FROM (messages JOIN messages_acl ON ((((((messages_acl.object_id = 0) OR (messages_acl.object_id = messages.id)) AND (messages_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND messages_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.messages_view OWNER TO yala;

--
-- Name: poland; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE poland (
    id integer NOT NULL,
    parent_id integer,
    level smallint NOT NULL,
    name character varying(256) NOT NULL
);


ALTER TABLE public.poland OWNER TO yala;

--
-- Name: poland_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE poland_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.poland_acl OWNER TO yala;

--
-- Name: poland_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE poland_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.poland_id_seq OWNER TO yala;

--
-- Name: poland_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE poland_id_seq OWNED BY poland.id;


--
-- Name: poland_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('poland_id_seq', 3897, true);


--
-- Name: poland_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW poland_view AS
    SELECT poland.id, poland.parent_id, poland.level, poland.name FROM (poland JOIN poland_acl ON ((((((poland_acl.object_id = 0) OR (poland_acl.object_id = poland.id)) AND (poland_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND poland_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.poland_view OWNER TO yala;

--
-- Name: projects; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE projects (
    id integer NOT NULL,
    name character varying(256) NOT NULL,
    code character varying(256) NOT NULL,
    description text,
    created_date date DEFAULT now() NOT NULL,
    start_date date DEFAULT '1970-01-01'::date NOT NULL,
    end_date date DEFAULT '1970-01-01'::date NOT NULL,
    status smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.projects OWNER TO yala;

--
-- Name: projects_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE projects_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.projects_acl OWNER TO yala;

--
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE projects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.projects_id_seq OWNER TO yala;

--
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('projects_id_seq', 2, true);


--
-- Name: projects_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW projects_view AS
    SELECT projects.id, projects.name, projects.code, projects.description, projects.created_date, projects.start_date, projects.end_date, projects.status FROM (projects JOIN projects_acl ON ((((((projects_acl.object_id = 0) OR (projects_acl.object_id = projects.id)) AND (projects_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND projects_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.projects_view OWNER TO yala;

--
-- Name: quiz_scores; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE quiz_scores (
    id integer NOT NULL,
    user_id integer NOT NULL,
    quiz_id integer NOT NULL,
    level smallint NOT NULL,
    score bigint NOT NULL,
    start_time integer,
    total_time integer,
    status integer DEFAULT 0
);


ALTER TABLE public.quiz_scores OWNER TO yala;

--
-- Name: quiz_scores_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE quiz_scores_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.quiz_scores_acl OWNER TO yala;

--
-- Name: quiz_scores_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE quiz_scores_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.quiz_scores_id_seq OWNER TO yala;

--
-- Name: quiz_scores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE quiz_scores_id_seq OWNED BY quiz_scores.id;


--
-- Name: quiz_scores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('quiz_scores_id_seq', 2, true);


--
-- Name: quiz_scores_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW quiz_scores_view AS
    SELECT quiz_scores.id, quiz_scores.user_id, quiz_scores.quiz_id, quiz_scores.level, quiz_scores.score, quiz_scores.start_time, quiz_scores.total_time, quiz_scores.status FROM (quiz_scores JOIN quiz_scores_acl ON ((((((quiz_scores_acl.object_id = 0) OR (quiz_scores_acl.object_id = quiz_scores.id)) AND (quiz_scores_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND quiz_scores_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.quiz_scores_view OWNER TO yala;

--
-- Name: quiz_users; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE quiz_users (
    id integer NOT NULL,
    quiz_id integer NOT NULL,
    user_id integer NOT NULL
);


ALTER TABLE public.quiz_users OWNER TO yala;

--
-- Name: quiz_users_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE quiz_users_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.quiz_users_acl OWNER TO yala;

--
-- Name: quiz_users_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE quiz_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.quiz_users_id_seq OWNER TO yala;

--
-- Name: quiz_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE quiz_users_id_seq OWNED BY quiz_users.id;


--
-- Name: quiz_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('quiz_users_id_seq', 1, true);


--
-- Name: quiz_users_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW quiz_users_view AS
    SELECT quiz_users.id, quiz_users.quiz_id, quiz_users.user_id FROM (quiz_users JOIN quiz_users_acl ON ((((((quiz_users_acl.object_id = 0) OR (quiz_users_acl.object_id = quiz_users.id)) AND (quiz_users_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND quiz_users_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.quiz_users_view OWNER TO yala;

--
-- Name: quizzes; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE quizzes (
    id integer NOT NULL,
    name character varying(256) NOT NULL,
    description text,
    time_limit integer NOT NULL,
    url character varying(256) NOT NULL
);


ALTER TABLE public.quizzes OWNER TO yala;

--
-- Name: quizzes_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE quizzes_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.quizzes_acl OWNER TO yala;

--
-- Name: quizzes_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE quizzes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.quizzes_id_seq OWNER TO yala;

--
-- Name: quizzes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE quizzes_id_seq OWNED BY quizzes.id;


--
-- Name: quizzes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('quizzes_id_seq', 1, true);


--
-- Name: quizzes_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW quizzes_view AS
    SELECT quizzes.id, quizzes.name, quizzes.description, quizzes.time_limit, quizzes.url FROM (quizzes JOIN quizzes_acl ON ((((((quizzes_acl.object_id = 0) OR (quizzes_acl.object_id = quizzes.id)) AND (quizzes_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND quizzes_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.quizzes_view OWNER TO yala;

--
-- Name: reports; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE reports (
    id integer NOT NULL,
    parent_id integer,
    name character varying(256) NOT NULL,
    description character varying(256),
    path character varying(256),
    project_id integer
);


ALTER TABLE public.reports OWNER TO yala;

--
-- Name: reports_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE reports_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.reports_acl OWNER TO yala;

--
-- Name: reports_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE reports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.reports_id_seq OWNER TO yala;

--
-- Name: reports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE reports_id_seq OWNED BY reports.id;


--
-- Name: reports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('reports_id_seq', 10, true);


--
-- Name: reports_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW reports_view AS
    SELECT reports.id, reports.parent_id, reports.name, reports.description, reports.path, reports.project_id FROM (reports JOIN reports_acl ON ((((((reports_acl.object_id = 0) OR (reports_acl.object_id = reports.id)) AND (reports_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND reports_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.reports_view OWNER TO yala;

--
-- Name: resource_types; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE resource_types (
    id integer NOT NULL,
    name character varying(256)
);


ALTER TABLE public.resource_types OWNER TO yala;

--
-- Name: resource_types_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE resource_types_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.resource_types_acl OWNER TO yala;

--
-- Name: resource_types_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE resource_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.resource_types_id_seq OWNER TO yala;

--
-- Name: resource_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE resource_types_id_seq OWNED BY resource_types.id;


--
-- Name: resource_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('resource_types_id_seq', 1, false);


--
-- Name: resource_types_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW resource_types_view AS
    SELECT resource_types.id, resource_types.name FROM (resource_types JOIN resource_types_acl ON ((((((resource_types_acl.object_id = 0) OR (resource_types_acl.object_id = resource_types.id)) AND (resource_types_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND resource_types_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.resource_types_view OWNER TO yala;

--
-- Name: resources; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE resources (
    id integer NOT NULL,
    training_center_id integer,
    resource_type_id integer,
    amount integer
);


ALTER TABLE public.resources OWNER TO yala;

--
-- Name: resources_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE resources_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.resources_acl OWNER TO yala;

--
-- Name: resources_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE resources_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.resources_id_seq OWNER TO yala;

--
-- Name: resources_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE resources_id_seq OWNED BY resources.id;


--
-- Name: resources_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('resources_id_seq', 1, false);


--
-- Name: resources_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW resources_view AS
    SELECT resources.id, resources.training_center_id, resources.resource_type_id, resources.amount FROM (resources JOIN resources_acl ON ((((((resources_acl.object_id = 0) OR (resources_acl.object_id = resources.id)) AND (resources_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND resources_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.resources_view OWNER TO yala;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE roles (
    id integer NOT NULL,
    name character varying(256) NOT NULL
);


ALTER TABLE public.roles OWNER TO yala;

--
-- Name: roles_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE roles_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.roles_acl OWNER TO yala;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO yala;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE roles_id_seq OWNED BY roles.id;


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('roles_id_seq', 1, false);


--
-- Name: roles_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW roles_view AS
    SELECT roles.id, roles.name FROM (roles JOIN roles_acl ON ((((((roles_acl.object_id = 0) OR (roles_acl.object_id = roles.id)) AND (roles_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND roles_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.roles_view OWNER TO yala;

--
-- Name: rooms; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE rooms (
    id integer NOT NULL,
    training_center_id integer NOT NULL,
    name character varying(256) NOT NULL,
    symbol character varying(256) NOT NULL,
    description character varying(256),
    available_space integer NOT NULL
);


ALTER TABLE public.rooms OWNER TO yala;

--
-- Name: rooms_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE rooms_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.rooms_acl OWNER TO yala;

--
-- Name: rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE rooms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.rooms_id_seq OWNER TO yala;

--
-- Name: rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE rooms_id_seq OWNED BY rooms.id;


--
-- Name: rooms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('rooms_id_seq', 4, true);


--
-- Name: rooms_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW rooms_view AS
    SELECT rooms.id, rooms.training_center_id, rooms.name, rooms.symbol, rooms.description, rooms.available_space FROM (rooms JOIN rooms_acl ON ((((((rooms_acl.object_id = 0) OR (rooms_acl.object_id = rooms.id)) AND (rooms_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND rooms_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.rooms_view OWNER TO yala;

--
-- Name: survey_detailed_results; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_detailed_results (
    id integer NOT NULL,
    survey_result_id integer,
    question_id integer,
    answer_id integer,
    answer_content text
);


ALTER TABLE public.survey_detailed_results OWNER TO yala;

--
-- Name: survey_detailed_results_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_detailed_results_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.survey_detailed_results_acl OWNER TO yala;

--
-- Name: survey_detailed_results_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE survey_detailed_results_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.survey_detailed_results_id_seq OWNER TO yala;

--
-- Name: survey_detailed_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE survey_detailed_results_id_seq OWNED BY survey_detailed_results.id;


--
-- Name: survey_detailed_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('survey_detailed_results_id_seq', 1, true);


--
-- Name: survey_detailed_results_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW survey_detailed_results_view AS
    SELECT survey_detailed_results.id, survey_detailed_results.survey_result_id, survey_detailed_results.question_id, survey_detailed_results.answer_id, survey_detailed_results.answer_content FROM (survey_detailed_results JOIN survey_detailed_results_acl ON ((((((survey_detailed_results_acl.object_id = 0) OR (survey_detailed_results_acl.object_id = survey_detailed_results.id)) AND (survey_detailed_results_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND survey_detailed_results_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.survey_detailed_results_view OWNER TO yala;

--
-- Name: survey_possible_answers; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_possible_answers (
    id integer NOT NULL,
    question_id integer,
    content character varying(256),
    correct smallint,
    selected_by_default smallint
);


ALTER TABLE public.survey_possible_answers OWNER TO yala;

--
-- Name: survey_possible_answers_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_possible_answers_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.survey_possible_answers_acl OWNER TO yala;

--
-- Name: survey_possible_answers_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE survey_possible_answers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.survey_possible_answers_id_seq OWNER TO yala;

--
-- Name: survey_possible_answers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE survey_possible_answers_id_seq OWNED BY survey_possible_answers.id;


--
-- Name: survey_possible_answers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('survey_possible_answers_id_seq', 5, true);


--
-- Name: survey_possible_answers_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW survey_possible_answers_view AS
    SELECT survey_possible_answers.id, survey_possible_answers.question_id, survey_possible_answers.content, survey_possible_answers.correct, survey_possible_answers.selected_by_default FROM (survey_possible_answers JOIN survey_possible_answers_acl ON ((((((survey_possible_answers_acl.object_id = 0) OR (survey_possible_answers_acl.object_id = survey_possible_answers.id)) AND (survey_possible_answers_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND survey_possible_answers_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.survey_possible_answers_view OWNER TO yala;

--
-- Name: survey_questions; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_questions (
    id integer NOT NULL,
    survey_id integer,
    type character varying(256),
    title character varying(256),
    help character varying(256),
    required smallint,
    "position" integer DEFAULT 0
);


ALTER TABLE public.survey_questions OWNER TO yala;

--
-- Name: survey_questions_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_questions_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.survey_questions_acl OWNER TO yala;

--
-- Name: survey_questions_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE survey_questions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.survey_questions_id_seq OWNER TO yala;

--
-- Name: survey_questions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE survey_questions_id_seq OWNED BY survey_questions.id;


--
-- Name: survey_questions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('survey_questions_id_seq', 1, true);


--
-- Name: survey_questions_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW survey_questions_view AS
    SELECT survey_questions.id, survey_questions.survey_id, survey_questions.type, survey_questions.title, survey_questions.help, survey_questions.required, survey_questions."position" FROM (survey_questions JOIN survey_questions_acl ON ((((((survey_questions_acl.object_id = 0) OR (survey_questions_acl.object_id = survey_questions.id)) AND (survey_questions_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND survey_questions_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.survey_questions_view OWNER TO yala;

--
-- Name: survey_results; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_results (
    id integer NOT NULL,
    user_id integer,
    survey_id integer,
    percent_result double precision,
    completed smallint,
    created timestamp without time zone
);


ALTER TABLE public.survey_results OWNER TO yala;

--
-- Name: survey_results_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_results_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.survey_results_acl OWNER TO yala;

--
-- Name: survey_results_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE survey_results_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.survey_results_id_seq OWNER TO yala;

--
-- Name: survey_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE survey_results_id_seq OWNED BY survey_results.id;


--
-- Name: survey_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('survey_results_id_seq', 1, true);


--
-- Name: survey_results_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW survey_results_view AS
    SELECT survey_results.id, survey_results.user_id, survey_results.survey_id, survey_results.percent_result, survey_results.completed, survey_results.created FROM (survey_results JOIN survey_results_acl ON ((((((survey_results_acl.object_id = 0) OR (survey_results_acl.object_id = survey_results.id)) AND (survey_results_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND survey_results_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.survey_results_view OWNER TO yala;

--
-- Name: survey_users; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_users (
    id integer NOT NULL,
    survey_id integer,
    user_id integer,
    filled smallint,
    deadline date,
    sent timestamp without time zone
);


ALTER TABLE public.survey_users OWNER TO yala;

--
-- Name: survey_users_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE survey_users_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.survey_users_acl OWNER TO yala;

--
-- Name: survey_users_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE survey_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.survey_users_id_seq OWNER TO yala;

--
-- Name: survey_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE survey_users_id_seq OWNED BY survey_users.id;


--
-- Name: survey_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('survey_users_id_seq', 1, true);


--
-- Name: survey_users_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW survey_users_view AS
    SELECT survey_users.id, survey_users.survey_id, survey_users.user_id, survey_users.filled, survey_users.deadline, survey_users.sent FROM (survey_users JOIN survey_users_acl ON ((((((survey_users_acl.object_id = 0) OR (survey_users_acl.object_id = survey_users.id)) AND (survey_users_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND survey_users_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.survey_users_view OWNER TO yala;

--
-- Name: surveys; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE surveys (
    id integer NOT NULL,
    user_id integer,
    name character varying(256) NOT NULL,
    description text,
    type character varying(256),
    archived smallint,
    project_id integer,
    created_date timestamp without time zone DEFAULT now(),
    library smallint,
    completed timestamp without time zone
);


ALTER TABLE public.surveys OWNER TO yala;

--
-- Name: surveys_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE surveys_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.surveys_acl OWNER TO yala;

--
-- Name: surveys_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE surveys_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.surveys_id_seq OWNER TO yala;

--
-- Name: surveys_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE surveys_id_seq OWNED BY surveys.id;


--
-- Name: surveys_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('surveys_id_seq', 1, true);


--
-- Name: surveys_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW surveys_view AS
    SELECT surveys.id, surveys.user_id, surveys.name, surveys.description, surveys.type, surveys.archived, surveys.project_id, surveys.created_date, surveys.library, surveys.completed FROM (surveys JOIN surveys_acl ON ((((((surveys_acl.object_id = 0) OR (surveys_acl.object_id = surveys.id)) AND (surveys_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND surveys_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.surveys_view OWNER TO yala;

--
-- Name: training_centers; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE training_centers (
    id integer NOT NULL,
    name character varying(256) NOT NULL,
    street character varying(256) NOT NULL,
    zip_code character varying(256) NOT NULL,
    city character varying(256) NOT NULL,
    manager character varying(256),
    url character varying(256),
    rating integer DEFAULT 5 NOT NULL,
    room_amount integer DEFAULT 0,
    seats_amount integer DEFAULT 0,
    code character varying(256) DEFAULT 'DTC'::character varying NOT NULL,
    description text,
    phone_number character varying(256)
);


ALTER TABLE public.training_centers OWNER TO yala;

--
-- Name: training_centers_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE training_centers_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.training_centers_acl OWNER TO yala;

--
-- Name: training_centers_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE training_centers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.training_centers_id_seq OWNER TO yala;

--
-- Name: training_centers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE training_centers_id_seq OWNED BY training_centers.id;


--
-- Name: training_centers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('training_centers_id_seq', 4, true);


--
-- Name: training_centers_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW training_centers_view AS
    SELECT training_centers.id, training_centers.name, training_centers.street, training_centers.zip_code, training_centers.city, training_centers.manager, training_centers.url, training_centers.rating, training_centers.room_amount, training_centers.seats_amount, training_centers.code, training_centers.description, training_centers.phone_number FROM (training_centers JOIN training_centers_acl ON ((((((training_centers_acl.object_id = 0) OR (training_centers_acl.object_id = training_centers.id)) AND (training_centers_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND training_centers_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.training_centers_view OWNER TO yala;

--
-- Name: user_profile; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE user_profile (
    id integer NOT NULL,
    user_id integer NOT NULL,
    sex character varying(1),
    national_identity character varying(256),
    address_city character varying(256),
    address_zip_code character varying(256),
    address_street character varying(256),
    poland_id integer,
    phone_number character varying(256),
    fax_number character varying(256),
    mobile_number character varying(256),
    birth_date date,
    birth_place character varying(256),
    work_name character varying(256),
    work_city character varying(256),
    work_zip_code character varying(256),
    work_street character varying(256),
    work_tax_identification_number character varying(256),
    tax_identification_number character varying(256),
    tax_office character varying(256),
    tax_office_address character varying(256),
    identification_name character varying(256),
    identification_number character varying(256),
    identification_publisher character varying(256),
    father_name character varying(256),
    mother_name character varying(256),
    nfz character varying(256),
    bank character varying(256),
    printed smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.user_profile OWNER TO yala;

--
-- Name: user_profile_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE user_profile_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.user_profile_acl OWNER TO yala;

--
-- Name: user_profile_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE user_profile_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.user_profile_id_seq OWNER TO yala;

--
-- Name: user_profile_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE user_profile_id_seq OWNED BY user_profile.id;


--
-- Name: user_profile_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('user_profile_id_seq', 2, true);


--
-- Name: user_profile_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW user_profile_view AS
    SELECT user_profile.id, user_profile.user_id, user_profile.sex, user_profile.national_identity, user_profile.address_city, user_profile.address_zip_code, user_profile.address_street, user_profile.poland_id, user_profile.phone_number, user_profile.fax_number, user_profile.mobile_number, user_profile.birth_date, user_profile.birth_place, user_profile.work_name, user_profile.work_city, user_profile.work_zip_code, user_profile.work_street, user_profile.work_tax_identification_number, user_profile.tax_identification_number, user_profile.tax_office, user_profile.tax_office_address, user_profile.identification_name, user_profile.identification_number, user_profile.identification_publisher, user_profile.father_name, user_profile.mother_name, user_profile.nfz, user_profile.bank, user_profile.printed FROM (user_profile JOIN user_profile_acl ON ((((((user_profile_acl.object_id = 0) OR (user_profile_acl.object_id = user_profile.id)) AND (user_profile_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND user_profile_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.user_profile_view OWNER TO yala;

--
-- Name: users; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    username character varying(256) NOT NULL,
    first_name character varying(256) NOT NULL,
    last_name character varying(256) NOT NULL,
    plain_password character varying(256),
    role_id integer,
    email character varying(256),
    key character varying(256),
    is_google smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.users OWNER TO yala;

--
-- Name: users_acl; Type: TABLE; Schema: public; Owner: yala; Tablespace: 
--

CREATE TABLE users_acl (
    object_id integer DEFAULT 0,
    username name DEFAULT "current_user"(),
    _select boolean DEFAULT true,
    _update boolean DEFAULT true,
    _insert boolean DEFAULT true,
    _delete boolean DEFAULT true
);


ALTER TABLE public.users_acl OWNER TO yala;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: yala
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO yala;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yala
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yala
--

SELECT pg_catalog.setval('users_id_seq', 16, true);


--
-- Name: users_view; Type: VIEW; Schema: public; Owner: yala
--

CREATE VIEW users_view AS
    SELECT users.id, users.username, users.first_name, users.last_name, users.plain_password, users.role_id, users.email, users.key, users.is_google FROM (users JOIN users_acl ON ((((((users_acl.object_id = 0) OR (users_acl.object_id = users.id)) AND (users_acl.username = ANY (ARRAY["current_user"(), '*'::name]))) AND users_acl._select) OR (SELECT pg_user.usesuper FROM pg_user WHERE (pg_user.usename = "current_user"())))));


ALTER TABLE public.users_view OWNER TO yala;

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE acl ALTER COLUMN id SET DEFAULT nextval('acl_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE apps ALTER COLUMN id SET DEFAULT nextval('apps_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE course_schedule ALTER COLUMN id SET DEFAULT nextval('course_schedule_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE course_units ALTER COLUMN id SET DEFAULT nextval('course_units_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE courses ALTER COLUMN id SET DEFAULT nextval('courses_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE exam_grades ALTER COLUMN id SET DEFAULT nextval('exam_grades_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE exams ALTER COLUMN id SET DEFAULT nextval('exams_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE files ALTER COLUMN id SET DEFAULT nextval('files_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE google_tokens ALTER COLUMN id SET DEFAULT nextval('google_tokens_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE group_users ALTER COLUMN id SET DEFAULT nextval('group_users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE groups ALTER COLUMN id SET DEFAULT nextval('groups_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE lesson_presence ALTER COLUMN id SET DEFAULT nextval('lesson_presence_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE lessons ALTER COLUMN id SET DEFAULT nextval('lessons_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE message_attachments ALTER COLUMN id SET DEFAULT nextval('message_attachments_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE message_users ALTER COLUMN id SET DEFAULT nextval('message_users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE messages ALTER COLUMN id SET DEFAULT nextval('messages_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE poland ALTER COLUMN id SET DEFAULT nextval('poland_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE quiz_scores ALTER COLUMN id SET DEFAULT nextval('quiz_scores_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE quiz_users ALTER COLUMN id SET DEFAULT nextval('quiz_users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE quizzes ALTER COLUMN id SET DEFAULT nextval('quizzes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE reports ALTER COLUMN id SET DEFAULT nextval('reports_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE resource_types ALTER COLUMN id SET DEFAULT nextval('resource_types_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE resources ALTER COLUMN id SET DEFAULT nextval('resources_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE roles ALTER COLUMN id SET DEFAULT nextval('roles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE rooms ALTER COLUMN id SET DEFAULT nextval('rooms_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE survey_detailed_results ALTER COLUMN id SET DEFAULT nextval('survey_detailed_results_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE survey_possible_answers ALTER COLUMN id SET DEFAULT nextval('survey_possible_answers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE survey_questions ALTER COLUMN id SET DEFAULT nextval('survey_questions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE survey_results ALTER COLUMN id SET DEFAULT nextval('survey_results_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE survey_users ALTER COLUMN id SET DEFAULT nextval('survey_users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE surveys ALTER COLUMN id SET DEFAULT nextval('surveys_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE training_centers ALTER COLUMN id SET DEFAULT nextval('training_centers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE user_profile ALTER COLUMN id SET DEFAULT nextval('user_profile_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: yala
--

ALTER TABLE users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Data for Name: acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY acl (id, table_name, username, _select, _update, _insert, _delete, updated, object_id) FROM stdin;
3241	lessons	trener	t	f	f	f	2011-12-08 15:36:29.442728	32
1574	groups	simpli0	t	f	f	f	2011-12-01 16:04:04.925636	3
1588	exam_grades	simpli0	t	f	f	f	2011-12-01 16:05:15.54684	1
1591	course_schedule	simpli0	t	f	f	f	2011-12-01 16:05:53.162466	1
1563	lessons	trener	t	f	f	f	2011-12-01 16:01:31.911659	20
1575	courses	simpli0	t	f	f	f	2011-12-01 16:04:20.654517	3
1576	course_units	simpli0	t	f	f	f	2011-12-01 16:04:20.657523	3
1577	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.66015	20
1578	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.663303	21
1579	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.665291	22
1580	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.667264	23
3242	lessons	trener	t	f	f	f	2011-12-08 15:36:31.966964	33
1581	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.669248	24
1582	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.671758	25
1583	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.673768	26
1584	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.676579	27
1585	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.6786	28
1586	lessons	simpli0	t	f	f	f	2011-12-01 16:04:20.680581	29
1589	lesson_presence	simpli0	t	f	f	f	2011-12-01 16:05:30.976908	4
5311	lessons	yala	t	t	t	t	2012-01-16 11:10:22.322695	0
5312	lessons	admin	t	t	t	t	2012-01-16 11:10:22.331536	0
5313	lessons	googlecalendar	t	t	t	t	2012-01-16 11:10:22.333323	0
5314	courses	yala	t	t	t	t	2012-01-16 11:10:22.336305	0
5315	courses	admin	t	t	t	t	2012-01-16 11:10:22.338078	0
5316	courses	googlecalendar	t	t	t	t	2012-01-16 11:10:22.339899	0
5317	groups	yala	t	t	t	t	2012-01-16 11:10:22.342829	0
5318	groups	admin	t	t	t	t	2012-01-16 11:10:22.344497	0
5319	groups	googlecalendar	t	t	t	t	2012-01-16 11:10:22.346224	0
5320	group_users	yala	t	t	t	t	2012-01-16 11:10:22.349297	0
5321	group_users	admin	t	t	t	t	2012-01-16 11:10:22.351133	0
5322	group_users	googlecalendar	t	t	t	t	2012-01-16 11:10:22.352941	0
5323	users	yala	t	t	t	t	2012-01-16 11:10:22.355998	0
5324	users	admin	t	t	t	t	2012-01-16 11:10:22.357754	0
5325	users	googlecalendar	t	t	t	t	2012-01-16 11:10:22.360224	0
5326	course_units	yala	t	t	t	t	2012-01-16 11:10:22.363468	0
5327	course_units	admin	t	t	t	t	2012-01-16 11:10:22.365603	0
5328	course_units	googlecalendar	t	t	t	t	2012-01-16 11:10:22.367362	0
5329	projects	yala	t	t	t	t	2012-01-16 11:10:22.370263	0
260	quizzes	adanow0	t	f	f	f	2011-11-25 13:54:51.605413	1
261	messages	adanow0	t	f	f	f	2011-11-25 13:55:21.394123	1
262	message_users	adanow0	t	t	f	t	2011-11-25 13:55:21.396943	1
265	quiz_scores	adanow0	t	f	f	f	2011-11-25 13:58:43.934327	1
282	surveys	trener	t	f	f	f	2011-11-25 14:02:31.432421	1
283	survey_questions	trener	t	f	f	f	2011-11-25 14:02:31.439053	1
284	survey_possible_answers	trener	t	f	f	f	2011-11-25 14:02:31.446647	1
285	survey_possible_answers	trener	t	f	f	f	2011-11-25 14:02:31.449185	2
286	survey_possible_answers	trener	t	f	f	f	2011-11-25 14:02:31.451546	3
287	survey_possible_answers	trener	t	f	f	f	2011-11-25 14:02:31.454044	4
288	survey_possible_answers	trener	t	f	f	f	2011-11-25 14:02:31.456505	5
3362	lessons	trener	t	f	f	f	2011-12-09 10:26:02.665606	34
4058	messages	robert_posiadala_gammanet_pl	t	t	t	t	2011-12-14 14:10:27.000156	2
4059	message_users	robert_posiadala_gammanet_pl	t	t	t	t	2011-12-14 14:10:27.019524	3
4060	message_users	robert_posiadala_gammanet_pl	t	t	t	t	2011-12-14 14:10:27.025519	4
5330	projects	admin	t	t	t	t	2012-01-16 11:10:22.371973	0
297	survey_results	adanow0	t	t	t	t	2011-11-25 14:10:07.728296	1
298	survey_users	adanow0	t	t	f	f	2011-11-25 14:10:07.758718	1
299	surveys	adanow0	t	f	f	f	2011-11-25 14:10:07.763704	1
300	survey_questions	adanow0	t	f	f	f	2011-11-25 14:10:07.771349	1
301	survey_possible_answers	adanow0	t	f	f	f	2011-11-25 14:10:07.779467	1
302	survey_possible_answers	adanow0	t	f	f	f	2011-11-25 14:10:07.783493	2
303	survey_possible_answers	adanow0	t	f	f	f	2011-11-25 14:10:07.786866	3
304	survey_possible_answers	adanow0	t	f	f	f	2011-11-25 14:10:07.791229	4
305	survey_possible_answers	adanow0	t	f	f	f	2011-11-25 14:10:07.794624	5
306	survey_detailed_results	adanow0	t	t	t	t	2011-11-25 14:10:07.807202	1
5331	projects	googlecalendar	t	t	t	t	2012-01-16 11:10:22.373816	0
5332	training_centers	yala	t	t	t	t	2012-01-16 11:10:22.376625	0
5333	training_centers	admin	t	t	t	t	2012-01-16 11:10:22.378389	0
5334	training_centers	googlecalendar	t	t	t	t	2012-01-16 11:10:22.380114	0
3382	lessons	trener	t	f	f	f	2011-12-09 12:15:06.27938	38
5335	survey_detailed_results	yala	t	t	t	t	2012-01-16 11:10:22.383472	0
5336	survey_detailed_results	admin	t	t	t	t	2012-01-16 11:10:22.385217	0
5337	survey_detailed_results	googlecalendar	t	t	t	t	2012-01-16 11:10:22.387063	0
3363	lessons	trener	t	f	f	f	2011-12-09 10:26:05.046773	35
4061	messages	robert_posiadala_gammanet_pl	t	t	t	t	2011-12-14 14:11:02.254924	3
4062	message_users	robert_posiadala_gammanet_pl	t	t	t	t	2011-12-14 14:11:02.264191	5
3384	lessons	trener	t	f	f	f	2011-12-09 12:15:31.209492	39
3385	lessons	trener	t	f	f	f	2011-12-09 12:15:31.211642	40
3386	lessons	trener	t	f	f	f	2011-12-09 12:15:31.21378	41
3387	lessons	trener	t	f	f	f	2011-12-09 12:15:31.21634	42
3388	lessons	trener	t	f	f	f	2011-12-09 12:15:31.218515	43
3389	lessons	trener	t	f	f	f	2011-12-09 12:15:31.220757	44
3390	lessons	trener	t	f	f	f	2011-12-09 12:15:31.223218	45
3391	lessons	trener	t	f	f	f	2011-12-09 12:15:31.225874	46
3392	lessons	trener	t	f	f	f	2011-12-09 12:15:31.228005	47
3393	lessons	trener	t	f	f	f	2011-12-09 12:15:31.230158	48
3394	lessons	trener	t	f	f	f	2011-12-09 12:15:31.232951	49
3395	lessons	trener	t	f	f	f	2011-12-09 12:15:31.235094	50
3396	lessons	trener	t	f	f	f	2011-12-09 12:15:31.237607	51
3397	lessons	trener	t	f	f	f	2011-12-09 12:15:31.240418	52
3398	lessons	trener	t	f	f	f	2011-12-09 12:15:31.242582	53
3399	lessons	trener	t	f	f	f	2011-12-09 12:15:31.244717	54
3400	lessons	trener	t	f	f	f	2011-12-09 12:15:31.246864	55
4063	message_users	robert_posiadala_gammanet_pl	t	t	t	t	2011-12-14 14:11:02.269514	6
5338	survey_possible_answers	yala	t	t	t	t	2012-01-16 11:10:22.390059	0
5339	survey_possible_answers	admin	t	t	t	t	2012-01-16 11:10:22.391878	0
5340	survey_possible_answers	googlecalendar	t	t	t	t	2012-01-16 11:10:22.393554	0
5341	survey_questions	yala	t	t	t	t	2012-01-16 11:10:22.396646	0
5342	survey_questions	admin	t	t	t	t	2012-01-16 11:10:22.398858	0
5343	survey_questions	googlecalendar	t	t	t	t	2012-01-16 11:10:22.4006	0
5344	survey_results	yala	t	t	t	t	2012-01-16 11:10:22.403584	0
5345	survey_results	admin	t	t	t	t	2012-01-16 11:10:22.405322	0
5346	survey_results	googlecalendar	t	t	t	t	2012-01-16 11:10:22.40729	0
5347	survey_users	yala	t	t	t	t	2012-01-16 11:10:22.410435	0
5348	survey_users	admin	t	t	t	t	2012-01-16 11:10:22.412119	0
5349	survey_users	googlecalendar	t	t	t	t	2012-01-16 11:10:22.413839	0
5350	surveys	yala	t	t	t	t	2012-01-16 11:10:22.416869	0
5351	surveys	admin	t	t	t	t	2012-01-16 11:10:22.418638	0
5352	surveys	googlecalendar	t	t	t	t	2012-01-16 11:10:22.420388	0
5353	quizzes	yala	t	t	t	t	2012-01-16 11:10:22.423585	0
5354	quizzes	admin	t	t	t	t	2012-01-16 11:10:22.425363	0
5355	quizzes	googlecalendar	t	t	t	t	2012-01-16 11:10:22.42717	0
5356	quiz_users	yala	t	t	t	t	2012-01-16 11:10:22.430215	0
5357	quiz_users	admin	t	t	t	t	2012-01-16 11:10:22.432239	0
5358	quiz_users	googlecalendar	t	t	t	t	2012-01-16 11:10:22.434076	0
5359	quiz_scores	yala	t	t	t	t	2012-01-16 11:10:22.437188	0
5360	quiz_scores	admin	t	t	t	t	2012-01-16 11:10:22.438928	0
5361	quiz_scores	googlecalendar	t	t	t	t	2012-01-16 11:10:22.440585	0
5362	reports	yala	t	t	t	t	2012-01-16 11:10:22.443571	0
5363	reports	admin	t	t	t	t	2012-01-16 11:10:22.445239	0
5364	reports	googlecalendar	t	t	t	t	2012-01-16 11:10:22.446981	0
5365	user_profile	yala	t	t	t	t	2012-01-16 11:10:22.451247	0
5366	user_profile	admin	t	t	t	t	2012-01-16 11:10:22.453129	0
5367	user_profile	googlecalendar	t	t	t	t	2012-01-16 11:10:22.454973	0
5368	messages	yala	t	t	t	t	2012-01-16 11:10:22.458536	0
3381	lessons	trener	t	f	f	f	2011-12-09 12:14:57.535592	37
5369	messages	admin	t	t	t	t	2012-01-16 11:10:22.460258	0
5370	messages	googlecalendar	t	t	t	t	2012-01-16 11:10:22.462022	0
1565	lessons	trener	t	f	f	f	2011-12-01 16:02:24.294222	21
1566	lessons	trener	t	f	f	f	2011-12-01 16:02:24.296436	22
1567	lessons	trener	t	f	f	f	2011-12-01 16:02:24.298635	23
1568	lessons	trener	t	f	f	f	2011-12-01 16:02:24.300806	24
1569	lessons	trener	t	f	f	f	2011-12-01 16:02:24.303189	25
1570	lessons	trener	t	f	f	f	2011-12-01 16:02:24.305695	26
1571	lessons	trener	t	f	f	f	2011-12-01 16:02:24.308627	27
1572	lessons	trener	t	f	f	f	2011-12-01 16:02:24.310924	28
1573	lessons	trener	t	f	f	f	2011-12-01 16:02:24.313081	29
1587	exams	simpli0	t	f	f	f	2011-12-01 16:05:05.530187	1
1590	lesson_presence	simpli0	t	f	f	f	2011-12-01 16:05:32.565252	5
5371	message_users	yala	t	t	t	t	2012-01-16 11:10:22.465139	0
5372	message_users	admin	t	t	t	t	2012-01-16 11:10:22.46692	0
5373	message_users	googlecalendar	t	t	t	t	2012-01-16 11:10:22.468646	0
5374	message_attachments	yala	t	t	t	t	2012-01-16 11:10:22.471677	0
5375	message_attachments	admin	t	t	t	t	2012-01-16 11:10:22.473926	0
5376	message_attachments	googlecalendar	t	t	t	t	2012-01-16 11:10:22.475824	0
5377	lesson_presence	yala	t	t	t	t	2012-01-16 11:10:22.478928	0
5378	lesson_presence	admin	t	t	t	t	2012-01-16 11:10:22.480799	0
5379	lesson_presence	googlecalendar	t	t	t	t	2012-01-16 11:10:22.482569	0
5380	exams	yala	t	t	t	t	2012-01-16 11:10:22.485645	0
5381	exams	admin	t	t	t	t	2012-01-16 11:10:22.487368	0
5382	exams	googlecalendar	t	t	t	t	2012-01-16 11:10:22.48916	0
5383	exam_grades	yala	t	t	t	t	2012-01-16 11:10:22.492244	0
5384	exam_grades	admin	t	t	t	t	2012-01-16 11:10:22.49396	0
5385	exam_grades	googlecalendar	t	t	t	t	2012-01-16 11:10:22.495785	0
5386	course_schedule	yala	t	t	t	t	2012-01-16 11:10:22.498859	0
5387	course_schedule	admin	t	t	t	t	2012-01-16 11:10:22.500647	0
5388	course_schedule	googlecalendar	t	t	t	t	2012-01-16 11:10:22.502466	0
5389	users	trener	t	f	f	f	2012-01-16 11:10:22.505914	0
5390	courses	trener	t	f	f	f	2012-01-16 11:10:22.508685	0
5391	course_units	trener	t	f	f	f	2012-01-16 11:10:22.511327	0
5392	groups	trener	t	f	f	f	2012-01-16 11:10:22.513981	0
5393	quizzes	trener	t	f	f	f	2012-01-16 11:10:22.516567	0
5394	quiz_users	trener	t	f	t	f	2012-01-16 11:10:22.519553	0
5395	quiz_scores	trener	t	f	f	f	2012-01-16 11:10:22.522264	0
5396	user_profile	trener	f	f	t	f	2012-01-16 11:10:22.525313	0
5397	lesson_presence	trener	f	f	t	f	2012-01-16 11:10:22.527969	0
5398	exams	trener	f	f	t	f	2012-01-16 11:10:22.530806	0
5399	exam_grades	trener	f	f	t	f	2012-01-16 11:10:22.533454	0
5400	course_schedule	trener	f	f	t	f	2012-01-16 11:10:22.536107	0
5401	messages	trener	f	f	t	f	2012-01-16 11:10:22.538898	0
5402	message_users	trener	f	f	t	f	2012-01-16 11:10:22.541597	0
5403	message_attachments	trener	f	f	t	f	2012-01-16 11:10:22.544345	0
5404	survey_detailed_results	trener	f	f	t	f	2012-01-16 11:10:22.547025	0
5405	survey_possible_answers	trener	f	f	t	f	2012-01-16 11:10:22.549938	0
5406	survey_questions	trener	f	f	t	f	2012-01-16 11:10:22.552931	0
5407	survey_results	trener	f	f	t	f	2012-01-16 11:10:22.555629	0
5408	survey_users	trener	f	f	t	f	2012-01-16 11:10:22.558399	0
5409	surveys	trener	f	f	t	f	2012-01-16 11:10:22.561177	0
5410	users	adanow0	t	f	f	f	2012-01-16 11:10:22.566213	0
5411	users	simpli0	t	f	f	f	2012-01-16 11:10:22.568702	0
5412	users	robert_posiadala_gammanet_pl	t	f	f	f	2012-01-16 11:10:22.570634	0
5413	users	cypherq	t	f	f	f	2012-01-16 11:10:22.573541	0
5414	user_profile	adanow0	f	f	t	f	2012-01-16 11:10:22.576897	0
5415	user_profile	simpli0	f	f	t	f	2012-01-16 11:10:22.579204	0
5416	user_profile	robert_posiadala_gammanet_pl	f	f	t	f	2012-01-16 11:10:22.581133	0
5417	user_profile	cypherq	f	f	t	f	2012-01-16 11:10:22.583013	0
5418	messages	adanow0	f	f	t	f	2012-01-16 11:10:22.587028	0
5419	messages	simpli0	f	f	t	f	2012-01-16 11:10:22.589063	0
5420	messages	robert_posiadala_gammanet_pl	f	f	t	f	2012-01-16 11:10:22.591053	0
5421	messages	cypherq	f	f	t	f	2012-01-16 11:10:22.593857	0
5422	message_users	adanow0	f	f	t	f	2012-01-16 11:10:22.597556	0
5423	message_users	simpli0	f	f	t	f	2012-01-16 11:10:22.599437	0
5424	message_users	robert_posiadala_gammanet_pl	f	f	t	f	2012-01-16 11:10:22.60224	0
5425	message_users	cypherq	f	f	t	f	2012-01-16 11:10:22.604149	0
5426	message_attachments	adanow0	f	f	t	f	2012-01-16 11:10:22.607737	0
5427	message_attachments	simpli0	f	f	t	f	2012-01-16 11:10:22.610506	0
5428	message_attachments	robert_posiadala_gammanet_pl	f	f	t	f	2012-01-16 11:10:22.612404	0
5429	message_attachments	cypherq	f	f	t	f	2012-01-16 11:10:22.614347	0
5430	quiz_scores	adanow0	f	f	t	f	2012-01-16 11:10:22.617873	0
5431	quiz_scores	simpli0	f	f	t	f	2012-01-16 11:10:22.61974	0
5432	quiz_scores	robert_posiadala_gammanet_pl	f	f	t	f	2012-01-16 11:10:22.621712	0
5433	quiz_scores	cypherq	f	f	t	f	2012-01-16 11:10:22.624292	0
5434	survey_detailed_results	adanow0	f	f	t	f	2012-01-16 11:10:22.628177	0
5435	survey_detailed_results	simpli0	f	f	t	f	2012-01-16 11:10:22.630247	0
5436	survey_detailed_results	robert_posiadala_gammanet_pl	f	f	t	f	2012-01-16 11:10:22.632555	0
5437	survey_detailed_results	cypherq	f	f	t	f	2012-01-16 11:10:22.634502	0
5438	survey_results	adanow0	f	f	t	f	2012-01-16 11:10:22.638116	0
5439	survey_results	simpli0	f	f	t	f	2012-01-16 11:10:22.640881	0
5440	survey_results	robert_posiadala_gammanet_pl	f	f	t	f	2012-01-16 11:10:22.642782	0
5441	survey_results	cypherq	f	f	t	f	2012-01-16 11:10:22.64476	0
\.


--
-- Data for Name: apps; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY apps (id, domain, token) FROM stdin;
\.


--
-- Data for Name: apps_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY apps_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	*	t	t	t	t
\.


--
-- Data for Name: course_schedule; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY course_schedule (id, course_unit_id, lesson_date, schedule, subject) FROM stdin;
1	3	2012-01-28	seks na dechach\r\n	Lekcja zapoznawcza
\.


--
-- Data for Name: course_schedule_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY course_schedule_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_simpli0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
\.


--
-- Data for Name: course_units; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY course_units (id, name, hour_amount, course_id, user_id) FROM stdin;
3	piano	10	3	3
5	bąki w prążki	20	5	3
6	wstążki	20	6	3
7	chrząszczę i żuki	20	7	3
\.


--
-- Data for Name: course_units_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY course_units_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
3	yala_simpli0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	t	f	f	f
\.


--
-- Data for Name: courses; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY courses (id, training_center_id, name, code, level, price, description, created_date, project_id, group_id, color, status, start_date, end_date, show_on_www, hash) FROM stdin;
3	4	Nauka Gry na instrumentach	NGI	\N	30		2011-12-01	2	3	#FF00FF	1	2011-12-02 12:00:00	2012-01-28 10:45:00	1	3d790ae932f1790b8066afecd532024b
5	4	Szkąlenię testowę	ABCD	\N	50		2011-12-08	2	\N	#FF5144	1	2011-12-09 15:30:00	2011-12-09 18:15:00	0	64d278cd77c95e005f5ff726b0757d4d
6	4	Szkolenie Świąteczne	000	\N	0		2011-12-09	2	\N	#85FF60	1	2011-12-09 19:30:00	2011-12-10 22:30:00	0	a09aa8176ad446495ec98a3d4a8f330a
7	4	Żółta Łódź Z Bąkiem	0010	\N	0		2011-12-09	2	\N	#8EF3FF	1	2011-12-11 10:00:00	2012-02-08 09:15:00	0	fa652a278d988c892caecbe15f1b5022
\.


--
-- Data for Name: courses_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY courses_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
3	yala_simpli0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	t	f	f	f
\.


--
-- Data for Name: doctrine_migration_version; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY doctrine_migration_version (version) FROM stdin;
85
\.


--
-- Data for Name: exam_grades; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY exam_grades (id, exam_id, user_id, grade) FROM stdin;
1	1	5	1.75
\.


--
-- Data for Name: exam_grades_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY exam_grades_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_simpli0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
\.


--
-- Data for Name: exams; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY exams (id, course_unit_id, name, type, created_date) FROM stdin;
1	3	test	\N	2011-12-01
\.


--
-- Data for Name: exams_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY exams_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_simpli0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
\.


--
-- Data for Name: files; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY files (id, hash, size, created_date, downloads, filename, user_id) FROM stdin;
\.


--
-- Data for Name: files_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY files_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	*	t	t	t	t
\.


--
-- Data for Name: google_tokens; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY google_tokens (id, user_id, scope, token) FROM stdin;
\.


--
-- Data for Name: google_tokens_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY google_tokens_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	*	t	t	t	t
\.


--
-- Data for Name: group_users; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY group_users (id, group_id, user_id, status) FROM stdin;
3	3	5	0
\.


--
-- Data for Name: group_users_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY group_users_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
\.


--
-- Data for Name: groups; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY groups (id, name, advance_level) FROM stdin;
3	agnieszki	1
\.


--
-- Data for Name: groups_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY groups_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
3	yala_simpli0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	t	f	f	f
\.


--
-- Data for Name: lesson_presence; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY lesson_presence (id, lesson_id, user_id) FROM stdin;
4	20	5
5	21	5
\.


--
-- Data for Name: lesson_presence_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY lesson_presence_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
4	yala_simpli0	t	f	f	f
5	yala_simpli0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
\.


--
-- Data for Name: lessons; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY lessons (id, course_unit_id, room_id, user_id, start_date, end_date, cycle_id, sequence) FROM stdin;
20	3	4	3	2011-12-02 12:00:00	2011-12-02 14:30:00	\N	0
21	3	4	3	2011-12-03 10:00:00	2011-12-03 10:45:00	21	0
23	3	4	3	2011-12-17 10:00:00	2011-12-17 10:45:00	21	0
24	3	4	3	2011-12-24 10:00:00	2011-12-24 10:45:00	21	0
25	3	4	3	2011-12-31 10:00:00	2011-12-31 10:45:00	21	0
26	3	4	3	2012-01-07 10:00:00	2012-01-07 10:45:00	21	0
27	3	4	3	2012-01-14 10:00:00	2012-01-14 10:45:00	21	0
28	3	4	3	2012-01-21 10:00:00	2012-01-21 10:45:00	21	0
29	3	4	3	2012-01-28 10:00:00	2012-01-28 10:45:00	21	0
32	5	4	3	2011-12-09 15:30:00	2011-12-09 16:15:00	\N	0
33	5	4	3	2011-12-09 17:30:00	2011-12-09 18:15:00	\N	0
34	6	4	3	2011-12-09 19:30:00	2011-12-09 21:00:00	\N	0
35	6	4	3	2011-12-10 20:00:00	2011-12-10 22:30:00	\N	0
38	7	4	3	2011-12-11 10:00:00	2011-12-11 12:00:00	\N	0
39	7	4	3	2011-12-14 08:30:00	2011-12-14 09:15:00	39	0
40	7	4	3	2011-12-16 08:30:00	2011-12-16 09:15:00	39	0
41	7	4	3	2011-12-21 08:30:00	2011-12-21 09:15:00	39	0
42	7	4	3	2011-12-23 08:30:00	2011-12-23 09:15:00	39	0
43	7	4	3	2011-12-28 08:30:00	2011-12-28 09:15:00	39	0
44	7	4	3	2011-12-30 08:30:00	2011-12-30 09:15:00	39	0
45	7	4	3	2012-01-04 08:30:00	2012-01-04 09:15:00	39	0
46	7	4	3	2012-01-06 08:30:00	2012-01-06 09:15:00	39	0
47	7	4	3	2012-01-11 08:30:00	2012-01-11 09:15:00	39	0
48	7	4	3	2012-01-13 08:30:00	2012-01-13 09:15:00	39	0
49	7	4	3	2012-01-18 08:30:00	2012-01-18 09:15:00	39	0
50	7	4	3	2012-01-20 08:30:00	2012-01-20 09:15:00	39	0
51	7	4	3	2012-01-25 08:30:00	2012-01-25 09:15:00	39	0
52	7	4	3	2012-01-27 08:30:00	2012-01-27 09:15:00	39	0
53	7	4	3	2012-02-01 08:30:00	2012-02-01 09:15:00	39	0
54	7	4	3	2012-02-03 08:30:00	2012-02-03 09:15:00	39	0
55	7	4	3	2012-02-08 08:30:00	2012-02-08 09:15:00	39	0
37	7	4	3	2011-12-11 13:30:00	2011-12-11 14:15:00	\N	0
22	3	4	3	2011-12-11 15:30:00	2011-12-11 18:30:00	21	0
\.


--
-- Data for Name: lessons_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY lessons_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
32	yala_trener	t	f	f	f
20	yala_trener	t	f	f	f
20	yala_simpli0	t	f	f	f
21	yala_simpli0	t	f	f	f
22	yala_simpli0	t	f	f	f
23	yala_simpli0	t	f	f	f
33	yala_trener	t	f	f	f
24	yala_simpli0	t	f	f	f
25	yala_simpli0	t	f	f	f
26	yala_simpli0	t	f	f	f
27	yala_simpli0	t	f	f	f
28	yala_simpli0	t	f	f	f
29	yala_simpli0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
34	yala_trener	t	f	f	f
38	yala_trener	t	f	f	f
35	yala_trener	t	f	f	f
39	yala_trener	t	f	f	f
40	yala_trener	t	f	f	f
41	yala_trener	t	f	f	f
42	yala_trener	t	f	f	f
43	yala_trener	t	f	f	f
44	yala_trener	t	f	f	f
45	yala_trener	t	f	f	f
46	yala_trener	t	f	f	f
47	yala_trener	t	f	f	f
48	yala_trener	t	f	f	f
49	yala_trener	t	f	f	f
50	yala_trener	t	f	f	f
51	yala_trener	t	f	f	f
52	yala_trener	t	f	f	f
53	yala_trener	t	f	f	f
54	yala_trener	t	f	f	f
55	yala_trener	t	f	f	f
37	yala_trener	t	f	f	f
21	yala_trener	t	f	f	f
22	yala_trener	t	f	f	f
23	yala_trener	t	f	f	f
24	yala_trener	t	f	f	f
25	yala_trener	t	f	f	f
26	yala_trener	t	f	f	f
27	yala_trener	t	f	f	f
28	yala_trener	t	f	f	f
29	yala_trener	t	f	f	f
\.


--
-- Data for Name: message_attachments; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY message_attachments (id, message_id, file_id) FROM stdin;
\.


--
-- Data for Name: message_attachments_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY message_attachments_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
0	yala_adanow0	f	f	t	f
0	yala_simpli0	f	f	t	f
0	yala_robert_posiadala_gammanet_pl	f	f	t	f
0	yala_cypherq	f	f	t	f
\.


--
-- Data for Name: message_users; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY message_users (id, message_id, user_id, read_date, folder) FROM stdin;
2	1	2	2011-11-25 13:55:21	2
1	1	4	2011-11-25 13:56:35	1
4	2	12	2011-12-14 14:10:27	2
3	2	12	2011-12-14 14:10:39	1
6	3	12	2011-12-14 14:11:02	2
5	3	12	2011-12-14 14:11:06	1
\.


--
-- Data for Name: message_users_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY message_users_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_adanow0	t	t	f	t
3	yala_robert_posiadala_gammanet_pl	t	t	t	t
4	yala_robert_posiadala_gammanet_pl	t	t	t	t
5	yala_robert_posiadala_gammanet_pl	t	t	t	t
6	yala_robert_posiadala_gammanet_pl	t	t	t	t
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
0	yala_adanow0	f	f	t	f
0	yala_simpli0	f	f	t	f
0	yala_robert_posiadala_gammanet_pl	f	f	t	f
0	yala_cypherq	f	f	t	f
\.


--
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY messages (id, subject, body, send_date, sender_id, recipient_list) FROM stdin;
1	masz wiadomość	<p><span style="font-family:Arial"><font size="4">﻿od admina<br></font></span></p>	2011-11-25 13:55:21	2	Nauka gry na instrumentach
2	test		2011-12-14 14:10:26	12	Robert Posiadała (robert.posiadala@gammanet.pl)
3	RE: test		2011-12-14 14:11:02	12	Robert Posiadała (robert_posiadala_gammanet_pl)
\.


--
-- Data for Name: messages_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY messages_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_adanow0	t	f	f	f
2	yala_robert_posiadala_gammanet_pl	t	t	t	t
3	yala_robert_posiadala_gammanet_pl	t	t	t	t
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
0	yala_adanow0	f	f	t	f
0	yala_simpli0	f	f	t	f
0	yala_robert_posiadala_gammanet_pl	f	f	t	f
0	yala_cypherq	f	f	t	f
\.


--
-- Data for Name: poland; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY poland (id, parent_id, level, name) FROM stdin;
1	\N	1	Dolnośląskie
2	1	2	Ząbkowicki
3	2	3	Bardo
4	1	2	Dzierżoniowski
5	4	3	Bielawa
6	1	2	Oleśnicki
7	6	3	Bierutów
8	6	3	Bierutów - miasto
9	6	3	Bierutów - obszar wiejski
10	1	2	Zgorzelecki
11	10	3	Bogatynia
12	10	3	Bogatynia - miasto
13	10	3	Bogatynia - obszar wiejski
14	1	2	Wałbrzyski
15	14	3	Boguszów-Gorce
16	1	2	Bolesławiecki
17	16	3	Bolesławiec
18	1	2	Jaworski
19	18	3	Bolków
20	18	3	Bolków - miasto
21	18	3	Bolków - obszar wiejski
22	1	2	Strzeliński
23	22	3	Borów
24	1	2	Wołowski
25	24	3	Brzeg Dolny
26	24	3	Brzeg Dolny - miasto
27	24	3	Brzeg Dolny - obszar wiejski
28	1	2	Kłodzki
29	28	3	Bystrzyca Kłodzka
30	28	3	Bystrzyca Kłodzka - miasto
31	28	3	Bystrzyca Kłodzka - obszar wiejski
32	1	2	Polkowicki
33	32	3	Chocianów
34	32	3	Chocianów - miasto
35	32	3	Chocianów - obszar wiejski
36	1	2	Legnicki
37	36	3	Chojnów
38	2	3	Ciepłowody
39	1	2	Milicki
40	39	3	Cieszków
41	14	3	Czarny Bór
42	1	2	Wrocławski
43	42	3	Czernica
44	42	3	Długołęka
45	1	2	Świdnicki
46	45	3	Dobromierz
47	6	3	Dobroszyce
48	1	2	Oławski
49	48	3	Domaniów
50	28	3	Duszniki-Zdrój
51	6	3	Dziadowa Kłoda
52	4	3	Dzierżoniów
53	32	3	Gaworzyce
54	1	2	Głogowski
55	54	3	Głogów
56	14	3	Głuszyca
57	14	3	Głuszyca - miasto
58	14	3	Głuszyca - obszar wiejski
59	1	2	Górowski
60	59	3	Góra
61	59	3	Góra - miasto
62	59	3	Góra - obszar wiejski
63	32	3	Grębocice
64	16	3	Gromadka
65	1	2	Lwówecki
66	65	3	Gryfów Śląski
67	65	3	Gryfów Śląski - miasto
68	65	3	Gryfów Śląski - obszar wiejski
69	1	2	Jeleniogórski
70	69	3	Janowice Wielkie
71	18	3	Jawor
72	45	3	Jaworzyna Śląska
73	45	3	Jaworzyna Śląska - miasto
74	45	3	Jaworzyna Śląska - obszar wiejski
75	14	3	Jedlina-Zdrój
76	48	3	Jelcz-Laskowice
77	48	3	Jelcz-Laskowice - miasto
78	48	3	Jelcz-Laskowice - obszar wiejski
79	59	3	Jemielno
80	54	3	Jerzmanowa
81	69	3	Jeżów Sudecki
82	42	3	Jordanów Śląski
83	2	3	Kamieniec Ząbkowicki
84	1	2	Kamiennogórski
85	84	3	Kamienna Góra
86	69	3	Karpacz
87	42	3	Kąty Wrocławskie
88	42	3	Kąty Wrocławskie - miasto
89	42	3	Kąty Wrocławskie - obszar wiejski
90	28	3	Kłodzko
91	42	3	Kobierzyce
92	22	3	Kondratowice
93	1	2	Średzki
94	93	3	Kostomłoty
95	54	3	Kotla
96	69	3	Kowary
97	39	3	Krośnice
98	36	3	Krotoszyce
99	28	3	Kudowa-Zdrój
100	36	3	Kunice
101	28	3	Lądek-Zdrój
102	28	3	Lądek-Zdrój - miasto
103	28	3	Lądek-Zdrój - obszar wiejski
104	36	3	Legnickie Pole
105	1	2	Lubański
106	105	3	Leśna
107	105	3	Leśna - miasto
108	105	3	Leśna - obszar wiejski
109	28	3	Lewin Kłodzki
110	105	3	Lubań
111	84	3	Lubawka
112	84	3	Lubawka - miasto
113	84	3	Lubawka - obszar wiejski
114	1	2	Lubiński
115	114	3	Lubin
116	65	3	Lubomierz
117	65	3	Lubomierz - miasto
118	65	3	Lubomierz - obszar wiejski
119	65	3	Lwówek Śląski
120	65	3	Lwówek Śląski - miasto
121	65	3	Lwówek Śląski - obszar wiejski
122	4	3	Łagiewniki
123	1	2	Miasto Jelenia Góra
124	123	3	Miasto Jelenia Góra
125	1	2	Miasto Legnica
126	125	3	Miasto Legnica
127	1	2	Miasto Wałbrzych
128	127	3	Miasto Wałbrzych
129	1	2	Miasto Wrocław
130	129	3	Miasto Wrocław
131	93	3	Malczyce
132	45	3	Marcinowice
133	84	3	Marciszów
134	18	3	Męcinka
135	14	3	Mieroszów
136	14	3	Mieroszów - miasto
137	14	3	Mieroszów - obszar wiejski
138	42	3	Mietków
139	6	3	Międzybórz
140	6	3	Międzybórz - miasto
141	6	3	Międzybórz - obszar wiejski
142	28	3	Międzylesie
143	28	3	Międzylesie - miasto
144	28	3	Międzylesie - obszar wiejski
145	93	3	Miękinia
146	39	3	Milicz
147	39	3	Milicz - miasto
148	39	3	Milicz - obszar wiejski
149	36	3	Miłkowice
150	65	3	Mirsk
151	65	3	Mirsk - miasto
152	65	3	Mirsk - obszar wiejski
153	18	3	Mściwojów
154	69	3	Mysłakowice
155	59	3	Niechlów
156	4	3	Niemcza
157	4	3	Niemcza - miasto
158	4	3	Niemcza - obszar wiejski
159	28	3	Nowa Ruda
160	16	3	Nowogrodziec
161	16	3	Nowogrodziec - miasto
162	16	3	Nowogrodziec - obszar wiejski
163	1	2	Trzebnicki
164	163	3	Oborniki Śląskie
165	163	3	Oborniki Śląskie - miasto
166	163	3	Oborniki Śląskie - obszar wiejski
167	6	3	Oleśnica
168	105	3	Olszyna
169	48	3	Oława
170	16	3	Osiecznica
171	18	3	Paszowice
172	54	3	Pęcław
173	69	3	Piechowice
174	1	2	Złotoryjski
175	174	3	Pielgrzymka
176	10	3	Pieńsk
177	10	3	Pieńsk - miasto
178	10	3	Pieńsk - obszar wiejski
179	4	3	Pieszyce
180	4	3	Piława Górna
181	105	3	Platerówka
182	69	3	Podgórzyn
183	28	3	Polanica-Zdrój
184	32	3	Polkowice
185	32	3	Polkowice - miasto
186	32	3	Polkowice - obszar wiejski
187	36	3	Prochowice
188	36	3	Prochowice - miasto
189	36	3	Prochowice - obszar wiejski
190	163	3	Prusice
191	32	3	Przemków
192	32	3	Przemków - miasto
193	32	3	Przemków - obszar wiejski
194	22	3	Przeworno
195	28	3	Radków
196	28	3	Radków - miasto
197	28	3	Radków - obszar wiejski
198	32	3	Radwanice
199	114	3	Rudna
200	36	3	Ruja
201	42	3	Siechnice - miasto
202	105	3	Siekierczyn
203	42	3	Sobótka
204	42	3	Sobótka - miasto
205	42	3	Sobótka - obszar wiejski
206	69	3	Stara Kamienica
207	14	3	Stare Bogaczowice
208	2	3	Stoszowice
209	28	3	Stronie Śląskie
210	28	3	Stronie Śląskie - miasto
211	28	3	Stronie Śląskie - obszar wiejski
212	45	3	Strzegom
213	45	3	Strzegom - miasto
214	45	3	Strzegom - obszar wiejski
215	22	3	Strzelin
216	22	3	Strzelin - miasto
217	22	3	Strzelin - obszar wiejski
218	10	3	Sulików
219	6	3	Syców
220	6	3	Syców - miasto
221	6	3	Syców - obszar wiejski
222	14	3	Szczawno-Zdrój
223	28	3	Szczytna
224	28	3	Szczytna - miasto
225	28	3	Szczytna - obszar wiejski
226	69	3	Szklarska Poręba
227	114	3	Ściana - obszar wiejski
228	114	3	Ścinawa
229	114	3	Ścinawa - miasto
230	93	3	Środa Śląska
231	93	3	Środa Śląska - miasto
232	93	3	Środa Śląska - obszar wiejski
233	45	3	Świdnica
234	45	3	Świebodzice
235	105	3	Świeradów-Zdrój
236	174	3	Świerzawa
237	174	3	Świerzawa - miasto
238	174	3	Świerzawa - obszar wiejski
239	42	3	Święta Katarzyna
240	42	3	Święta Katarzyna - obszar wiejski
241	163	3	Trzebnica
242	163	3	Trzebnica - miasto
243	163	3	Trzebnica - obszar wiejski
244	6	3	Twardogóra
245	6	3	Twardogóra - miasto
246	6	3	Twardogóra - obszar wiejski
247	93	3	Udanin
248	14	3	Walim
249	16	3	Warta Bolesławiecka
250	18	3	Wądroże Wielkie
251	59	3	Wąsosz
252	59	3	Wąsosz - miasto
253	59	3	Wąsosz - obszar wiejski
254	10	3	Węgliniec
255	10	3	Węgliniec - miasto
256	10	3	Węgliniec - obszar wiejski
257	22	3	Wiązów
258	22	3	Wiązów - miasto
259	22	3	Wiązów - obszar wiejski
260	24	3	Wińsko
261	163	3	Wisznia Mała
262	65	3	Wleń
263	65	3	Wleń - miasto
264	65	3	Wleń - obszar wiejski
265	174	3	Wojcieszów
266	24	3	Wołów
267	24	3	Wołów - miasto
268	24	3	Wołów - obszar wiejski
269	129	3	Wrocław-Fabryczna
270	129	3	Wrocław-Krzyki
271	129	3	Wrocław-Psie Pole
272	129	3	Wrocław-Stare Miasto
273	129	3	Wrocław-Śródmieście
274	174	3	Zagrodno
275	10	3	Zawidów
276	163	3	Zawonia
277	2	3	Ząbkowice Śląskie
278	2	3	Ząbkowice Śląskie - miasto
279	2	3	Ząbkowice Śląskie - obszar wiejski
280	10	3	Zgorzelec
281	2	3	Ziębice
282	2	3	Ziębice - miasto
283	2	3	Ziębice - obszar wiejski
284	174	3	Złotoryja
285	2	3	Złoty Stok
286	2	3	Złoty Stok - miasto
287	2	3	Złoty Stok - obszar wiejski
288	45	3	Żarów
289	45	3	Żarów - miasto
290	45	3	Żarów - obszar wiejski
291	163	3	Żmigród
292	163	3	Żmigród - miasto
293	163	3	Żmigród - obszar wiejski
294	42	3	Żórawina
295	54	3	Żukowice
296	2	3	Bardo - miasto
297	2	3	Bardo - obszar wiejski
298	\N	1	Kujawsko-Pomorskie
299	298	2	Aleksandrowski
300	299	3	Bądkowo
301	298	2	Bydgoski
302	301	3	Białe Błota
303	298	2	Lipnowski
304	303	3	Bobrowniki
305	298	2	Brodnicki
306	305	3	Bobrowo
307	298	2	Włocławski
308	307	3	Boniewo
309	305	3	Brodnica
310	307	3	Brześć Kujawski
311	307	3	Brześć Kujawski - miasto
312	307	3	Brześć Kujawski - obszar wiejski
313	305	3	Brzozie
314	298	2	Rypiński
315	314	3	Brzuze
316	298	2	Świecki
317	316	3	Bukowiec
318	298	2	Radziejowski
319	318	3	Bytoń
320	298	2	Tucholski
321	320	3	Cekcyn
322	298	2	Chełmiński
323	322	3	Chełmno
324	298	2	Toruński
325	324	3	Chełmża
326	307	3	Choceń
327	307	3	Chodecz
328	307	3	Chodecz - miasto
329	307	3	Chodecz - obszar wiejski
330	303	3	Chrostkowo
331	298	2	Golubsko-Dobrzyński
332	331	3	Ciechocin
333	299	3	Ciechocinek
334	324	3	Czernikowo
335	298	2	Mogileński
336	335	3	Dąbrowa
337	298	2	Inowrocławski
338	337	3	Dąbrowa Biskupia
339	301	3	Dąbrowa Chełmińska
340	298	2	Wąbrzeski
341	340	3	Dębowa Łąka
342	301	3	Dobrcz
343	318	3	Dobre
344	303	3	Dobrzyń nad Wisłą
345	303	3	Dobrzyń nad Wisłą - miasto
346	303	3	Dobrzyń nad Wisłą - obszar wiejski
347	316	3	Dragacz
348	316	3	Drzycim
349	307	3	Fabianki
350	298	2	Żniński
351	350	3	Gąsawa
352	337	3	Gniewkowo
353	337	3	Gniewkowo - miasto
354	337	3	Gniewkowo - obszar wiejski
355	331	3	Golub-Dobrzyń
356	320	3	Gostycyn
357	305	3	Górzno
358	305	3	Górzno - miasto
359	305	3	Górzno - obszar wiejski:
360	305	3	Grążawy
361	298	2	Grudziądzki
362	361	3	Grudziądz
363	361	3	Gruta
364	337	3	Inowrocław
365	307	3	Izbica Kujawska
366	307	3	Izbica Kujawska - miasto
367	307	3	Izbica Kujawska - obszar wiejski
368	305	3	Jabłonowo Pomorskie
369	305	3	Jabłonowo Pomorskie - miasto
370	305	3	Jabłonowo Pomorskie - obszar wiejski
371	337	3	Janikowo
372	337	3	Janikowo - miasto
373	337	3	Janikowo - obszar wiejski
374	350	3	Janowiec Wielkopolski
375	350	3	Janowiec Wielkopolski - miasto
376	350	3	Janowiec Wielkopolski - obszar wiejski
377	335	3	Jeziora Wielkie
378	316	3	Jeżewo
379	298	2	Sępoleński
380	379	3	Kamień Krajeński
381	379	3	Kamień Krajeński - miasto
382	379	3	Kamień Krajeński - obszar wiejski
383	298	2	Nakielski
384	383	3	Kcynia
385	383	3	Kcynia - miasto
386	383	3	Kcynia - obszar wiejski
387	320	3	Kęsowo
388	322	3	Kijewo Królewskie
389	303	3	Kikół
390	299	3	Koneck
391	301	3	Koronowo
392	301	3	Koronowo - miasto
393	301	3	Koronowo - obszar wiejski
394	307	3	Kowal
395	331	3	Kowalewo Pomorskie
396	331	3	Kowalewo Pomorskie - miasto
397	331	3	Kowalewo Pomorskie - obszar wiejski
398	337	3	Kruszwica
399	337	3	Kruszwica - miasto
400	337	3	Kruszwica - obszar wiejski
401	340	3	Książki
402	303	3	Lipno
403	322	3	Lisewo
404	316	3	Lniano
405	307	3	Lubanie
406	324	3	Lubicz
407	307	3	Lubień Kujawski
408	307	3	Lubień Kujawski - miasto
409	307	3	Lubień Kujawski - obszar wiejski
410	320	3	Lubiewo
411	307	3	Lubraniec
412	307	3	Lubraniec - miasto
413	307	3	Lubraniec - obszar wiejski
414	350	3	Łabiszyn
415	350	3	Łabiszyn - miasto
416	350	3	Łabiszyn - obszar wiejski
417	361	3	Łasin
418	361	3	Łasin - miasto
419	361	3	Łasin - obszar wiejski
420	324	3	Łubianka
421	324	3	Łysomice
422	298	2	Miasto Bydgoszcz
423	422	3	Miasto Bydgoszcz
424	298	2	Miasto Grudziądz
425	424	3	Miasto Grudziądz
426	298	2	Miasto Toruń
427	426	3	Miasto Toruń
428	298	2	Miasto Włocławek
429	428	3	Miasto Włocławek
430	335	3	Mogilno
431	335	3	Mogilno - miasto
432	335	3	Mogilno - obszar wiejski
433	383	3	Mrocza
434	383	3	Mrocza - miasto
435	383	3	Mrocza - obszar wiejski
436	383	3	Nakło nad Notecią
437	383	3	Nakło nad Notecią - miasto
438	383	3	Nakło nad Notecią - obszar wiejski
439	299	3	Nieszawa
440	301	3	Nowa Wieś Wielka
441	316	3	Nowe
442	316	3	Nowe - miasto
443	316	3	Nowe - obszar wiejski
444	324	3	Obrowo
445	316	3	Osie
446	305	3	Osiek
447	301	3	Osielsko
448	318	3	Osięciny
449	337	3	Pakość
450	337	3	Pakość - miasto
451	337	3	Pakość - obszar wiejski
452	322	3	Papowo Biskupie
453	318	3	Piotrków Kujawski
454	318	3	Piotrków Kujawski - miasto
455	318	3	Piotrków Kujawski - obszar wiejski
456	340	3	Płużnica
457	316	3	Pruszcz
458	299	3	Raciążek
459	331	3	Radomin
460	318	3	Radziejów
461	361	3	Radzyń Chełmiński
462	361	3	Radzyń Chełmiński - miasto
463	361	3	Radzyń Chełmiński - obszar wiejski
464	314	3	Rogowo
465	350	3	Rogowo
466	361	3	Rogóźno
467	337	3	Rojewo
468	314	3	Rypin
469	383	3	Sadki
470	379	3	Sępólno Krajeńskie
471	379	3	Sępólno Krajeńskie - miasto
472	379	3	Sępólno Krajeńskie - obszar wiejski
473	301	3	Sicienko
474	303	3	Skępe
475	303	3	Skępe - miasto
476	303	3	Skępe - obszar wiejski
477	314	3	Skrwilno
478	301	3	Solec Kujawski
479	301	3	Solec Kujawski - miasto
480	301	3	Solec Kujawski - obszar wiejski
481	379	3	Sośno
482	322	3	Stolno
483	335	3	Strzelno
484	335	3	Strzelno - miasto
485	335	3	Strzelno - obszar wiejski
486	383	3	Szubin
487	383	3	Szubin - miasto
488	383	3	Szubin - obszar wiejski
489	320	3	Śliwice
490	316	3	Świecie
491	316	3	Świecie - miasto
492	316	3	Świecie - obszar wiejski
493	361	3	Świecie nad Osą
494	305	3	Świedziebnia
495	316	3	Świekatowo
496	303	3	Tłuchowo
497	318	3	Topólka
498	320	3	Tuchola
499	320	3	Tuchola - miasto
500	320	3	Tuchola - obszar wiejski
501	322	3	Unisław
502	299	3	Waganiec
503	316	3	Warlubie
504	340	3	Wąbrzeźno
505	314	3	Wąpielsk
506	303	3	Wielgie
507	324	3	Wielka Nieszawka
508	379	3	Więcbork
509	379	3	Więcbork - miasto
510	379	3	Więcbork - obszar wiejski
511	307	3	Włocławek
512	299	3	Zakrzewo
513	305	3	Zbiczno
514	331	3	Zbójno
515	324	3	Zławieś Wielka
516	337	3	Złotniki Kujawskie
517	350	3	Żnin
518	350	3	Żnin - miasto
519	350	3	Żnin - obszar wiejski
520	299	3	Aleksandrów Kujawski
521	350	3	Barcin
522	350	3	Barcin - miasto
523	350	3	Barcin - obszar wiejski
524	307	3	Baruchowo
525	\N	1	Lubelskie
526	525	2	Lubartowski
527	526	3	Abramów
528	525	2	Łukowski
529	528	3	Adamów
530	525	2	Zamojski
531	530	3	Adamów
532	525	2	Biłgorajski
533	532	3	Aleksandrów
534	525	2	Janowski
535	534	3	Batorz
536	525	2	Tomaszowski
537	536	3	Bełżec
538	525	2	Lubelski
539	538	3	Bełżyce
540	538	3	Bełżyce - miasto
541	538	3	Bełżyce - obszar wiejski
542	525	2	Bialski
543	542	3	Biała Podlaska
544	525	2	Chełmski
545	544	3	Białopole
546	532	3	Biłgoraj
547	532	3	Biszcza
548	525	2	Radzyński
549	548	3	Borki
550	538	3	Borzechów
551	538	3	Bychawa
552	538	3	Bychawa - miasto
553	538	3	Bychawa - obszar wiejski
554	544	3	Chełm
555	525	2	Opolski
556	555	3	Chodel
557	534	3	Chrzanów
558	525	2	Łęczyński
559	558	3	Cyców
560	548	3	Czemierniki
561	525	2	Rycki
562	561	3	Dęblin
563	525	2	Parczewski
564	563	3	Dębowa Kłoda
565	525	2	Hrubieszowski
566	565	3	Dołhobyczów
567	544	3	Dorohusk
568	542	3	Drelów
569	544	3	Dubienka
570	525	2	Kraśnicki
571	570	3	Dzierzkowice
572	534	3	Dzwola
573	525	2	Krasnostawski
574	573	3	Fajsławice
575	526	3	Firlej
576	532	3	Frampol
577	532	3	Frampol - miasto
578	532	3	Frampol - obszar wiejski
579	538	3	Garbów
580	538	3	Głusk
581	534	3	Godziszów
582	532	3	Goraj
583	573	3	Gorzków
584	570	3	Gościeradów
585	530	3	Grabowiec
586	525	2	Włodawski
587	586	3	Hanna
588	586	3	Hańsk
589	565	3	Horodło
590	565	3	Hrubieszów
591	573	3	Izbica
592	538	3	Jabłonna
593	563	3	Jabłoń
594	525	2	Puławski
595	594	3	Janowiec
596	534	3	Janów Lubelski
597	534	3	Janów Lubelski - miasto
598	534	3	Janów Lubelski - obszar wiejski
599	542	3	Janów Podlaski
600	536	3	Jarczów
601	538	3	Jastków
602	526	3	Jeziorzany
603	532	3	Józefów
604	555	3	Józefów
605	532	3	Józefów - miasto
606	532	3	Józefów - obszar wiejski
607	544	3	Kamień
608	526	3	Kamionka
609	555	3	Karczmiska
610	594	3	Kazimierz Dolny
611	594	3	Kazimierz Dolny - miasto
612	594	3	Kazimierz Dolny - obszar wiejski
613	548	3	Kąkolewnica Wschodnia
614	561	3	Kłoczew
615	526	3	Kock
616	526	3	Kock - miasto
617	526	3	Kock - obszar wiejski
618	542	3	Kodeń
619	548	3	Komarówka Podlaska
620	530	3	Komarów-Osada
621	538	3	Konopnica
622	542	3	Konstantynów
623	594	3	Końskowola
624	530	3	Krasnobród
625	530	3	Krasnobród - miasto
626	530	3	Krasnobród - obszar wiejski
627	573	3	Krasnystaw
628	573	3	Kraśniczyn
629	570	3	Kraśnik
630	536	3	Krynice
631	538	3	Krzczonów
632	528	3	Krzywda
633	532	3	Księżpol
634	594	3	Kurów
635	542	3	Leśna Podlaska
636	544	3	Leśniowice
637	526	3	Lubartów
638	536	3	Lubycza Królewska
639	558	3	Ludwin
640	530	3	Łabunie
641	536	3	Łaszczów
642	555	3	Łaziska
643	558	3	Łęczna
644	558	3	Łęczna - miasto
645	558	3	Łęczna - obszar wiejski
646	542	3	Łomazy
647	573	3	Łopiennik Górny
648	532	3	Łukowa
649	528	3	Łuków
650	525	2	Miasto Biała Podlaska
651	650	3	Miasto Biała Podlaska
652	525	2	Miasto Chełm
653	652	3	Miasto Chełm
654	525	2	Miasto Lublin
655	654	3	Miasto Lublin
656	525	2	Miasto Zamość
657	656	3	Miasto Zamość
658	594	3	Markuszów
659	525	2	Świdnicki
660	659	3	Mełgiew
661	530	3	Miączyn
662	526	3	Michów
663	542	3	Międzyrzec Podlaski
664	563	3	Milanów
665	558	3	Milejów
666	565	3	Mircze
667	534	3	Modliborzyce
668	594	3	Nałęczów
669	594	3	Nałęczów - miasto
670	594	3	Nałęczów - obszar wiejski
671	538	3	Niedrzwica Duża
672	526	3	Niedźwiada
673	530	3	Nielisz
674	538	3	Niemce
675	561	3	Nowodwór
676	532	3	Obsza
677	555	3	Opole Lubelskie
678	555	3	Opole Lubelskie - miasto
679	555	3	Opole Lubelskie - obszar wiejski
680	526	3	Ostrów Lubelski
681	526	3	Ostrów Lubelski - miasto
682	526	3	Ostrów Lubelski - obszar wiejski
683	526	3	Ostrówek
684	563	3	Parczew
685	563	3	Parczew - miasto
686	563	3	Parczew - obszar wiejski
687	659	3	Piaski
688	659	3	Piaski - miasto
689	659	3	Piaski - obszar wiejski
690	542	3	Piszczac
691	563	3	Podedwórze
692	555	3	Poniatowa
693	555	3	Poniatowa - miasto
694	555	3	Poniatowa - obszar wiejski
695	532	3	Potok Górny
696	534	3	Potok Wielki
697	558	3	Puchaczów
698	594	3	Puławy
699	536	3	Rachanie
700	530	3	Radecznica
701	548	3	Radzyń Podlaski
702	573	3	Rejowiec
703	544	3	Rejowiec Fabryczny
704	542	3	Rokitno
705	542	3	Rossosz
706	544	3	Ruda-Huta
707	573	3	Rudnik
708	659	3	Rybczewice
709	561	3	Ryki
710	561	3	Ryki - miasto
711	561	3	Ryki - obszar wiejski
712	544	3	Sawin
713	526	3	Serniki
714	528	3	Serokomla
715	544	3	Siedliszcze
716	563	3	Siemień
717	573	3	Siennica Różana
718	530	3	Sitno
719	530	3	Skierbieszów
720	542	3	Sławatycze
721	563	3	Sosnowica
722	542	3	Sosnówka
723	558	3	Spiczyn
724	528	3	Stanin
725	586	3	Stary Brus
726	530	3	Stary Zamość
727	561	3	Stężyca
728	528	3	Stoczek Łukowski
729	538	3	Strzyżewice
730	530	3	Sułów
731	536	3	Susiec
732	570	3	Szastarka
733	530	3	Szczebrzeszyn
734	530	3	Szczebrzeszyn - miasto
735	530	3	Szczebrzeszyn - obszar wiejski
736	659	3	Świdnik
737	536	3	Tarnawatka
738	532	3	Tarnogród
739	532	3	Tarnogród - miasto
740	532	3	Tarnogród - obszar wiejski
741	536	3	Telatyn
742	542	3	Terespol
743	532	3	Tereszpol
744	536	3	Tomaszów Lubelski
745	659	3	Trawniki
746	528	3	Trzebieszów
747	565	3	Trzeszczany
748	570	3	Trzydnik Duży
749	542	3	Tuczna
750	532	3	Turobin
751	536	3	Tyszowce
752	565	3	Uchanie
753	548	3	Ulan-Majorat
754	536	3	Ulhówek
755	561	3	Ułęż
756	586	3	Urszulin
757	570	3	Urzędów
758	526	3	Uścimów
759	594	3	Wąwolnica
760	565	3	Werbkowice
761	544	3	Wierzbica
762	570	3	Wilkołaz
763	555	3	Wilków
764	542	3	Wisznice
765	586	3	Włodawa
766	548	3	Wohyń
767	538	3	Wojciechów
768	528	3	Wojcieszków
769	544	3	Wojsławice
770	528	3	Wola Mysłowska
771	586	3	Wola Uhruska
772	538	3	Wólka
773	586	3	Wyryki
774	538	3	Wysokie
775	538	3	Zakrzew
776	570	3	Zakrzówek
777	542	3	Zalesie
778	530	3	Zamość
779	530	3	Zwierzyniec
780	530	3	Zwierzyniec - miasto
781	530	3	Zwierzyniec - obszar wiejski
782	544	3	Żmudź
783	573	3	Żółkiewka
784	594	3	Żyrzyn
785	570	3	Annopol
786	570	3	Annopol - miasto
787	570	3	Annopol - obszar wiejski
788	594	3	Baranów
789	\N	1	Lubuskie
790	789	2	Międzyrzecki
791	790	3	Bledzew
792	789	2	Gorzowski
793	792	3	Bogdaniec
794	789	2	Zielonogórski
795	794	3	Bojadła
796	789	2	Krośnieński
797	796	3	Borowice
798	789	2	Żarski
799	798	3	Brody
800	789	2	Żagański
801	800	3	Brzeźnica
802	796	3	Bytnica
803	789	2	Nowosolski
804	803	3	Bytom Odrzański
805	803	3	Bytom Odrzański - miasto
806	803	3	Bytom Odrzański - obszar wiejski
807	789	2	Słubicki
808	807	3	Cybinka
809	807	3	Cybinka - miasto
810	807	3	Cybinka - obszar wiejski
811	794	3	Czerwieńsk
812	794	3	Czerwieńsk - miasto
813	794	3	Czerwieńsk - obszar wiejski
814	796	3	Dąbie
815	792	3	Deszczno
816	789	2	Strzelecko-Drezdenecki
817	816	3	Dobiegniew
818	816	3	Dobiegniew - miasto
819	816	3	Dobiegniew - obszar wiejski
820	816	3	Drezdenko
821	816	3	Drezdenko - miasto
822	816	3	Drezdenko - obszar wiejski
823	800	3	Gozdnica
824	807	3	Górzyca
825	796	3	Gubin
826	800	3	Iłowa
827	800	3	Iłowa - miasto
828	800	3	Iłowa - obszar wiejski
829	798	3	Jasień
830	798	3	Jasień - miasto
831	798	3	Jasień - obszar wiejski
832	794	3	Kargowa
833	794	3	Kargowa - miasto
834	794	3	Kargowa - obszar wiejski
835	792	3	Kłodawa
836	803	3	Kolsko
837	792	3	Kostrzyn
838	803	3	Kożuchów
839	803	3	Kożuchów - miasto
840	803	3	Kożuchów - obszar wiejski
841	796	3	Krosno Odrzańskie
842	796	3	Krosno Odrzańskie - miasto
843	796	3	Krosno Odrzańskie - obszar wiejski
844	789	2	Sulęciński
845	844	3	Krzeszyce
846	798	3	Lipinki Łużyckie
847	792	3	Lubiszyn
848	844	3	Lubniewice
849	844	3	Lubniewice - miasto
850	789	2	Świebodziński
851	850	3	Lubrza
852	798	3	Lubsko
853	798	3	Lubsko - miasto
854	798	3	Lubsko - obszar wiejski
855	850	3	Łagów
856	798	3	Łęknica
857	789	2	Miasto Gorzów Wielkopolski
858	857	3	Miasto Gorzów Wielkopolski
859	789	2	Miasto Zielona Góra
860	859	3	Miasto Zielona Góra
861	800	3	Małomice
862	800	3	Małomice - miasto
863	800	3	Małomice - obszar wiejski
864	796	3	Maszewo
865	790	3	Międzyrzecz
866	790	3	Międzyrzecz - miasto
867	790	3	Międzyrzecz - obszar wiejski
868	800	3	Niegosławice
869	803	3	Nowa Sól
870	803	3	Nowe Miasteczko
871	803	3	Nowe Miasteczko - miasto
872	803	3	Nowe Miasteczko - obszar wiejski
873	794	3	Nowogród Bobrzański
874	794	3	Nowogród Bobrzański - miasto
875	794	3	Nowogród Bobrzański - obszar wiejski
876	807	3	Ośno Lubuskie
877	807	3	Ośno Lubuskie - miasto
878	807	3	Ośno Lubuskie - obszar wiejski
879	803	3	Otyń
880	798	3	Przewóz
881	790	3	Przytoczna
882	790	3	Pszczew
883	807	3	Rzepin
884	807	3	Rzepin - miasto
885	807	3	Rzepin - obszar wiejski
886	792	3	Santok
887	803	3	Siedlisko
888	850	3	Skąpe
889	790	3	Skwierzyna
890	790	3	Skwierzyna - miasto
891	790	3	Skwierzyna - obszar wiejski
892	803	3	Sława
893	803	3	Sława - miasto
894	803	3	Sława - obszar wiejski
895	844	3	Słońsk
896	807	3	Słubice
897	807	3	Słubice - miasto
898	807	3	Słubice - obszar wiejski
899	816	3	Stare Kurowo
900	816	3	Strzelce Krajeńskie
901	816	3	Strzelce Krajeńskie - miasto
902	816	3	Strzelce Krajeńskie - obszar wiejski
903	794	3	Sulechów
904	794	3	Sulechów - miasto
905	794	3	Sulechów - obszar wiejski
906	844	3	Sulęcin
907	844	3	Sulęcin - miasto
908	844	3	Sulęcin - obszar wiejski
909	850	3	Szczaniec
910	803	3	Szlichtyngowa
911	803	3	Szlichtyngowa - miasto
912	803	3	Szlichtyngowa - obszar wiejski
913	800	3	Szprotawa
914	800	3	Szprotawa - miasto
915	800	3	Szprotawa - obszar wiejski
916	794	3	Świdnica
917	850	3	Świebodzin
918	850	3	Świebodzin - miasto
919	850	3	Świebodzin - obszar wiejski
920	844	3	Torzym
921	844	3	Torzym - miasto
922	844	3	Torzym - obszar wiejski
923	790	3	Trzciel
924	790	3	Trzciel - miasto
925	790	3	Trzciel - obszar wiejski
926	794	3	Trzebiechów
927	798	3	Trzebiel
928	798	3	Tuplice
929	792	3	Witnica
930	792	3	Witnica - miasto
931	792	3	Witnica - obszar wiejski
932	803	3	Wschowa
933	803	3	Wschowa - miasto
934	803	3	Wschowa - obszar wiejski
935	800	3	Wymiarki
936	794	3	Zabór
937	850	3	Zbąszynek
938	850	3	Zbąszynek - miasto
939	850	3	Zbąszynek - obszar wiejski
940	794	3	Zielona Góra
941	816	3	Zwierzyn
942	800	3	Żagań
943	798	3	Żary
944	794	3	Babimost
945	794	3	Babimost - miasto
946	794	3	Babimost - obszar wiejski
947	\N	1	Łódzkie
948	947	2	Piotrkowski
949	948	3	Aleksandrów
950	947	2	Kutnowski
951	950	3	Bedlno
952	947	2	Bełchatowski
953	952	3	Bełchatów
954	947	2	Tomaszowski
955	954	3	Będków
956	947	2	Wieluński
957	956	3	Biała
958	947	2	Rawski
959	958	3	Biała Rawska
960	958	3	Biała Rawska - miasto
961	958	3	Biała Rawska - obszar wiejski
962	947	2	Opoczyński
963	962	3	Białaczów
964	947	2	Łowicki
965	964	3	Bielawy
966	947	2	Sieradzki
967	966	3	Błaszki
968	966	3	Błaszki - miasto
969	966	3	Błaszki - obszar wiejski
970	947	2	Wieruszowski
971	970	3	Bolesławiec
972	947	2	Skierniewicki
973	972	3	Bolimów
974	966	3	Brąszewice
975	947	2	Łódzki Wschodni
976	975	3	Brójce
977	975	3	Brzeziny
978	966	3	Brzeźnio
979	947	2	Łaski
980	979	3	Buczek
981	954	3	Budziszewice
982	966	3	Burzenin
983	964	3	Chąśno
984	958	3	Cielądz
985	948	3	Czarnocin
986	956	3	Czarnożyły
987	970	3	Czastary
988	954	3	Czerniewice
989	947	2	Poddębicki
990	989	3	Dalików
991	947	2	Łęczycki
992	991	3	Daszyna
993	950	3	Dąbrowice
994	947	2	Pabianicki
995	994	3	Dłutów
996	975	3	Dmosin
997	994	3	Dobroń
998	947	2	Radomszczański
999	998	3	Dobryszyce
1000	964	3	Domaniewice
1001	952	3	Drużbice
1002	962	3	Drzewica
1003	962	3	Drzewica - miasto
1004	962	3	Drzewica - obszar wiejski
1005	947	2	Pajęczański
1006	1005	3	Działoszyn
1007	1005	3	Działoszyn - miasto
1008	1005	3	Działoszyn - obszar wiejski
1009	970	3	Galewice
1010	998	3	Gidle
1011	947	2	Zgierski
1012	1011	3	Głowno
1013	972	3	Głuchów
1014	972	3	Godzianów
1015	998	3	Gomunice
1016	948	3	Gorzkowice
1017	966	3	Goszczanów
1018	991	3	Góra Świętej Małgorzaty
1019	948	3	Grabica
1020	991	3	Grabów
1021	954	3	Inowłódz
1022	975	3	Jeżów
1023	998	3	Kamieńsk
1024	998	3	Kamieńsk - miasto
1025	998	3	Kamieńsk - obszar wiejski
1026	1005	3	Kiełczygłów
1027	964	3	Kiernozia
1028	952	3	Kleszczów
1029	966	3	Klonowa
1030	952	3	Kluki
1031	998	3	Kobiele Wielkie
1032	964	3	Kocierzew Południowy
1033	998	3	Kodrąb
1034	975	3	Koluszki
1035	975	3	Koluszki - miasto
1036	975	3	Koluszki - obszar wiejski
1037	956	3	Konopnica
1038	994	3	Konstantynów Łódzki
1039	972	3	Kowiesy
1040	950	3	Krośniewice
1041	950	3	Krośniewice - miasto
1042	950	3	Krośniewice - obszar wiejski
1043	950	3	Krzyżanów
1044	994	3	Ksawerów
1045	950	3	Kutno
1046	998	3	Lgota Wielka
1047	972	3	Lipce Reymontowskie
1048	954	3	Lubochnia
1049	994	3	Lutomiersk
1050	970	3	Lututów
1051	998	3	Ładzice
1052	950	3	Łanięta
1053	979	3	Łask
1054	979	3	Łask - miasto
1055	979	3	Łask - obszar wiejski
1056	991	3	Łęczyca
1057	948	3	Łęki Szlacheckie
1058	964	3	Łowicz
1059	947	2	Miasto Łódź
1060	1059	3	Łódź-Bałuty
1061	1059	3	Łódź-Górna
1062	1059	3	Łódź-Polesie
1063	1059	3	Łódź-Śródmieście
1064	1059	3	Łódź-Widzew
1065	970	3	Łubnice
1066	964	3	Łyszkowice
1067	1059	3	Miasto Łódź
1068	947	2	Miasto Piotrków Trybunalski
1069	1068	3	Miasto Piotrków Trybunalski
1070	947	2	Miasto Skierniewice
1071	1070	3	Miasto Skierniewice
1072	972	3	Maków
1073	998	3	Masłowice
1074	962	3	Mniszków
1075	956	3	Mokrsko
1076	948	3	Moszczenica
1077	964	3	Nieborów
1078	1005	3	Nowa Brzeźnica
1079	950	3	Nowe Ostrowy
1080	975	3	Nowosolna
1081	972	3	Nowy Kawęczyn
1082	962	3	Opoczno
1083	962	3	Opoczno - miasto
1084	962	3	Opoczno - obszar wiejski
1085	950	3	Oporów
1086	956	3	Osjaków
1087	956	3	Ostrówek
1088	1011	3	Ozorków
1089	994	3	Pabianice
1090	1005	3	Pajęczno
1091	1005	3	Pajęczno - miasto
1092	1005	3	Pajęczno - obszar wiejski
1093	962	3	Paradyż
1094	1011	3	Parzęczew
1095	956	3	Pątnów
1096	989	3	Pęczniew
1097	991	3	Piątek
1098	989	3	Poddębice
1099	989	3	Poddębice - miasto
1100	989	3	Poddębice - obszar wiejski
1101	962	3	Poświętne
1102	998	3	Przedbórz
1103	998	3	Przedbórz - miasto
1104	998	3	Przedbórz - obszar wiejski
1105	998	3	Radomsko
1106	958	3	Rawa Mazowiecka
1107	958	3	Regnów
1108	948	3	Ręczno
1109	975	3	Rogów
1110	954	3	Rokiciny
1111	948	3	Rozprza
1112	952	3	Rusiec
1113	1005	3	Rząśnia
1114	954	3	Rzeczyca
1115	975	3	Rzgów
1116	958	3	Sadkowice
1117	979	3	Sędziejowice
1118	1005	3	Siemkowice
1119	966	3	Sieradz
1120	972	3	Skierniewice
1121	956	3	Skomlin
1122	962	3	Sławno
1123	972	3	Słupia
1124	970	3	Sokolniki
1125	1011	3	Stryków
1126	1011	3	Stryków - miasto
1127	1011	3	Stryków - obszar wiejski
1128	950	3	Strzelce
1129	1005	3	Strzelce Wielkie
1130	948	3	Sulejów
1131	948	3	Sulejów - miasto
1132	948	3	Sulejów - obszar wiejski
1133	1005	3	Sulmierzyce
1134	947	2	Zduńskowolski
1135	1134	3	Szadek
1136	1134	3	Szadek - miasto
1137	1134	3	Szadek - obszar wiejski
1138	952	3	Szczerców
1139	991	3	Świnice Warckie
1140	954	3	Tomaszów Mazowiecki
1141	975	3	Tuszyn
1142	975	3	Tuszyn - miasto
1143	975	3	Tuszyn - obszar wiejski
1144	954	3	Ujazd
1145	989	3	Uniejów
1146	989	3	Uniejów - miasto
1147	989	3	Uniejów - obszar wiejski
1148	966	3	Warta
1149	966	3	Warta - miasto
1150	966	3	Warta - obszar wiejski
1151	989	3	Wartkowice
1152	979	3	Widawa
1153	998	3	Wielgomłyny
1154	956	3	Wieluń
1155	956	3	Wieluń - miasto
1156	956	3	Wieluń - obszar wiejski
1157	970	3	Wieruszów
1158	970	3	Wieruszów - miasto
1159	970	3	Wieruszów - obszar wiejski
1160	956	3	Wierzchlas
1161	991	3	Witonia
1162	979	3	Wodzierady
1163	948	3	Wola Krzysztoporska
1164	948	3	Wolbórz
1165	966	3	Wróblew
1166	989	3	Zadzim
1167	1134	3	Zapolice
1168	964	3	Zduny
1169	1134	3	Zduńska Wola
1170	952	3	Zelów
1171	952	3	Zelów - miasto
1172	952	3	Zelów - obszar wiejski
1173	1011	3	Zgierz
1174	966	3	Złoczew
1175	966	3	Złoczew - miasto
1176	966	3	Złoczew - obszar wiejski
1177	962	3	Żarnów
1178	954	3	Żelechlinek
1179	950	3	Żychlin
1180	950	3	Żychlin - miasto
1181	950	3	Żychlin - obszar wiejski
1182	998	3	Żytno
1183	1011	3	Aleksandrów Łódzki
1184	1011	3	Aleksandrów Łódzki - miasto
1185	1011	3	Aleksandrów Łódzki - obszar wiejski
1186	975	3	Andrespol
1187	\N	1	Małopolskie
1188	1187	2	Tatrzański
1189	1188	3	Biały Dunajec
1190	1187	2	Gorlicki
1191	1190	3	Biecz
1192	1190	3	Biecz - miasto
1193	1190	3	Biecz - obszar wiejski
1194	1187	2	Wielicki
1195	1194	3	Biskupice
1196	1190	3	Bobowa
1197	1187	2	Bocheński
1198	1197	3	Bochnia
1199	1187	2	Dąbrowski
1200	1199	3	Bolesław
1201	1187	2	Olkuski
1202	1201	3	Bolesław
1203	1187	2	Brzeski
1204	1203	3	Borzęcin
1205	1203	3	Brzesko
1206	1203	3	Brzesko - miasto
1207	1203	3	Brzesko - obszar wiejski
1208	1187	2	Oświęcimski
1209	1208	3	Brzeszcze
1210	1208	3	Brzeszcze - miasto
1211	1208	3	Brzeszcze - obszar wiejski
1212	1187	2	Wadowicki
1213	1212	3	Brzeźnica
1214	1187	2	Suski
1215	1214	3	Budzów
1216	1188	3	Bukowina Tatrzańska
1217	1201	3	Bukowno
1218	1214	3	Bystra-Sidzina
1219	1187	2	Miechowski
1220	1219	3	Charsznica
1221	1208	3	Chełmek
1222	1208	3	Chełmek - miasto
1223	1208	3	Chełmek - obszar wiejski
1224	1187	2	Nowosądecki
1225	1224	3	Chełmiec
1226	1187	2	Chrzanowski
1227	1226	3	Chrzanów
1228	1226	3	Chrzanów - miasto
1229	1226	3	Chrzanów - obszar wiejski
1230	1187	2	Tarnowski
1231	1230	3	Ciężkowice
1232	1230	3	Ciężkowice - miasto
1233	1230	3	Ciężkowice - obszar wiejski
1234	1187	2	Nowotarski
1235	1234	3	Czarny Dunajec
1236	1203	3	Czchów
1237	1187	2	Krakowski
1238	1237	3	Czernichów
1239	1234	3	Czorsztyn
1240	1199	3	Dąbrowa Tarnowska
1241	1199	3	Dąbrowa Tarnowska - miasto
1242	1199	3	Dąbrowa Tarnowska - obszar wiejski
1243	1203	3	Dębno
1244	1187	2	Myślenicki
1245	1244	3	Dobczyce
1246	1244	3	Dobczyce - miasto
1247	1244	3	Dobczyce - obszar wiejski
1248	1187	2	Limanowski
1249	1248	3	Dobra
1250	1197	3	Drwinia
1251	1194	3	Gdów
1252	1203	3	Gnojnik
1253	1219	3	Gołcza
1254	1190	3	Gorlice
1255	1199	3	Gręboszów
1256	1230	3	Gromnik
1257	1224	3	Gródek nad Dunajcem
1258	1224	3	Grybów
1259	1237	3	Igołomia-Wawrzeńczyce
1260	1237	3	Iwanowice
1261	1203	3	Iwkowa
1262	1234	3	Jabłonka
1263	1237	3	Jerzmanowice-Przeginia
1264	1248	3	Jodłownik
1265	1214	3	Jordanów
1266	1212	3	Kalwaria Zebrzydowska
1267	1212	3	Kalwaria Zebrzydowska - miasto
1268	1212	3	Kalwaria Zebrzydowska - obszar wiejski
1269	1248	3	Kamienica
1270	1224	3	Kamionka Wielka
1271	1208	3	Kęty
1272	1208	3	Kęty - miasto
1273	1208	3	Kęty - obszar wiejski
1274	1201	3	Klucze
1275	1194	3	Kłaj
1276	1237	3	Kocmyrzów-Luborzyca
1277	1187	2	Proszowicki
1278	1277	3	Koniusza
1279	1224	3	Korzenna
1280	1277	3	Koszyce
1281	1188	3	Kościelisko
1282	1219	3	Kozłów
1283	1187	2	Miasto Kraków
1284	1283	3	Kraków-Krowodrza
1285	1283	3	Kraków-Nowa Huta
1286	1283	3	Kraków-Podgórze
1287	1283	3	Kraków-Śródmieście
1288	1234	3	Krościenko nad Dunajcem
1289	1224	3	Krynica
1290	1224	3	Krynica - miasto
1291	1224	3	Krynica - obszar wiejski
1292	1237	3	Krzeszowice
1293	1237	3	Krzeszowice - miasto
1294	1237	3	Krzeszowice - obszar wiejski
1295	1219	3	Książ Wielki
1296	1212	3	Lanckorona
1297	1248	3	Laskowa
1298	1226	3	Libiąż
1299	1226	3	Libiąż - miasto
1300	1226	3	Libiąż - obszar wiejski
1301	1248	3	Limanowa
1302	1190	3	Lipinki
1303	1197	3	Lipnica Murowana
1304	1234	3	Lipnica Wielka
1305	1230	3	Lisia Góra
1306	1237	3	Liszki
1307	1244	3	Lubień
1308	1224	3	Łabowa
1309	1197	3	Łapanów
1310	1234	3	Łapsze Niżne
1311	1224	3	Łącko
1312	1224	3	Łososina Dolna
1313	1248	3	Łukowica
1314	1190	3	Łużna
1315	1283	3	Miasto Kraków
1316	1187	2	Miasto Nowy Sącz
1317	1316	3	Miasto Nowy Sącz
1318	1187	2	Miasto Tarnów
1319	1318	3	Miasto Tarnów
1320	1214	3	Maków Podhalański
1321	1214	3	Maków Podhalański - miasto
1322	1214	3	Maków Podhalański - obszar wiejski
1323	1199	3	Mędrzechów
1324	1237	3	Michałowice
1325	1219	3	Miechów
1326	1219	3	Miechów - miasto
1327	1219	3	Miechów - obszar wiejski
1328	1237	3	Mogilany
1329	1190	3	Moszczenica
1330	1248	3	Mszana Dolna
1331	1212	3	Mucharz
1332	1224	3	Muszyna
1333	1224	3	Muszyna - miasto
1334	1224	3	Muszyna - obszar wiejski
1335	1244	3	Myślenice
1336	1244	3	Myślenice - miasto
1337	1244	3	Myślenice - obszar wiejski
1338	1224	3	Nawojowa
1339	1248	3	Niedźwiedź
1340	1194	3	Niepołomice
1341	1194	3	Niepołomice - miasto
1342	1194	3	Niepołomice - obszar wiejski
1343	1277	3	Nowe Brzesko
1344	1234	3	Nowy Targ
1345	1197	3	Nowy Wiśnicz
1346	1197	3	Nowy Wiśnicz - miasto
1347	1197	3	Nowy Wiśnicz - obszar wiejski
1348	1234	3	Ochotnica Dolna
1349	1199	3	Olesno
1350	1201	3	Olkusz
1351	1201	3	Olkusz - miasto
1352	1201	3	Olkusz - obszar wiejski
1353	1208	3	Osiek
1354	1208	3	Oświęcim
1355	1277	3	Pałecznica
1356	1244	3	Pcim
1357	1224	3	Piwniczna
1358	1224	3	Piwniczna - miasto
1359	1224	3	Piwniczna - obszar wiejski
1360	1230	3	Pleśna
1361	1224	3	Podegrodzie
1362	1208	3	Polanka Wielka
1363	1188	3	Poronin
1364	1277	3	Proszowice
1365	1277	3	Proszowice - miasto
1366	1277	3	Proszowice - obszar wiejski
1367	1208	3	Przeciszów
1368	1234	3	Raba Wyżna
1369	1234	3	Rabka
1370	1234	3	Rabka - miasto
1371	1234	3	Rabka - obszar wiejski
1372	1244	3	Raciechowice
1373	1219	3	Racławice
1374	1199	3	Radgoszcz
1375	1230	3	Radłów
1376	1277	3	Radziemice
1377	1190	3	Ropa
1378	1230	3	Ryglice
1379	1224	3	Rytro
1380	1230	3	Rzepiennik Strzyżewski
1381	1197	3	Rzezawa
1382	1190	3	Sękowa
1383	1244	3	Siepraw
1384	1237	3	Skała
1385	1237	3	Skała - miasto
1386	1237	3	Skała - obszar wiejski
1387	1237	3	Skawina
1388	1237	3	Skawina - miasto
1389	1237	3	Skawina - obszar wiejski
1390	1230	3	Skrzyszów
1391	1219	3	Słaboszów
1392	1201	3	Sławków
1393	1237	3	Słomniki
1394	1237	3	Słomniki - miasto
1395	1237	3	Słomniki - obszar wiejski
1396	1248	3	Słopnice
1397	1234	3	Spytkowice
1398	1212	3	Spytkowice
1399	1224	3	Stary Sącz
1400	1224	3	Stary Sącz - miasto
1401	1224	3	Stary Sącz - obszar wiejski
1402	1214	3	Stryszawa
1403	1212	3	Stryszów
1404	1214	3	Sucha Beskidzka
1405	1244	3	Sułkowice
1406	1244	3	Sułkowice - miasto
1407	1244	3	Sułkowice - obszar wiejski
1408	1237	3	Sułoszowa
1409	1234	3	Szaflary
1410	1234	3	Szczawnica
1411	1199	3	Szczucin
1412	1203	3	Szczurowa
1413	1237	3	Świątniki Górne
1414	1237	3	Świątniki Górne - miasto
1415	1237	3	Świątniki Górne - obszar wiejski
1416	1230	3	Tarnów
1417	1244	3	Tokarnia
1418	1212	3	Tomice
1419	1197	3	Trzciana
1420	1226	3	Trzebinia
1421	1226	3	Trzebinia - miasto
1422	1226	3	Trzebinia - obszar wiejski
1423	1201	3	Trzyciąż
1424	1230	3	Tuchów
1425	1230	3	Tuchów - miasto
1426	1230	3	Tuchów - obszar wiejski
1427	1248	3	Tymbark
1428	1190	3	Uście Gorlickie
1429	1212	3	Wadowice
1430	1212	3	Wadowice - miasto
1431	1212	3	Wadowice - obszar wiejski
1432	1194	3	Wieliczka
1433	1194	3	Wieliczka - miasto
1434	1194	3	Wieliczka - obszar wiejski
1435	1237	3	Wielka Wieś
1436	1212	3	Wieprz
1437	1230	3	Wierzchosławice
1438	1230	3	Wietrzychowice
1439	1244	3	Wiśniowa
1440	1230	3	Wojnicz
1441	1201	3	Wolbrom
1442	1201	3	Wolbrom - miasto
1443	1201	3	Wolbrom - obszar wiejski
1444	1237	3	Zabierzów
1445	1230	3	Zakliczyn
1446	1188	3	Zakopane
1447	1208	3	Zator
1448	1208	3	Zator - miasto
1449	1208	3	Zator - obszar wiejski
1450	1214	3	Zawoja
1451	1214	3	Zembrzyce
1452	1237	3	Zielonki
1453	1230	3	Żabno
1454	1230	3	Żabno - miasto
1455	1230	3	Żabno - obszar wiejski
1456	1197	3	Żegocina
1457	1226	3	Alwernia
1458	1226	3	Alwernia - miasto
1459	1226	3	Alwernia - obszar wiejski
1460	1212	3	Andrychów
1461	1212	3	Andrychów - miasto
1462	1212	3	Andrychów - obszar wiejski
1463	1226	3	Babice
1464	\N	1	Mazowieckie
1465	1464	2	Grójecki
1466	1465	3	Belsk Duży
1467	1464	2	Białobrzeski
1468	1467	3	Białobrzegi
1469	1467	3	Białobrzegi - miasto
1470	1467	3	Białobrzegi - obszar wiejski
1471	1464	2	Sokołowski
1472	1471	3	Bielany
1473	1464	2	Płocki
1474	1473	3	Bielsk
1475	1464	2	Żuromiński
1476	1475	3	Bieżuń
1477	1475	3	Bieżuń - miasto
1478	1475	3	Bieżuń - obszar wiejski
1479	1465	3	Błędów
1480	1464	2	Warszawski Zachodni
1481	1480	3	Błonie
1482	1480	3	Błonie - miasto
1483	1480	3	Błonie - obszar wiejski
1484	1473	3	Bodzanów
1485	1464	2	Ostrowski
1486	1485	3	Boguty-Pianki
1487	1464	2	Przysuski
1488	1487	3	Borkowice
1489	1464	2	Garwoliński
1490	1489	3	Borowie
1491	1464	2	Wyszkowski
1492	1491	3	Brańszczyk
1493	1464	2	Sochaczewski
1494	1493	3	Brochów
1495	1485	3	Brok
1496	1485	3	Brok - miasto
1497	1485	3	Brok - obszar wiejski
1498	1473	3	Brudzeń Duży
1499	1464	2	Pruszkowski
1500	1499	3	Brwinów
1501	1499	3	Brwinów - miasto
1502	1499	3	Brwinów - obszar wiejski
1503	1473	3	Bulkowo
1504	1464	2	Miński
1505	1504	3	Cegłów
1506	1464	2	Otwocki
1507	1506	3	Celestynów
1508	1471	3	Ceranów
1509	1464	2	Szydłowiecki
1510	1509	3	Chlewiska
1511	1464	2	Przasnyski
1512	1511	3	Chorzele
1513	1511	3	Chorzele - miasto
1514	1511	3	Chorzele - obszar wiejski
1515	1464	2	Lipski
1516	1515	3	Chotcza
1517	1465	3	Chynów
1518	1464	2	Ciechanowski
1519	1518	3	Ciechanów
1520	1515	3	Ciepielów
1521	1464	2	Ostrołęcki
1522	1521	3	Czarnia
1523	1511	3	Czernice Borowe
1524	1521	3	Czerwin
1525	1464	2	Płoński
1526	1525	3	Czerwińsk nad Wisłą
1527	1464	2	Makowski
1528	1527	3	Czerwonka
1529	1464	2	Nowodworski
1530	1529	3	Czosnów
1531	1464	2	Wołomiński
1532	1531	3	Dąbrówka
1533	1504	3	Dębe Wielkie
1534	1491	3	Długosiodło
1535	1504	3	Dobre
1536	1464	2	Siedlecki
1537	1536	3	Domanice
1538	1473	3	Drobin
1539	1473	3	Drobin - miasto
1540	1473	3	Drobin - obszar wiejski
1541	1525	3	Dzierzążnia
1542	1464	2	Mławski
1543	1542	3	Dzierzgowo
1544	1464	2	Kozienicki
1545	1544	3	Garbatka-Letnisko
1546	1489	3	Garwolin
1547	1473	3	Gąbin
1548	1473	3	Gąbin - miasto
1549	1473	3	Gąbin - obszar wiejski
1550	1487	3	Gielniów
1551	1518	3	Glinojeck
1552	1518	3	Glinojeck - miasto
1553	1518	3	Glinojeck - obszar wiejski
1554	1544	3	Głowaczów
1555	1544	3	Gniewoszów
1556	1518	3	Gołymin-Ośrodek
1557	1464	2	Gostyniński
1558	1557	3	Gostynin
1559	1465	3	Goszczyn
1560	1521	3	Goworowo
1561	1464	2	Sierpecki
1562	1561	3	Gozdowo
1563	1464	2	Piaseczyński
1564	1563	3	Góra Kalwaria
1565	1563	3	Góra Kalwaria - miasto
1566	1563	3	Góra Kalwaria - obszar wiejski
1567	1489	3	Górzno
1568	1464	2	Radomski
1569	1568	3	Gózd
1570	1544	3	Grabów nad Pilicą
1571	1464	2	Węgrowski
1572	1571	3	Grębków
1573	1464	2	Grodziski
1574	1573	3	Grodzisk Mazowiecki
1575	1573	3	Grodzisk Mazowiecki - miasto
1576	1573	3	Grodzisk Mazowiecki - obszar wiejski
1577	1465	3	Grójec
1578	1465	3	Grójec - miasto
1579	1465	3	Grójec - obszar wiejski
1580	1518	3	Grudusk
1581	1464	2	Pułtuski
1582	1581	3	Gzy
1583	1504	3	Halinów
1584	1464	2	Łosicki
1585	1584	3	Huszlew
1586	1493	3	Iłów
1587	1568	3	Iłża
1588	1568	3	Iłża - miasto
1589	1568	3	Iłża - obszar wiejski
1590	1480	3	Izabelin
1591	1464	2	Legionowski
1592	1591	3	Jabłonna
1593	1471	3	Jabłonna Lacka
1594	1531	3	Jadów
1595	1573	3	Jaktorów
1596	1504	3	Jakubów
1597	1465	3	Jasieniec
1598	1509	3	Jastrząb
1599	1568	3	Jastrzębia
1600	1568	3	Jedlińsk
1601	1568	3	Jedlnia-Letnisko
1602	1511	3	Jednorożec
1603	1525	3	Joniec
1604	1506	3	Józefów
1605	1521	3	Kadzidło
1606	1504	3	Kałuszyn
1607	1504	3	Kałuszyn - miasto
1608	1504	3	Kałuszyn - obszar wiejski
1609	1480	3	Kampinos
1610	1506	3	Karczew
1611	1506	3	Karczew - miasto
1612	1506	3	Karczew - obszar wiejski
1613	1527	3	Karniewo
1614	1464	2	Zwoleński
1615	1614	3	Kazanów
1616	1531	3	Klembów
1617	1487	3	Klwów
1618	1531	3	Kobyłka
1619	1506	3	Kołbiel
1620	1563	3	Konstancin-Jeziorna
1621	1563	3	Konstancin-Jeziorna - miasto
1622	1563	3	Konstancin-Jeziorna - obszar wiejski
1623	1536	3	Korczew
1624	1571	3	Korytnica
1625	1471	3	Kosów Lacki
1626	1536	3	Kotuń
1627	1568	3	Kowala
1628	1544	3	Kozienice
1629	1544	3	Kozienice - miasto
1630	1544	3	Kozienice - obszar wiejski
1631	1511	3	Krasne
1632	1527	3	Krasnosielc
1633	1511	3	Krzynowłoga Mała
1634	1475	3	Kuczbork-Osada
1635	1504	3	Latowicz
1636	1591	3	Legionowo
1637	1521	3	Lelis
1638	1529	3	Leoncin
1639	1480	3	Leszno
1640	1563	3	Lesznowola
1641	1542	3	Lipowiec Kościelny
1642	1515	3	Lipsko
1643	1515	3	Lipsko - miasto
1644	1515	3	Lipsko - obszar wiejski
1645	1571	3	Liw
1646	1475	3	Lubowidz
1647	1475	3	Lutocin
1648	1489	3	Łaskarzew
1649	1473	3	Łąck
1650	1571	3	Łochów
1651	1571	3	Łochów - miasto
1652	1571	3	Łochów - obszar wiejski
1653	1480	3	Łomianki
1654	1480	3	Łomianki - miasto
1655	1480	3	Łomianki - obszar wiejski
1656	1584	3	Łosice
1657	1584	3	Łosice - miasto
1658	1584	3	Łosice - obszar wiejski
1659	1521	3	Łyse
1660	1464	2	Miasto Ostrołęka
1661	1660	3	Miasto Ostrołęka
1662	1464	2	Miasto Płock
1663	1662	3	Miasto Płock
1664	1464	2	Miasto Radom
1665	1664	3	Miasto Radom
1666	1464	2	Miasto Siedlce
1667	1666	3	Miasto Siedlce
1668	1489	3	Maciejowice
1669	1544	3	Magnuszew
1670	1527	3	Maków Mazowiecki
1671	1473	3	Mała Wieś
1672	1485	3	Małkinia Górna
1673	1531	3	Marki
1674	1489	3	Miastków Kościelny
1675	1499	3	Michałowice
1676	1571	3	Miedzna
1677	1573	3	Milanówek
1678	1504	3	Mińsk Mazowiecki
1679	1509	3	Mirów
1680	1542	3	Mława
1681	1493	3	Młodzieszyn
1682	1527	3	Młynarze
1683	1561	3	Mochowo
1684	1465	3	Mogielnica
1685	1465	3	Mogielnica - miasto
1686	1465	3	Mogielnica - obszar wiejski
1687	1536	3	Mokobody
1688	1536	3	Mordy
1689	1536	3	Mordy - miasto
1690	1536	3	Mordy - obszar wiejski
1691	1504	3	Mrozy
1692	1464	2	Żyrardowski
1693	1692	3	Mszczonów
1694	1692	3	Mszczonów - miasto
1695	1692	3	Mszczonów - obszar wiejski
1696	1521	3	Myszyniec
1697	1521	3	Myszyniec - miasto
1698	1521	3	Myszyniec - obszar wiejski
1699	1499	3	Nadarzyn
1700	1525	3	Naruszewo
1701	1529	3	Nasielsk
1702	1529	3	Nasielsk - miasto
1703	1529	3	Nasielsk - obszar wiejski
1704	1591	3	Nieporęt
1705	1493	3	Nowa Sucha
1706	1525	3	Nowe Miasto
1707	1465	3	Nowe Miasto nad Pilicą
1708	1465	3	Nowe Miasto nad Pilicą - miasto
1709	1465	3	Nowe Miasto nad Pilicą - obszar wiejski
1710	1473	3	Nowy Duninów
1711	1529	3	Nowy Dwór Mazowiecki
1712	1485	3	Nur
1713	1581	3	Obryte
1714	1487	3	Odrzywół
1715	1518	3	Ojrzeń
1716	1584	3	Olszanka
1717	1521	3	Olszewo-Borki
1718	1518	3	Opinogóra Górna
1719	1509	3	Orońsko
1720	1506	3	Osieck
1721	1485	3	Ostrów Mazowiecka
1722	1506	3	Otwock
1723	1480	3	Ożarów Mazowiecki
1724	1480	3	Ożarów Mazowiecki - miasto
1725	1480	3	Ożarów Mazowiecki - obszar wiejski
1726	1557	3	Pacyna
1727	1536	3	Paprotnia
1728	1489	3	Parysów
1729	1563	3	Piaseczno
1730	1563	3	Piaseczno - miasto
1731	1563	3	Piaseczno - obszar wiejski
1732	1499	3	Piastów
1733	1489	3	Pilawa
1734	1489	3	Pilawa - miasto
1735	1489	3	Pilawa - obszar wiejski
1736	1568	3	Pionki
1737	1584	3	Platerów
1738	1527	3	Płoniawy-Bramura
1739	1525	3	Płońsk
1740	1465	3	Pniewy
1741	1573	3	Podkowa Leśna
1742	1581	3	Pokrzywnica
1743	1614	3	Policzna
1744	1529	3	Pomiechówek
1745	1531	3	Poświętne
1746	1487	3	Potworów
1747	1563	3	Prażmów
1748	1467	3	Promna
1749	1499	3	Pruszków
1750	1511	3	Przasnysz
1751	1536	3	Przesmyki
1752	1614	3	Przyłęk
1753	1487	3	Przysucha
1754	1487	3	Przysucha - miasto
1755	1487	3	Przysucha - obszar wiejski
1756	1568	3	Przytyk
1757	1581	3	Pułtusk
1758	1581	3	Pułtusk - miasto
1759	1581	3	Pułtusk - obszar wiejski
1760	1692	3	Puszcza Mariańska
1761	1525	3	Raciąż
1762	1473	3	Radzanowo
1763	1467	3	Radzanów
1764	1542	3	Radzanów
1765	1692	3	Radziejowice
1766	1531	3	Radzymin
1767	1531	3	Radzymin - miasto
1768	1531	3	Radzymin - obszar wiejski
1769	1499	3	Raszyn
1770	1518	3	Regimin
1771	1471	3	Repki
1772	1561	3	Rościszewo
1773	1527	3	Różan
1774	1527	3	Różan - miasto
1775	1527	3	Różan - obszar wiejski
1776	1487	3	Rusinów
1777	1493	3	Rybno
1778	1491	3	Rząśnik
1779	1515	3	Rzeczniów
1780	1521	3	Rzekuń
1781	1527	3	Rzewnie
1782	1471	3	Sabnie
1783	1571	3	Sadowne
1784	1557	3	Sanniki
1785	1584	3	Sarnaki
1786	1591	3	Serock
1787	1591	3	Serock - miasto
1788	1591	3	Serock - obszar wiejski
1789	1544	3	Sieciechów
1790	1536	3	Siedlce
1791	1475	3	Siemiątkowo Koziebrodzkie
1792	1504	3	Siennica
1793	1515	3	Sienno
1794	1561	3	Sierpc
1795	1568	3	Skaryszew
1796	1568	3	Skaryszew - miasto
1797	1568	3	Skaryszew - obszar wiejski
1798	1536	3	Skórzec
1799	1473	3	Słubice
1800	1473	3	Słupno
1801	1506	3	Sobienie-Jeziory
1802	1489	3	Sobolew
1803	1493	3	Sochaczew
1804	1525	3	Sochocin
1805	1471	3	Sokołów Podlaski
1806	1515	3	Solec nad Wisłą
1807	1491	3	Somianka
1808	1518	3	Sońsk
1809	1504	3	Stanisławów
1810	1473	3	Stara Biała
1811	1467	3	Stara Błotnica
1812	1584	3	Stara Kornica
1813	1480	3	Stare Babice
1814	1473	3	Staroźreby
1815	1485	3	Stary Lubotyń
1816	1471	3	Sterdyń
1817	1571	3	Stoczek
1818	1531	3	Strachówka
1819	1467	3	Stromiec
1820	1542	3	Strzegowo
1821	1542	3	Stupsk
1822	1536	3	Suchożebry
1823	1504	3	Sulejówek
1824	1527	3	Sypniewo
1825	1557	3	Szczawin Kościelny
1826	1561	3	Szczutowo
1827	1527	3	Szelków
1828	1542	3	Szreńsk
1829	1485	3	Szulborze Wielkie
1830	1509	3	Szydłowiec
1831	1509	3	Szydłowiec - miasto
1832	1509	3	Szydłowiec - obszar wiejski
1833	1542	3	Szydłowo
1834	1581	3	Świercze
1835	1465	3	Tarczyn
1836	1614	3	Tczów
1837	1493	3	Teresin
1838	1531	3	Tłuszcz
1839	1531	3	Tłuszcz - miasto
1840	1531	3	Tłuszcz - obszar wiejski
1841	1489	3	Trojanów
1842	1521	3	Troszyn
1843	1465	3	Warka
1844	1465	3	Warka - miasto
1845	1465	3	Warka - obszar wiejski
1846	1464	2	Warszawski
1847	1846	3	Warszawa - Bemowo
1848	1846	3	Warszawa - Białołęka
1849	1846	3	Warszawa - Bielany
1850	1846	3	Warszawa - Centrum
1851	1846	3	Warszawa - Mokotów
1852	1846	3	Warszawa - Ochota
1853	1846	3	Warszawa - Praga Południe
1854	1846	3	Warszawa - Praga Północ
1855	1846	3	Warszawa - Rembertów
1856	1846	3	Warszawa - Śródmieście
1857	1846	3	Warszawa - Targówek
1858	1846	3	Warszawa - Ursus
1859	1846	3	Warszawa - Ursynów
1860	1846	3	Warszawa - Wawer
1861	1846	3	Warszawa - Wilanów
1862	1846	3	Warszawa - Włochy
1863	1846	3	Warszawa - Wola
1864	1846	3	Warszawa - Żoliborz
1865	1485	3	Wąsewo
1866	1504	3	Wesoła
1867	1571	3	Węgrów
1868	1506	3	Wiązowna
1869	1542	3	Wieczfnia Kościelna
1870	1591	3	Wieliszew
1871	1487	3	Wieniawa
1872	1568	3	Wierzbica
1873	1571	3	Wierzbno
1874	1489	3	Wilga
1875	1581	3	Winnica
1876	1692	3	Wiskitki
1877	1536	3	Wiśniew
1878	1542	3	Wiśniewo
1879	1536	3	Wodynie
1880	1568	3	Wolanów
1881	1531	3	Wołomin
1882	1531	3	Wołomin - miasto
1883	1531	3	Wołomin - obszar wiejski
1884	1491	3	Wyszków
1885	1491	3	Wyszków - miasto
1886	1491	3	Wyszków - obszar wiejski
1887	1473	3	Wyszogród
1888	1473	3	Wyszogród - miasto
1889	1473	3	Wyszogród - obszar wiejski
1890	1467	3	Wyśmierzyce
1891	1467	3	Wyśmierzyce - miasto
1892	1467	3	Wyśmierzyce - obszar wiejski
1893	1491	3	Zabrodzie
1894	1529	3	Zakroczym
1895	1529	3	Zakroczym - miasto
1896	1529	3	Zakroczym - obszar wiejski
1897	1568	3	Zakrzew
1898	1525	3	Załuski
1899	1485	3	Zaręby Kościelne
1900	1581	3	Zatory
1901	1561	3	Zawidz
1902	1531	3	Ząbki
1903	1536	3	Zbuczyn Poduchowny
1904	1531	3	Zielonka
1905	1614	3	Zwoleń
1906	1614	3	Zwoleń - miasto
1907	1614	3	Zwoleń - obszar wiejski
1908	1573	3	Żabia Wola
1909	1489	3	Żelechów
1910	1489	3	Żelechów - miasto
1911	1489	3	Żelechów - obszar wiejski
1912	1475	3	Żuromin
1913	1475	3	Żuromin - miasto
1914	1475	3	Żuromin - obszar wiejski
1915	1692	3	Żyrardów
1916	1485	3	Andrzejewo
1917	1525	3	Baboszewo
1918	1521	3	Baranowo
1919	1573	3	Baranów
1920	\N	1	Opolskie
1921	1920	2	Prudnicki
1922	1921	3	Biała
1923	1921	3	Biała - miasto
1924	1921	3	Biała - obszar wiejski
1925	1920	2	Kędzierzyńsko-Kozielski
1926	1925	3	Bierawa
1927	1920	2	Głubczycki
1928	1927	3	Branice
1929	1920	2	Brzeski
1930	1929	3	Brzeg
1931	1920	2	Kluczborski
1932	1931	3	Byczyna
1933	1931	3	Byczyna - miasto
1934	1931	3	Byczyna - obszar wiejski
1935	1920	2	Opolski
1936	1935	3	Chrząstowice
1937	1925	3	Cisek
1938	1935	3	Dąbrowa
1939	1920	2	Oleski
1940	1939	3	Dobrodzień
1941	1939	3	Dobrodzień - miasto
1942	1939	3	Dobrodzień - obszar wiejski
1943	1935	3	Dobrzeń Wielki
1944	1920	2	Namysłowski
1945	1944	3	Domaszowice
1946	1921	3	Głogówek
1947	1921	3	Głogówek - miasto
1948	1921	3	Głogówek - obszar wiejski
1949	1927	3	Głubczyce
1950	1927	3	Głubczyce - miasto
1951	1927	3	Głubczyce - obszar wiejski
1952	1920	2	Nyski
1953	1952	3	Głuchołazy
1954	1952	3	Głuchołazy - miasto
1955	1952	3	Głuchołazy - obszar wiejski
1956	1920	2	Krapkowicki
1957	1956	3	Gogolin
1958	1956	3	Gogolin - miasto
1959	1956	3	Gogolin - obszar wiejski
1960	1939	3	Gorzów Śląski
1961	1939	3	Gorzów Śląski - miasto
1962	1939	3	Gorzów Śląski - obszar wiejski
1963	1929	3	Grodków
1964	1929	3	Grodków - miasto
1965	1929	3	Grodków - obszar wiejski
1966	1920	2	Strzelecki
1967	1966	3	Izbicko
1968	1966	3	Jemielnica
1969	1952	3	Kamiennik
1970	1925	3	Kędzierzyn-Koźle
1971	1927	3	Kietrz
1972	1927	3	Kietrz - miasto
1973	1927	3	Kietrz - obszar wiejski
1974	1931	3	Kluczbork
1975	1931	3	Kluczbork - miasto
1976	1931	3	Kluczbork - obszar wiejski
1977	1966	3	Kolonowskie
1978	1966	3	Kolonowskie - miasto
1979	1966	3	Kolonowskie - obszar wiejski
1980	1935	3	Komprachcice
1981	1952	3	Korfantów
1982	1952	3	Korfantów - miasto
1983	1952	3	Korfantów - obszar wiejski
1984	1956	3	Krapkowice
1985	1956	3	Krapkowice - miasto
1986	1956	3	Krapkowice - obszar wiejski
1987	1931	3	Lasowice Wielkie
1988	1966	3	Leśnica
1989	1966	3	Leśnica - miasto
1990	1966	3	Leśnica - obszar wiejski
1991	1929	3	Lewin Brzeski
1992	1929	3	Lewin Brzeski - miasto
1993	1929	3	Lewin Brzeski - obszar wiejski
1994	1921	3	Lubrza
1995	1929	3	Lubsza
1996	1952	3	Łambinowice
1997	1935	3	Łubniany
1998	1920	2	Miasto Opole
1999	1998	3	Miasto Opole
2000	1935	3	Murów
2001	1944	3	Namysłów
2002	1944	3	Namysłów - miasto
2003	1944	3	Namysłów - obszar wiejski
2004	1935	3	Niemodlin
2005	1935	3	Niemodlin - miasto
2006	1935	3	Niemodlin - obszar wiejski
2007	1952	3	Nysa
2008	1952	3	Nysa - miasto
2009	1952	3	Nysa - obszar wiejski
2010	1939	3	Olesno
2011	1939	3	Olesno - miasto
2012	1939	3	Olesno - obszar wiejski
2013	1929	3	Olszanka
2014	1952	3	Otmuchów
2015	1952	3	Otmuchów - miasto
2016	1952	3	Otmuchów - obszar wiejski
2017	1935	3	Ozimek
2018	1935	3	Ozimek - miasto
2019	1935	3	Ozimek - obszar wiejski
2020	1952	3	Paczków
2021	1952	3	Paczków - miasto
2022	1952	3	Paczków - obszar wiejski
2023	1952	3	Pakosławice
2024	1925	3	Pawłowiczki
2025	1944	3	Pokój
2026	1925	3	Polska Cerekiew
2027	1935	3	Popielów
2028	1939	3	Praszka
2029	1939	3	Praszka - miasto
2030	1939	3	Praszka - obszar wiejski
2031	1935	3	Prószków
2032	1921	3	Prudnik
2033	1921	3	Prudnik - miasto
2034	1921	3	Prudnik - obszar wiejski
2035	1939	3	Radłów
2036	1925	3	Reńska Wieś
2037	1939	3	Rudniki
2038	1952	3	Skoroszyce
2039	1966	3	Strzelce Opolskie
2040	1966	3	Strzelce Opolskie - miasto
2041	1966	3	Strzelce Opolskie - obszar wiejski
2042	1956	3	Strzeleczki
2043	1944	3	Świerczów
2044	1935	3	Tarnów Opolski
2045	1935	3	Tułowice
2046	1935	3	Turawa
2047	1966	3	Ujazd
2048	1966	3	Ujazd - miasto
2049	1966	3	Ujazd - obszar wiejski
2050	1956	3	Walce
2051	1944	3	Wilków
2052	1931	3	Wołczyn
2053	1931	3	Wołczyn - miasto
2054	1931	3	Wołczyn - obszar wiejski
2055	1966	3	Zawadzkie
2056	1966	3	Zawadzkie - miasto
2057	1966	3	Zawadzkie - obszar wiejski
2058	1956	3	Zdzieszowice
2059	1956	3	Zdzieszowice - miasto
2060	1956	3	Zdzieszowice - obszar wiejski
2061	1939	3	Zębowice
2062	1927	3	Baborów
2063	1927	3	Baborów - miasto
2064	1927	3	Baborów - obszar wiejski
2065	\N	1	Podkarpackie
2066	2065	2	Przeworski
2067	2066	3	Adamówka
2068	2065	2	Sanocki
2069	2068	3	Besko
2070	2065	2	Łańcucki
2071	2070	3	Białobrzegi
2072	2065	2	Przemyski
2073	2072	3	Bircza
2074	2065	2	Rzeszowski
2075	2074	3	Błażowa
2076	2074	3	Błażowa - miasto
2077	2074	3	Błażowa - obszar wiejski
2078	2074	3	Boguchwała
2079	2065	2	Stalowowolski
2080	2079	3	Bojanów
2081	2065	2	Mielecki
2082	2081	3	Borowa
2083	2065	2	Dębicki
2084	2083	3	Brzostek
2085	2065	2	Brzozowski
2086	2085	3	Brzozów
2087	2085	3	Brzozów - miasto
2088	2085	3	Brzozów - obszar wiejski
2089	2065	2	Jasielski
2090	2089	3	Brzyska
2091	2068	3	Bukowsko
2092	2065	2	Jarosławski
2093	2092	3	Chłopice
2094	2074	3	Chmielnik
2095	2065	2	Krośnieński
2096	2095	3	Chorkówka
2097	2065	2	Lubaczowski
2098	2097	3	Cieszanów
2099	2097	3	Cieszanów - miasto
2100	2097	3	Cieszanów - obszar wiejski
2101	2065	2	Bieszczadzki
2102	2101	3	Cisna
2103	2065	2	Kolbuszowski
2104	2103	3	Cmolas
2105	2101	3	Czarna
2106	2083	3	Czarna
2107	2070	3	Czarna
2108	2081	3	Czermin
2109	2065	2	Strzyżowski
2110	2109	3	Czudec
2111	2083	3	Dębica
2112	2089	3	Dębowiec
2113	2085	3	Domaradz
2114	2072	3	Dubiecko
2115	2095	3	Dukla
2116	2095	3	Dukla - miasto
2117	2095	3	Dukla - obszar wiejski
2118	2085	3	Dydnia
2119	2074	3	Dynów
2120	2072	3	Fredropol
2121	2109	3	Frysztak
2122	2066	3	Gać
2123	2081	3	Gawłuszowice
2124	2074	3	Głogów Małopolski
2125	2074	3	Głogów Małopolski - miasto
2126	2074	3	Głogów Małopolski - obszar wiejski
2127	2065	2	Tarnobrzeski
2128	2127	3	Gorzyce
2129	2127	3	Grębów
2130	2065	2	Leżajski
2131	2130	3	Grodzisko Dolne
2132	2085	3	Haczów
2133	2065	2	Niżański
2134	2133	3	Harasiuki
2135	2097	3	Horyniec
2136	2074	3	Hyżne
2137	2065	2	Ropczycko-Sędziszowski
2138	2137	3	Iwierzyce
2139	2095	3	Iwonicz -Zdrój
2140	2095	3	Iwonicz -Zdrój - miasto
2141	2095	3	Iwonicz -Zdrój - obszar wiejski
2142	2133	3	Jarocin
2143	2092	3	Jarosław
2144	2085	3	Jasienica Rosielna
2145	2089	3	Jasło
2146	2066	3	Jawornik Polski
2147	2095	3	Jedlicze
2148	2095	3	Jedlicze - miasto
2149	2095	3	Jedlicze - obszar wiejski
2150	2133	3	Jeżowe
2151	2083	3	Jodłowa
2152	2074	3	Kamień
2153	2066	3	Kańczuga
2154	2066	3	Kańczuga - miasto
2155	2066	3	Kańczuga - obszar wiejski
2156	2103	3	Kolbuszowa
2157	2103	3	Kolbuszowa - miasto
2158	2103	3	Kolbuszowa - obszar wiejski
2159	2089	3	Kołaczyce
2160	2068	3	Komańcza
2161	2095	3	Korczyna
2162	2072	3	Krasiczyn
2163	2074	3	Krasne
2164	2089	3	Krempna
2165	2095	3	Krościenko Wyżne
2166	2133	3	Krzeszów
2167	2072	3	Krzywcza
2168	2130	3	Kuryłówka
2169	2092	3	Laszki
2170	2101	3	Lesko
2171	2101	3	Lesko - miasto
2172	2101	3	Lesko - obszar wiejski
2173	2130	3	Leżajsk
2174	2097	3	Lubaczów
2175	2074	3	Lubenia
2176	2101	3	Lutowiska
2177	2070	3	Łańcut
2178	2065	2	Miasto Krosno
2179	2178	3	Miasto Krosno
2180	2065	2	Miasto Przemyśl
2181	2180	3	Miasto Przemyśl
2182	2065	2	Miasto Rzeszów
2183	2182	3	Miasto Rzeszów
2184	2065	2	Miasto Tarnobrzeg
2185	2184	3	Miasto Tarnobrzeg
2186	2103	3	Majdan Królewski
2187	2070	3	Markowa
2188	2072	3	Medyka
2189	2095	3	Miejsce Piastowe
2190	2081	3	Mielec
2191	2097	3	Narol
2192	2097	3	Narol - miasto
2193	2097	3	Narol - obszar wiejski
2194	2109	3	Niebylec
2195	2133	3	Nisko
2196	2133	3	Nisko - miasto
2197	2133	3	Nisko - obszar wiejski
2198	2103	3	Niwiska
2199	2127	3	Nowa Dęba
2200	2127	3	Nowa Dęba - miasto
2201	2127	3	Nowa Dęba - obszar wiejski
2202	2130	3	Nowa Sarzyna
2203	2130	3	Nowa Sarzyna - miasto
2204	2130	3	Nowa Sarzyna - obszar wiejski
2205	2089	3	Nowy Żmigród
2206	2085	3	Nozdrzec
2207	2097	3	Oleszyce
2208	2097	3	Oleszyce - miasto
2209	2097	3	Oleszyce - obszar wiejski
2210	2101	3	Olszanica
2211	2072	3	Orły
2212	2089	3	Osiek Jasielski
2213	2137	3	Ostrów
2214	2081	3	Padew Narodowa
2215	2092	3	Pawłosiów
2216	2083	3	Pilzno
2217	2083	3	Pilzno - miasto
2218	2083	3	Pilzno - obszar wiejski
2219	2092	3	Pruchnik
2220	2081	3	Przecław
2221	2072	3	Przemyśl
2222	2066	3	Przeworsk
2223	2079	3	Pysznica
2224	2079	3	Radomyśl
2225	2081	3	Radomyśl Wielki
2226	2081	3	Radomyśl Wielki - miasto
2227	2081	3	Radomyśl Wielki - obszar wiejski
2228	2092	3	Radymno
2229	2070	3	Rakszawa
2230	2103	3	Raniżów
2231	2092	3	Rokietnica
2232	2137	3	Ropczyce
2233	2137	3	Ropczyce - miasto
2234	2137	3	Ropczyce - obszar wiejski
2235	2092	3	Roźwienica
2236	2133	3	Rudnik nad Sanem
2237	2133	3	Rudnik nad Sanem - miasto
2238	2133	3	Rudnik nad Sanem - obszar wiejski
2239	2095	3	Rymanów
2240	2095	3	Rymanów - miasto
2241	2095	3	Rymanów - obszar wiejski
2242	2068	3	Sanok
2243	2137	3	Sędziszów Małopolski
2244	2137	3	Sędziszów Małopolski - miasto
2245	2137	3	Sędziszów Małopolski - obszar wiejski
2246	2066	3	Sieniawa
2247	2066	3	Sieniawa - miasto
2248	2066	3	Sieniawa - obszar wiejski
2249	2089	3	Skołyszyn
2250	2074	3	Sokołów Małopolski
2251	2074	3	Sokołów Małopolski - miasto
2252	2074	3	Sokołów Małopolski - obszar wiejski
2253	2101	3	Solina
2254	2079	3	Stalowa Wola
2255	2103	3	Stary Dzikowiec
2256	2097	3	Stary Dzików
2257	2109	3	Strzyżów
2258	2109	3	Strzyżów - miasto
2259	2109	3	Strzyżów - obszar wiejski
2260	2072	3	Stubno
2261	2089	3	Szerzyny
2262	2074	3	Świlcza
2263	2089	3	Tarnowiec
2264	2066	3	Tryńcza
2265	2074	3	Trzebownisko
2266	2081	3	Tuszów Narodowy
2267	2074	3	Tyczyn
2268	2074	3	Tyczyn - miasto
2269	2074	3	Tyczyn - obszar wiejski
2270	2068	3	Tyrawa Wołoska
2271	2133	3	Ulanów
2272	2133	3	Ulanów - miasto
2273	2133	3	Ulanów - obszar wiejski
2274	2101	3	Ustrzyki Dolne
2275	2101	3	Ustrzyki Dolne - miasto
2276	2101	3	Ustrzyki Dolne - obszar wiejski
2277	2081	3	Wadowice Górne
2278	2092	3	Wiązownica
2279	2097	3	Wielkie Oczy
2280	2137	3	Wielopole Skrzyńskie
2281	2109	3	Wiśniowa
2282	2095	3	Wojaszówka
2283	2068	3	Zagórz
2284	2068	3	Zagórz - miasto
2285	2068	3	Zagórz - obszar wiejski
2286	2079	3	Zaklików
2287	2079	3	Zaleszany
2288	2068	3	Zarszyn
2289	2066	3	Zarzecze
2290	2070	3	Żołynia
2291	2072	3	Żurawica
2292	2083	3	Żyraków
2293	2101	3	Baligród
2294	2127	3	Baranów Sandomierski
2295	2127	3	Baranów Sandomierski - miasto
2296	2127	3	Baranów Sandomierski - obszar wiejski
2297	\N	1	Podlaskie
2298	2297	2	Hajnowski
2299	2298	3	Białowieża
2300	2297	2	Bielski
2301	2300	3	Bielsk Podlaski
2302	2300	3	Boćki
2303	2300	3	Brańsk
2304	2297	2	Białostocki
2305	2304	3	Choroszcz
2306	2304	3	Choroszcz - miasto
2307	2304	3	Choroszcz - obszar wiejski
2308	2297	2	Wysokomazowiecki
2309	2308	3	Ciechanowiec
2310	2308	3	Ciechanowiec - miasto
2311	2308	3	Ciechanowiec - obszar wiejski
2312	2304	3	Czarna Białostocka
2313	2304	3	Czarna Białostocka - miasto
2314	2304	3	Czarna Białostocka - obszar wiejski
2315	2298	3	Czeremcha
2316	2298	3	Czyże
2317	2308	3	Czyżew-Osada
2318	2297	2	Sokólski
2319	2318	3	Dąbrowa Białostocka
2320	2318	3	Dąbrowa Białostocka - miasto
2321	2318	3	Dąbrowa Białostocka - obszar wiejski
2322	2304	3	Dobrzyniewo Kościelne
2323	2297	2	Siemiatycki
2324	2323	3	Drohiczyn
2325	2323	3	Drohiczyn - miasto
2326	2323	3	Drohiczyn - obszar wiejski
2327	2298	3	Dubicze Cerkiewne
2328	2323	3	Dziadkowice
2329	2297	2	Sejneński
2330	2329	3	Giby
2331	2297	2	Moniecki
2332	2331	3	Goniądz
2333	2331	3	Goniądz - miasto
2334	2331	3	Goniądz - obszar wiejski
2335	2297	2	Kolneński
2336	2335	3	Grabowo
2337	2297	2	Grajewski
2338	2337	3	Grajewo
2339	2323	3	Grodzisk
2340	2304	3	Gródek
2341	2298	3	Hajnówka
2342	2318	3	Janów
2343	2331	3	Jasionówka
2344	2331	3	Jaświły
2345	2297	2	Łomżyński
2346	2345	3	Jedwabne
2347	2345	3	Jedwabne - miasto
2348	2345	3	Jedwabne - obszar wiejski
2349	2297	2	Suwalski
2350	2349	3	Jeleniewo
2351	2304	3	Juchnowiec Kościelny
2352	2298	3	Kleszczele
2353	2298	3	Kleszczele - miasto
2354	2298	3	Kleszczele - obszar wiejski
2355	2308	3	Klukowo
2356	2331	3	Knyszyn
2357	2331	3	Knyszyn - miasto
2358	2331	3	Knyszyn - obszar wiejski
2359	2308	3	Kobylin-Borzymy
2360	2335	3	Kolno
2361	2297	2	Zambrowski
2362	2361	3	Kołaki Kościelne
2363	2318	3	Korycin
2364	2329	3	Krasnopol
2365	2318	3	Krynki
2366	2331	3	Krypno
2367	2308	3	Kulesze Kościelne
2368	2318	3	Kuźnica
2369	2297	2	Augustowski
2370	2369	3	Lipsk
2371	2369	3	Lipsk - miasto
2372	2369	3	Lipsk - obszar wiejski
2373	2304	3	Łapy
2374	2304	3	Łapy - miasto
2375	2304	3	Łapy - obszar wiejski
2376	2345	3	Łomża
2377	2297	2	Miasto Białystok
2378	2377	3	Miasto Białystok
2379	2297	2	Miasto Łomża
2380	2379	3	Miasto Łomża
2381	2297	2	Miasto Suwałki
2382	2381	3	Miasto Suwałki
2383	2335	3	Mały Płock
2384	2345	3	Miastkowo
2385	2304	3	Michałowo
2386	2323	3	Mielnik
2387	2323	3	Milejczyce
2388	2331	3	Mońki
2389	2331	3	Mońki - miasto
2390	2331	3	Mońki - obszar wiejski
2391	2298	3	Narew
2392	2298	3	Narewka
2393	2308	3	Nowe Piekuty
2394	2369	3	Nowinka
2395	2345	3	Nowogród
2396	2345	3	Nowogród - miasto
2397	2345	3	Nowogród - obszar wiejski
2398	2318	3	Nowy Dwór.
2399	2323	3	Nurzec-Stacja
2400	2300	3	Orla
2401	2323	3	Perlejewo
2402	2345	3	Piątnica
2403	2369	3	Płaska
2404	2304	3	Poświętne
2405	2349	3	Przerośl
2406	2345	3	Przytuły
2407	2329	3	Puńsk
2408	2349	3	Raczki
2409	2337	3	Radziłów
2410	2337	3	Rajgród
2411	2337	3	Rajgród - miasto
2412	2337	3	Rajgród - obszar wiejski
2413	2300	3	Rudka
2414	2349	3	Rutka-Tartak
2415	2361	3	Rutki
2416	2329	3	Sejny
2417	2318	3	Sidra
2418	2323	3	Siemiatycze
2419	2308	3	Sokoły
2420	2318	3	Sokółka
2421	2318	3	Sokółka - miasto
2422	2318	3	Sokółka - obszar wiejski
2423	2335	3	Stawiski
2424	2335	3	Stawiski - miasto
2425	2335	3	Stawiski - obszar wiejski
2426	2318	3	Suchowola
2427	2318	3	Suchowola - miasto
2428	2318	3	Suchowola - obszar wiejski
2429	2304	3	Supraśl
2430	2304	3	Supraśl - miasto
2431	2304	3	Supraśl - obszar wiejski
2432	2304	3	Suraż
2433	2304	3	Suraż - miasto
2434	2304	3	Suraż - obszar wiejski
2435	2349	3	Suwałki
2436	2337	3	Szczuczyn
2437	2337	3	Szczuczyn - miasto
2438	2337	3	Szczuczyn - obszar wiejski
2439	2308	3	Szepietowo
2440	2369	3	Sztabin
2441	2318	3	Szudziałowo
2442	2361	3	Szumowo
2443	2349	3	Szypliszki
2444	2345	3	Śniadowo
2445	2331	3	Trzcianne
2446	2335	3	Turośl
2447	2304	3	Turośń Kościelna
2448	2304	3	Tykocin
2449	2304	3	Tykocin - miasto
2450	2304	3	Tykocin - obszar wiejski
2451	2304	3	Wasilków
2452	2304	3	Wasilków - miasto
2453	2304	3	Wasilków - obszar wiejski
2454	2337	3	Wąsosz
2455	2345	3	Wizna
2456	2349	3	Wiżajny
2457	2308	3	Wysokie Mazowieckie
2458	2300	3	Wyszki
2459	2304	3	Zabłudów
2460	2304	3	Zabłudów - miasto
2461	2304	3	Zabłudów - obszar wiejski
2462	2361	3	Zambrów
2463	2304	3	Zawady
2464	2345	3	Zbójna
2465	2349	3	Bakałarzewo
2466	2369	3	Augustów
2467	2369	3	Bargłów Kościelny
2468	\N	1	Pomorskie
2469	2468	2	Starogardzki
2470	2469	3	Bobowo
2471	2468	2	Bytowski
2472	2471	3	Borzytuchom
2473	2468	2	Chojnicki
2474	2473	3	Brusy
2475	2473	3	Brusy - miasto
2476	2473	3	Brusy - obszar wiejski
2477	2471	3	Bytów
2478	2471	3	Bytów - miasto
2479	2471	3	Bytów - obszar wiejski
2480	2468	2	Gdański
2481	2480	3	Cedry Wielkie
2482	2468	2	Lęborski
2483	2482	3	Cewice
2484	2468	2	Kartuski
2485	2484	3	Chmielno
2486	2468	2	Wejherowski
2487	2486	3	Choczewo
2488	2473	3	Chojnice
2489	2471	3	Czarna Dąbrówka
2490	2469	3	Czarna Woda
2491	2468	2	Człuchowski
2492	2491	3	Czarne
2493	2491	3	Czarne - miasto
2494	2491	3	Czarne - obszar wiejski
2495	2473	3	Czersk
2496	2473	3	Czersk - miasto
2497	2473	3	Czersk - obszar wiejski
2498	2491	3	Człuchów
2499	2468	2	Słupski
2500	2499	3	Damnica
2501	2491	3	Debrzno
2502	2491	3	Debrzno - miasto
2503	2491	3	Debrzno - obszar wiejski
2504	2499	3	Dębnica Kaszubska
2505	2468	2	Kościerski
2506	2505	3	Dziemiany
2507	2468	2	Malborski
2508	2507	3	Dzierzgoń
2509	2507	3	Dzierzgoń - miasto
2510	2507	3	Dzierzgoń - obszar wiejski
2511	2468	2	Kwidzyński
2512	2511	3	Gardeja
2513	2499	3	Główczyce
2514	2468	2	Tczewski
2515	2514	3	Gniew
2516	2514	3	Gniew - miasto
2517	2514	3	Gniew - obszar wiejski
2518	2486	3	Gniewino
2519	2468	2	Pucki
2520	2519	3	Hel
2521	2519	3	Jastarnia
2522	2469	3	Kaliska
2523	2505	3	Karsin
2524	2484	3	Kartuzy
2525	2484	3	Kartuzy - miasto
2526	2484	3	Kartuzy - obszar wiejski
2527	2499	3	Kępice
2528	2499	3	Kępice - miasto
2529	2499	3	Kępice - obszar wiejski
2530	2499	3	Kobylnica
2531	2491	3	Koczała
2532	2480	3	Kolbudy Górne
2533	2471	3	Kołczygłowy
2534	2473	3	Konarzyny
2535	2519	3	Kosakowo
2536	2505	3	Kościerzyna
2537	2519	3	Krokowa
2538	2468	2	Nowodworski
2539	2538	3	Krynica Morska
2540	2511	3	Kwidzyn
2541	2482	3	Lębork
2542	2507	3	Lichnowy
2543	2486	3	Linia
2544	2505	3	Liniewo
2545	2471	3	Lipnica
2546	2505	3	Lipusz
2547	2469	3	Lubichowo
2548	2486	3	Luzino
2549	2482	3	Łeba
2550	2486	3	Łęczyce
2551	2468	2	Miasto Gdańsk
2552	2551	3	Miasto Gdańsk
2553	2468	2	Miasto Gdynia
2554	2553	3	Miasto Gdynia
2555	2468	2	Miasto Słupsk
2556	2555	3	Miasto Słupsk
2557	2468	2	Miasto Sopot
2558	2557	3	Miasto Sopot
2559	2507	3	Malbork
2560	2471	3	Miastko
2561	2471	3	Miastko - miasto
2562	2471	3	Miastko - obszar wiejski
2563	2507	3	Mikołajki Pomorskie
2564	2507	3	Miłoradz
2565	2514	3	Morzeszczyn
2566	2505	3	Nowa Karczma
2567	2482	3	Nowa Wieś Lęborska
2568	2538	3	Nowy Dwór Gdański
2569	2538	3	Nowy Dwór Gdański - miasto
2570	2538	3	Nowy Dwór Gdański - obszar wiejski
2571	2507	3	Nowy Staw
2572	2507	3	Nowy Staw - miasto
2573	2507	3	Nowy Staw - obszar wiejski
2574	2469	3	Osieczna
2575	2469	3	Osiek
2576	2538	3	Ostaszewo
2577	2471	3	Parchowo
2578	2514	3	Pelpin
2579	2514	3	Pelpin - miasto
2580	2514	3	Pelpin - obszar wiejski
2581	2499	3	Potęgowo
2582	2511	3	Prabuty
2583	2511	3	Prabuty - miasto
2584	2511	3	Prabuty - obszar wiejski
2585	2480	3	Pruszcz Gdański
2586	2491	3	Przechlewo
2587	2484	3	Przodkowo
2588	2480	3	Przywidz
2589	2480	3	Pszczółki
2590	2519	3	Puck
2591	2486	3	Reda
2592	2486	3	Rumia
2593	2511	3	Ryjewo
2594	2491	3	Rzeczenica
2595	2511	3	Sadlinki
2596	2484	3	Sierakowice
2597	2469	3	Skarszewy
2598	2469	3	Skarszewy - miasto
2599	2469	3	Skarszewy - obszar wiejski
2600	2469	3	Skórcz
2601	2499	3	Słupsk
2602	2469	3	Smętowo Graniczne
2603	2499	3	Smołdzino
2604	2484	3	Somonino
2605	2505	3	Stara Kiszewa
2606	2507	3	Stare Pole
2607	2469	3	Starogard Gdański
2608	2507	3	Stary Dzierzgoń
2609	2507	3	Stary Targ
2610	2538	3	Stegna
2611	2484	3	Stężyca
2612	2471	3	Studzienice
2613	2514	3	Subkowy
2614	2480	3	Suchy Dąb
2615	2484	3	Sulęczyno
2616	2486	3	Szemud
2617	2507	3	Sztum
2618	2507	3	Sztum - miasto
2619	2507	3	Sztum - obszar wiejski
2620	2538	3	Sztutowo
2621	2514	3	Tczew
2622	2480	3	Trąbki Wielkie
2623	2471	3	Trzebielino
2624	2471	3	Tuchomie
2625	2499	3	Ustka
2626	2486	3	Wejherowo
2627	2482	3	Wicko
2628	2519	3	Władysławowo
2629	2469	3	Zblewo
2630	2484	3	Żukowo
2631	2484	3	Żukowo - miasto
2632	2484	3	Żukowo - obszar wiejski
2633	\N	1	Śląskie
2634	2633	2	Bielski
2635	2634	3	Bestwina
2636	2633	2	Będziński
2637	2636	3	Będzin
2638	2633	2	Tyski
2639	2638	3	Bieruń
2640	2633	2	Częstochowski
2641	2640	3	Blachownia
2642	2640	3	Blachownia - miasto
2643	2640	3	Blachownia - obszar wiejski
2644	2636	3	Bobrowniki
2645	2638	3	Bojszowy
2646	2633	2	Lubliniecki
2647	2646	3	Boronów
2648	2633	2	Cieszyński
2649	2648	3	Brenna
2650	2634	3	Buczkowice
2651	2638	3	Chełm Śląski
2652	2648	3	Chybie
2653	2646	3	Ciasna
2654	2648	3	Cieszyn
2655	2634	3	Czechowice-Dziedzice
2656	2634	3	Czechowice-Dziedzice - miasto
2657	2634	3	Czechowice-Dziedzice - obszar wiejski
2658	2636	3	Czeladź
2659	2633	2	Żywiecki
2660	2659	3	Czernichów
2661	2633	2	Rybnicki
2662	2661	3	Czerwionka-Leszczyny
2663	2661	3	Czerwionka-Leszczyny - miasto
2664	2661	3	Czerwionka-Leszczyny - obszar wiejski
2665	2640	3	Dąbrowa Zielona
2666	2648	3	Dębowiec
2667	2661	3	Gaszowice
2668	2633	2	Gliwicki
2669	2668	3	Gierałtowice
2670	2659	3	Gilowice
2671	2633	2	Pszczyński
2672	2671	3	Goczałkowice-Zdrój
2673	2633	2	Wodzisławski
2674	2673	3	Godów
2675	2648	3	Goleszów
2676	2673	3	Gorzyce
2677	2648	3	Hażlach
2678	2646	3	Herby
2679	2638	3	Imielin
2680	2633	2	Zawierciański
2681	2680	3	Irządze
2682	2648	3	Istebna
2683	2640	3	Janów
2684	2634	3	Jasienica
2685	2634	3	Jaworze
2686	2661	3	Jejkowice
2687	2659	3	Jeleśnia
2688	2633	2	Tarnogórski
2689	2688	3	Kalety
2690	2640	3	Kamienica Polska
2691	2633	2	Kłobucki
2692	2691	3	Kłobuck
2693	2691	3	Kłobuck - miasto
2694	2691	3	Kłobuck - obszar wiejski
2695	2640	3	Kłomnice
2696	2668	3	Knurów
2697	2671	3	Kobiór
2698	2646	3	Kochanowice
2699	2640	3	Koniecpol
2700	2640	3	Koniecpol - miasto
2701	2640	3	Koniecpol - obszar wiejski
2702	2640	3	Konopiska
2703	2633	2	Raciborski
2704	2703	3	Kornowac
2705	2659	3	Koszarawa
2706	2646	3	Koszęcin
2707	2633	2	Myszkowski
2708	2707	3	Koziegłowy
2709	2707	3	Koziegłowy - miasto
2710	2707	3	Koziegłowy - obszar wiejski
2711	2634	3	Kozy
2712	2680	3	Kroczyce
2713	2688	3	Krupski Młyn
2714	2640	3	Kruszyna
2715	2703	3	Krzanowice
2716	2691	3	Krzepice
2717	2691	3	Krzepice - miasto
2718	2691	3	Krzepice - obszar wiejski
2719	2703	3	Krzyżanowice
2720	2703	3	Kuźnia Raciborska
2721	2703	3	Kuźnia Raciborska - miasto
2722	2703	3	Kuźnia Raciborska - obszar wiejski
2723	2640	3	Lelów
2724	2638	3	Lędziny
2725	2691	3	Lipie
2726	2659	3	Lipowa
2727	2646	3	Lubliniec
2728	2673	3	Lubomia
2729	2661	3	Lyski
2730	2633	2	Mikołowski
2731	2730	3	Łaziska Górne
2732	2680	3	Łazy
2733	2680	3	Łazy - miasto
2734	2680	3	Łazy - obszar wiejski
2735	2659	3	Łękawica
2736	2659	3	Łodygowice
2737	2633	2	Miasto Bielsko-Biała
2738	2737	3	Miasto Bielsko-Biała
2739	2633	2	Miasto Bytom
2740	2739	3	Miasto Bytom
2741	2633	2	Miasto Chorzów
2742	2741	3	Miasto Chorzów
2743	2633	2	Miasto Częstochowa
2744	2743	3	Miasto Częstochowa
2745	2633	2	Miasto Dąbrowa Górnicza
2746	2745	3	Miasto Dąbrowa Górnicza
2747	2633	2	Miasto Gliwice
2748	2747	3	Miasto Gliwice
2749	2633	2	Miasto Jastrzębie-Zdrój
2750	2749	3	Miasto Jastrzębie-Zdrój
2751	2633	2	Miasto Jaworzno
2752	2751	3	Miasto Jaworzno
2753	2633	2	Miasto Katowice
2754	2753	3	Miasto Katowice
2755	2633	2	Miasto Mysłowice
2756	2755	3	Miasto Mysłowice
2757	2633	2	Miasto Piekary Śląskie
2758	2757	3	Miasto Piekary Śląskie
2759	2633	2	Miasto Ruda Śląska
2760	2759	3	Miasto Ruda Śląska
2761	2633	2	Miasto Rybnik
2762	2761	3	Miasto Rybnik
2763	2633	2	Miasto Siemianowice Śląskie
2764	2763	3	Miasto Siemianowice Śląskie
2765	2633	2	Miasto Sosnowiec
2766	2765	3	Miasto Sosnowiec
2767	2633	2	Miasto Świętochłowice
2768	2767	3	Miasto Świętochłowice
2769	2633	2	Miasto Tychy
2770	2769	3	Miasto Tychy
2771	2633	2	Miasto Zabrze
2772	2771	3	Miasto Zabrze
2773	2633	2	Miasto Żory
2774	2773	3	Miasto Żory
2775	2673	3	Marklowice
2776	2688	3	Miasteczko Śląskie
2777	2671	3	Miedźna
2778	2691	3	Miedźno
2779	2636	3	Mierzęcice
2780	2730	3	Mikołów
2781	2659	3	Milówka
2782	2640	3	Mstów
2783	2673	3	Mszana
2784	2640	3	Mykanów
2785	2707	3	Myszków
2786	2703	3	Nędza
2787	2707	3	Niegowa
2788	2680	3	Ogrodzieniec
2789	2680	3	Ogrodzieniec - miasto
2790	2680	3	Ogrodzieniec - obszar wiejski
2791	2640	3	Olsztyn
2792	2691	3	Opatów
2793	2730	3	Ornontowice
2794	2730	3	Orzesze
2795	2688	3	Ożarowice
2796	2691	3	Panki
2797	2671	3	Pawłowice
2798	2646	3	Pawonków
2799	2703	3	Pietrowice Wielkie
2800	2668	3	Pilchowice
2801	2680	3	Pilica
2802	2680	3	Pilica - miasto
2803	2680	3	Pilica - obszar wiejski
2804	2640	3	Poczesna
2805	2691	3	Popów
2806	2707	3	Poraj
2807	2634	3	Porąbka
2808	2680	3	Poręba
2809	2640	3	Przyrów
2810	2691	3	Przystajń
2811	2636	3	Psary
2812	2671	3	Pszczyna
2813	2671	3	Pszczyna - miasto
2814	2671	3	Pszczyna - obszar wiejski
2815	2673	3	Pszów
2816	2668	3	Pyskowice
2817	2703	3	Racibórz
2818	2673	3	Radlin
2819	2659	3	Radziechowy-Wieprz
2820	2688	3	Radzionków
2821	2659	3	Rajcza
2822	2640	3	Rędziny
2823	2703	3	Rudnik
2824	2668	3	Rudziniec
2825	2673	3	Rydułtowy
2826	2636	3	Siewierz
2827	2636	3	Siewierz - miasto
2828	2636	3	Siewierz - obszar wiejski
2829	2648	3	Skoczów
2830	2648	3	Skoczów - miasto
2831	2648	3	Skoczów - obszar wiejski
2832	2668	3	Sośnicowice
2833	2668	3	Sośnicowice - miasto
2834	2668	3	Sośnicowice - obszar wiejski
2835	2640	3	Starcza
2836	2648	3	Strumień
2837	2648	3	Strumień - miasto
2838	2648	3	Strumień - obszar wiejski
2839	2671	3	Suszec
2840	2680	3	Szczekociny
2841	2680	3	Szczekociny - miasto
2842	2680	3	Szczekociny - obszar wiejski
2843	2634	3	Szczyrk
2844	2659	3	Ślemień
2845	2688	3	Świerklaniec
2846	2661	3	Świerklany
2847	2659	3	Świnna
2848	2688	3	Tarnowskie Góry
2849	2668	3	Toszek
2850	2668	3	Toszek - miasto
2851	2668	3	Toszek - obszar wiejski
2852	2688	3	Tworóg
2853	2659	3	Ujsoły
2854	2648	3	Ustroń
2855	2659	3	Węgierska Górka
2856	2668	3	Wielowieś
2857	2634	3	Wilamowice
2858	2634	3	Wilamowice - miasto
2859	2634	3	Wilamowice - obszar wiejski
2860	2634	3	Wilkowice
2861	2648	3	Wisła
2862	2680	3	Włodowice
2863	2673	3	Wodzisław Śląski
2864	2636	3	Wojkowice
2865	2646	3	Woźniki
2866	2646	3	Woźniki - miasto
2867	2646	3	Woźniki - obszar wiejski
2868	2691	3	Wręczyca Wielka
2869	2730	3	Wyry
2870	2680	3	Zawiercie
2871	2688	3	Zbrosławice
2872	2648	3	Zebrzydowice
2873	2707	3	Żarki
2874	2707	3	Żarki - miasto
2875	2707	3	Żarki - obszar wiejski
2876	2680	3	Żarnowiec
2877	2659	3	Żywiec
2878	\N	1	Świętokrzyskie
2879	2878	2	Kazimierski
2880	2879	3	Bejsce
2881	2878	2	Kielecki
2882	2881	3	Bieliny
2883	2878	2	Skarżyski
2884	2883	3	Bliżyn
2885	2878	2	Ostrowiecki
2886	2885	3	Bodzechów
2887	2881	3	Bodzentyn
2888	2881	3	Bodzentyn - miasto
2889	2881	3	Bodzentyn - obszar wiejski
2890	2878	2	Staszowski
2891	2890	3	Bogoria
2892	2878	2	Starachowicki
2893	2892	3	Brody
2894	2878	2	Buski
2895	2894	3	Busko-Zdrój
2896	2894	3	Busko-Zdrój - miasto
2897	2894	3	Busko-Zdrój - obszar wiejski
2898	2881	3	Chęciny
2899	2881	3	Chęciny - miasto
2900	2881	3	Chęciny - obszar wiejski
2901	2881	3	Chmielnik
2902	2881	3	Chmielnik - miasto
2903	2881	3	Chmielnik - obszar wiejski
2904	2879	3	Czarnocin
2905	2885	3	Ćmielów
2906	2885	3	Ćmielów - miasto
2907	2885	3	Ćmielów - obszar wiejski
2908	2881	3	Daleszyce
2909	2878	2	Sandomierski
2910	2909	3	Dwikozy
2911	2878	2	Pińczowski
2912	2911	3	Działoszyce
2913	2911	3	Działoszyce - miasto
2914	2911	3	Działoszyce - obszar wiejski
2915	2878	2	Konecki
2916	2915	3	Fałków
2917	2894	3	Gnojno
2918	2915	3	Gowarczów
2919	2881	3	Górno
2920	2878	2	Jędrzejowski
2921	2920	3	Imielno
2922	2878	2	Opatowski
2923	2922	3	Iwaniska
2924	2920	3	Jędrzejów
2925	2920	3	Jędrzejów - miasto
2926	2920	3	Jędrzejów - obszar wiejski
2927	2879	3	Kazimierza Wielka
2928	2879	3	Kazimierza Wielka - miasto
2929	2879	3	Kazimierza Wielka - obszar wiejski
2930	2911	3	Kije
2931	2909	3	Klimontów
2932	2878	2	Włoszczowski
2933	2932	3	Kluczewsko
2934	2915	3	Końskie
2935	2915	3	Końskie - miasto
2936	2915	3	Końskie - obszar wiejski
2937	2909	3	Koprzywnica
2938	2932	3	Krasocin
2939	2885	3	Kunów
2940	2885	3	Kunów - miasto
2941	2885	3	Kunów - obszar wiejski
2942	2922	3	Lipnik
2943	2881	3	Łagów
2944	2883	3	Łączna
2945	2909	3	Łoniów
2946	2881	3	Łopuszno
2947	2890	3	Łubnice
2948	2878	2	Miasto Kielce
2949	2948	3	Miasto Kielce
2950	2920	3	Małogoszcz
2951	2920	3	Małogoszcz - miasto
2952	2920	3	Małogoszcz - obszar wiejski
2953	2881	3	Masłów
2954	2911	3	Michałów
2955	2881	3	Miedziana Góra
2956	2892	3	Mirzec
2957	2881	3	Mniów
2958	2881	3	Morawica
2959	2932	3	Moskorzew
2960	2920	3	Nagłowice
2961	2881	3	Nowa Słupia
2962	2894	3	Nowy Korczyn
2963	2909	3	Obrazów
2964	2920	3	Oksa
2965	2890	3	Oleśnica
2966	2879	3	Opatowiec
2967	2922	3	Opatów
2968	2922	3	Opatów - miasto
2969	2922	3	Opatów - obszar wiejski
2970	2890	3	Osiek
2971	2890	3	Osiek - miasto
2972	2890	3	osiek - obszar wiejski
2973	2885	3	Ostrowiec Świętokrzyski
2974	2922	3	Ożarów
2975	2922	3	Ożarów - miasto
2976	2922	3	Ożarów - obszar wiejski
2977	2894	3	Pacanów
2978	2892	3	Pawłów
2979	2881	3	Piekoszów
2980	2881	3	Pierzchnica
2981	2911	3	Pińczów
2982	2911	3	Pińczów - miasto
2983	2911	3	Pińczów - obszar wiejski
2984	2890	3	Połaniec
2985	2890	3	Połaniec - miasto
2986	2890	3	Połaniec - obszar wiejski
2987	2932	3	Radków
2988	2915	3	Radoszyce
2989	2881	3	Raków
2990	2915	3	Ruda Maleniecka
2991	2890	3	Rytwiany
2992	2922	3	Sadowie
2993	2909	3	Samborzec
2994	2909	3	Sandomierz
2995	2932	3	Secemin
2996	2920	3	Sędziszów
2997	2920	3	Sędziszów - miasto
2998	2920	3	Sędziszów - obszar wiejski
2999	2881	3	Sitkówka-Nowiny
3000	2879	3	Skalbmierz
3001	2879	3	Skalbmierz - miasto
3002	2879	3	Skalbmierz - obszar wiejski
3003	2883	3	Skarżysko Kościelne
3004	2883	3	Skarżysko-Kamienna
3005	2920	3	Słupia (Jędrzejowska)
3006	2915	3	Słupia (Konecka)
3007	2915	3	Smyków
3008	2920	3	Sobków
3009	2894	3	Solec-Zdrój
3010	2892	3	Starachowice
3011	2890	3	Staszów
3012	2890	3	Staszów - miasto
3013	2890	3	Staszów - obszar wiejski
3014	2915	3	Stąporków
3015	2915	3	Stąporków - miasto
3016	2915	3	Stąporków - obszar wiejski
3017	2894	3	Stopnica
3018	2881	3	Strawczyn
3019	2883	3	Suchedniów
3020	2883	3	Suchedniów - miasto
3021	2883	3	Suchedniów - obszar wiejski
3022	2890	3	Szydłów
3023	2922	3	Tarłów
3024	2894	3	Tuczępy
3025	2885	3	Waśniów
3026	2892	3	Wąchock
3027	2892	3	Wąchock - miasto
3028	2892	3	Wąchock - obszar wiejski
3029	2909	3	Wilczyce
3030	2894	3	Wiślica
3031	2932	3	Włoszczowa
3032	2932	3	Włoszczowa - miasto
3033	2932	3	Włoszczowa - obszar wiejski
3034	2920	3	Wodzisław
3035	2922	3	Wojciechowice
3036	2881	3	Zagnańsk
3037	2909	3	Zawichost
3038	2909	3	Zawichost - miasto
3039	2909	3	Zawichost - obszar wiejski
3040	2911	3	Złota
3041	2922	3	Baćkowice
3042	2885	3	Bałtów
3043	\N	1	Warmińsko-Mazurskie
3044	3043	2	Piski
3045	3044	3	Biała Piska
3046	3044	3	Biała Piska - miasto
3047	3044	3	Biała Piska - obszar wiejski
3048	3043	2	Nowomiejski
3049	3048	3	Biskupiec
3050	3043	2	Olsztyński
3051	3050	3	Biskupiec
3052	3050	3	Biskupiec - miasto
3053	3050	3	Biskupiec - obszar wiejski
3054	3043	2	Bartoszycki
3055	3054	3	Bisztynek
3056	3054	3	Bisztynek - miasto
3057	3054	3	Bisztynek - obszar wiejski
3058	3043	2	Braniewski
3059	3058	3	Braniewo
3060	3043	2	Giżycki
3061	3896	3	Budry
3062	3043	2	Ostródzki
3063	3062	3	Dąbrówno
3064	3050	3	Dobre Miasto
3065	3050	3	Dobre Miasto - miasto
3066	3050	3	Dobre Miasto - obszar wiejski
3067	3043	2	Gołdapski
3068	3067	3	Dubeninki
3069	3050	3	Dywity
3070	3043	2	Działdowski
3071	3070	3	Działdowo
3072	3043	2	Szczycieński
3073	3072	3	Dźwierzuty
3074	3043	2	Elbląski
3075	3074	3	Elbląg
3076	3043	2	Ełcki
3077	3076	3	Ełk
3078	3058	3	Frombork
3079	3058	3	Frombork - miasto
3080	3058	3	Frombork - obszar wiejski
3081	3050	3	Gietrzwałd
3082	3060	3	Giżycko
3083	3074	3	Godkowo
3084	3067	3	Gołdap
3085	3043	2	Goldabski
3086	3085	3	Gołdap - miasto
3087	3085	3	Gołdap - obszar wiejski
3088	3054	3	Górowo Iławeckie
3089	3048	3	Grodziczno
3090	3074	3	Gronowo Elbląskie
3091	3062	3	Grunwald
3092	3043	2	Iławski
3093	3092	3	Iława
3094	3070	3	Iłowo-Osada
3095	3043	2	Nidzicki
3096	3095	3	Janowiec Kościelny
3097	3095	3	Janowo
3098	3072	3	Jedwabno
3099	3050	3	Jeziorany
3100	3050	3	Jeziorany - miasto
3101	3050	3	Jeziorany - obszar wiejski
3102	3050	3	Jonkowo
3103	3076	3	Kalinowo
3104	3092	3	Kętrzyn
3105	3092	3	Kisielice
3106	3092	3	Kisielice - miasto
3107	3092	3	Kisielice - obszar wiejski
3108	3043	2	Lidzbarski
3109	3108	3	Kiwity
3110	3050	3	Kolno
3111	3092	3	Korsze
3112	3092	3	Korsze - miasto
3113	3092	3	Korsze - obszar wiejski
3114	3043	2	Olecki
3115	3114	3	Kowale Oleckie
3116	3095	3	Kozłowo
3117	3060	3	Kruklanki
3118	3048	3	Kurzętnik
3119	3058	3	Lelkowo
3120	3070	3	Lidzbark
3121	3070	3	Lidzbark - miasto
3122	3070	3	Lidzbark - obszar wiejski
3123	3108	3	Lidzbark Warmiński
3124	3092	3	Lubawa
3125	3108	3	Lubomino
3126	3062	3	Łukta
3127	3043	2	Miasto Elbląg
3128	3127	3	Miasto Elbląg
3129	3043	2	Miasto Olsztyn
3130	3129	3	Miasto Olsztyn
3131	3062	3	Małdyty
3132	3074	3	Markusy
3133	3043	2	Mrągowski
3134	3133	3	Mikołajki
3135	3133	3	Mikołajki - miasto
3136	3133	3	Mikołajki - obszar wiejski
3137	3074	3	Milejewo
3138	3062	3	Miłakowo
3139	3062	3	Miłakowo - miasto
3140	3062	3	Miłakowo - obszar wiejski
3141	3060	3	Miłki
3142	3062	3	Miłomłyn
3143	3062	3	Miłomłyn - miasto
3144	3062	3	Miłomłyn - obszar wiejski
3145	3074	3	Młynary
3146	3074	3	Młynary - miasto
3147	3074	3	Młynary - obszar wiejski
3148	3062	3	Morąg
3149	3062	3	Morąg - miasto
3150	3062	3	Morąg - obszar wiejski
3151	3133	3	Mrągowo
3152	3095	3	Nidzica
3153	3095	3	Nidzica - miasto
3154	3095	3	Nidzica - obszar wiejski
3155	3048	3	Nowe Miasto Lubawskie
3156	3114	3	Olecko
3157	3114	3	Olecko - miasto
3158	3114	3	Olecko - obszar wiejski
3159	3050	3	Olsztynek
3160	3050	3	Olsztynek - miasto
3161	3050	3	Olsztynek - obszar wiejski
3162	3108	3	Orneta
3163	3108	3	Orneta - miasto
3164	3108	3	Orneta - obszar wiejski
3165	3044	3	Orzysz
3166	3044	3	Orzysz - miasto
3167	3044	3	Orzysz - obszar wiejski
3168	3062	3	Ostróda
3169	3074	3	Pasłęk
3170	3074	3	Pasłęk - miasto
3171	3074	3	Pasłęk - obszar wiejski
3172	3072	3	Pasym
3173	3072	3	Pasym - miasto
3174	3072	3	Pasym - obszar wiejski
3175	3133	3	Piecki
3176	3058	3	Pieniężno
3177	3058	3	Pieniężno - miasto
3178	3058	3	Pieniężno - obszar wiejski
3179	3044	3	Pisz
3180	3044	3	Pisz - miasto
3181	3044	3	Pisz - obszar wiejski
3182	3058	3	Płoskinia
3183	3070	3	Płośnica
3184	3896	3	Pozezdrze
3185	3076	3	Prostki
3186	3050	3	Purda
3187	3892	3	Reszel
3188	3892	3	Reszel - miasto
3189	3892	3	Reszel - obszar wiejski
3190	3072	3	Rozogi
3191	3044	3	Ruciane-Nida
3192	3044	3	Ruciane-Nida - miasto
3193	3044	3	Ruciane-Nida - obszar wiejski
3194	3070	3	Rybno
3195	3074	3	Rychliki
3196	3060	3	Ryn
3197	3060	3	Ryn - miasto
3198	3060	3	Ryn - obszar wiejski
3199	3054	3	Sępopol
3200	3054	3	Sępopol - miasto
3201	3054	3	Sępopol - obszar wiejski
3202	3133	3	Sorkwity
3203	3092	3	Srokowo
3204	3076	3	Stare Juchy
3205	3050	3	Stawiguda
3206	3092	3	Susz
3207	3092	3	Susz - miasto
3208	3092	3	Susz - obszar wiejski
3209	3072	3	Szczytno
3210	3050	3	Świątki
3211	3067	3	Świętajno
3212	3072	3	Świętajno
3213	3074	3	Tolkmicko
3214	3074	3	Tolkmicko - miasto
3215	3074	3	Tolkmicko - obszar wiejski
3216	3896	3	Węgorzewo
3217	3060	3	Węgorzewo - miasto
3218	3060	3	Węgorzewo - obszar wiejski
3219	3072	3	Wielbark
3220	3114	3	Wieliczki
3221	3058	3	Wilczęta
3222	3060	3	Wydminy
3223	3092	3	Zalewo
3224	3092	3	Zalewo - miasto
3225	3092	3	Zalewo - obszar wiejski
3226	3060	3	Banie Mazurskie
3227	3092	3	Barciany
3228	3050	3	Barczewo
3229	3050	3	Barczewo - miasto
3230	3050	3	Barczewo - obszar wiejski
3231	3054	3	Bartoszyce
3232	\N	1	Wielkopolskie
3233	3232	2	Kępiński
3234	3233	3	Baranów
3235	3232	2	Pilski
3236	3235	3	Białośliwie
3237	3232	2	Kaliski
3238	3237	3	Blizanów
3239	3232	2	Rawicki
3240	3239	3	Bojanowo
3241	3239	3	Bojanowo - miasto
3242	3239	3	Bojanowo - obszar wiejski
3243	3232	2	Gostyński
3244	3243	3	Borek Wielkopolski
3245	3243	3	Borek Wielkopolski - miasto
3246	3243	3	Borek Wielkopolski - obszar wiejski
3247	3233	3	Bralin
3248	3232	2	Śremski
3249	3248	3	Brodnica
3250	3232	2	Turecki
3251	3250	3	Brudzew
3252	3237	3	Brzeziny
3253	3232	2	Chodzieski
3254	3253	3	Budzyń
3255	3232	2	Poznański
3256	3255	3	Buk
3257	3255	3	Buk - miasto
3258	3255	3	Buk - obszar wiejski
3259	3237	3	Ceków-Kolonia
3260	3232	2	Pleszewski
3261	3260	3	Chocz
3262	3232	2	Kolski
3263	3262	3	Chodów
3264	3253	3	Chodzież
3265	3232	2	Międzychodzki
3266	3265	3	Chrzypsko Wielkie
3267	3232	2	Ostrzeszowski
3268	3267	3	Czajków
3269	3232	2	Czarnkowsko-Trzcianecki
3270	3269	3	Czarnków
3271	3232	2	Kościański
3272	3271	3	Czempiń
3273	3271	3	Czempiń - miasto
3274	3271	3	Czempiń - obszar wiejski
3275	3260	3	Czermin
3276	3232	2	Gnieźnieński
3277	3276	3	Czerniejewo
3278	3276	3	Czerniejewo - miasto
3279	3276	3	Czerniejewo - obszar wiejski
3280	3255	3	Czerwonak
3281	3232	2	Wągrowiecki
3282	3281	3	Damasławek
3283	3262	3	Dąbie
3284	3262	3	Dąbie - miasto
3285	3262	3	Dąbie - obszar wiejski
3286	3250	3	Dobra
3287	3250	3	Dobra - miasto
3288	3250	3	Dobra - obszar wiejski
3289	3260	3	Dobrzyca
3290	3248	3	Dolsk
3291	3248	3	Dolsk - miasto
3292	3248	3	Dolsk - obszar wiejski
3293	3232	2	Średzki
3294	3293	3	Dominowo
3295	3255	3	Dopiewo
3296	3267	3	Doruchów
3297	3269	3	Drawsko
3298	3232	2	Szamotulski
3299	3298	3	Duszniki
3300	3260	3	Gizałki
3301	3276	3	Gniezno
3302	3237	3	Godziesze Wielkie
3303	3232	2	Koniński
3304	3303	3	Golina
3305	3303	3	Golina - miasto
3306	3303	3	Golina - obszar wiejski
3307	3281	3	Gołańcz
3308	3281	3	Gołańcz - miasto
3309	3281	3	Gołańcz - obszar wiejski
3310	3260	3	Gołuchów
3311	3243	3	Gostyń
3312	3243	3	Gostyń - miasto
3313	3243	3	Gostyń - obszar wiejski
3314	3267	3	Grabów nad Prosną
3315	3267	3	Grabów nad Prosną - miasto
3316	3267	3	Grabów nad Prosną - obszar wiejski
3317	3232	2	Grodziski
3318	3317	3	Granowo
3319	3303	3	Grodziec
3320	3317	3	Grodzisk Wielkopolski
3321	3317	3	Grodzisk Wielkopolski - miasto
3322	3317	3	Grodzisk Wielkopolski - obszar wiejski
3323	3262	3	Grzegorzew
3324	3232	2	Jarociński
3325	3324	3	Jaraczewo
3326	3324	3	Jarocin
3327	3324	3	Jarocin - miasto
3328	3324	3	Jarocin - obszar wiejski
3329	3232	2	Złotowski
3330	3329	3	Jastrowie
3331	3329	3	Jastrowie - miasto
3332	3329	3	Jastrowie - obszar wiejski
3333	3239	3	Jutrosin
3334	3239	3	Jutrosin - miasto
3335	3239	3	Jutrosin - obszar wiejski
3336	3235	3	Kaczory
3337	3317	3	Kamieniec
3338	3250	3	Kawęczyn
3339	3303	3	Kazimierz Biskupi
3340	3298	3	Kaźmierz
3341	3233	3	Kępno
3342	3233	3	Kępno - miasto
3343	3233	3	Kępno - obszar wiejski
3344	3276	3	Kiszkowo
3345	3303	3	Kleczew
3346	3303	3	Kleczew - miasto
3347	3303	3	Kleczew - obszar wiejski
3348	3255	3	Kleszczewo
3349	3276	3	Kłecko
3350	3276	3	Kłecko - miasto
3351	3276	3	Kłecko - obszar wiejski
3352	3262	3	Kłodawa
3353	3262	3	Kłodawa - miasto
3354	3262	3	Kłodawa - obszar wiejski
3355	3267	3	Kobyla Góra
3356	3232	2	Krotoszyński
3357	3356	3	Kobylin
3358	3356	3	Kobylin - miasto
3359	3356	3	Kobylin - obszar wiejski
3360	3232	2	Wrzesiński
3361	3360	3	Kołaczkowo
3362	3262	3	Koło
3363	3255	3	Komorniki
3364	3255	3	Kostrzyn
3365	3255	3	Kostrzyn - miasto
3366	3255	3	Kostrzyn - obszar wiejski
3367	3271	3	Kościan
3368	3262	3	Kościelec
3369	3324	3	Kotlin
3370	3356	3	Koźmin Wielkopolski
3371	3356	3	Koźmin Wielkopolski - miasto
3372	3356	3	Koźmin Wielkopolski - obszar wiejski
3373	3237	3	Koźminek
3374	3255	3	Kórnik
3375	3255	3	Kórnik - miasto
3376	3255	3	Kórnik - obszar wiejski
3377	3329	3	Krajenka
3378	3329	3	Krajenka - miasto
3379	3329	3	Krajenka - obszar wiejski
3380	3303	3	Kramsk
3381	3267	3	Kraszewice
3382	3243	3	Krobia
3383	3243	3	Krobia - miasto
3384	3243	3	Krobia - obszar wiejski
3385	3356	3	Krotoszyn
3386	3356	3	Krotoszyn - miasto
3387	3356	3	Krotoszyn - obszar wiejski
3388	3232	2	Leszczyński
3389	3388	3	Krzemieniewo
3390	3293	3	Krzykosy
3391	3303	3	Krzymów
3392	3271	3	Krzywiń
3393	3271	3	Krzywiń - miasto
3394	3271	3	Krzywiń - obszar wiejski
3395	3269	3	Krzyż Wielkopolski
3396	3269	3	Krzyż Wielkopolski - miasto
3397	3269	3	Krzyż Wielkopolski - obszar wiejski
3398	3248	3	Książ Wielkopolski
3399	3248	3	Książ Wielkopolski - miasto
3400	3248	3	Książ Wielkopolski - obszar wiejski
3401	3232	2	Nowotomyski
3402	3401	3	Kuślin
3403	3265	3	Kwilcz
3404	3232	2	Słupecki
3405	3404	3	Lądek
3406	3329	3	Lipka
3407	3388	3	Lipno
3408	3237	3	Lisków
3409	3269	3	Lubasz
3410	3255	3	Luboń
3411	3401	3	Lwówek
3412	3401	3	Lwówek - miasto
3413	3401	3	Lwówek - obszar wiejski
3414	3233	3	Łęka Opatowska
3415	3235	3	Łobżenica
3416	3235	3	Łobżenica - miasto
3417	3235	3	Łobżenica - obszar wiejski
3418	3276	3	Łubowo
3419	3232	2	Miasto Kalisz
3420	3419	3	Miasto Kalisz
3421	3232	2	Miasto Konin
3422	3421	3	Miasto Konin
3423	3232	2	Miasto Leszno
3424	3423	3	Miasto Leszno
3425	3232	2	Miasto Poznań
3426	3425	3	Miasto Poznań
3427	3250	3	Malanów
3428	3253	3	Margonin
3429	3253	3	Margonin - miasto
3430	3253	3	Margonin - obszar wiejski
3431	3235	3	Miasteczko Krajeńskie
3432	3401	3	Miedzichowo
3433	3239	3	Miejska Górka
3434	3239	3	Miejska Górka - miasto
3435	3239	3	Miejska Górka - obszar wiejski
3436	3276	3	Mieleszyn
3437	3281	3	Mieścisko
3438	3265	3	Międzychód
3439	3265	3	Międzychód - miasto
3440	3265	3	Międzychód - obszar wiejski
3441	3267	3	Mikstat
3442	3267	3	Mikstat - miasto
3443	3267	3	Mikstat - obszar wiejski
3444	3360	3	Miłosław
3445	3360	3	Miłosław - obszar wiejski
3446	3255	3	Mosina
3447	3255	3	Mosina - miasto
3448	3255	3	Mosina - obszar wiejski
3449	3255	3	Murowana Goślina
3450	3255	3	Murowana Goślina - miasto
3451	3255	3	Murowana Goślina - obszar wiejski
3452	3237	3	Mycielin
3453	3360	3	Nekla
3454	3276	3	Niechanowo
3455	3293	3	Nowe Miasto nad Wartą
3456	3232	2	Ostrowski
3457	3456	3	Nowe Skalmierzyce
3458	3456	3	Nowe Skalmierzyce - miasto
3459	3456	3	Nowe Skalmierzyce - obszar wiejski
3460	3401	3	Nowy Tomyśl
3461	3401	3	Nowy Tomyśl - miasto
3462	3401	3	Nowy Tomyśl - obszar wiejski
3463	3232	2	Obornicki
3464	3463	3	Oborniki
3465	3463	3	Oborniki - miasto
3466	3463	3	Oborniki - obszar wiejski
3467	3298	3	Obrzycko
3468	3456	3	Odolanów
3469	3456	3	Odolanów - miasto
3470	3456	3	Odolanów - obszar wiejski
3471	3329	3	Okonek
3472	3329	3	Okonek - miasto
3473	3329	3	Okonek - obszar wiejski
3474	3262	3	Olszówka
3475	3401	3	Opalenica
3476	3401	3	Opalenica - miasto
3477	3401	3	Opalenica - obszar wiejski
3478	3237	3	Opatówek
3479	3404	3	Orchowo
3480	3388	3	Osieczna
3481	3388	3	Osieczna - miasto
3482	3388	3	Osieczna - obszar wiejski
3483	3262	3	Osiek Mały
3484	3298	3	Ostroróg
3485	3298	3	Ostroróg - miasto
3486	3298	3	Ostroróg - obszar wiejski
3487	3404	3	Ostrowite
3488	3456	3	Ostrów Wielkopolski
3489	3267	3	Ostrzeszów
3490	3267	3	Ostrzeszów - miasto
3491	3267	3	Ostrzeszów - obszar wiejski
3492	3239	3	Pakosław
3493	3233	3	Perzów
3494	3243	3	Pępowo
3495	3243	3	Piaski
3496	3235	3	Piła
3497	3260	3	Pleszew
3498	3260	3	Pleszew - miasto
3499	3260	3	Pleszew - obszar wiejski
3500	3298	3	Pniewy
3501	3298	3	Pniewy - miasto
3502	3298	3	Pniewy - obszar wiejski
3503	3255	3	Pobiedziska
3504	3255	3	Pobiedziska - miasto
3505	3255	3	Pobiedziska - obszar wiejski
3506	3243	3	Pogorzela
3507	3243	3	Pogorzela - miasto
3508	3243	3	Pogorzela - obszar wiejski
3509	3269	3	Połajewo
3510	3243	3	Poniec
3511	3243	3	Poniec - miasto
3512	3243	3	Poniec - obszar wiejski
3513	3404	3	Powidz
3514	3425	3	Poznań-Grunwald
3515	3425	3	Poznań-Jeżyce
3516	3425	3	Poznań-Nowe Miasto
3517	3425	3	Poznań-Stare Miasto
3518	3425	3	Poznań-Wilda
3519	3262	3	Przedecz
3520	3262	3	Przedecz - miasto
3521	3262	3	Przedecz - obszar wiejski
3522	3232	2	Wolsztyński
3523	3522	3	Przemęt
3524	3456	3	Przygodzice
3525	3250	3	Przykona
3526	3255	3	Puszczykowo
3527	3360	3	Pyzdry
3528	3360	3	Pyzdry - miasto
3529	3360	3	Pyzdry - obszar wiejski
3530	3317	3	Rakoniewice
3531	3317	3	Rakoniewice - miasto
3532	3317	3	Rakoniewice - obszar wiejski
3533	3456	3	Raszków
3534	3456	3	Raszków - miasto
3535	3456	3	Raszków - obszar wiejski
3536	3239	3	Rawicz
3537	3239	3	Rawicz - miasto
3538	3239	3	Rawicz - obszar wiejski
3539	3463	3	Rogoźno
3540	3463	3	Rogoźno - miasto
3541	3463	3	Rogoźno - obszar wiejski
3542	3255	3	Rokietnica
3543	3356	3	Rozdrażew
3544	3233	3	Rychtal
3545	3303	3	Rychwał
3546	3303	3	Rychwał - miasto
3547	3303	3	Rychwał - obszar wiejski
3548	3463	3	Ryczywół
3549	3388	3	Rydzyna
3550	3388	3	Rydzyna - miasto
3551	3388	3	Rydzyna - obszar wiejski
3552	3303	3	Rzgów
3553	3522	3	Siedlec
3554	3265	3	Sieraków
3555	3265	3	Sieraków - miasto
3556	3265	3	Sieraków - obszar wiejski
3557	3456	3	Sieroszewice
3558	3281	3	Skoki
3559	3281	3	Skoki - miasto
3560	3281	3	Skoki - obszar wiejski
3561	3303	3	Skulsk
3562	3404	3	Słupca
3563	3303	3	Sompolno
3564	3303	3	Sompolno - miasto
3565	3303	3	Sompolno - obszar wiejski
3566	3456	3	Sośnie
3567	3303	3	Stare Miasto
3568	3237	3	Stawiszyn
3569	3237	3	Stawiszyn - miasto
3570	3237	3	Stawiszyn - obszar wiejski
3571	3255	3	Stęszew
3572	3255	3	Stęszew - miasto
3573	3255	3	Stęszew - obszar wiejski
3574	3404	3	Strzałkowo
3575	3255	3	Suchy Las
3576	3356	3	Sulmierzyce
3577	3255	3	Swarzędz
3578	3255	3	Swarzędz - miasto
3579	3255	3	Swarzędz - obszar wiejski
3580	3253	3	Szamocin
3581	3253	3	Szamocin - miasto
3582	3253	3	Szamocin - obszar wiejski
3583	3298	3	Szamotuły
3584	3298	3	Szamotuły - miasto
3585	3298	3	Szamotuły - obszar wiejski
3586	3237	3	Szczytniki
3587	3235	3	Szydłowo
3588	3303	3	Ślesin
3589	3303	3	Ślesin - miasto
3590	3303	3	Ślesin - obszar wiejski
3591	3271	3	Śmigiel
3592	3271	3	Śmigiel - miasto
3593	3271	3	Śmigiel - obszar wiejski
3594	3248	3	Śrem
3595	3248	3	Śrem - miasto
3596	3248	3	Śrem - obszar wiejski
3597	3293	3	Środa WielkoPolska
3598	3293	3	Środa WielkoPolska - miasto
3599	3293	3	Środa WielkoPolska - obszar wiejski
3600	3388	3	Święciechowa
3601	3255	3	Tarnowo Podgórne
3602	3329	3	Tarnówka
3603	3269	3	Trzcianka
3604	3269	3	Trzcianka - miasto
3605	3269	3	Trzcianka - obszar wiejski
3606	3233	3	Trzcinica
3607	3276	3	Trzemeszno
3608	3276	3	Trzemeszno - miasto
3609	3276	3	Trzemeszno - obszar wiejski
3610	3250	3	Tuliszków
3611	3250	3	Tuliszków - miasto
3612	3250	3	Tuliszków - obszar wiejski
3613	3250	3	Turek
3614	3235	3	Ujście
3615	3235	3	Ujście - miasto
3616	3235	3	Ujście - obszar wiejski
3617	3281	3	Wapno
3618	3281	3	Wągrowiec
3619	3269	3	Wieleń
3620	3269	3	Wieleń - miasto
3621	3269	3	Wieleń - obszar wiejski
3622	3317	3	Wielichowo
3623	3317	3	Wielichowo - miasto
3624	3317	3	Wielichowo - obszar wiejski
3625	3303	3	Wierzbinek
3626	3388	3	Wijewo
3627	3303	3	Wilczyn
3628	3276	3	Witkowo
3629	3276	3	Witkowo - miasto
3630	3276	3	Witkowo - obszar wiejski
3631	3250	3	Władysławów
3632	3388	3	Włoszakowice
3633	3522	3	Wolsztyn
3634	3522	3	Wolsztyn - miasto
3635	3522	3	Wolsztyn - obszar wiejski
3636	3298	3	Wronki
3637	3298	3	Wronki - miasto
3638	3298	3	Wronki - obszar wiejski
3639	3360	3	Września
3640	3360	3	Września - miasto
3641	3360	3	Września - obszar wiejski
3642	3235	3	Wyrzysk
3643	3235	3	Wyrzysk - miasto
3644	3235	3	Wyrzysk - obszar wiejski
3645	3235	3	Wysoka
3646	3235	3	Wysoka - miasto
3647	3235	3	Wysoka - obszar wiejski
3648	3404	3	Zagórów
3649	3404	3	Zagórów - miasto
3650	3404	3	Zagórów - obszar wiejski
3651	3329	3	Zakrzewo
3652	3293	3	Zaniemyśl
3653	3401	3	Zbąszyń
3654	3401	3	Zbąszyń - miasto
3655	3401	3	Zbąszyń - obszar wiejski
3656	3356	3	Zduny
3657	3356	3	Zduny - miasto
3658	3356	3	Zduny - obszar wiejski
3659	3329	3	Złotów
3660	3237	3	Żelazków
3661	3324	3	Żerków
3662	3324	3	Żerków - miasto
3663	3324	3	Żerków - obszar wiejski
3664	3262	3	Babiak
3665	\N	1	Zachodniopomorskie
3666	3665	2	Szczecinecki
3667	3666	3	Barwice
3668	3666	3	Barwice - miasto
3669	3666	3	Barwice - obszar wiejski
3670	3665	2	Koszaliński
3671	3670	3	Będzino
3672	3665	2	Białogardzki
3673	3672	3	Białogard
3674	3666	3	Biały Bór
3675	3666	3	Biały Bór - miasto
3676	3666	3	Biały Bór - obszar wiejski
3677	3665	2	Pyrzycki
3678	3677	3	Bielice
3679	3665	2	Choszczeński
3680	3679	3	Bierzwnik
3681	3670	3	Biesiekierz
3682	3670	3	Bobolice
3683	3670	3	Bobolice - miasto
3684	3670	3	Bobolice - obszar wiejski
3685	3665	2	Myśliborski
3686	3685	3	Boleszkowice
3687	3666	3	Borne Sulinowo
3688	3666	3	Borne Sulinowo - miasto
3689	3666	3	Borne Sulinowo - obszar wiejski
3690	3665	2	Gryficki
3691	3690	3	Brojce
3692	3665	2	Świdwiński
3693	3692	3	Brzeżno
3694	3665	2	Gryfiński
3695	3694	3	Cedynia
3696	3694	3	Cedynia - miasto
3697	3694	3	Cedynia - obszar wiejski
3698	3665	2	Stargardzki
3699	3698	3	Chociwel
3700	3698	3	Chociwel - miasto
3701	3698	3	Chociwel - obszar wiejski
3702	3694	3	Chojna
3703	3694	3	Chojna - miasto
3704	3694	3	Chojna - obszar wiejski
3705	3679	3	Choszczno
3706	3679	3	Choszczno - miasto
3707	3679	3	Choszczno - obszar wiejski
3708	3665	2	Drawski
3709	3708	3	Czaplinek
3710	3708	3	Czaplinek - miasto
3711	3708	3	Czaplinek - obszar wiejski
3712	3665	2	Wałecki
3713	3712	3	Człopa
3714	3712	3	Człopa - miasto
3715	3712	3	Człopa - obszar wiejski
3716	3665	2	Sławieński
3717	3716	3	Darłowo
3718	3685	3	Dębno
3719	3685	3	Dębno - miasto
3720	3685	3	Dębno - obszar wiejski
3721	3665	2	Goleniowski
3722	3721	3	Dobra
3723	3721	3	Dobra - miasto
3724	3721	3	Dobra - obszar wiejski
3725	3665	2	Policki
3726	3725	3	Dobra (Szczecińska)
3727	3698	3	Dobrzany
3728	3698	3	Dobrzany - miasto
3729	3698	3	Dobrzany - obszar wiejski
3730	3698	3	Dolice
3731	3679	3	Drawno
3732	3679	3	Drawno - miasto
3733	3679	3	Drawno - obszar wiejski
3734	3708	3	Drawsko Pomorskie
3735	3708	3	Drawsko Pomorskie - miasto
3736	3708	3	Drawsko Pomorskie - obszar wiejski
3737	3665	2	Kołobrzeski
3738	3737	3	Dygowo
3739	3665	2	Kamieński
3740	3739	3	Dziwnów
3741	3739	3	Golczewo
3742	3739	3	Golczewo - miasto
3743	3739	3	Golczewo - obszar wiejski
3744	3721	3	Goleniów
3745	3721	3	Goleniów - miasto
3746	3721	3	Goleniów - obszar wiejski
3747	3737	3	Gościno
3748	3690	3	Gryfice
3749	3690	3	Gryfice - miasto
3750	3690	3	Gryfice - obszar wiejski
3751	3694	3	Gryfino
3752	3694	3	Gryfino - miasto
3753	3694	3	Gryfino - obszar wiejski
3754	3666	3	Grzmiąca
3755	3698	3	Ińsko
3756	3698	3	Ińsko - miasto
3757	3698	3	Ińsko - obszar wiejski
3758	3708	3	Kalisz Pomorski
3759	3708	3	Kalisz Pomorski - miasto
3760	3708	3	Kalisz Pomorski - obszar wiejski
3761	3739	3	Kamień Pomorski
3762	3739	3	Kamień Pomorski - miasto
3763	3739	3	Kamień Pomorski - obszar wiejski
3764	3672	3	Karlino
3765	3672	3	Karlino - miasto
3766	3672	3	Karlino - obszar wiejski
3767	3690	3	Karnice
3768	3698	3	Kobylanka
3769	3725	3	Kołbaskowo
3770	3737	3	Kołobrzeg
3771	3677	3	Kozielice
3772	3679	3	Krzęcin
3773	3677	3	Lipiany
3774	3677	3	Lipiany - miasto
3775	3677	3	Lipiany - obszar wiejski
3776	3698	3	Łobez
3777	3698	3	Łobez - miasto
3778	3698	3	Łobez - obszar wiejski
3779	3665	2	Miasto Koszalin
3780	3779	3	Miasto Koszalin
3781	3665	2	Miasto Szczecin
3782	3781	3	Miasto Szczecin
3783	3665	2	Miasto Świnoujście
3784	3783	3	Miasto Świnoujście
3785	3716	3	Malechowo
3786	3670	3	Manowo
3787	3698	3	Marianowo
3788	3721	3	Maszewo
3789	3721	3	Maszewo - miasto
3790	3721	3	Maszewo - obszar wiejski
3791	3670	3	Mielno
3792	3694	3	Mieszkowice
3793	3694	3	Mieszkowice - miasto
3794	3694	3	Mieszkowice - obszar wiejski
3795	3739	3	Międzyzdroje
3796	3739	3	Międzyzdroje - miasto
3797	3739	3	Międzyzdroje - obszar wiejski
3798	3712	3	Mirosławiec
3799	3712	3	Mirosławiec - miasto
3800	3712	3	Mirosławiec - obszar wiejski
3801	3694	3	Moryń
3802	3694	3	Moryń - miasto
3803	3694	3	Moryń - obszar wiejski
3804	3685	3	Myślibórz
3805	3685	3	Myślibórz - miasto
3806	3685	3	Myślibórz - obszar wiejski
3807	3725	3	Nowe Warpno
3808	3725	3	Nowe Warpno - miasto
3809	3725	3	Nowe Warpno - obszar wiejski
3810	3721	3	Nowogard
3811	3721	3	Nowogard - miasto
3812	3721	3	Nowogard - obszar wiejski
3813	3685	3	Nowogródek Pomorski
3814	3721	3	Osina
3815	3708	3	Ostrowice
3816	3679	3	Pełczyce
3817	3679	3	Pełczyce - miasto
3818	3679	3	Pełczyce - obszar wiejski
3819	3690	3	Płoty
3820	3690	3	Płoty - miasto
3821	3690	3	Płoty - obszar wiejski
3822	3670	3	Polanów
3823	3670	3	Polanów - miasto
3824	3670	3	Polanów - obszar wiejski
3825	3725	3	Police
3826	3725	3	Police - miasto
3827	3725	3	Police - obszar wiejski
3828	3692	3	Połczyn-Zdrój
3829	3692	3	Połczyn-Zdrój - miasto
3830	3692	3	Połczyn-Zdrój - obszar wiejski
3831	3716	3	Postomino
3832	3677	3	Przelewice
3833	3721	3	Przybiernów
3834	3677	3	Pyrzyce
3835	3677	3	Pyrzyce - miasto
3836	3677	3	Pyrzyce - obszar wiejski
3837	3690	3	Radowo Małe
3838	3692	3	Rąbino
3839	3679	3	Recz
3840	3679	3	Recz - miasto
3841	3679	3	Recz - obszar wiejski
3842	3690	3	Resko
3843	3690	3	Resko - miasto
3844	3690	3	Resko - obszar wiejski
3845	3690	3	Rewal
3846	3737	3	Rymań
3847	3670	3	Sianów
3848	3670	3	Sianów - miasto
3849	3670	3	Sianów - obszar wiejski
3850	3737	3	Siemyśl
3851	3716	3	Sławno
3852	3692	3	Sławoborze
3853	3698	3	Stara Dąbrowa
3854	3694	3	Stare Czarnowo
3855	3698	3	Stargard Szczeciński
3856	3721	3	Stepnica
3857	3698	3	Suchań
3858	3698	3	Suchań - miasto
3859	3698	3	Suchań - obszar wiejski
3860	3666	3	Szczecinek
3861	3692	3	Świdwin
3862	3739	3	Świerzno
3863	3670	3	Świeszyno
3864	3694	3	Trzcińsko-Zdrój
3865	3694	3	Trzcińsko-Zdrój - miasto
3866	3694	3	Trzcińsko-Zdrój - obszar wiejski
3867	3690	3	Trzebiatów
3868	3690	3	Trzebiatów - miasto
3869	3690	3	Trzebiatów - obszar wiejski
3870	3712	3	Tuczno
3871	3712	3	Tuczno - miasto
3872	3712	3	Tuczno - obszar wiejski
3873	3672	3	Tychowo
3874	3737	3	Ustronie Morskie
3875	3712	3	Wałcz
3876	3677	3	Warnice
3877	3698	3	Węgorzyno
3878	3698	3	Węgorzyno - miasto
3879	3698	3	Węgorzyno - obszar wiejski
3880	3694	3	Widuchowa
3881	3708	3	Wierzchowo
3882	3739	3	Wolin
3883	3739	3	Wolin - miasto
3884	3739	3	Wolin - obszar wiejski
3885	3708	3	Złocieniec
3886	3708	3	Złocieniec - miasto
3887	3708	3	Złocieniec - obszar wiejski
3888	3694	3	Banie
3889	3685	3	Barlinek
3890	3685	3	Barlinek - miasto
3891	3685	3	Barlinek - obszar wiejski
3892	3043	2	Kętrzyński
3896	3043	2	Węgorzewski
3897	2633	2	Bieruńsko-Lędziński
\.


--
-- Data for Name: poland_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY poland_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	*	t	t	t	t
\.


--
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY projects (id, name, code, description, created_date, start_date, end_date, status) FROM stdin;
2	Osiedlowy Dom Kultury	ODK		2011-12-01	2011-12-01	2012-12-01	1
\.


--
-- Data for Name: projects_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY projects_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
\.


--
-- Data for Name: quiz_scores; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY quiz_scores (id, user_id, quiz_id, level, score, start_time, total_time, status) FROM stdin;
1	4	1	1	118	1322225886	36	0
2	2	1	1	118	1322752134	44	0
\.


--
-- Data for Name: quiz_scores_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY quiz_scores_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_adanow0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	t	f	f	f
0	yala_adanow0	f	f	t	f
0	yala_simpli0	f	f	t	f
0	yala_robert_posiadala_gammanet_pl	f	f	t	f
0	yala_cypherq	f	f	t	f
\.


--
-- Data for Name: quiz_users; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY quiz_users (id, quiz_id, user_id) FROM stdin;
1	1	4
\.


--
-- Data for Name: quiz_users_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY quiz_users_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	t	f	t	f
\.


--
-- Data for Name: quizzes; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY quizzes (id, name, description, time_limit, url) FROM stdin;
1	Memory Game	gra na zapamiętywanie	1800	http://www.playdorado.pl/memory-game
\.


--
-- Data for Name: quizzes_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY quizzes_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_adanow0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	t	f	f	f
\.


--
-- Data for Name: reports; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY reports (id, parent_id, name, description, path, project_id) FROM stdin;
1	\N	Lista obecności uczestników szkolenia	Generuje listę uczestników danej grupy szkoleniowej	presence_list.jrxml	\N
2	\N	Lista na drzwi	Lista na drzwi	door_list.jrxml	\N
3	\N	Zaświadczenia	Zaświadczenia	certificates.jrxml	\N
4	\N	Potwierdzenie odbioru zaświadczeń	Potwierdzenie odbioru zaświadczeń	certificates_receive_confirmation.jrxml	\N
5	\N	Potwierdzenie odbioru loginów	Potwierdzenie odbioru loginów	logins_receive_confirmation.jrxml	\N
6	\N	Potwierdzenie odbioru materiałów	Potwierdzenie odbioru materiałów	training_materials_receive_confirmation.jrxml	\N
7	\N	Harmonogram szkolenia	Harmonogram szkolenia	course_schedule.jrxml	\N
8	\N	Karta zgłoszeniowa	Karta zgłoszeniowa	registration_form.jrxml	\N
9	\N	Wyniki ankiety/testu	Wyniki ankiety/testu	survey_results.jrxml	\N
\.


--
-- Data for Name: reports_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY reports_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
\.


--
-- Data for Name: resource_types; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY resource_types (id, name) FROM stdin;
\.


--
-- Data for Name: resource_types_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY resource_types_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	*	t	t	t	t
\.


--
-- Data for Name: resources; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY resources (id, training_center_id, resource_type_id, amount) FROM stdin;
\.


--
-- Data for Name: resources_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY resources_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	*	t	t	t	t
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY roles (id, name) FROM stdin;
1	admin
2	user
3	project leader
4	center leader
5	trainer
\.


--
-- Data for Name: roles_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY roles_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	*	t	t	t	t
\.


--
-- Data for Name: rooms; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY rooms (id, training_center_id, name, symbol, description, available_space) FROM stdin;
4	4	piano	piano	Do lekcji gry na pianinie	2
\.


--
-- Data for Name: rooms_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY rooms_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	*	t	t	t	t
\.


--
-- Data for Name: survey_detailed_results; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_detailed_results (id, survey_result_id, question_id, answer_id, answer_content) FROM stdin;
1	1	1	2	\N
\.


--
-- Data for Name: survey_detailed_results_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_detailed_results_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_adanow0	t	t	t	t
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
0	yala_adanow0	f	f	t	f
0	yala_simpli0	f	f	t	f
0	yala_robert_posiadala_gammanet_pl	f	f	t	f
0	yala_cypherq	f	f	t	f
\.


--
-- Data for Name: survey_possible_answers; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_possible_answers (id, question_id, content, correct, selected_by_default) FROM stdin;
1	1	Serwis plotkarski	\N	\N
2	1	Serwis społecznościowy	\N	\N
3	1	portal	\N	\N
4	1	Gazeta	\N	\N
5	1	czasopismo	\N	\N
\.


--
-- Data for Name: survey_possible_answers_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_possible_answers_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_trener	t	f	f	f
2	yala_trener	t	f	f	f
3	yala_trener	t	f	f	f
4	yala_trener	t	f	f	f
5	yala_trener	t	f	f	f
1	yala_adanow0	t	f	f	f
2	yala_adanow0	t	f	f	f
3	yala_adanow0	t	f	f	f
4	yala_adanow0	t	f	f	f
5	yala_adanow0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
\.


--
-- Data for Name: survey_questions; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_questions (id, survey_id, type, title, help, required, "position") FROM stdin;
1	1	multichoice	Co to jest facebook	\N	1	0
\.


--
-- Data for Name: survey_questions_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_questions_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_trener	t	f	f	f
1	yala_adanow0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
\.


--
-- Data for Name: survey_results; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_results (id, user_id, survey_id, percent_result, completed, created) FROM stdin;
1	4	1	\N	1	\N
\.


--
-- Data for Name: survey_results_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_results_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_adanow0	t	t	t	t
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
0	yala_adanow0	f	f	t	f
0	yala_simpli0	f	f	t	f
0	yala_robert_posiadala_gammanet_pl	f	f	t	f
0	yala_cypherq	f	f	t	f
\.


--
-- Data for Name: survey_users; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_users (id, survey_id, user_id, filled, deadline, sent) FROM stdin;
1	1	4	1	\N	2011-11-25 14:01:56
\.


--
-- Data for Name: survey_users_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY survey_users_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_adanow0	t	t	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
\.


--
-- Data for Name: surveys; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY surveys (id, user_id, name, description, type, archived, project_id, created_date, library, completed) FROM stdin;
1	2	Ankieta ze znajomości internetu	\N	survey	\N	\N	2011-11-25 14:01:48.779985	1	\N
\.


--
-- Data for Name: surveys_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY surveys_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
1	yala_trener	t	f	f	f
1	yala_adanow0	t	f	f	f
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
\.


--
-- Data for Name: training_centers; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY training_centers (id, name, street, zip_code, city, manager, url, rating, room_amount, seats_amount, code, description, phone_number) FROM stdin;
4	Osiedlowy Dom Kultury ORBITA	Kosmonautów 118	61-467	Poznań	Janina Kowalska	\N	5	1	2	ORBITA	<p><span style="font-family:Arial"><font id="__elementToFocus__" size="2">﻿</font></span><strong style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); ">Osiedlowy Dom Kultury „Orbita”</strong><span class="Apple-style-span" style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; background-color: rgb(255, 255, 255); ">&nbsp;</span><span class="Apple-style-span" style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); ">należy do Poznańskiej Spółdzielni Mieszkaniowej „Winogrady”. Dom kultury organizuje zajęcia stałe:</span></p><ul style="padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; list-style-position: outside; list-style-type: none; color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); "><li>Aerobik</li><li>Nauka gry na instrumentach</li></ul><p style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); ">-pianino</p><p style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); ">-gitara</p><p style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); ">-organy</p><ul style="padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; list-style-position: outside; list-style-type: none; color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); "><li>Zajęcia w harcówce</li><li>Klub Seniora</li><li>Zajęcia teatralne dla dzieci w wieku 7-12 lat</li><li>choreoterapia</li><li>Gimnastyka dla dorosłych "Zdrowy kręgosłup"</li></ul><p style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); ">Na terenie Domu Kultura działa biblioteka.</p><p style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); ">Biblioteka</p><p style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); ">poniedziałek, środa, piątek – 13:00 – 19:00</p><p style="color: rgb(55, 55, 55); font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; text-align: justify; background-color: rgb(255, 255, 255); ">wtorek, czwartek – 9:00 – 14:00</p>	
\.


--
-- Data for Name: training_centers_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY training_centers_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
\.


--
-- Data for Name: user_profile; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY user_profile (id, user_id, sex, national_identity, address_city, address_zip_code, address_street, poland_id, phone_number, fax_number, mobile_number, birth_date, birth_place, work_name, work_city, work_zip_code, work_street, work_tax_identification_number, tax_identification_number, tax_office, tax_office_address, identification_name, identification_number, identification_publisher, father_name, mother_name, nfz, bank, printed) FROM stdin;
1	4	M		Poznań			\N		\N		\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0
2	5	M		Poznań			\N		\N		\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0
\.


--
-- Data for Name: user_profile_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY user_profile_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	f	f	t	f
0	yala_adanow0	f	f	t	f
0	yala_simpli0	f	f	t	f
0	yala_robert_posiadala_gammanet_pl	f	f	t	f
0	yala_cypherq	f	f	t	f
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY users (id, username, first_name, last_name, plain_password, role_id, email, key, is_google) FROM stdin;
1	yala	Administrator	Systemu	\N	1	\N	\N	0
2	admin	Administrator	Systemu	\N	1	\N	\N	0
3	trener	Łukasz	Mirek	\N	5	\N	\N	0
4	adanow0	Adam	Nowak	da0c5331	2	adam@nowak.pl	\N	0
5	simpli0	Simer	Plimer	99480971	2	simer@gammanet.pl	\N	0
7	googlecalendar	google	calendar	\N	1	\N	\N	0
12	robert_posiadala_gammanet_pl	Robert	Posiadała	\N	2	robert.posiadala@gammanet.pl	\N	1
16	cypherq	Benedykt	Dryl	af0083ce	2	cypherq@promienko.pl	\N	0
\.


--
-- Data for Name: users_acl; Type: TABLE DATA; Schema: public; Owner: yala
--

COPY users_acl (object_id, username, _select, _update, _insert, _delete) FROM stdin;
0	yala_yala	t	t	t	t
0	yala_admin	t	t	t	t
0	yala_googlecalendar	t	t	t	t
0	yala_trener	t	f	f	f
0	yala_adanow0	t	f	f	f
0	yala_simpli0	t	f	f	f
0	yala_robert_posiadala_gammanet_pl	t	f	f	f
0	yala_cypherq	t	f	f	f
\.


--
-- Name: acl_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY acl
    ADD CONSTRAINT acl_pkey PRIMARY KEY (id);


--
-- Name: apps_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY apps
    ADD CONSTRAINT apps_pkey PRIMARY KEY (id);


--
-- Name: course_schedule_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY course_schedule
    ADD CONSTRAINT course_schedule_pkey PRIMARY KEY (id);


--
-- Name: course_units_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY course_units
    ADD CONSTRAINT course_units_pkey PRIMARY KEY (id);


--
-- Name: courses_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY courses
    ADD CONSTRAINT courses_pkey PRIMARY KEY (id);


--
-- Name: exam_grades_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY exam_grades
    ADD CONSTRAINT exam_grades_pkey PRIMARY KEY (id);


--
-- Name: exams_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY exams
    ADD CONSTRAINT exams_pkey PRIMARY KEY (id);


--
-- Name: files_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY files
    ADD CONSTRAINT files_pkey PRIMARY KEY (id);


--
-- Name: google_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY google_tokens
    ADD CONSTRAINT google_tokens_pkey PRIMARY KEY (id);


--
-- Name: group_users_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY group_users
    ADD CONSTRAINT group_users_pkey PRIMARY KEY (id);


--
-- Name: groups_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_pkey PRIMARY KEY (id);


--
-- Name: lesson_presence_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY lesson_presence
    ADD CONSTRAINT lesson_presence_pkey PRIMARY KEY (id);


--
-- Name: lessons_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY lessons
    ADD CONSTRAINT lessons_pkey PRIMARY KEY (id);


--
-- Name: message_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY message_attachments
    ADD CONSTRAINT message_attachments_pkey PRIMARY KEY (id);


--
-- Name: message_users_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY message_users
    ADD CONSTRAINT message_users_pkey PRIMARY KEY (id);


--
-- Name: messages_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- Name: poland_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY poland
    ADD CONSTRAINT poland_pkey PRIMARY KEY (id);


--
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- Name: quiz_scores_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY quiz_scores
    ADD CONSTRAINT quiz_scores_pkey PRIMARY KEY (id);


--
-- Name: quiz_users_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY quiz_users
    ADD CONSTRAINT quiz_users_pkey PRIMARY KEY (id);


--
-- Name: quizzes_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY quizzes
    ADD CONSTRAINT quizzes_pkey PRIMARY KEY (id);


--
-- Name: reports_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY reports
    ADD CONSTRAINT reports_pkey PRIMARY KEY (id);


--
-- Name: resource_types_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY resource_types
    ADD CONSTRAINT resource_types_pkey PRIMARY KEY (id);


--
-- Name: resources_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY resources
    ADD CONSTRAINT resources_pkey PRIMARY KEY (id);


--
-- Name: roles_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY rooms
    ADD CONSTRAINT rooms_pkey PRIMARY KEY (id);


--
-- Name: survey_detailed_results_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY survey_detailed_results
    ADD CONSTRAINT survey_detailed_results_pkey PRIMARY KEY (id);


--
-- Name: survey_possible_answers_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY survey_possible_answers
    ADD CONSTRAINT survey_possible_answers_pkey PRIMARY KEY (id);


--
-- Name: survey_questions_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY survey_questions
    ADD CONSTRAINT survey_questions_pkey PRIMARY KEY (id);


--
-- Name: survey_results_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY survey_results
    ADD CONSTRAINT survey_results_pkey PRIMARY KEY (id);


--
-- Name: survey_users_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY survey_users
    ADD CONSTRAINT survey_users_pkey PRIMARY KEY (id);


--
-- Name: surveys_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY surveys
    ADD CONSTRAINT surveys_pkey PRIMARY KEY (id);


--
-- Name: training_centers_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY training_centers
    ADD CONSTRAINT training_centers_pkey PRIMARY KEY (id);


--
-- Name: unique_user_survey_answer_ids_idx; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY survey_detailed_results
    ADD CONSTRAINT unique_user_survey_answer_ids_idx UNIQUE (answer_id, survey_result_id, question_id);


--
-- Name: unique_user_survey_ids_idx; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY survey_results
    ADD CONSTRAINT unique_user_survey_ids_idx UNIQUE (user_id, survey_id);


--
-- Name: uq_email_idx; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT uq_email_idx UNIQUE (email);


--
-- Name: uq_survey_for_user_idx; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY survey_users
    ADD CONSTRAINT uq_survey_for_user_idx UNIQUE (user_id, survey_id);


--
-- Name: user_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY user_profile
    ADD CONSTRAINT user_profile_pkey PRIMARY KEY (id);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: yala; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX acl_key ON acl USING btree (table_name, username, object_id);


--
-- Name: acl_updated_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE INDEX acl_updated_key ON acl USING btree (updated);


--
-- Name: apps_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX apps_acl_key ON apps_acl USING btree (object_id, username);


--
-- Name: course_schedule_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX course_schedule_acl_key ON course_schedule_acl USING btree (object_id, username);


--
-- Name: course_units_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX course_units_acl_key ON course_units_acl USING btree (object_id, username);


--
-- Name: courses_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX courses_acl_key ON courses_acl USING btree (object_id, username);


--
-- Name: exam_grades_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX exam_grades_acl_key ON exam_grades_acl USING btree (object_id, username);


--
-- Name: exams_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX exams_acl_key ON exams_acl USING btree (object_id, username);


--
-- Name: files_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX files_acl_key ON files_acl USING btree (object_id, username);


--
-- Name: google_tokens_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX google_tokens_acl_key ON google_tokens_acl USING btree (object_id, username);


--
-- Name: group_users_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX group_users_acl_key ON group_users_acl USING btree (object_id, username);


--
-- Name: groups_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX groups_acl_key ON groups_acl USING btree (object_id, username);


--
-- Name: lesson_presence_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX lesson_presence_acl_key ON lesson_presence_acl USING btree (object_id, username);


--
-- Name: lessons_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX lessons_acl_key ON lessons_acl USING btree (object_id, username);


--
-- Name: message_attachments_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX message_attachments_acl_key ON message_attachments_acl USING btree (object_id, username);


--
-- Name: message_users_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX message_users_acl_key ON message_users_acl USING btree (object_id, username);


--
-- Name: messages_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX messages_acl_key ON messages_acl USING btree (object_id, username);


--
-- Name: poland_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX poland_acl_key ON poland_acl USING btree (object_id, username);


--
-- Name: projects_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX projects_acl_key ON projects_acl USING btree (object_id, username);


--
-- Name: quiz_scores_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX quiz_scores_acl_key ON quiz_scores_acl USING btree (object_id, username);


--
-- Name: quiz_users_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX quiz_users_acl_key ON quiz_users_acl USING btree (object_id, username);


--
-- Name: quizzes_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX quizzes_acl_key ON quizzes_acl USING btree (object_id, username);


--
-- Name: reports_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX reports_acl_key ON reports_acl USING btree (object_id, username);


--
-- Name: resource_types_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX resource_types_acl_key ON resource_types_acl USING btree (object_id, username);


--
-- Name: resources_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX resources_acl_key ON resources_acl USING btree (object_id, username);


--
-- Name: roles_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX roles_acl_key ON roles_acl USING btree (object_id, username);


--
-- Name: rooms_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX rooms_acl_key ON rooms_acl USING btree (object_id, username);


--
-- Name: survey_detailed_results_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX survey_detailed_results_acl_key ON survey_detailed_results_acl USING btree (object_id, username);


--
-- Name: survey_possible_answers_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX survey_possible_answers_acl_key ON survey_possible_answers_acl USING btree (object_id, username);


--
-- Name: survey_questions_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX survey_questions_acl_key ON survey_questions_acl USING btree (object_id, username);


--
-- Name: survey_results_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX survey_results_acl_key ON survey_results_acl USING btree (object_id, username);


--
-- Name: survey_users_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX survey_users_acl_key ON survey_users_acl USING btree (object_id, username);


--
-- Name: surveys_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX surveys_acl_key ON surveys_acl USING btree (object_id, username);


--
-- Name: training_centers_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX training_centers_acl_key ON training_centers_acl USING btree (object_id, username);


--
-- Name: user_profile_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX user_profile_acl_key ON user_profile_acl USING btree (object_id, username);


--
-- Name: users_acl_key; Type: INDEX; Schema: public; Owner: yala; Tablespace: 
--

CREATE UNIQUE INDEX users_acl_key ON users_acl USING btree (object_id, username);


--
-- Name: apps_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE apps_delete AS ON DELETE TO apps WHERE (NOT acl_has_right('apps'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: apps_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE apps_insert AS ON INSERT TO apps WHERE (NOT acl_has_right('apps'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: apps_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE apps_update AS ON UPDATE TO apps WHERE (NOT acl_has_right('apps'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: course_schedule_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE course_schedule_delete AS ON DELETE TO course_schedule WHERE (NOT acl_has_right('course_schedule'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: course_schedule_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE course_schedule_insert AS ON INSERT TO course_schedule WHERE (NOT acl_has_right('course_schedule'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: course_schedule_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE course_schedule_update AS ON UPDATE TO course_schedule WHERE (NOT acl_has_right('course_schedule'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: course_units_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE course_units_delete AS ON DELETE TO course_units WHERE (NOT acl_has_right('course_units'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: course_units_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE course_units_insert AS ON INSERT TO course_units WHERE (NOT acl_has_right('course_units'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: course_units_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE course_units_update AS ON UPDATE TO course_units WHERE (NOT acl_has_right('course_units'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: courses_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE courses_delete AS ON DELETE TO courses WHERE (NOT acl_has_right('courses'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: courses_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE courses_insert AS ON INSERT TO courses WHERE (NOT acl_has_right('courses'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: courses_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE courses_update AS ON UPDATE TO courses WHERE (NOT acl_has_right('courses'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: exam_grades_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE exam_grades_delete AS ON DELETE TO exam_grades WHERE (NOT acl_has_right('exam_grades'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: exam_grades_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE exam_grades_insert AS ON INSERT TO exam_grades WHERE (NOT acl_has_right('exam_grades'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: exam_grades_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE exam_grades_update AS ON UPDATE TO exam_grades WHERE (NOT acl_has_right('exam_grades'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: exams_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE exams_delete AS ON DELETE TO exams WHERE (NOT acl_has_right('exams'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: exams_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE exams_insert AS ON INSERT TO exams WHERE (NOT acl_has_right('exams'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: exams_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE exams_update AS ON UPDATE TO exams WHERE (NOT acl_has_right('exams'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: files_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE files_delete AS ON DELETE TO files WHERE (NOT acl_has_right('files'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: files_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE files_insert AS ON INSERT TO files WHERE (NOT acl_has_right('files'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: files_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE files_update AS ON UPDATE TO files WHERE (NOT acl_has_right('files'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: google_tokens_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE google_tokens_delete AS ON DELETE TO google_tokens WHERE (NOT acl_has_right('google_tokens'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: google_tokens_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE google_tokens_insert AS ON INSERT TO google_tokens WHERE (NOT acl_has_right('google_tokens'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: google_tokens_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE google_tokens_update AS ON UPDATE TO google_tokens WHERE (NOT acl_has_right('google_tokens'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: group_users_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE group_users_delete AS ON DELETE TO group_users WHERE (NOT acl_has_right('group_users'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: group_users_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE group_users_insert AS ON INSERT TO group_users WHERE (NOT acl_has_right('group_users'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: group_users_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE group_users_update AS ON UPDATE TO group_users WHERE (NOT acl_has_right('group_users'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: groups_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE groups_delete AS ON DELETE TO groups WHERE (NOT acl_has_right('groups'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: groups_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE groups_insert AS ON INSERT TO groups WHERE (NOT acl_has_right('groups'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: groups_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE groups_update AS ON UPDATE TO groups WHERE (NOT acl_has_right('groups'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: lesson_presence_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE lesson_presence_delete AS ON DELETE TO lesson_presence WHERE (NOT acl_has_right('lesson_presence'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: lesson_presence_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE lesson_presence_insert AS ON INSERT TO lesson_presence WHERE (NOT acl_has_right('lesson_presence'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: lesson_presence_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE lesson_presence_update AS ON UPDATE TO lesson_presence WHERE (NOT acl_has_right('lesson_presence'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: lessons_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE lessons_delete AS ON DELETE TO lessons WHERE (NOT acl_has_right('lessons'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: lessons_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE lessons_insert AS ON INSERT TO lessons WHERE (NOT acl_has_right('lessons'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: lessons_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE lessons_update AS ON UPDATE TO lessons WHERE (NOT acl_has_right('lessons'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: message_attachments_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE message_attachments_delete AS ON DELETE TO message_attachments WHERE (NOT acl_has_right('message_attachments'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: message_attachments_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE message_attachments_insert AS ON INSERT TO message_attachments WHERE (NOT acl_has_right('message_attachments'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: message_attachments_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE message_attachments_update AS ON UPDATE TO message_attachments WHERE (NOT acl_has_right('message_attachments'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: message_users_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE message_users_delete AS ON DELETE TO message_users WHERE (NOT acl_has_right('message_users'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: message_users_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE message_users_insert AS ON INSERT TO message_users WHERE (NOT acl_has_right('message_users'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: message_users_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE message_users_update AS ON UPDATE TO message_users WHERE (NOT acl_has_right('message_users'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: messages_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE messages_delete AS ON DELETE TO messages WHERE (NOT acl_has_right('messages'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: messages_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE messages_insert AS ON INSERT TO messages WHERE (NOT acl_has_right('messages'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: messages_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE messages_update AS ON UPDATE TO messages WHERE (NOT acl_has_right('messages'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: poland_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE poland_delete AS ON DELETE TO poland WHERE (NOT acl_has_right('poland'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: poland_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE poland_insert AS ON INSERT TO poland WHERE (NOT acl_has_right('poland'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: poland_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE poland_update AS ON UPDATE TO poland WHERE (NOT acl_has_right('poland'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: projects_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE projects_delete AS ON DELETE TO projects WHERE (NOT acl_has_right('projects'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: projects_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE projects_insert AS ON INSERT TO projects WHERE (NOT acl_has_right('projects'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: projects_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE projects_update AS ON UPDATE TO projects WHERE (NOT acl_has_right('projects'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: quiz_scores_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE quiz_scores_delete AS ON DELETE TO quiz_scores WHERE (NOT acl_has_right('quiz_scores'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: quiz_scores_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE quiz_scores_insert AS ON INSERT TO quiz_scores WHERE (NOT acl_has_right('quiz_scores'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: quiz_scores_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE quiz_scores_update AS ON UPDATE TO quiz_scores WHERE (NOT acl_has_right('quiz_scores'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: quiz_users_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE quiz_users_delete AS ON DELETE TO quiz_users WHERE (NOT acl_has_right('quiz_users'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: quiz_users_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE quiz_users_insert AS ON INSERT TO quiz_users WHERE (NOT acl_has_right('quiz_users'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: quiz_users_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE quiz_users_update AS ON UPDATE TO quiz_users WHERE (NOT acl_has_right('quiz_users'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: quizzes_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE quizzes_delete AS ON DELETE TO quizzes WHERE (NOT acl_has_right('quizzes'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: quizzes_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE quizzes_insert AS ON INSERT TO quizzes WHERE (NOT acl_has_right('quizzes'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: quizzes_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE quizzes_update AS ON UPDATE TO quizzes WHERE (NOT acl_has_right('quizzes'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: reports_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE reports_delete AS ON DELETE TO reports WHERE (NOT acl_has_right('reports'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: reports_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE reports_insert AS ON INSERT TO reports WHERE (NOT acl_has_right('reports'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: reports_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE reports_update AS ON UPDATE TO reports WHERE (NOT acl_has_right('reports'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: resource_types_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE resource_types_delete AS ON DELETE TO resource_types WHERE (NOT acl_has_right('resource_types'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: resource_types_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE resource_types_insert AS ON INSERT TO resource_types WHERE (NOT acl_has_right('resource_types'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: resource_types_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE resource_types_update AS ON UPDATE TO resource_types WHERE (NOT acl_has_right('resource_types'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: resources_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE resources_delete AS ON DELETE TO resources WHERE (NOT acl_has_right('resources'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: resources_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE resources_insert AS ON INSERT TO resources WHERE (NOT acl_has_right('resources'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: resources_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE resources_update AS ON UPDATE TO resources WHERE (NOT acl_has_right('resources'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: roles_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE roles_delete AS ON DELETE TO roles WHERE (NOT acl_has_right('roles'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: roles_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE roles_insert AS ON INSERT TO roles WHERE (NOT acl_has_right('roles'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: roles_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE roles_update AS ON UPDATE TO roles WHERE (NOT acl_has_right('roles'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: rooms_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE rooms_delete AS ON DELETE TO rooms WHERE (NOT acl_has_right('rooms'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: rooms_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE rooms_insert AS ON INSERT TO rooms WHERE (NOT acl_has_right('rooms'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: rooms_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE rooms_update AS ON UPDATE TO rooms WHERE (NOT acl_has_right('rooms'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_detailed_results_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_detailed_results_delete AS ON DELETE TO survey_detailed_results WHERE (NOT acl_has_right('survey_detailed_results'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_detailed_results_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_detailed_results_insert AS ON INSERT TO survey_detailed_results WHERE (NOT acl_has_right('survey_detailed_results'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_detailed_results_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_detailed_results_update AS ON UPDATE TO survey_detailed_results WHERE (NOT acl_has_right('survey_detailed_results'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_possible_answers_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_possible_answers_delete AS ON DELETE TO survey_possible_answers WHERE (NOT acl_has_right('survey_possible_answers'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_possible_answers_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_possible_answers_insert AS ON INSERT TO survey_possible_answers WHERE (NOT acl_has_right('survey_possible_answers'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_possible_answers_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_possible_answers_update AS ON UPDATE TO survey_possible_answers WHERE (NOT acl_has_right('survey_possible_answers'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_questions_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_questions_delete AS ON DELETE TO survey_questions WHERE (NOT acl_has_right('survey_questions'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_questions_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_questions_insert AS ON INSERT TO survey_questions WHERE (NOT acl_has_right('survey_questions'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_questions_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_questions_update AS ON UPDATE TO survey_questions WHERE (NOT acl_has_right('survey_questions'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_results_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_results_delete AS ON DELETE TO survey_results WHERE (NOT acl_has_right('survey_results'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_results_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_results_insert AS ON INSERT TO survey_results WHERE (NOT acl_has_right('survey_results'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_results_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_results_update AS ON UPDATE TO survey_results WHERE (NOT acl_has_right('survey_results'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_users_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_users_delete AS ON DELETE TO survey_users WHERE (NOT acl_has_right('survey_users'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_users_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_users_insert AS ON INSERT TO survey_users WHERE (NOT acl_has_right('survey_users'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: survey_users_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE survey_users_update AS ON UPDATE TO survey_users WHERE (NOT acl_has_right('survey_users'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: surveys_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE surveys_delete AS ON DELETE TO surveys WHERE (NOT acl_has_right('surveys'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: surveys_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE surveys_insert AS ON INSERT TO surveys WHERE (NOT acl_has_right('surveys'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: surveys_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE surveys_update AS ON UPDATE TO surveys WHERE (NOT acl_has_right('surveys'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: training_centers_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE training_centers_delete AS ON DELETE TO training_centers WHERE (NOT acl_has_right('training_centers'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: training_centers_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE training_centers_insert AS ON INSERT TO training_centers WHERE (NOT acl_has_right('training_centers'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: training_centers_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE training_centers_update AS ON UPDATE TO training_centers WHERE (NOT acl_has_right('training_centers'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: user_profile_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE user_profile_delete AS ON DELETE TO user_profile WHERE (NOT acl_has_right('user_profile'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: user_profile_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE user_profile_insert AS ON INSERT TO user_profile WHERE (NOT acl_has_right('user_profile'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: user_profile_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE user_profile_update AS ON UPDATE TO user_profile WHERE (NOT acl_has_right('user_profile'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: users_delete; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE users_delete AS ON DELETE TO users WHERE (NOT acl_has_right('users'::name, old.id, 'delete'::character varying)) DO INSTEAD NOTHING;


--
-- Name: users_insert; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE users_insert AS ON INSERT TO users WHERE (NOT acl_has_right('users'::name, 0, 'insert'::character varying)) DO INSTEAD NOTHING;


--
-- Name: users_update; Type: RULE; Schema: public; Owner: yala
--

CREATE RULE users_update AS ON UPDATE TO users WHERE (NOT acl_has_right('users'::name, new.id, 'update'::character varying)) DO INSTEAD NOTHING;


--
-- Name: acl_table_change; Type: TRIGGER; Schema: public; Owner: yala
--

CREATE TRIGGER acl_table_change
    BEFORE UPDATE ON acl
    FOR EACH ROW
    EXECUTE PROCEDURE acl_table_change();


--
-- Name: acl_table_delete; Type: TRIGGER; Schema: public; Owner: yala
--

CREATE TRIGGER acl_table_delete
    BEFORE UPDATE ON acl
    FOR EACH ROW
    EXECUTE PROCEDURE acl_table_delete();


--
-- Name: fk_course_schedule_group; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY course_schedule
    ADD CONSTRAINT fk_course_schedule_group FOREIGN KEY (course_unit_id) REFERENCES course_units(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_course_units_courses; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY course_units
    ADD CONSTRAINT fk_course_units_courses FOREIGN KEY (course_id) REFERENCES courses(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_course_units_users; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY course_units
    ADD CONSTRAINT fk_course_units_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_courses_groups; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY courses
    ADD CONSTRAINT fk_courses_groups FOREIGN KEY (group_id) REFERENCES groups(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_courses_project_id; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY courses
    ADD CONSTRAINT fk_courses_project_id FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_courses_training_center; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY courses
    ADD CONSTRAINT fk_courses_training_center FOREIGN KEY (training_center_id) REFERENCES training_centers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_exam_grades_exams; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY exam_grades
    ADD CONSTRAINT fk_exam_grades_exams FOREIGN KEY (exam_id) REFERENCES exams(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_exam_grades_users; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY exam_grades
    ADD CONSTRAINT fk_exam_grades_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_exams_course_units; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY exams
    ADD CONSTRAINT fk_exams_course_units FOREIGN KEY (course_unit_id) REFERENCES course_units(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_files_users; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY files
    ADD CONSTRAINT fk_files_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_lesson_course_unit; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY lessons
    ADD CONSTRAINT fk_lesson_course_unit FOREIGN KEY (course_unit_id) REFERENCES course_units(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_lesson_presence_lessons; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY lesson_presence
    ADD CONSTRAINT fk_lesson_presence_lessons FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_lesson_presence_users; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY lesson_presence
    ADD CONSTRAINT fk_lesson_presence_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_lesson_training_room; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY lessons
    ADD CONSTRAINT fk_lesson_training_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_lesson_user; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY lessons
    ADD CONSTRAINT fk_lesson_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_message_attachments_files; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY message_attachments
    ADD CONSTRAINT fk_message_attachments_files FOREIGN KEY (file_id) REFERENCES files(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_message_attachments_messages; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY message_attachments
    ADD CONSTRAINT fk_message_attachments_messages FOREIGN KEY (message_id) REFERENCES messages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_messages_users; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY messages
    ADD CONSTRAINT fk_messages_users FOREIGN KEY (sender_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_messages_users_messages; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY message_users
    ADD CONSTRAINT fk_messages_users_messages FOREIGN KEY (message_id) REFERENCES messages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_messages_users_users; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY message_users
    ADD CONSTRAINT fk_messages_users_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_poland_poland; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY poland
    ADD CONSTRAINT fk_poland_poland FOREIGN KEY (parent_id) REFERENCES poland(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_quiz_users_quizzes; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY quiz_users
    ADD CONSTRAINT fk_quiz_users_quizzes FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_quiz_users_users; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY quiz_users
    ADD CONSTRAINT fk_quiz_users_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_reports_project_id; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY reports
    ADD CONSTRAINT fk_reports_project_id FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_resource_types; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY resources
    ADD CONSTRAINT fk_resource_types FOREIGN KEY (resource_type_id) REFERENCES resource_types(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: fk_rooms_training_centers; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY rooms
    ADD CONSTRAINT fk_rooms_training_centers FOREIGN KEY (training_center_id) REFERENCES training_centers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_self_report_templates; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY reports
    ADD CONSTRAINT fk_self_report_templates FOREIGN KEY (parent_id) REFERENCES reports(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_survey_users_survey; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY survey_users
    ADD CONSTRAINT fk_survey_users_survey FOREIGN KEY (survey_id) REFERENCES surveys(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_survey_users_user; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY survey_users
    ADD CONSTRAINT fk_survey_users_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_tokens__user; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY google_tokens
    ADD CONSTRAINT fk_tokens__user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_training_centers_resources; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY resources
    ADD CONSTRAINT fk_training_centers_resources FOREIGN KEY (training_center_id) REFERENCES training_centers(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: fk_user_profile_poland; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY user_profile
    ADD CONSTRAINT fk_user_profile_poland FOREIGN KEY (poland_id) REFERENCES poland(id) ON UPDATE CASCADE;


--
-- Name: fk_user_profile_users; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY user_profile
    ADD CONSTRAINT fk_user_profile_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_users_roles; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY users
    ADD CONSTRAINT fk_users_roles FOREIGN KEY (role_id) REFERENCES roles(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: groups_user_fk; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY group_users
    ADD CONSTRAINT groups_user_fk FOREIGN KEY (group_id) REFERENCES groups(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: quiz_scores_quizes; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY quiz_scores
    ADD CONSTRAINT quiz_scores_quizes FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: quiz_scores_users; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY quiz_scores
    ADD CONSTRAINT quiz_scores_users FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: survey_question_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY survey_possible_answers
    ADD CONSTRAINT survey_question_id_fk FOREIGN KEY (question_id) REFERENCES survey_questions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: survey_survey_question; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY survey_questions
    ADD CONSTRAINT survey_survey_question FOREIGN KEY (survey_id) REFERENCES surveys(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: survey_survey_result; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY survey_detailed_results
    ADD CONSTRAINT survey_survey_result FOREIGN KEY (survey_result_id) REFERENCES survey_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_groups_fk; Type: FK CONSTRAINT; Schema: public; Owner: yala
--

ALTER TABLE ONLY group_users
    ADD CONSTRAINT user_groups_fk FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE acl FROM PUBLIC;
REVOKE ALL ON TABLE acl FROM yala;
GRANT ALL ON TABLE acl TO yala;
GRANT ALL ON TABLE acl TO PUBLIC;


--
-- Name: apps; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE apps FROM PUBLIC;
REVOKE ALL ON TABLE apps FROM yala;
GRANT ALL ON TABLE apps TO yala;
GRANT ALL ON TABLE apps TO PUBLIC;


--
-- Name: apps_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE apps_acl FROM PUBLIC;
REVOKE ALL ON TABLE apps_acl FROM yala;
GRANT ALL ON TABLE apps_acl TO yala;
GRANT SELECT ON TABLE apps_acl TO PUBLIC;


--
-- Name: apps_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE apps_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE apps_id_seq FROM yala;
GRANT ALL ON SEQUENCE apps_id_seq TO yala;
GRANT ALL ON SEQUENCE apps_id_seq TO PUBLIC;


--
-- Name: apps_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE apps_view FROM PUBLIC;
REVOKE ALL ON TABLE apps_view FROM yala;
GRANT ALL ON TABLE apps_view TO yala;
GRANT ALL ON TABLE apps_view TO PUBLIC;


--
-- Name: course_schedule; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE course_schedule FROM PUBLIC;
REVOKE ALL ON TABLE course_schedule FROM yala;
GRANT ALL ON TABLE course_schedule TO yala;
GRANT ALL ON TABLE course_schedule TO PUBLIC;


--
-- Name: course_schedule_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE course_schedule_acl FROM PUBLIC;
REVOKE ALL ON TABLE course_schedule_acl FROM yala;
GRANT ALL ON TABLE course_schedule_acl TO yala;
GRANT SELECT ON TABLE course_schedule_acl TO PUBLIC;


--
-- Name: course_schedule_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE course_schedule_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE course_schedule_id_seq FROM yala;
GRANT ALL ON SEQUENCE course_schedule_id_seq TO yala;
GRANT ALL ON SEQUENCE course_schedule_id_seq TO PUBLIC;


--
-- Name: course_schedule_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE course_schedule_view FROM PUBLIC;
REVOKE ALL ON TABLE course_schedule_view FROM yala;
GRANT ALL ON TABLE course_schedule_view TO yala;
GRANT ALL ON TABLE course_schedule_view TO PUBLIC;


--
-- Name: course_units; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE course_units FROM PUBLIC;
REVOKE ALL ON TABLE course_units FROM yala;
GRANT ALL ON TABLE course_units TO yala;
GRANT ALL ON TABLE course_units TO PUBLIC;


--
-- Name: course_units_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE course_units_acl FROM PUBLIC;
REVOKE ALL ON TABLE course_units_acl FROM yala;
GRANT ALL ON TABLE course_units_acl TO yala;
GRANT SELECT ON TABLE course_units_acl TO PUBLIC;


--
-- Name: course_units_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE course_units_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE course_units_id_seq FROM yala;
GRANT ALL ON SEQUENCE course_units_id_seq TO yala;
GRANT ALL ON SEQUENCE course_units_id_seq TO PUBLIC;


--
-- Name: course_units_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE course_units_view FROM PUBLIC;
REVOKE ALL ON TABLE course_units_view FROM yala;
GRANT ALL ON TABLE course_units_view TO yala;
GRANT ALL ON TABLE course_units_view TO PUBLIC;


--
-- Name: courses; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE courses FROM PUBLIC;
REVOKE ALL ON TABLE courses FROM yala;
GRANT ALL ON TABLE courses TO yala;
GRANT ALL ON TABLE courses TO PUBLIC;


--
-- Name: courses_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE courses_acl FROM PUBLIC;
REVOKE ALL ON TABLE courses_acl FROM yala;
GRANT ALL ON TABLE courses_acl TO yala;
GRANT SELECT ON TABLE courses_acl TO PUBLIC;


--
-- Name: courses_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE courses_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE courses_id_seq FROM yala;
GRANT ALL ON SEQUENCE courses_id_seq TO yala;
GRANT ALL ON SEQUENCE courses_id_seq TO PUBLIC;


--
-- Name: courses_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE courses_view FROM PUBLIC;
REVOKE ALL ON TABLE courses_view FROM yala;
GRANT ALL ON TABLE courses_view TO yala;
GRANT ALL ON TABLE courses_view TO PUBLIC;


--
-- Name: exam_grades; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE exam_grades FROM PUBLIC;
REVOKE ALL ON TABLE exam_grades FROM yala;
GRANT ALL ON TABLE exam_grades TO yala;
GRANT ALL ON TABLE exam_grades TO PUBLIC;


--
-- Name: exam_grades_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE exam_grades_acl FROM PUBLIC;
REVOKE ALL ON TABLE exam_grades_acl FROM yala;
GRANT ALL ON TABLE exam_grades_acl TO yala;
GRANT SELECT ON TABLE exam_grades_acl TO PUBLIC;


--
-- Name: exam_grades_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE exam_grades_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE exam_grades_id_seq FROM yala;
GRANT ALL ON SEQUENCE exam_grades_id_seq TO yala;
GRANT ALL ON SEQUENCE exam_grades_id_seq TO PUBLIC;


--
-- Name: exam_grades_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE exam_grades_view FROM PUBLIC;
REVOKE ALL ON TABLE exam_grades_view FROM yala;
GRANT ALL ON TABLE exam_grades_view TO yala;
GRANT ALL ON TABLE exam_grades_view TO PUBLIC;


--
-- Name: exams; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE exams FROM PUBLIC;
REVOKE ALL ON TABLE exams FROM yala;
GRANT ALL ON TABLE exams TO yala;
GRANT ALL ON TABLE exams TO PUBLIC;


--
-- Name: exams_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE exams_acl FROM PUBLIC;
REVOKE ALL ON TABLE exams_acl FROM yala;
GRANT ALL ON TABLE exams_acl TO yala;
GRANT SELECT ON TABLE exams_acl TO PUBLIC;


--
-- Name: exams_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE exams_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE exams_id_seq FROM yala;
GRANT ALL ON SEQUENCE exams_id_seq TO yala;
GRANT ALL ON SEQUENCE exams_id_seq TO PUBLIC;


--
-- Name: exams_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE exams_view FROM PUBLIC;
REVOKE ALL ON TABLE exams_view FROM yala;
GRANT ALL ON TABLE exams_view TO yala;
GRANT ALL ON TABLE exams_view TO PUBLIC;


--
-- Name: files; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE files FROM PUBLIC;
REVOKE ALL ON TABLE files FROM yala;
GRANT ALL ON TABLE files TO yala;
GRANT ALL ON TABLE files TO PUBLIC;


--
-- Name: files_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE files_acl FROM PUBLIC;
REVOKE ALL ON TABLE files_acl FROM yala;
GRANT ALL ON TABLE files_acl TO yala;
GRANT SELECT ON TABLE files_acl TO PUBLIC;


--
-- Name: files_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE files_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE files_id_seq FROM yala;
GRANT ALL ON SEQUENCE files_id_seq TO yala;
GRANT ALL ON SEQUENCE files_id_seq TO PUBLIC;


--
-- Name: files_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE files_view FROM PUBLIC;
REVOKE ALL ON TABLE files_view FROM yala;
GRANT ALL ON TABLE files_view TO yala;
GRANT ALL ON TABLE files_view TO PUBLIC;


--
-- Name: google_tokens; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE google_tokens FROM PUBLIC;
REVOKE ALL ON TABLE google_tokens FROM yala;
GRANT ALL ON TABLE google_tokens TO yala;
GRANT ALL ON TABLE google_tokens TO PUBLIC;


--
-- Name: google_tokens_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE google_tokens_acl FROM PUBLIC;
REVOKE ALL ON TABLE google_tokens_acl FROM yala;
GRANT ALL ON TABLE google_tokens_acl TO yala;
GRANT SELECT ON TABLE google_tokens_acl TO PUBLIC;


--
-- Name: google_tokens_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE google_tokens_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE google_tokens_id_seq FROM yala;
GRANT ALL ON SEQUENCE google_tokens_id_seq TO yala;
GRANT ALL ON SEQUENCE google_tokens_id_seq TO PUBLIC;


--
-- Name: google_tokens_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE google_tokens_view FROM PUBLIC;
REVOKE ALL ON TABLE google_tokens_view FROM yala;
GRANT ALL ON TABLE google_tokens_view TO yala;
GRANT ALL ON TABLE google_tokens_view TO PUBLIC;


--
-- Name: group_users; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE group_users FROM PUBLIC;
REVOKE ALL ON TABLE group_users FROM yala;
GRANT ALL ON TABLE group_users TO yala;
GRANT ALL ON TABLE group_users TO PUBLIC;


--
-- Name: group_users_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE group_users_acl FROM PUBLIC;
REVOKE ALL ON TABLE group_users_acl FROM yala;
GRANT ALL ON TABLE group_users_acl TO yala;
GRANT SELECT ON TABLE group_users_acl TO PUBLIC;


--
-- Name: group_users_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE group_users_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE group_users_id_seq FROM yala;
GRANT ALL ON SEQUENCE group_users_id_seq TO yala;
GRANT ALL ON SEQUENCE group_users_id_seq TO PUBLIC;


--
-- Name: group_users_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE group_users_view FROM PUBLIC;
REVOKE ALL ON TABLE group_users_view FROM yala;
GRANT ALL ON TABLE group_users_view TO yala;
GRANT ALL ON TABLE group_users_view TO PUBLIC;


--
-- Name: groups; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE groups FROM PUBLIC;
REVOKE ALL ON TABLE groups FROM yala;
GRANT ALL ON TABLE groups TO yala;
GRANT ALL ON TABLE groups TO PUBLIC;


--
-- Name: groups_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE groups_acl FROM PUBLIC;
REVOKE ALL ON TABLE groups_acl FROM yala;
GRANT ALL ON TABLE groups_acl TO yala;
GRANT SELECT ON TABLE groups_acl TO PUBLIC;


--
-- Name: groups_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE groups_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE groups_id_seq FROM yala;
GRANT ALL ON SEQUENCE groups_id_seq TO yala;
GRANT ALL ON SEQUENCE groups_id_seq TO PUBLIC;


--
-- Name: groups_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE groups_view FROM PUBLIC;
REVOKE ALL ON TABLE groups_view FROM yala;
GRANT ALL ON TABLE groups_view TO yala;
GRANT ALL ON TABLE groups_view TO PUBLIC;


--
-- Name: lesson_presence; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE lesson_presence FROM PUBLIC;
REVOKE ALL ON TABLE lesson_presence FROM yala;
GRANT ALL ON TABLE lesson_presence TO yala;
GRANT ALL ON TABLE lesson_presence TO PUBLIC;


--
-- Name: lesson_presence_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE lesson_presence_acl FROM PUBLIC;
REVOKE ALL ON TABLE lesson_presence_acl FROM yala;
GRANT ALL ON TABLE lesson_presence_acl TO yala;
GRANT SELECT ON TABLE lesson_presence_acl TO PUBLIC;


--
-- Name: lesson_presence_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE lesson_presence_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE lesson_presence_id_seq FROM yala;
GRANT ALL ON SEQUENCE lesson_presence_id_seq TO yala;
GRANT ALL ON SEQUENCE lesson_presence_id_seq TO PUBLIC;


--
-- Name: lesson_presence_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE lesson_presence_view FROM PUBLIC;
REVOKE ALL ON TABLE lesson_presence_view FROM yala;
GRANT ALL ON TABLE lesson_presence_view TO yala;
GRANT ALL ON TABLE lesson_presence_view TO PUBLIC;


--
-- Name: lessons; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE lessons FROM PUBLIC;
REVOKE ALL ON TABLE lessons FROM yala;
GRANT ALL ON TABLE lessons TO yala;
GRANT ALL ON TABLE lessons TO PUBLIC;


--
-- Name: lessons_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE lessons_acl FROM PUBLIC;
REVOKE ALL ON TABLE lessons_acl FROM yala;
GRANT ALL ON TABLE lessons_acl TO yala;
GRANT SELECT ON TABLE lessons_acl TO PUBLIC;


--
-- Name: lessons_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE lessons_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE lessons_id_seq FROM yala;
GRANT ALL ON SEQUENCE lessons_id_seq TO yala;
GRANT ALL ON SEQUENCE lessons_id_seq TO PUBLIC;


--
-- Name: lessons_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE lessons_view FROM PUBLIC;
REVOKE ALL ON TABLE lessons_view FROM yala;
GRANT ALL ON TABLE lessons_view TO yala;
GRANT ALL ON TABLE lessons_view TO PUBLIC;


--
-- Name: message_attachments; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE message_attachments FROM PUBLIC;
REVOKE ALL ON TABLE message_attachments FROM yala;
GRANT ALL ON TABLE message_attachments TO yala;
GRANT ALL ON TABLE message_attachments TO PUBLIC;


--
-- Name: message_attachments_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE message_attachments_acl FROM PUBLIC;
REVOKE ALL ON TABLE message_attachments_acl FROM yala;
GRANT ALL ON TABLE message_attachments_acl TO yala;
GRANT SELECT ON TABLE message_attachments_acl TO PUBLIC;


--
-- Name: message_attachments_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE message_attachments_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE message_attachments_id_seq FROM yala;
GRANT ALL ON SEQUENCE message_attachments_id_seq TO yala;
GRANT ALL ON SEQUENCE message_attachments_id_seq TO PUBLIC;


--
-- Name: message_attachments_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE message_attachments_view FROM PUBLIC;
REVOKE ALL ON TABLE message_attachments_view FROM yala;
GRANT ALL ON TABLE message_attachments_view TO yala;
GRANT ALL ON TABLE message_attachments_view TO PUBLIC;


--
-- Name: message_users; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE message_users FROM PUBLIC;
REVOKE ALL ON TABLE message_users FROM yala;
GRANT ALL ON TABLE message_users TO yala;
GRANT ALL ON TABLE message_users TO PUBLIC;


--
-- Name: message_users_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE message_users_acl FROM PUBLIC;
REVOKE ALL ON TABLE message_users_acl FROM yala;
GRANT ALL ON TABLE message_users_acl TO yala;
GRANT SELECT ON TABLE message_users_acl TO PUBLIC;


--
-- Name: message_users_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE message_users_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE message_users_id_seq FROM yala;
GRANT ALL ON SEQUENCE message_users_id_seq TO yala;
GRANT ALL ON SEQUENCE message_users_id_seq TO PUBLIC;


--
-- Name: message_users_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE message_users_view FROM PUBLIC;
REVOKE ALL ON TABLE message_users_view FROM yala;
GRANT ALL ON TABLE message_users_view TO yala;
GRANT ALL ON TABLE message_users_view TO PUBLIC;


--
-- Name: messages; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE messages FROM PUBLIC;
REVOKE ALL ON TABLE messages FROM yala;
GRANT ALL ON TABLE messages TO yala;
GRANT ALL ON TABLE messages TO PUBLIC;


--
-- Name: messages_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE messages_acl FROM PUBLIC;
REVOKE ALL ON TABLE messages_acl FROM yala;
GRANT ALL ON TABLE messages_acl TO yala;
GRANT SELECT ON TABLE messages_acl TO PUBLIC;


--
-- Name: messages_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE messages_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE messages_id_seq FROM yala;
GRANT ALL ON SEQUENCE messages_id_seq TO yala;
GRANT ALL ON SEQUENCE messages_id_seq TO PUBLIC;


--
-- Name: messages_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE messages_view FROM PUBLIC;
REVOKE ALL ON TABLE messages_view FROM yala;
GRANT ALL ON TABLE messages_view TO yala;
GRANT ALL ON TABLE messages_view TO PUBLIC;


--
-- Name: poland; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE poland FROM PUBLIC;
REVOKE ALL ON TABLE poland FROM yala;
GRANT ALL ON TABLE poland TO yala;
GRANT ALL ON TABLE poland TO PUBLIC;


--
-- Name: poland_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE poland_acl FROM PUBLIC;
REVOKE ALL ON TABLE poland_acl FROM yala;
GRANT ALL ON TABLE poland_acl TO yala;
GRANT SELECT ON TABLE poland_acl TO PUBLIC;


--
-- Name: poland_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE poland_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE poland_id_seq FROM yala;
GRANT ALL ON SEQUENCE poland_id_seq TO yala;
GRANT ALL ON SEQUENCE poland_id_seq TO PUBLIC;


--
-- Name: poland_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE poland_view FROM PUBLIC;
REVOKE ALL ON TABLE poland_view FROM yala;
GRANT ALL ON TABLE poland_view TO yala;
GRANT ALL ON TABLE poland_view TO PUBLIC;


--
-- Name: projects; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE projects FROM PUBLIC;
REVOKE ALL ON TABLE projects FROM yala;
GRANT ALL ON TABLE projects TO yala;
GRANT ALL ON TABLE projects TO PUBLIC;


--
-- Name: projects_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE projects_acl FROM PUBLIC;
REVOKE ALL ON TABLE projects_acl FROM yala;
GRANT ALL ON TABLE projects_acl TO yala;
GRANT SELECT ON TABLE projects_acl TO PUBLIC;


--
-- Name: projects_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE projects_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE projects_id_seq FROM yala;
GRANT ALL ON SEQUENCE projects_id_seq TO yala;
GRANT ALL ON SEQUENCE projects_id_seq TO PUBLIC;


--
-- Name: projects_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE projects_view FROM PUBLIC;
REVOKE ALL ON TABLE projects_view FROM yala;
GRANT ALL ON TABLE projects_view TO yala;
GRANT ALL ON TABLE projects_view TO PUBLIC;


--
-- Name: quiz_scores; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE quiz_scores FROM PUBLIC;
REVOKE ALL ON TABLE quiz_scores FROM yala;
GRANT ALL ON TABLE quiz_scores TO yala;
GRANT ALL ON TABLE quiz_scores TO PUBLIC;


--
-- Name: quiz_scores_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE quiz_scores_acl FROM PUBLIC;
REVOKE ALL ON TABLE quiz_scores_acl FROM yala;
GRANT ALL ON TABLE quiz_scores_acl TO yala;
GRANT SELECT ON TABLE quiz_scores_acl TO PUBLIC;


--
-- Name: quiz_scores_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE quiz_scores_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE quiz_scores_id_seq FROM yala;
GRANT ALL ON SEQUENCE quiz_scores_id_seq TO yala;
GRANT ALL ON SEQUENCE quiz_scores_id_seq TO PUBLIC;


--
-- Name: quiz_scores_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE quiz_scores_view FROM PUBLIC;
REVOKE ALL ON TABLE quiz_scores_view FROM yala;
GRANT ALL ON TABLE quiz_scores_view TO yala;
GRANT ALL ON TABLE quiz_scores_view TO PUBLIC;


--
-- Name: quiz_users; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE quiz_users FROM PUBLIC;
REVOKE ALL ON TABLE quiz_users FROM yala;
GRANT ALL ON TABLE quiz_users TO yala;
GRANT ALL ON TABLE quiz_users TO PUBLIC;


--
-- Name: quiz_users_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE quiz_users_acl FROM PUBLIC;
REVOKE ALL ON TABLE quiz_users_acl FROM yala;
GRANT ALL ON TABLE quiz_users_acl TO yala;
GRANT SELECT ON TABLE quiz_users_acl TO PUBLIC;


--
-- Name: quiz_users_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE quiz_users_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE quiz_users_id_seq FROM yala;
GRANT ALL ON SEQUENCE quiz_users_id_seq TO yala;
GRANT ALL ON SEQUENCE quiz_users_id_seq TO PUBLIC;


--
-- Name: quiz_users_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE quiz_users_view FROM PUBLIC;
REVOKE ALL ON TABLE quiz_users_view FROM yala;
GRANT ALL ON TABLE quiz_users_view TO yala;
GRANT ALL ON TABLE quiz_users_view TO PUBLIC;


--
-- Name: quizzes; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE quizzes FROM PUBLIC;
REVOKE ALL ON TABLE quizzes FROM yala;
GRANT ALL ON TABLE quizzes TO yala;
GRANT ALL ON TABLE quizzes TO PUBLIC;


--
-- Name: quizzes_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE quizzes_acl FROM PUBLIC;
REVOKE ALL ON TABLE quizzes_acl FROM yala;
GRANT ALL ON TABLE quizzes_acl TO yala;
GRANT SELECT ON TABLE quizzes_acl TO PUBLIC;


--
-- Name: quizzes_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE quizzes_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE quizzes_id_seq FROM yala;
GRANT ALL ON SEQUENCE quizzes_id_seq TO yala;
GRANT ALL ON SEQUENCE quizzes_id_seq TO PUBLIC;


--
-- Name: quizzes_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE quizzes_view FROM PUBLIC;
REVOKE ALL ON TABLE quizzes_view FROM yala;
GRANT ALL ON TABLE quizzes_view TO yala;
GRANT ALL ON TABLE quizzes_view TO PUBLIC;


--
-- Name: reports; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE reports FROM PUBLIC;
REVOKE ALL ON TABLE reports FROM yala;
GRANT ALL ON TABLE reports TO yala;
GRANT ALL ON TABLE reports TO PUBLIC;


--
-- Name: reports_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE reports_acl FROM PUBLIC;
REVOKE ALL ON TABLE reports_acl FROM yala;
GRANT ALL ON TABLE reports_acl TO yala;
GRANT SELECT ON TABLE reports_acl TO PUBLIC;


--
-- Name: reports_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE reports_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE reports_id_seq FROM yala;
GRANT ALL ON SEQUENCE reports_id_seq TO yala;
GRANT ALL ON SEQUENCE reports_id_seq TO PUBLIC;


--
-- Name: reports_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE reports_view FROM PUBLIC;
REVOKE ALL ON TABLE reports_view FROM yala;
GRANT ALL ON TABLE reports_view TO yala;
GRANT ALL ON TABLE reports_view TO PUBLIC;


--
-- Name: resource_types; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE resource_types FROM PUBLIC;
REVOKE ALL ON TABLE resource_types FROM yala;
GRANT ALL ON TABLE resource_types TO yala;
GRANT ALL ON TABLE resource_types TO PUBLIC;


--
-- Name: resource_types_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE resource_types_acl FROM PUBLIC;
REVOKE ALL ON TABLE resource_types_acl FROM yala;
GRANT ALL ON TABLE resource_types_acl TO yala;
GRANT SELECT ON TABLE resource_types_acl TO PUBLIC;


--
-- Name: resource_types_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE resource_types_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE resource_types_id_seq FROM yala;
GRANT ALL ON SEQUENCE resource_types_id_seq TO yala;
GRANT ALL ON SEQUENCE resource_types_id_seq TO PUBLIC;


--
-- Name: resource_types_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE resource_types_view FROM PUBLIC;
REVOKE ALL ON TABLE resource_types_view FROM yala;
GRANT ALL ON TABLE resource_types_view TO yala;
GRANT ALL ON TABLE resource_types_view TO PUBLIC;


--
-- Name: resources; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE resources FROM PUBLIC;
REVOKE ALL ON TABLE resources FROM yala;
GRANT ALL ON TABLE resources TO yala;
GRANT ALL ON TABLE resources TO PUBLIC;


--
-- Name: resources_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE resources_acl FROM PUBLIC;
REVOKE ALL ON TABLE resources_acl FROM yala;
GRANT ALL ON TABLE resources_acl TO yala;
GRANT SELECT ON TABLE resources_acl TO PUBLIC;


--
-- Name: resources_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE resources_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE resources_id_seq FROM yala;
GRANT ALL ON SEQUENCE resources_id_seq TO yala;
GRANT ALL ON SEQUENCE resources_id_seq TO PUBLIC;


--
-- Name: resources_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE resources_view FROM PUBLIC;
REVOKE ALL ON TABLE resources_view FROM yala;
GRANT ALL ON TABLE resources_view TO yala;
GRANT ALL ON TABLE resources_view TO PUBLIC;


--
-- Name: roles; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE roles FROM PUBLIC;
REVOKE ALL ON TABLE roles FROM yala;
GRANT ALL ON TABLE roles TO yala;
GRANT ALL ON TABLE roles TO PUBLIC;


--
-- Name: roles_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE roles_acl FROM PUBLIC;
REVOKE ALL ON TABLE roles_acl FROM yala;
GRANT ALL ON TABLE roles_acl TO yala;
GRANT SELECT ON TABLE roles_acl TO PUBLIC;


--
-- Name: roles_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE roles_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE roles_id_seq FROM yala;
GRANT ALL ON SEQUENCE roles_id_seq TO yala;
GRANT ALL ON SEQUENCE roles_id_seq TO PUBLIC;


--
-- Name: roles_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE roles_view FROM PUBLIC;
REVOKE ALL ON TABLE roles_view FROM yala;
GRANT ALL ON TABLE roles_view TO yala;
GRANT ALL ON TABLE roles_view TO PUBLIC;


--
-- Name: rooms; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE rooms FROM PUBLIC;
REVOKE ALL ON TABLE rooms FROM yala;
GRANT ALL ON TABLE rooms TO yala;
GRANT ALL ON TABLE rooms TO PUBLIC;


--
-- Name: rooms_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE rooms_acl FROM PUBLIC;
REVOKE ALL ON TABLE rooms_acl FROM yala;
GRANT ALL ON TABLE rooms_acl TO yala;
GRANT SELECT ON TABLE rooms_acl TO PUBLIC;


--
-- Name: rooms_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE rooms_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE rooms_id_seq FROM yala;
GRANT ALL ON SEQUENCE rooms_id_seq TO yala;
GRANT ALL ON SEQUENCE rooms_id_seq TO PUBLIC;


--
-- Name: rooms_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE rooms_view FROM PUBLIC;
REVOKE ALL ON TABLE rooms_view FROM yala;
GRANT ALL ON TABLE rooms_view TO yala;
GRANT ALL ON TABLE rooms_view TO PUBLIC;


--
-- Name: survey_detailed_results; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_detailed_results FROM PUBLIC;
REVOKE ALL ON TABLE survey_detailed_results FROM yala;
GRANT ALL ON TABLE survey_detailed_results TO yala;
GRANT ALL ON TABLE survey_detailed_results TO PUBLIC;


--
-- Name: survey_detailed_results_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_detailed_results_acl FROM PUBLIC;
REVOKE ALL ON TABLE survey_detailed_results_acl FROM yala;
GRANT ALL ON TABLE survey_detailed_results_acl TO yala;
GRANT SELECT ON TABLE survey_detailed_results_acl TO PUBLIC;


--
-- Name: survey_detailed_results_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE survey_detailed_results_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE survey_detailed_results_id_seq FROM yala;
GRANT ALL ON SEQUENCE survey_detailed_results_id_seq TO yala;
GRANT ALL ON SEQUENCE survey_detailed_results_id_seq TO PUBLIC;


--
-- Name: survey_detailed_results_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_detailed_results_view FROM PUBLIC;
REVOKE ALL ON TABLE survey_detailed_results_view FROM yala;
GRANT ALL ON TABLE survey_detailed_results_view TO yala;
GRANT ALL ON TABLE survey_detailed_results_view TO PUBLIC;


--
-- Name: survey_possible_answers; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_possible_answers FROM PUBLIC;
REVOKE ALL ON TABLE survey_possible_answers FROM yala;
GRANT ALL ON TABLE survey_possible_answers TO yala;
GRANT ALL ON TABLE survey_possible_answers TO PUBLIC;


--
-- Name: survey_possible_answers_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_possible_answers_acl FROM PUBLIC;
REVOKE ALL ON TABLE survey_possible_answers_acl FROM yala;
GRANT ALL ON TABLE survey_possible_answers_acl TO yala;
GRANT SELECT ON TABLE survey_possible_answers_acl TO PUBLIC;


--
-- Name: survey_possible_answers_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE survey_possible_answers_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE survey_possible_answers_id_seq FROM yala;
GRANT ALL ON SEQUENCE survey_possible_answers_id_seq TO yala;
GRANT ALL ON SEQUENCE survey_possible_answers_id_seq TO PUBLIC;


--
-- Name: survey_possible_answers_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_possible_answers_view FROM PUBLIC;
REVOKE ALL ON TABLE survey_possible_answers_view FROM yala;
GRANT ALL ON TABLE survey_possible_answers_view TO yala;
GRANT ALL ON TABLE survey_possible_answers_view TO PUBLIC;


--
-- Name: survey_questions; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_questions FROM PUBLIC;
REVOKE ALL ON TABLE survey_questions FROM yala;
GRANT ALL ON TABLE survey_questions TO yala;
GRANT ALL ON TABLE survey_questions TO PUBLIC;


--
-- Name: survey_questions_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_questions_acl FROM PUBLIC;
REVOKE ALL ON TABLE survey_questions_acl FROM yala;
GRANT ALL ON TABLE survey_questions_acl TO yala;
GRANT SELECT ON TABLE survey_questions_acl TO PUBLIC;


--
-- Name: survey_questions_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE survey_questions_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE survey_questions_id_seq FROM yala;
GRANT ALL ON SEQUENCE survey_questions_id_seq TO yala;
GRANT ALL ON SEQUENCE survey_questions_id_seq TO PUBLIC;


--
-- Name: survey_questions_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_questions_view FROM PUBLIC;
REVOKE ALL ON TABLE survey_questions_view FROM yala;
GRANT ALL ON TABLE survey_questions_view TO yala;
GRANT ALL ON TABLE survey_questions_view TO PUBLIC;


--
-- Name: survey_results; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_results FROM PUBLIC;
REVOKE ALL ON TABLE survey_results FROM yala;
GRANT ALL ON TABLE survey_results TO yala;
GRANT ALL ON TABLE survey_results TO PUBLIC;


--
-- Name: survey_results_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_results_acl FROM PUBLIC;
REVOKE ALL ON TABLE survey_results_acl FROM yala;
GRANT ALL ON TABLE survey_results_acl TO yala;
GRANT SELECT ON TABLE survey_results_acl TO PUBLIC;


--
-- Name: survey_results_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE survey_results_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE survey_results_id_seq FROM yala;
GRANT ALL ON SEQUENCE survey_results_id_seq TO yala;
GRANT ALL ON SEQUENCE survey_results_id_seq TO PUBLIC;


--
-- Name: survey_results_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_results_view FROM PUBLIC;
REVOKE ALL ON TABLE survey_results_view FROM yala;
GRANT ALL ON TABLE survey_results_view TO yala;
GRANT ALL ON TABLE survey_results_view TO PUBLIC;


--
-- Name: survey_users; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_users FROM PUBLIC;
REVOKE ALL ON TABLE survey_users FROM yala;
GRANT ALL ON TABLE survey_users TO yala;
GRANT ALL ON TABLE survey_users TO PUBLIC;


--
-- Name: survey_users_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_users_acl FROM PUBLIC;
REVOKE ALL ON TABLE survey_users_acl FROM yala;
GRANT ALL ON TABLE survey_users_acl TO yala;
GRANT SELECT ON TABLE survey_users_acl TO PUBLIC;


--
-- Name: survey_users_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE survey_users_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE survey_users_id_seq FROM yala;
GRANT ALL ON SEQUENCE survey_users_id_seq TO yala;
GRANT ALL ON SEQUENCE survey_users_id_seq TO PUBLIC;


--
-- Name: survey_users_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE survey_users_view FROM PUBLIC;
REVOKE ALL ON TABLE survey_users_view FROM yala;
GRANT ALL ON TABLE survey_users_view TO yala;
GRANT ALL ON TABLE survey_users_view TO PUBLIC;


--
-- Name: surveys; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE surveys FROM PUBLIC;
REVOKE ALL ON TABLE surveys FROM yala;
GRANT ALL ON TABLE surveys TO yala;
GRANT ALL ON TABLE surveys TO PUBLIC;


--
-- Name: surveys_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE surveys_acl FROM PUBLIC;
REVOKE ALL ON TABLE surveys_acl FROM yala;
GRANT ALL ON TABLE surveys_acl TO yala;
GRANT SELECT ON TABLE surveys_acl TO PUBLIC;


--
-- Name: surveys_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE surveys_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE surveys_id_seq FROM yala;
GRANT ALL ON SEQUENCE surveys_id_seq TO yala;
GRANT ALL ON SEQUENCE surveys_id_seq TO PUBLIC;


--
-- Name: surveys_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE surveys_view FROM PUBLIC;
REVOKE ALL ON TABLE surveys_view FROM yala;
GRANT ALL ON TABLE surveys_view TO yala;
GRANT ALL ON TABLE surveys_view TO PUBLIC;


--
-- Name: training_centers; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE training_centers FROM PUBLIC;
REVOKE ALL ON TABLE training_centers FROM yala;
GRANT ALL ON TABLE training_centers TO yala;
GRANT ALL ON TABLE training_centers TO PUBLIC;


--
-- Name: training_centers_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE training_centers_acl FROM PUBLIC;
REVOKE ALL ON TABLE training_centers_acl FROM yala;
GRANT ALL ON TABLE training_centers_acl TO yala;
GRANT SELECT ON TABLE training_centers_acl TO PUBLIC;


--
-- Name: training_centers_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE training_centers_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE training_centers_id_seq FROM yala;
GRANT ALL ON SEQUENCE training_centers_id_seq TO yala;
GRANT ALL ON SEQUENCE training_centers_id_seq TO PUBLIC;


--
-- Name: training_centers_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE training_centers_view FROM PUBLIC;
REVOKE ALL ON TABLE training_centers_view FROM yala;
GRANT ALL ON TABLE training_centers_view TO yala;
GRANT ALL ON TABLE training_centers_view TO PUBLIC;


--
-- Name: user_profile; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE user_profile FROM PUBLIC;
REVOKE ALL ON TABLE user_profile FROM yala;
GRANT ALL ON TABLE user_profile TO yala;
GRANT ALL ON TABLE user_profile TO PUBLIC;


--
-- Name: user_profile_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE user_profile_acl FROM PUBLIC;
REVOKE ALL ON TABLE user_profile_acl FROM yala;
GRANT ALL ON TABLE user_profile_acl TO yala;
GRANT SELECT ON TABLE user_profile_acl TO PUBLIC;


--
-- Name: user_profile_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE user_profile_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE user_profile_id_seq FROM yala;
GRANT ALL ON SEQUENCE user_profile_id_seq TO yala;
GRANT ALL ON SEQUENCE user_profile_id_seq TO PUBLIC;


--
-- Name: user_profile_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE user_profile_view FROM PUBLIC;
REVOKE ALL ON TABLE user_profile_view FROM yala;
GRANT ALL ON TABLE user_profile_view TO yala;
GRANT ALL ON TABLE user_profile_view TO PUBLIC;


--
-- Name: users; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE users FROM PUBLIC;
REVOKE ALL ON TABLE users FROM yala;
GRANT ALL ON TABLE users TO yala;
GRANT ALL ON TABLE users TO PUBLIC;


--
-- Name: users_acl; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE users_acl FROM PUBLIC;
REVOKE ALL ON TABLE users_acl FROM yala;
GRANT ALL ON TABLE users_acl TO yala;
GRANT SELECT ON TABLE users_acl TO PUBLIC;


--
-- Name: users_id_seq; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON SEQUENCE users_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE users_id_seq FROM yala;
GRANT ALL ON SEQUENCE users_id_seq TO yala;
GRANT ALL ON SEQUENCE users_id_seq TO PUBLIC;


--
-- Name: users_view; Type: ACL; Schema: public; Owner: yala
--

REVOKE ALL ON TABLE users_view FROM PUBLIC;
REVOKE ALL ON TABLE users_view FROM yala;
GRANT ALL ON TABLE users_view TO yala;
GRANT ALL ON TABLE users_view TO PUBLIC;


--
-- PostgreSQL database dump complete
--

